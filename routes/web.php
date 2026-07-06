<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\TrainingNeedController;
use App\Http\Controllers\AssessmentController;
use App\Http\Controllers\TrainingHistoryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::post('/logout', function (Request $request) {
    Auth::logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('dashboard')->with('success', 'Anda berhasil logout.');
})->name('logout');
Route::view('/alur-sistem', 'system-flow')->name('system-flow');
Route::get('/manajemen-pengguna', function () {
    \App\Support\Access::denyIfCannot('master-data.manage');
    return view('users-management');
})->name('users-management');
Route::get('/jabatan-standar-kompetensi', function () {
    \App\Support\Access::denyIfCannot('master-data.manage');
    return view('positions-competencies');
})->name('positions-competencies');
Route::get('/penilaian-kinerja', function () {
    \App\Support\Access::denyIfCannot('assessments.manage');
    return view('performance');
})->name('performance');
Route::get('/perencanaan-pelatihan', function () {
    \App\Support\Access::denyIfCannot('training-needs.manage');
    return view('training-plans');
})->name('training-plans');
Route::get('/master-data', function () {
    \App\Support\Access::denyIfCannot('master-data.manage');
    return view('master-data');
})->name('master-data');
Route::post('/run-analysis', [DashboardController::class, 'runAnalysis'])->name('run-analysis');

Route::resource('employees', EmployeeController::class);
Route::post('employees/{employee}/training-histories', [TrainingHistoryController::class, 'store'])->name('employees.training-histories.store');
Route::delete('training-histories/{trainingHistory}', [TrainingHistoryController::class, 'destroy'])->name('training-histories.destroy');
Route::resource('training-needs', TrainingNeedController::class);
Route::resource('assessments', AssessmentController::class);

// Additional routes
Route::get('/training-needs-report', [TrainingNeedController::class, 'report'])->name('training-needs.report');
Route::get('/training-recommendations', [TrainingNeedController::class, 'recommendations'])->name('training-recommendations');
Route::get('/assessments-bulk/create', [AssessmentController::class, 'bulkCreate'])->name('assessments.bulk-create');
Route::post('/assessments-bulk', [AssessmentController::class, 'bulkStore'])->name('assessments.bulk-store');
