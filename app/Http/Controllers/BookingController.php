<?php

namespace App\Http\Controllers;

use App\Http\Requests\DateRangeValidationRequest;
use App\Http\Services\BookingService;

class BookingController extends Controller
{
    private BookingService $bookingService;

    private $startDate;

    private $endDate;

    public function __construct(BookingService $bookingService)
    {
        $this->bookingService = $bookingService;
    }

    /**
     * Check parking availability for a given date range.
     *
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkAvailability(DateRangeValidationRequest $request)
    {
        $this->getStartEndDateFromRequest($request);

        $data = [
            'availability' => $this->bookingService->getAvailableSpaces($this->startDate, $this->endDate),
        ];

        return $this->toResponseArray('Available Parking Slots.', $data);
    }

    /**
     * Check parking availability for a given date range.
     *
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkPricing(DateRangeValidationRequest $request)
    {
        $this->getStartEndDateFromRequest($request);

        $data = $this->bookingService->calculateBookingPrice($this->startDate, $this->endDate);

        return $this->toResponseArray('Parking fee for the given date range.', $data);
    }

    /**
     * Create a new booking for a given date range.
     *
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(DateRangeValidationRequest $request)
    {
        $this->getStartEndDateFromRequest($request);

        $booking = $this->bookingService->createBooking($this->startDate, $this->endDate);

        return $this->toResponseArray(
            $booking ? 'Booking successful' : 'No available parking spaces for the requested date range',
            $booking,
            $booking ? 201 : 200
        );
    }

    /**
     * Amend already created booking.
     *
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(DateRangeValidationRequest $request, $id)
    {
        $this->getStartEndDateFromRequest($request);

        $booking = $this->bookingService->updateBooking($id, $this->startDate, $this->endDate);

        return $this->toResponseArray(
            $booking ? 'Booking amended successfully' : 'No available parking spaces for the requested date range',
            $booking,
        );
    }

    /**
     * Cancel a booking.
     *
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function cancel($id)
    {
        $booking = $this->bookingService->cancelBooking($id);

        return $this->toResponseArray(
            $booking ? 'Booking cancelled successfully' : 'An error occurred'
        );
    }

    /**
     * Transforms response data into a standardized JSON format for API responses.
     *
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function toResponseArray($message, $data = [], $status = 200)
    {
        return response()->json([
            'message' => $message,
            'data' => $data,
        ], $status);
    }

    /**
     * Set start date and end date from the validated request.
     *
     *
     * @return void
     */
    private function getStartEndDateFromRequest($request)
    {
        $this->startDate = $request->input('start_date');
        $this->endDate = $request->input('end_date');
    }
}
