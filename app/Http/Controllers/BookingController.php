<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBookingRequest;
use App\Http\Requests\UpdateBookingRequest;
use App\Models\Booking;
use App\Models\Room;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BookingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Booking::with(['room', 'user']);

        // Filter by user role
        if (!$user->isStaff()) {
            $query->where('user_id', $user->id);
        }

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('room_id')) {
            $query->where('room_id', $request->room_id);
        }

        if ($request->filled('check_in_date')) {
            $query->whereDate('check_in_date', '>=', $request->check_in_date);
        }

        if ($request->filled('guest_name')) {
            $query->where('guest_name', 'like', "%{$request->guest_name}%");
        }

        $bookings = $query->latest()->paginate(15)->withQueryString();

        $filters = [
            'statuses' => ['pending', 'confirmed', 'checked_in', 'checked_out', 'cancelled'],
            'rooms' => Room::select('id', 'number', 'type')->get(),
        ];

        return Inertia::render('bookings/index', [
            'bookings' => $bookings,
            'filters' => $filters,
            'queryParams' => $request->query(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $availableRooms = Room::available()->get();

        // If specific dates are provided, filter by availability
        if ($request->filled('check_in_date') && $request->filled('check_out_date')) {
            $availableRooms = $availableRooms->filter(function ($room) use ($request) {
                return $room->isAvailableForDates($request->check_in_date, $request->check_out_date);
            })->values();
        }

        return Inertia::render('bookings/create', [
            'rooms' => $availableRooms,
            'checkInDate' => $request->check_in_date,
            'checkOutDate' => $request->check_out_date,
            'guests' => $request->guests,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBookingRequest $request)
    {
        $validatedData = $request->validated();
        $room = Room::findOrFail($validatedData['room_id']);

        // Check room availability
        if (!$room->isAvailableForDates($validatedData['check_in_date'], $validatedData['check_out_date'])) {
            return redirect()->back()
                ->withErrors(['room_id' => 'Room is not available for selected dates.'])
                ->withInput();
        }

        // Check room capacity
        if ($validatedData['number_of_guests'] > $room->capacity) {
            return redirect()->back()
                ->withErrors(['number_of_guests' => "Room capacity is {$room->capacity} guests."])
                ->withInput();
        }

        // Calculate total price
        $checkIn = Carbon::parse($validatedData['check_in_date']);
        $checkOut = Carbon::parse($validatedData['check_out_date']);
        $nights = $checkIn->diffInDays($checkOut);
        $totalPrice = $nights * $room->price_per_night;

        $booking = Booking::create([
            ...$validatedData,
            'user_id' => auth()->id(),
            'total_price' => $totalPrice,
            'status' => 'confirmed',
        ]);

        return redirect()->route('bookings.show', $booking)
            ->with('success', 'Booking created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Booking $booking)
    {
        $user = auth()->user();

        // Check authorization
        if (!$user->isStaff() && $booking->user_id !== $user->id) {
            abort(403, 'Unauthorized to view this booking.');
        }

        $booking->load(['room', 'user']);

        return Inertia::render('bookings/show', [
            'booking' => $booking,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Booking $booking)
    {
        $user = auth()->user();

        // Check authorization
        if (!$user->isStaff() && $booking->user_id !== $user->id) {
            abort(403, 'Unauthorized to edit this booking.');
        }

        $booking->load('room');

        return Inertia::render('bookings/edit', [
            'booking' => $booking,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBookingRequest $request, Booking $booking)
    {
        $validatedData = $request->validated();

        // Recalculate total price if dates changed
        if (isset($validatedData['check_in_date']) || isset($validatedData['check_out_date'])) {
            $checkIn = Carbon::parse($validatedData['check_in_date'] ?? $booking->check_in_date);
            $checkOut = Carbon::parse($validatedData['check_out_date'] ?? $booking->check_out_date);
            $nights = $checkIn->diffInDays($checkOut);
            $validatedData['total_price'] = $nights * $booking->room->price_per_night;
        }

        $booking->update($validatedData);

        return redirect()->route('bookings.show', $booking)
            ->with('success', 'Booking updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Booking $booking)
    {
        $user = auth()->user();

        // Check authorization
        if (!$user->isAdmin() && $booking->user_id !== $user->id) {
            abort(403, 'Unauthorized to delete this booking.');
        }

        $booking->delete();

        return redirect()->route('bookings.index')
            ->with('success', 'Booking deleted successfully.');
    }


}