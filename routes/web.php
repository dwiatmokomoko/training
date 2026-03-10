<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\TrainingNeedController;
use App\Http\Controllers\AssessmentController;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::post('/run-analysis', [DashboardController::class, 'runAnalysis'])->name('run-analysis');

Route::resource('employees', EmployeeController::class);
Route::resource('training-needs', TrainingNeedController::class);
Route::resource('assessments', AssessmentController::class);

// Additional routes
Route::get('/training-needs-report', [TrainingNeedController::class, 'report'])->name('training-needs.report');
Route::get('/assessments-bulk/create', [AssessmentController::class, 'bulkCreate'])->name('assessments.bulk-create');
Route::post('/assessments-bulk', [AssessmentController::class, 'bulkStore'])->name('assessments.bulk-store');
