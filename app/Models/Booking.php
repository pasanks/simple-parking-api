<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    public const BOOKINGCAPACITY = 10;

    public const BOOKINGBASEPRICE = 10.00;

    public const BOOKINGWEEKENDPRICE = 15.00;

    public const BOOKINGSTATUSACTIVE = 'active';

    public const BOOKINGSTATUSCANCELLED = 'cancelled';

    protected $fillable = ['start_date', 'end_date', 'status', 'booking_fee'];
}
