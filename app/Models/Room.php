<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\Room
 *
 * @property int $id
 * @property string $number
 * @property string $type
 * @property string $description
 * @property int $capacity
 * @property float $price_per_night
 * @property string $status
 * @property array|null $amenities
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Booking> $bookings
 * @property-read int|null $bookings_count
 * 
 * @method static \Illuminate\Database\Eloquent\Builder|Room newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Room newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Room query()
 * @method static \Illuminate\Database\Eloquent\Builder|Room whereAmenities($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Room whereCapacity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Room whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Room whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Room whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Room whereNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Room wherePricePerNight($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Room whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Room whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Room whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Room available()
 * @method static \Database\Factories\RoomFactory factory($count = null, $state = [])
 * 
 * @mixin \Eloquent
 */
class Room extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'number',
        'type',
        'description',
        'capacity',
        'price_per_night',
        'status',
        'amenities',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'price_per_night' => 'decimal:2',
        'capacity' => 'integer',
        'amenities' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the bookings for the room.
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Scope a query to only include available rooms.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    /**
     * Check if room is available for given dates.
     *
     * @param string $check_in
     * @param string $check_out
     * @return bool
     */
    public function isAvailableForDates(string $check_in, string $check_out): bool
    {
        if ($this->status !== 'available') {
            return false;
        }

        $conflictingBookings = $this->bookings()
            ->where('status', '!=', 'cancelled')
            ->where(function ($query) use ($check_in, $check_out) {
                $query->whereBetween('check_in_date', [$check_in, $check_out])
                    ->orWhereBetween('check_out_date', [$check_in, $check_out])
                    ->orWhere(function ($q) use ($check_in, $check_out) {
                        $q->where('check_in_date', '<=', $check_in)
                          ->where('check_out_date', '>=', $check_out);
                    });
            })
            ->exists();

        return !$conflictingBookings;
    }
}