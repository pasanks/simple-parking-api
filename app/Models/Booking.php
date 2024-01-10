<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;
    const BookingCapacity = 10;
    const BookingBasePrice = 10.00;
    const BookingWeekendPrice = 15.00;
    const BookingStatusActive = 'active';
    const BookingStatusCancelled = 'cancelled';
    protected $fillable = ['start_date', 'end_date', 'status', 'booking_fee'];
}
