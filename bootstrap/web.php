<?php

use App\Models\Vehicle;
use Carbon\CarbonInterval;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MapController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\ProjectMapController;
use App\Http\Controllers\HeavyEquipmentController;
use App\Http\Controllers\OperatorHelperController;
use App\Http\Controllers\WorkAssignmentController;
use App\Http\Controllers\CartrackVehicleController;
use App\Http\Controllers\CartrackActivityController;
use App\Http\Controllers\CompletedProjectController;
use App\Http\Controllers\FieldConditionPhotoController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

require __DIR__ . '/auth.php';


// Guest routes (accessible without login)
Route::get('/', [GuestController::class, 'index'])->name('guest.index');
Route::get('/project-maps', [GuestController::class, 'project_map_index'])->name('guest.project-map');
Route::get('/project-map/search', [GuestController::class, 'search'])->name('guest.project-map.search');
Route::get('/project-maps/map', [GuestController::class, 'map'])->name('guest.map');

// Operator and Helper routes
Route::middleware(['auth', 'operator_helper'])->prefix('operator-helper')->group(function () {
    Route::get('/', [OperatorHelperController::class, 'dashboard'])->name('operator-helper.dashboard');
    Route::get('/check-in/{assignment}', [OperatorHelperController::class, 'checkInForm'])->name('operator-helper.check-in.form');
    Route::post('/check-in/{assignment}', [OperatorHelperController::class, 'checkIn'])->name('operator-helper.check-in');
    Route::get('/check-out/{assignment}', [OperatorHelperController::class, 'checkOutForm'])->name('operator-helper.check-out.form');
    Route::post('/check-out/{assignment}', [OperatorHelperController::class, 'checkOut'])->name('operator-helper.check-out');
    Route::get('/work-assignments/{workAssignment}/field-condition-photos', [FieldConditionPhotoController::class, 'index'])
        ->name('field-condition-photos.index');
    Route::post('/work-assignments/{workAssignment}/field-condition-photos', [FieldConditionPhotoController::class, 'store'])
        ->name('field-condition-photos.store');
    Route::delete('/field-condition-photos/{photo}', [FieldConditionPhotoController::class, 'destroy'])
        ->name('field-condition-photos.destroy');
    Route::get('/history', [OperatorHelperController::class, 'history'])->name('operator-helper.history');
    Route::get('/history/{workAssignment}', [OperatorHelperController::class, 'historyDetail'])->name('operator-helper.history.detail');

    Route::get('/profile/edit', [ProfileController::class, 'user_edit'])->name('operator-helper.profile.edit');
    Route::patch('/profile/update', [ProfileController::class, 'user_update'])->name('operator-helper.profile.update');
    Route::delete('/profile/delete', [ProfileController::class, 'user_destroy'])->name('operator-helper.profile.destroy');
    Route::post('/daily-check-in', [AttendanceController::class, 'dailyCheckIn'])->name('operator-helper.daily-check-in');
    Route::post('/daily-check-out', [AttendanceController::class, 'dailyCheckOut'])->name('operator-helper.daily-check-out');
});

