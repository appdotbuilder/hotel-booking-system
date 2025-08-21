<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Only superadmin can manage users
        if (!auth()->user()?->isSuperadmin()) {
            abort(403, 'Unauthorized.');
        }

        $query = User::query();

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%");
            });
        }

        $users = $query->latest()->paginate(15)->withQueryString();

        $filters = [
            'roles' => ['superadmin', 'admin', 'staff', 'guest'],
        ];

        return Inertia::render('users/index', [
            'users' => $users,
            'filters' => $filters,
            'queryParams' => $request->query(),
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        // Only superadmin can view user details
        if (!auth()->user()?->isSuperadmin()) {
            abort(403, 'Unauthorized.');
        }

        $user->load(['bookings' => function ($query) {
            $query->with('room')->latest()->limit(10);
        }]);

        return Inertia::render('users/show', [
            'user' => $user,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        // Only superadmin can edit users
        if (!auth()->user()?->isSuperadmin()) {
            abort(403, 'Unauthorized.');
        }

        return Inertia::render('users/edit', [
            'user' => $user,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        // Only superadmin can update users
        if (!auth()->user()?->isSuperadmin()) {
            abort(403, 'Unauthorized.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'role' => 'required|in:superadmin,admin,staff,guest',
        ]);

        $user->update($validated);

        return redirect()->route('users.show', $user)
            ->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        // Only superadmin can delete users
        if (!auth()->user()?->isSuperadmin()) {
            abort(403, 'Unauthorized.');
        }

        // Prevent deleting the last superadmin
        if ($user->isSuperadmin() && User::where('role', 'superadmin')->count() <= 1) {
            return redirect()->route('users.index')
                ->with('error', 'Cannot delete the last superadmin.');
        }

        // Check if user has active bookings
        $activeBookings = $user->bookings()
            ->whereIn('status', ['confirmed', 'checked_in'])
            ->where('check_out_date', '>=', now())
            ->exists();

        if ($activeBookings) {
            return redirect()->route('users.index')
                ->with('error', 'Cannot delete user with active bookings.');
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'User deleted successfully.');
    }
}