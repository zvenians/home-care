<?php

use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\DemoApiController;
use App\Http\Controllers\Api\ManagementController;
use App\Http\Controllers\Api\QuoteController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    abort_unless(config('homecare.demo_mode'), 503, 'Aktifkan autentikasi produksi sebelum deployment.');
    return view('app');
});

Route::post('/demo/role', function (Request $request) {
    abort_unless(config('homecare.demo_mode'), 403);
    $validated = $request->validate(['role' => 'required|in:admin,viewer,caregiver']);
    $request->session()->regenerate();
    $request->session()->put('demo_role', $validated['role']);
    if ($validated['role'] === 'viewer') {
        $request->session()->forget('demo_customer_id');
    }
    return response()->json(['role' => $validated['role']]);
});

Route::prefix('api')->group(function () {
    Route::get('/bootstrap', [DemoApiController::class, 'index']);
    Route::post('/quote', [QuoteController::class, 'quote']);
    Route::post('/bookings', [BookingController::class, 'store']);
    Route::post('/applications', [DemoApiController::class, 'apply']);
    Route::post('/reviews', [DemoApiController::class, 'review']);
    Route::patch('/bookings/{booking}', [BookingController::class, 'update'])->middleware('demo.admin');
    Route::delete('/bookings/{booking}', [BookingController::class, 'destroy'])->middleware('demo.admin');
    Route::post('/manage/{resource}', [ManagementController::class, 'store'])->middleware('demo.admin');
    Route::patch('/manage/{resource}/{id}', [ManagementController::class, 'update'])->middleware('demo.admin');
    Route::delete('/manage/{resource}/{id}', [ManagementController::class, 'destroy'])->middleware('demo.admin');
});
