<?php

use App\Http\Services\BookingService;
use App\Models\Booking;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $bookingService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->bookingService = new BookingService();
    }

    public function testGetInitiallyAvailableSpaces()
    {
        $testData = [
            'start_date' => '2024-01-06',
            'end_date' => '2024-01-07',
        ];

        // Test available spaces without creating a booking
        $availableParkingSpaces = $this->bookingService->getAvailableSpaces('2024-01-06', '2024-01-08');

        $this->assertIsArray($availableParkingSpaces);
        $this->assertCount(3, $availableParkingSpaces); // considering dates 6-9 [6th/7th/8th]
        $this->assertEquals(['2024-01-06' => 10, '2024-01-07' => 10, '2024-01-08' => 10], $availableParkingSpaces);
    }

    public function testGetAvailableSpaces()
    {
        Booking::factory()->create([
            'start_date' => '2024-01-06',
            'end_date' => '2024-01-07',
        ]);

        $availableParkingSpaces = $this->bookingService->getAvailableSpaces('2024-01-06', '2024-01-08');

        $this->assertIsArray($availableParkingSpaces);
        $this->assertCount(3, $availableParkingSpaces); // considering dates 6-9 [6th/7th]
        $this->assertEquals(['2024-01-06' => 9, '2024-01-07' => 9, '2024-01-08' => 10], $availableParkingSpaces);
    }

    public function testCalculateBookingPrice()
    {
        //Pricing
        //weekend 15.00
        //weekday 10.00
        //summer additional 2
        //for 2024-07-06 to 2024-07-08
        //breakdown
        //06th - 15.00  + 2 / 07th - 15.00  + 2/ 8th - 10.00 + 2 = 42

        $priceDetails = $this->bookingService->calculateBookingPrice('2024-07-06', '2024-07-08');

        $this->assertIsArray($priceDetails);
        $this->assertArrayHasKey('price-per-day', $priceDetails);
        $this->assertArrayHasKey('total', $priceDetails);
        $this->assertEquals([
            'total' => 46.00,
            'price-per-day' => [
                '2024-07-06' => 17.00,
                '2024-07-07' => 17.00,
                '2024-07-08' => 12.00,
            ],
        ], $priceDetails);
    }

    public function testCreateBooking()
    {
        $booking = $this->bookingService->createBooking('2024-01-06', '2024-01-08');
        $this->assertInstanceOf(Booking::class, $booking);
        $this->assertEquals('2024-01-06', $booking->start_date);
        $this->assertEquals('2024-01-08', $booking->end_date);

        // creating a booking when parking is not available
        Booking::factory()->count(9)->create();
        $booking = $this->bookingService->createBooking('2024-01-06', '2024-01-08');
        $this->assertNull($booking);
    }

    public function testUpdateBooking()
    {
        // Create a booking for testing
        $booking = $this->bookingService->createBooking('2024-01-06', '2024-01-08');

        // Test updating a booking with a new date range
        $updatedBooking = $this->bookingService->updateBooking($booking->id, '2024-01-10', '2024-01-15');
        $this->assertInstanceOf(Booking::class, $updatedBooking);
        $this->assertEquals('2024-01-10', $updatedBooking->start_date);
        $this->assertEquals('2024-01-15', $updatedBooking->end_date);
    }

    public function testCancelBooking()
    {
        $booking = Booking::factory()->create();

        $cancelledBooking = $this->bookingService->cancelBooking($booking->id);
        $this->assertInstanceOf(Booking::class, $cancelledBooking);
        $this->assertEquals(Booking::BookingStatusCancelled, $cancelledBooking->status);

    }
}
