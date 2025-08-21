<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Room;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class HotelController extends Controller
{
    /**
     * Display the hotel dashboard.
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        // Get available rooms with filtering
        $roomsQuery = Room::available();

        if ($request->filled('check_in_date') && $request->filled('check_out_date')) {
            $checkIn = $request->check_in_date;
            $checkOut = $request->check_out_date;
            
            $availableRooms = $roomsQuery->get()->filter(function ($room) use ($checkIn, $checkOut) {
                return $room->isAvailableForDates($checkIn, $checkOut);
            })->values();
        } else {
            $availableRooms = $roomsQuery->get();
        }

        if ($request->filled('guests')) {
            $availableRooms = $availableRooms->where('capacity', '>=', $request->guests)->values();
        }

        if ($request->filled('room_type')) {
            $availableRooms = $availableRooms->where('type', $request->room_type)->values();
        }

        // Get user's recent bookings
        $recentBookings = collect();
        if ($user) {
            $recentBookings = Booking::with('room')
                ->where('user_id', $user->id)
                ->latest()
                ->limit(5)
                ->get();
        }

        // Get statistics for staff/admin
        $stats = [];
        if ($user && $user->isStaff()) {
            $stats = [
                'total_rooms' => Room::count(),
                'available_rooms' => Room::available()->count(),
                'todays_checkins' => Booking::whereDate('check_in_date', today())
                    ->where('status', 'confirmed')
                    ->count(),
                'todays_checkouts' => Booking::whereDate('check_out_date', today())
                    ->whereIn('status', ['confirmed', 'checked_in'])
                    ->count(),
                'total_bookings' => Booking::count(),
                'total_users' => $user->isSuperadmin() ? User::count() : null,
            ];
        }

        // Get room types and filters
        $roomTypes = Room::distinct()->pluck('type');
        $maxCapacity = Room::max('capacity') ?? 1;

        return Inertia::render('welcome', [
            'availableRooms' => $availableRooms,
            'recentBookings' => $recentBookings,
            'stats' => $stats,
            'filters' => [
                'room_types' => $roomTypes,
                'max_capacity' => $maxCapacity,
            ],
            'queryParams' => $request->query(),
        ]);
    }

    /**
     * Check room availability for specific dates.
     */
    public function store(Request $request)
    {
        $request->validate([
            'check_in_date' => 'required|date|after_or_equal:today',
            'check_out_date' => 'required|date|after:check_in_date',
            'guests' => 'nullable|integer|min:1|max:10',
            'room_type' => 'nullable|string',
        ]);

        return redirect()->route('hotel.index', $request->only([
            'check_in_date', 
            'check_out_date', 
            'guests', 
            'room_type'
        ]));
    }
}