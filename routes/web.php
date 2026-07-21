<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TestController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\HomeController;

Route::get('/', [HomeController::class, 'index']);

Route::post('/change-language', [LanguageController::class, 'change'])->name('change.language');

Route::middleware(['auth'])->group(function () {
    Route::get('/welcome', [HomeController::class, 'welcome'])->name('welcome');
    
    Route::get('/export/users', [AdminController::class, 'exportUsers'])->name('admin.export.users');
    Route::get('/export/results', [AdminController::class, 'exportResults'])->name('admin.export.results');
    Route::get('/export/users/csv', [AdminController::class, 'exportUsersCsv'])->name('admin.export.users.csv');
    Route::get('/export/results/csv', [AdminController::class, 'exportResultsCsv'])->name('admin.export.results.csv');
    
    Route::get('/test/{subject}', [TestController::class, 'start'])->name('test.start');
    Route::post('/test/submit', [TestController::class, 'submit'])->name('test.submit');
    Route::get('/test/result/{id}', [TestController::class, 'result'])->name('test.result');
    
    // ===== МАРШРУТЫ ДЛЯ СЕРТИФИКАТОВ =====
    Route::get('/certificate/{id}', [CertificateController::class, 'show'])->name('certificate.show');
    Route::get('/certificate/{id}/download', [CertificateController::class, 'download'])->name('certificate.download');
    Route::get('/certificate/generate/{id}', [CertificateController::class, 'generate'])->name('certificate.generate');
    Route::get('/certificate/download/{id}', [TestController::class, 'downloadCertificate'])->name('certificate.download');
});

// ===== АДМИН-МАРШРУТЫ =====
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    
    Route::delete('/user/{id}', [AdminController::class, 'deleteUser'])->name('user.delete');
    Route::post('/user/{id}/block', [AdminController::class, 'blockUser'])->name('user.block');
    Route::get('/user/{id}', [AdminController::class, 'userDetails'])->name('user.details');
    
    Route::get('/questions', [AdminController::class, 'questions'])->name('questions');
    Route::get('/questions/create', [AdminController::class, 'createQuestion'])->name('questions.create');
    Route::post('/questions', [AdminController::class, 'storeQuestion'])->name('questions.store');
    Route::get('/questions/{id}/edit', [AdminController::class, 'editQuestion'])->name('questions.edit');
    Route::put('/questions/{id}', [AdminController::class, 'updateQuestion'])->name('questions.update');
    Route::delete('/questions/{id}', [AdminController::class, 'deleteQuestion'])->name('questions.delete');
});

Auth::routes();