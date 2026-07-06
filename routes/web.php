<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MasterCrudController;
use App\Http\Controllers\MasterDataController;
use App\Http\Controllers\TnaController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/', DashboardController::class)->name('dashboard');
    Route::view('/alur-penggunaan', 'help.workflow')->name('help.workflow');

    Route::get('/master-data', MasterDataController::class)->name('masters.index');
    Route::get('/master-data/{resource}', [MasterCrudController::class, 'index'])->name('masters.crud.index');
    Route::get('/master-data/{resource}/{id}', [MasterCrudController::class, 'show'])->whereNumber('id')->name('masters.crud.show');

    Route::middleware('role:admin,kepegawaian')->group(function () {
        Route::get('/master-data/{resource}/create', [MasterCrudController::class, 'create'])->name('masters.crud.create');
        Route::post('/master-data/{resource}', [MasterCrudController::class, 'store'])->name('masters.crud.store');
        Route::get('/master-data/{resource}/{id}/edit', [MasterCrudController::class, 'edit'])->name('masters.crud.edit');
        Route::put('/master-data/{resource}/{id}', [MasterCrudController::class, 'update'])->name('masters.crud.update');
        Route::delete('/master-data/{resource}/{id}', [MasterCrudController::class, 'destroy'])->name('masters.crud.destroy');

        Route::get('/nilai-saw/create', [TnaController::class, 'createSawScore'])->name('tna.saw-scores.create');
        Route::post('/nilai-saw', [TnaController::class, 'storeSawScore'])->name('tna.saw-scores.store');
        Route::get('/nilai-saw/{score}/edit', [TnaController::class, 'editSawScore'])->name('tna.saw-scores.edit');
        Route::put('/nilai-saw/{score}', [TnaController::class, 'updateSawScore'])->name('tna.saw-scores.update');
        Route::delete('/nilai-saw/{score}', [TnaController::class, 'destroySawScore'])->name('tna.saw-scores.destroy');
        Route::post('/penilaian-kinerja', [TnaController::class, 'storeAssessment'])->name('tna.assessments.store');
        Route::post('/perencanaan-pelatihan', [TnaController::class, 'storePlan'])->name('tna.planning.store');
    });

    Route::get('/nilai-saw', [TnaController::class, 'sawScores'])->name('tna.saw-scores.index');
    Route::get('/analisis-tna', [TnaController::class, 'analysis'])->name('tna.analysis');
    Route::get('/penilaian-kinerja', [TnaController::class, 'assessments'])->name('tna.assessments');
    Route::get('/perencanaan-pelatihan', [TnaController::class, 'planning'])->name('tna.planning');
    Route::get('/laporan', [TnaController::class, 'report'])->name('tna.report');
    Route::get('/laporan/export', [TnaController::class, 'export'])->name('tna.export');
});
