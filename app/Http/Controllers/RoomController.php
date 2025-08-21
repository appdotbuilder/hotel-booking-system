<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRoomRequest;
use App\Http\Requests\UpdateRoomRequest;
use App\Models\Room;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RoomController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Room::query();

        // Apply filters
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('capacity')) {
            $query->where('capacity', '>=', $request->capacity);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('number', 'like', "%{$request->search}%")
                  ->orWhere('description', 'like', "%{$request->search}%");
            });
        }

        $rooms = $query->latest()->paginate(12)->withQueryString();

        $filters = [
            'types' => Room::distinct()->pluck('type'),
            'statuses' => ['available', 'maintenance', 'out_of_order'],
        ];

        return Inertia::render('rooms/index', [
            'rooms' => $rooms,
            'filters' => $filters,
            'queryParams' => $request->query(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('rooms/create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRoomRequest $request)
    {
        $room = Room::create($request->validated());

        return redirect()->route('rooms.show', $room)
            ->with('success', 'Room created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Room $room)
    {
        $room->load(['bookings' => function ($query) {
            $query->with('user')
                  ->where('status', '!=', 'cancelled')
                  ->where('check_out_date', '>=', now())
                  ->orderBy('check_in_date');
        }]);

        return Inertia::render('rooms/show', [
            'room' => $room,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Room $room)
    {
        return Inertia::render('rooms/edit', [
            'room' => $room,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRoomRequest $request, Room $room)
    {
        $room->update($request->validated());

        return redirect()->route('rooms.show', $room)
            ->with('success', 'Room updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Room $room)
    {
        // Check if room has active bookings
        $activeBookings = $room->bookings()
            ->whereIn('status', ['confirmed', 'checked_in'])
            ->where('check_out_date', '>=', now())
            ->exists();

        if ($activeBookings) {
            return redirect()->route('rooms.index')
                ->with('error', 'Cannot delete room with active bookings.');
        }

        $room->delete();

        return redirect()->route('rooms.index')
            ->with('success', 'Room deleted successfully.');
    }
}