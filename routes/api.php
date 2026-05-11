<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Laravolt\Indonesia\Models\City;
use Laravolt\Indonesia\Models\District;
use Laravolt\Indonesia\Models\Village;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\WorkAssignmentController;

Route::post('/login', [AuthController::class, 'login']);

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/attendance/today', [AttendanceController::class, 'today']);
    Route::post('/attendance/check-in', [AttendanceController::class, 'checkIn']);
    Route::post('/attendance/check-out', [AttendanceController::class, 'checkOut']);

    Route::get('/work-assignments/ongoing', [WorkAssignmentController::class, 'ongoing']);
    Route::get('/work-assignments/history', [WorkAssignmentController::class, 'history']);
    Route::get('/work-assignments/history/{workAssignment}', [WorkAssignmentController::class, 'historyDetail']);
});

Route::get('/cities/{province}', function ($provinceId) {
    return City::where('province_id', $provinceId)->get();
});

Route::get('/districts/{city}', function ($cityId) {
    return District::where('city_code', $cityId)->get();
});

Route::get('/villages/{district}', function ($districtId) {
    return Village::where('district_code', $districtId)->get();
});

Route::get('/api/districts/{city}', function ($cityId) {
    dd($cityId);
    return \Laravolt\Indonesia\Models\District::where('city_code', $cityId)->get();
});

Route::get('/api/villages/{district}', function ($districtId) {
    return \Laravolt\Indonesia\Models\Village::where('district_code', $districtId)->get();
});
Route::get('/detect-district', function (Request $request) {
    $cityId = $request->input('city_id');
    $villageName = $request->input('village_name');

    $villages = Village::select(
        'district_code',
        DB::raw('MAX(name) as name'),
        DB::raw('MAX(district_code) as district_code') // Tambahkan kolom lain yang relevan
    )
        ->where('name', 'like', "%$villageName%")
        ->groupBy('district_code')
        ->get();


    if ($villages->count() === 1) {
        $district = District::where('code', $villages->first()->district_code)->first();
        return response()->json($district);
    }

    foreach ($villages as $village) {
        $district = District::where('code', $village->district_code)->where('city_code', $cityId)->first();
        if ($district) {
            return response()->json($district);
        }
    }

    return response()->json(null);
});
