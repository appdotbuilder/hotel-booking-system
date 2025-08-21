<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBookingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $booking = $this->route('booking');
        $user = auth()->user();

        // Superadmin and admin can update any booking
        if ($user?->isAdmin()) {
            return true;
        }

        // Staff can update bookings
        if ($user?->isStaff()) {
            return true;
        }

        // Users can only update their own bookings
        return $booking && $user && $booking->user_id === $user->id;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'guest_name' => 'required|string|max:255',
            'guest_email' => 'required|email|max:255',
            'guest_phone' => 'required|string|max:20',
            'check_in_date' => 'required|date|after_or_equal:today',
            'check_out_date' => 'required|date|after:check_in_date',
            'number_of_guests' => 'required|integer|min:1|max:10',
            'special_requests' => 'nullable|string|max:1000',
            'status' => 'sometimes|in:pending,confirmed,checked_in,checked_out,cancelled',
        ];
    }

    /**
     * Get custom error messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'guest_name.required' => 'Guest name is required.',
            'guest_email.required' => 'Guest email is required.',
            'guest_email.email' => 'Please provide a valid email address.',
            'guest_phone.required' => 'Guest phone number is required.',
            'check_in_date.required' => 'Check-in date is required.',
            'check_in_date.after_or_equal' => 'Check-in date must be today or later.',
            'check_out_date.required' => 'Check-out date is required.',
            'check_out_date.after' => 'Check-out date must be after check-in date.',
            'number_of_guests.required' => 'Number of guests is required.',
            'number_of_guests.min' => 'At least 1 guest is required.',
            'number_of_guests.max' => 'Maximum 10 guests allowed.',
            'status.in' => 'Invalid booking status.',
        ];
    }
}