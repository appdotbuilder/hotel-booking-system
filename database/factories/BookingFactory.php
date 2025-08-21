<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\Room;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Booking>
 */
class BookingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $checkInDate = $this->faker->dateTimeBetween('now', '+30 days');
        $checkOutDate = $this->faker->dateTimeBetween($checkInDate, $checkInDate->format('Y-m-d') . ' +7 days');
        
        $room = Room::inRandomOrder()->first() ?? Room::factory()->create();
        $nights = Carbon::parse($checkInDate)->diffInDays(Carbon::parse($checkOutDate));
        $totalPrice = $nights * $room->price_per_night;

        return [
            'user_id' => User::inRandomOrder()->first()->id ?? User::factory()->create()->id,
            'room_id' => $room->id,
            'guest_name' => $this->faker->name(),
            'guest_email' => $this->faker->safeEmail(),
            'guest_phone' => $this->faker->phoneNumber(),
            'check_in_date' => $checkInDate,
            'check_out_date' => $checkOutDate,
            'number_of_guests' => $this->faker->numberBetween(1, min($room->capacity, 4)),
            'total_price' => $totalPrice,
            'status' => $this->faker->randomElement(['confirmed', 'confirmed', 'pending', 'checked_in', 'cancelled']),
            'special_requests' => $this->faker->optional(0.3)->sentence(),
        ];
    }

    /**
     * Indicate that the booking is confirmed.
     */
    public function confirmed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'confirmed',
        ]);
    }

    /**
     * Indicate that the booking is pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
        ]);
    }

    /**
     * Indicate that the booking is cancelled.
     */
    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'cancelled',
        ]);
    }

    /**
     * Create a booking for past dates (checked out).
     */
    public function past(): static
    {
        return $this->state(function (array $attributes) {
            $checkInDate = $this->faker->dateTimeBetween('-30 days', '-7 days');
            $checkOutDate = $this->faker->dateTimeBetween($checkInDate, '-1 day');
            
            $room = Room::find($attributes['room_id']);
            $nights = Carbon::parse($checkInDate)->diffInDays(Carbon::parse($checkOutDate));
            $totalPrice = $nights * $room->price_per_night;

            return [
                'check_in_date' => $checkInDate,
                'check_out_date' => $checkOutDate,
                'total_price' => $totalPrice,
                'status' => 'checked_out',
            ];
        });
    }
}