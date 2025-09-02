<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\DashboardAdminController;
use App\Http\Controllers\DashboardUserController;
use App\Http\Controllers\HalamanAdminDiUserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportCutiUserController;
use App\Models\Office;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {

    // $user = User::where('no_payroll', 1223)->get();
    // dd($user);
    return view('welcome');
});

Route::get('/dashboard', function () {


    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Group untuk admin
Route::middleware(['auth', 'verified'])->group(function () {

    // Route dashboard umum: redirect sesuai role
    Route::get('/dashboard', function () {
        $user = Auth::user();
        return redirect()->route(
            $user->role === 'admin'
                ? 'admin.dashboard'
                : 'user.dashboard'
        );
    })->name('dashboard');

    // Dashboard admin lewat controller
    Route::get('/admin', [DashboardAdminController::class, 'index'])
        ->name('admin.dashboard');

    // Dashboard user lewat controller
    Route::get('/user', [DashboardUserController::class, 'index'])
        ->name('user.dashboard');
    Route::get('/user/ipamdt', [DashboardUserController::class, 'ipamdt'])
        ->name('user.dashboard.ipamdt');

    Route::get('/user/shift', [DashboardUserController::class, 'shift'])
        ->name('user.dashboard.shift');


    Route::get('/user/cuti', [ReportCutiUserController::class, 'user_cuti'])
        ->name('user.dashboard.cuti');

    //halaman admin di user

    Route::get('/user/halamanadmin', [HalamanAdminDiUserController::class, 'index'])
        ->name('halamanadmin.index');
});



Route::middleware('auth')->group(function () {
    Route::get('/attendance/index', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::get('/attendance/create', [AttendanceController::class, 'create'])->name('attendance.create');
    Route::get('/attendance/show/{id}/{tanggal}/{type}', [AttendanceController::class, 'show'])->name('attendance.show');
    Route::post('/attendance/check-in', [AttendanceController::class, 'checkIn'])->name('attendance.checkin');
});




require __DIR__ . '/auth.php';
