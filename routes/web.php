<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\HomeroomAttendanceController;
use App\Http\Controllers\AchievementController;
use App\Http\Controllers\ViolationController;
use App\Http\Controllers\CounselingController;
use App\Http\Controllers\CallLetterController;

Route::get('/', function () {
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

// Route khusus Admin untuk Data Siswa
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/students', [StudentController::class, 'index'])->name('students.index');
    Route::post('/students', [StudentController::class, 'store'])->name('students.store');
    Route::put('/students/{id}', [StudentController::class, 'update'])->name('students.update');
    Route::delete('/students/{id}', [StudentController::class, 'destroy'])->name('students.destroy');
});

//Data Guru
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('teachers', TeacherController::class);
});

//rute wali kelas
Route::middleware(['auth'])->prefix('homeroom')->name('homeroom.')->group(function () {
    // Menu Input Absensi Harian
    Route::get('/attendances', [HomeroomAttendanceController::class, 'index'])->name('attendances.index');
    Route::post('/attendances', [HomeroomAttendanceController::class, 'store'])->name('attendances.store');
    
    // Menu Rekapitulasi Semester Kelas Binaan
    Route::get('/attendances/recap', [HomeroomAttendanceController::class, 'recapitulation'])->name('attendances.recap');
});

// Route untuk Prestasi (Admin)
Route::middleware(['auth'])->group(function () {
    Route::get('/achievements', [AchievementController::class, 'index'])->name('achievements.index');
    Route::get('/achievements/create', [AchievementController::class, 'create'])->name('achievements.create');
    Route::post('/achievements', [AchievementController::class, 'store'])->name('achievements.store');
    Route::delete('/achievements/{id}', [AchievementController::class, 'destroy'])->name('achievements.destroy');
    Route::get('/achievements/{id}/edit', [AchievementController::class, 'edit'])->name('achievements.edit');
    Route::put('/achievements/{id}', [AchievementController::class, 'update'])->name('achievements.update');
    
    Route::delete('/achievements/{id}', [AchievementController::class, 'destroy'])->name('achievements.destroy');
});

// Route untuk Menu Pelanggaran
Route::middleware(['auth'])->group(function () {
        Route::resource('violations', ViolationController::class);
});

//bimbingan
Route::resource('counselings', CounselingController::class);
    Route::patch('/counselings/{id}/status', [App\Http\Controllers\CounselingController::class, 'updateStatus'])->name('counselings.updateStatus');
//spo
Route::middleware(['auth'])->group(function () {
    Route::resource('call-letters', CallLetterController::class);
    Route::patch('call-letters/{id}/status', [CallLetterController::class, 'updateStatus'])->name('call-letters.updateStatus');
    Route::patch('call-letters/{id}/notes', [CallLetterController::class, 'updateNotes'])->name('call-letters.updateNotes');
});


require __DIR__.'/auth.php';
