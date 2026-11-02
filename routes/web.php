<?php

// use Illuminate\Support\Facades\Route;
// use App\Http\Controllers\Admin\FeedbackController as AdminFeedbackController;
// use App\Http\Controllers\FeedbackController;

// Route::inertia('/', 'Welcome')->name('home');

// Route::middleware(['auth', 'verified'])->group(function () {
//     Route::inertia('dashboard', 'Dashboard')->name('dashboard');
// });

// Route::get('/', [FeedbackController::class, 'welcome'])->name('feedback.welcome');
// Route::post('/language', [FeedbackController::class, 'selectLanguage'])->name('feedback.language');
// Route::get('/step/{step}', [FeedbackController::class, 'showStep'])->whereNumber('step')->name('feedback.step');
// Route::post('/step/{step}', [FeedbackController::class, 'updateStep'])->whereNumber('step')->name('feedback.step.update');
// Route::get('/spin', [FeedbackController::class, 'spinPage'])->name('feedback.spin');
// Route::post('/spin', [FeedbackController::class, 'spin'])->name('feedback.spin.submit');
// Route::get('/result', [FeedbackController::class, 'result'])->name('feedback.result');

// Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
//     Route::get('/feedbacks', [AdminFeedbackController::class, 'index'])->name('feedbacks.index');
//     Route::get('/feedbacks/{feedback}', [AdminFeedbackController::class, 'show'])->name('feedbacks.show');
// });

// require __DIR__.'/settings.php';
// use Illuminate\Support\Facades\Route;
// use App\Http\Controllers\Admin\FeedbackController as AdminFeedbackController;
// use App\Http\Controllers\FeedbackController;

// Route::middleware(['auth', 'verified'])->group(function () {
//     Route::inertia('dashboard', 'Dashboard')->name('dashboard');
// });

// // Route::get('/', [FeedbackController::class, 'welcome'])->name('feedback.welcome');
// Route::get('/', [FeedbackController::class, 'welcome'])->name('home');
// Route::post('/language', [FeedbackController::class, 'selectLanguage'])->name('feedback.language');
// Route::get('/step/{step}', [FeedbackController::class, 'showStep'])->whereNumber('step')->name('feedback.step');
// Route::post('/step/{step}', [FeedbackController::class, 'updateStep'])->whereNumber('step')->name('feedback.step.update') ->middleware('throttle:feedback-step');
// Route::get('/spin', [FeedbackController::class, 'spinPage'])->name('feedback.spin');
// Route::post('/spin', [FeedbackController::class, 'spin'])->name('feedback.spin.submit')->middleware('throttle:spin');
// Route::get('/result', [FeedbackController::class, 'result'])->name('feedback.result');

// Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
//     Route::get('/feedbacks', [AdminFeedbackController::class, 'index'])->name('feedbacks.index');
//     Route::get('/feedbacks/{feedback}', [AdminFeedbackController::class, 'show'])->name('feedbacks.show');
// });

// require __DIR__ . '/settings.php';

use App\Http\Controllers\Admin\FeedbackController as AdminFeedbackController;
use App\Http\Controllers\FeedbackController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');
});

// Public feedback routes
Route::middleware('throttle:feedback-read')->group(function () {
    Route::get('/', [FeedbackController::class, 'welcome'])->name('home');
    Route::get('/step/{step}', [FeedbackController::class, 'showStep'])->whereNumber('step')->name('feedback.step');
    Route::get('/spin', [FeedbackController::class, 'spinPage'])->name('feedback.spin');
    Route::get('/result', [FeedbackController::class, 'result'])->name('feedback.result');
});

Route::post('/language', [FeedbackController::class, 'selectLanguage'])
    ->name('feedback.language')
    ->middleware('throttle:feedback-action');

Route::post('/step/{step}', [FeedbackController::class, 'updateStep'])
    ->whereNumber('step')
    ->name('feedback.step.update')
    ->middleware('throttle:feedback-step');

Route::post('/spin', [FeedbackController::class, 'spin'])
    ->name('feedback.spin.submit')
    ->middleware('throttle:spin');

// Admin routes
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/feedbacks', [AdminFeedbackController::class, 'index'])
        ->name('feedbacks.index')
        ->middleware('throttle:feedback-read');
    Route::get('/feedbacks/{feedback}', [AdminFeedbackController::class, 'show'])
        ->name('feedbacks.show')
        ->middleware('throttle:feedback-read');
});
Route::post('/done', [FeedbackController::class, 'done'])
    ->name('feedback.done')
    ->middleware('throttle:feedback-action');

require __DIR__.'/settings.php';
