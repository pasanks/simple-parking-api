<?php

use App\Http\Controllers\BookingController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/booking/create', [BookingController::class, 'store']);
Route::patch('/booking/update/{id}', [BookingController::class, 'update']);
Route::patch('/booking/cancel/{id}', [BookingController::class, 'cancel']);
Route::post('/booking/availability', [BookingController::class, 'checkAvailability']);
Route::post('/booking/price', [BookingController::class, 'checkPricing']);
