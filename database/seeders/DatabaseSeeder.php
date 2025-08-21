<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Room;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin users
        User::factory()->superadmin()->create([
            'name' => 'Super Admin',
            'email' => 'superadmin@hotel.com',
        ]);

        User::factory()->admin()->create([
            'name' => 'Hotel Admin',
            'email' => 'admin@hotel.com',
        ]);

        User::factory()->staff()->create([
            'name' => 'Hotel Staff',
            'email' => 'staff@hotel.com',
        ]);

        // Create test guest user
        User::factory()->create([
            'name' => 'Test Guest',
            'email' => 'guest@hotel.com',
            'role' => 'guest',
        ]);

        // Create additional guest users
        User::factory(20)->create();

        // Create rooms
        Room::factory(50)->create();

        // Create some rooms in maintenance
        Room::factory(3)->maintenance()->create();

        // Create bookings
        Booking::factory(30)->create();
        
        // Create some past bookings
        Booking::factory(15)->past()->create();
        
        // Create some cancelled bookings
        Booking::factory(5)->cancelled()->create();
    }
}
