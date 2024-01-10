<?php

namespace App\Http\Services;

use App\Models\Booking;
use Carbon\Carbon;

class BookingService
{
    /**
     * Get available parking slots for a given date range.
     * bookingID added as additional parameter so this function can be reused when updating a booking
     * to check availability.// phpcs:ignore
     *
     *
     * @return array
     */
    public function getAvailableSpaces($startDate, $endDate, $bookingId = null)
    {
        $availableSpaces = [];

        // Iterate through each date in the given range
        for ($checkDate = $startDate; $checkDate <= $endDate; $checkDate = date('Y-m-d', strtotime($checkDate . ' +1 day'))) {// phpcs:ignore
            // Count the number of available parking spaces for the check date
            $bookedSlots = Booking::where('start_date', '<=', $checkDate)
                ->where('end_date', '>=', $checkDate)
                ->when($bookingId !== null, function ($query) use ($bookingId) {
                    $query->where('id', '!=', $bookingId);
                })->count();

            $availableSlots = Booking::BOOKINGCAPACITY - $bookedSlots;

            // Ensure the available slots are not negative
            $availableSpaces[$checkDate] = max(0, $availableSlots);
        }

        return $availableSpaces;
    }

    /**
     * Assuming a very a basic pricing model.
     *
     *
     * @return array
     */
    public function calculateBookingPrice($startDate, $endDate)
    {
        // Determine summer pricing based on months (e.g., assuming June to August as summer)
        // Can do same for winter as well.
        $summerMonths = ['06', '07', '08'];

        $totalBookingFee = 0.00;
        $days = [];

        $totalDays = Carbon::parse($startDate)->diffInDays($endDate);

        // Iterate through each day
        for ($i = 0; $i <= $totalDays; $i++) {
            $checkDate = Carbon::parse($startDate)->addDays($i);

            // Check if it's a weekday or weekend
            $isWeekend = in_array($checkDate->dayOfWeek, [Carbon::SATURDAY, Carbon::SUNDAY]);

            // Check if it's summer /or can do same for winter
            $isSummer = in_array($checkDate->format('m'), $summerMonths);

            // Calculate price based on weekday or weekend and summer pricing
            $pricePerDay = $isWeekend ? Booking::BOOKINGWEEKENDPRICE : Booking::BOOKINGBASEPRICE;
            $pricePerDay += $isSummer ? 2 : 0;

            $days[$checkDate->toDateString()] = number_format($pricePerDay, 2, '.', ',');
            $totalBookingFee += $pricePerDay;
        }

        return [
            'price-per-day' => $days,
            'total' => number_format($totalBookingFee, 2, '.', ','),
        ];
    }

    /**
     * Create a new booking for a requested date range.
     * price value will be calculated automatically using above pricing model.
     *
     *
     * @return mixed
     */
    public function createBooking($startDate, $endDate)
    {
        $availableSpaces = $this->getAvailableSpaces($startDate, $endDate);

        $isParkingAvailableForGivenRange = in_array(0, array_values($availableSpaces));

        if (! $isParkingAvailableForGivenRange) {
            return Booking::create([
                'start_date' => $startDate,
                'end_date' => $endDate,
                'booking_fee' => $this->calculateBookingPrice($startDate, $endDate)['total'],
            ]);
        }

        return null;
    }

    /**
     * Update an already created booking.
     *
     *
     * @return null
     */
    public function updateBooking($bookingId, $startDate, $endDate)
    {
        $availableSpaces = $this->getAvailableSpaces($startDate, $endDate, $bookingId);

        $isParkingAvailableForGivenRange = in_array(0, array_values($availableSpaces));

        if (! $isParkingAvailableForGivenRange) {
            $booking = Booking::find($bookingId);
            $booking->update([
                'start_date' => $startDate,
                'end_date' => $endDate,
                'booking_fee' => $this->calculateBookingPrice($startDate, $endDate)['total'],
            ]);

            return $booking->fresh();
        }

        return null;
    }

    /**
     * Cancel a booking.
     *
     *
     * @return mixed
     */
    public function cancelBooking($bookingId)
    {
        $booking = Booking::find($bookingId);
        $booking->update([
            'status' => Booking::BOOKINGSTATUSCANCELLED,
        ]);

        return $booking->fresh();
    }
}