// Admin routes (accessible only to admins)
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard')->middleware('auth');

    // User routes
    Route::resource('users', UserController::class);

    // Heavy Equipment routes
    Route::resource('alat-berat', HeavyEquipmentController::class);
    Route::patch('/alat-berat/{alatBerat}/update-kondisi', [HeavyEquipmentController::class, 'updateKondisi'])->name('alat-berat.updateKondisi');
    // Profile route
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile/delete', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Work Assignment routes
    Route::resource('work-assignments', WorkAssignmentController::class);
    Route::get('/work-assignments/{assignment}/manage-users/{role}', [WorkAssignmentController::class, 'manageUsers'])->name('work-assignments.manage-users');
    Route::post('/work-assignments/{assignment}/add-user', [WorkAssignmentController::class, 'addUser'])->name('work-assignments.add-user');
    Route::delete('/work-assignments/remove-user/{assignmentUser}', [WorkAssignmentController::class, 'removeUser'])->name('work-assignments.remove-user');
    Route::patch('/work-assignments/{workAssignment}/update-status', [WorkAssignmentController::class, 'updateStatus'])->name('work-assignments.updateStatus');
    Route::get('/work-assignments/{workAssignment}/edit-hours-meter', [WorkAssignmentController::class, 'editHoursMeter'])->name('work-assignments.edit-hours-meter');
    Route::put('/work-assignments/{workAssignment}/update-hours-meter', [WorkAssignmentController::class, 'updateHoursMeter'])->name('work-assignments.update-hours-meter');
    // Search route
    Route::get('/work-assignments-search', [WorkAssignmentController::class, 'search'])
        ->name('work-assignments.search');
    Route::delete('/delete-image/{type}/{id}', [WorkAssignmentController::class, 'delete_image'])->name('delete.image');
    Route::post('/work-assignments/{workAssignment}/photos', [WorkAssignmentController::class, 'uploadPhotos'])->name('work-assignments.upload-photos');

    // Map
    Route::get('/project-map', [ProjectMapController::class, 'index'])->name('project-map.index');
    Route::get('/project-map/search', [ProjectMapController::class, 'search'])->name('project-map.search');
    // API route for getting work assignments by location
    Route::get('/api/work-assignments-by-location', [WorkAssignmentController::class, 'getWorkAssignmentsByLocation'])
        ->name('api.work-assignments.by-location');

    // Completed Project routes
    Route::resource('completed-projects', CompletedProjectController::class);

    Route::patch('/attendance-logs/{log}/update-hours-meter/{type}', [AttendanceController::class, 'updateHoursMeter'])->name('attendance.update-hours-meter');

    // Map routes
    Route::prefix('maps')->group(function () {
        Route::get('/completed-projects', [MapController::class, 'completedProjects'])->name('maps.completed_projects');
        Route::get('/active-projects', [MapController::class, 'activeProjects'])->name('maps.active_projects');
        Route::get('/search', [MapController::class, 'search'])->name('maps.search');
    });

    // Laporan
    Route::get('/laporan', [ReportController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/export', [ReportController::class, 'export'])->name('laporan.export');

    // API routes (if you need them for AJAX requests)
    Route::prefix('api')->group(function () {
        Route::get('/alat-berat', [HeavyEquipmentController::class, 'apiIndex']);
        Route::get('/work-assignments', [WorkAssignmentController::class, 'apiIndex']);
        Route::get('/completed-projects', [CompletedProjectController::class, 'apiIndex']);
    });

    // Cartrack Vehicle routes
    Route::prefix('cartrack-vehicle')->group(function () {
        Route::get('/', [CartrackVehicleController::class, 'index'])->name('cartrack-vehicle.index');
    });

    // Cartrack Activity routes
    Route::prefix('cartrack-activity')->group(function () {
        Route::get('/', [CartrackActivityController::class, 'index'])->name('cartrack-activity.index');
    });

    Route::prefix('cartrack-power-take-off')->group(function () {
        Route::get('/', [App\Http\Controllers\CartrackPowerTakeOffController::class, 'index'])->name('cartrack-power-take-off.index');
    });
});

Route::get('/api/equipment-usage-history', [DashboardController::class, 'getEquipmentUsageHistory'])
    ->name('api.equipment-usage-history')
    ->middleware('auth');
Route::get('/api/hours-meter-history/{id}', [HeavyEquipmentController::class, 'getHoursMeterHistory'])
    ->name('api.hours-meter-history');
Route::get('/api/equipment-tracking/{id}', [HeavyEquipmentController::class, 'getTrackingData'])
    ->name('api.equipment-tracking');


Route::get('/api/project-years', [GuestController::class, 'getProjectYears'])
    ->name('api.project-years');

Route::get('/api/projects', [GuestController::class, 'getProjects'])
    ->name('api.projects');

// Cartrack API for web
Route::get('/api/cartrack-vehicles', [CartrackActivityController::class, 'getCartrackVehicles'])
    ->name('api.cartrack-vehicles');

Route::post('/api/sync-cartrack', [CartrackVehicleController::class, 'syncCartrack'])->name('cartrack-vehicle.sync-cartrack');
Route::post('/api/sync-cartrack-with-heavy-equipment', [CartrackVehicleController::class, 'syncCartrackWithHeavyEquipment'])->name('cartrack-vehicle.sync-cartrack-with-heavy-equipment');

Route::post('/api/cartrack-activities', [CartrackActivityController::class, 'cartrackActivities']);
Route::post('/api/cartrack-statuses', [App\Http\Controllers\CartrackVehicleStatusController::class, 'cartrackStatus']);


Route::post('/api/sync-cartrack-activity', [CartrackActivityController::class, 'syncCartrackActivity'])
    ->name('api.sync-cartrack-activity');

Route::post('/api/sync-cartrack-power-take-off', [App\Http\Controllers\CartrackPowerTakeOffController::class, 'syncCartrackPowerTakeOff'])
    ->name('api.sync-cartrack-power-take-off');
