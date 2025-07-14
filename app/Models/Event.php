<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
   
    use HasFactory;

    protected $fillable=[
        'title',
        'description',
        'date',
        'capacity',
        'image',
        'created_by'
    ];

        protected $casts = [
        'date' => 'datetime',
        'capacity' => 'integer',
    ];

    public function creator(){
        return $this->belongsTo(User::class,'created_by');
    }
    public function bookings(){
        return $this->hasMany(Booking::class,);
    }


        public function getSpotsBookedAttribute()
    {
        // Simply count the number of associated bookings
        return $this->bookings()->count();
    }

    /**
     * Get the number of available spots for the event.
     */
    public function getSpotsAvailableAttribute()
    {
        return $this->capacity - $this->spots_booked;
    }
    // Append these computed attributes when converting to array/JSON
    protected $appends = ['spots_booked', 'spots_available'];
}
