<?php

namespace Database\Factories;

use App\Models\Room;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Room>
 */
class RoomFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $roomTypes = ['standard', 'deluxe', 'suite', 'executive', 'presidential'];
        $type = $this->faker->randomElement($roomTypes);
        
        $basePrice = match($type) {
            'standard' => $this->faker->randomFloat(2, 80, 120),
            'deluxe' => $this->faker->randomFloat(2, 120, 180),
            'suite' => $this->faker->randomFloat(2, 200, 300),
            'executive' => $this->faker->randomFloat(2, 250, 400),
            'presidential' => $this->faker->randomFloat(2, 400, 800),
            default => $this->faker->randomFloat(2, 100, 200),
        };

        $capacity = match($type) {
            'standard' => $this->faker->numberBetween(1, 2),
            'deluxe' => $this->faker->numberBetween(2, 3),
            'suite' => $this->faker->numberBetween(3, 4),
            'executive' => $this->faker->numberBetween(2, 4),
            'presidential' => $this->faker->numberBetween(4, 8),
            default => $this->faker->numberBetween(1, 4),
        };

        $amenities = $this->faker->randomElements([
            'WiFi', 'Air Conditioning', 'Mini Bar', 'TV', 'Balcony', 
            'Ocean View', 'Room Service', 'Safe', 'Coffee Maker', 
            'Jacuzzi', 'Kitchenette', 'Workspace'
        ], $this->faker->numberBetween(3, 8));

        return [
            'number' => $this->faker->unique()->regexify('[1-9][0-9]{2}'),
            'type' => $type,
            'description' => $this->faker->paragraph(3),
            'capacity' => $capacity,
            'price_per_night' => $basePrice,
            'status' => $this->faker->randomElement(['available', 'available', 'available', 'maintenance']),
            'amenities' => $amenities,
        ];
    }

    /**
     * Indicate that the room is in maintenance.
     */
    public function maintenance(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'maintenance',
        ]);
    }

    /**
     * Indicate that the room is out of order.
     */
    public function outOfOrder(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'out_of_order',
        ]);
    }
}