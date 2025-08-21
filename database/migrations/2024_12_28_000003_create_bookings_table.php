<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('room_id')->constrained()->onDelete('cascade');
            $table->string('guest_name')->comment('Primary guest name');
            $table->string('guest_email')->comment('Guest email address');
            $table->string('guest_phone')->comment('Guest phone number');
            $table->date('check_in_date')->comment('Check-in date');
            $table->date('check_out_date')->comment('Check-out date');
            $table->integer('number_of_guests')->comment('Number of guests');
            $table->decimal('total_price', 10, 2)->comment('Total booking price');
            $table->enum('status', ['pending', 'confirmed', 'checked_in', 'checked_out', 'cancelled'])
                  ->default('pending')
                  ->comment('Booking status');
            $table->text('special_requests')->nullable()->comment('Special requests from guest');
            $table->timestamps();
            
            // Indexes for performance
            $table->index('check_in_date');
            $table->index('check_out_date');
            $table->index('status');
            $table->index(['status', 'check_in_date']);
            $table->index(['room_id', 'check_in_date', 'check_out_date']);
            $table->index(['user_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};