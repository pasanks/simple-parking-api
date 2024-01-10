<?php

use App\Http\Services\BookingService;
use App\Models\Booking;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingControllerTest extends TestCase
{
    use RefreshDatabase;

    //very very basic tests just testing the json structure I need to do so many improvements here.

    public function testCheckAvailability()
    {
        $response = $this->postJson('/api/booking/availability', $this->createParameters());

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'availability',
                ],
            ]);
    }

    public function testCheckPricing()
    {
        $response = $this->postJson('/api/booking/price', $this->createParameters());

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'price-per-day',
                    'total',
                ],
            ]);
    }

    public function testCreate()
    {
        $response = $this->postJson('/api/booking/create', $this->createParameters());

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'data',
            ]);
    }

    protected function createParameters($customData = [])
    {
        return array_merge([
            'start_date' => '2024-01-10',
            'end_date' => '2024-01-15',
        ], $customData);
    }
}
