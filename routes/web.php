<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\TrainingNeedController;
use App\Http\Controllers\AssessmentController;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::view('/alur-sistem', 'system-flow')->name('system-flow');
Route::view('/manajemen-pengguna', 'users-management')->name('users-management');
Route::view('/jabatan-standar-kompetensi', 'positions-competencies')->name('positions-competencies');
Route::view('/penilaian-kinerja', 'performance')->name('performance');
Route::view('/perencanaan-pelatihan', 'training-plans')->name('training-plans');
Route::view('/master-data', 'master-data')->name('master-data');
Route::post('/run-analysis', [DashboardController::class, 'runAnalysis'])->name('run-analysis');

Route::resource('employees', EmployeeController::class);
Route::resource('training-needs', TrainingNeedController::class);
Route::resource('assessments', AssessmentController::class);

// Additional routes
Route::get('/training-needs-report', [TrainingNeedController::class, 'report'])->name('training-needs.report');
Route::get('/training-recommendations', [TrainingNeedController::class, 'recommendations'])->name('training-recommendations');
Route::get('/assessments-bulk/create', [AssessmentController::class, 'bulkCreate'])->name('assessments.bulk-create');
Route::post('/assessments-bulk', [AssessmentController::class, 'bulkStore'])->name('assessments.bulk-store');
