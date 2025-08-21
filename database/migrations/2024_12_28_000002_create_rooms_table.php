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
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->string('number')->unique()->comment('Room number');
            $table->string('type')->comment('Room type (single, double, suite, etc.)');
            $table->text('description')->nullable()->comment('Room description');
            $table->integer('capacity')->comment('Maximum number of guests');
            $table->decimal('price_per_night', 10, 2)->comment('Price per night');
            $table->enum('status', ['available', 'maintenance', 'out_of_order'])
                  ->default('available')
                  ->comment('Room availability status');
            $table->json('amenities')->nullable()->comment('Room amenities as JSON array');
            $table->timestamps();
            
            // Indexes for performance
            $table->index('type');
            $table->index('status');
            $table->index('capacity');
            $table->index(['status', 'type']);
            $table->index(['status', 'capacity']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};