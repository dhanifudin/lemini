<?php

use App\Http\Controllers\Student\Api\DashboardDataController;
use App\Http\Controllers\Student\Api\FeedbackIndexController;
use App\Http\Controllers\Student\Api\PracticeIndexController;
use App\Http\Controllers\Student\Api\QuizSessionController;
use App\Http\Controllers\Student\Api\QuizSessionResponseController;
use App\Http\Controllers\Student\Api\QuizSessionSubmitController;
use App\Http\Controllers\Student\Api\QuizTelemetryController;
use App\Http\Controllers\Student\Api\StoreReflectionController;
use App\Http\Controllers\Student\DashboardController;
use App\Http\Controllers\Student\FeedbackController;
use App\Http\Controllers\Student\PracticeController;
use App\Http\Controllers\Student\ProfileController;
use App\Http\Controllers\Teacher\Api\DashboardSummaryController as TeacherDashboardSummaryController;
use App\Http\Controllers\Teacher\Api\ModerateFeedbackController;
use App\Http\Controllers\Teacher\Api\PendingFeedbackController;
use App\Http\Controllers\Teacher\Api\QuizExportController;
use App\Http\Controllers\Teacher\Api\QuizSessionController as TeacherQuizSessionController;
use App\Http\Controllers\Teacher\Api\ReminderController;
use App\Http\Controllers\Teacher\DashboardController as TeacherDashboardController;
use App\Http\Controllers\Landing\LandingPageController;
use App\Http\Controllers\Landing\LearningStyleController;
use App\Http\Controllers\Landing\BehaviorTrackingController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

// Landing page routes
Route::get('/', [LandingPageController::class, 'index'])->name('home');

Route::prefix('api/landing')->group(function () {
    Route::post('learning-style/save', [LearningStyleController::class, 'save'])->name('landing.learning-style.save');
    Route::get('learning-style', [LearningStyleController::class, 'get'])->name('landing.learning-style.get');
    Route::post('track-behavior', [BehaviorTrackingController::class, 'track'])->name('landing.behavior.track');
    Route::get('analyze-behavior', [BehaviorTrackingController::class, 'analyze'])->name('landing.behavior.analyze');
});

Route::middleware(['auth', 'verified', 'role:student'])->group(function () {
    Route::get('dashboard', DashboardController::class)->name('dashboard');
    Route::get('feedback', FeedbackController::class)->name('student.feedback');
    Route::get('practice', PracticeController::class)->name('student.practice');
    Route::get('profile', ProfileController::class)->name('student.profile');

    Route::prefix('api')->name('student.api.')->group(function () {
        Route::get('dashboard', DashboardDataController::class)->name('dashboard');
        Route::get('feedback', FeedbackIndexController::class)->name('feedback');
        Route::get('practice-items', PracticeIndexController::class)->name('practice');
        Route::post('feedback/{feedback}/reflect', StoreReflectionController::class)->name('feedback.reflect');
        Route::post('quizzes', [QuizSessionController::class, 'store'])->name('quizzes.store');
        Route::post('quizzes/{quiz_session}/responses', [QuizSessionResponseController::class, 'store'])->name('quizzes.responses');
        Route::post('quizzes/{quiz_session}/submit', QuizSessionSubmitController::class)->name('quizzes.submit');
        Route::post('quizzes/{quiz_session}/telemetry', [QuizTelemetryController::class, 'store'])->name('quizzes.telemetry');
    });
});

require __DIR__.'/settings.php';

Route::middleware(['auth', 'verified', 'role:teacher'])->prefix('teacher')->name('teacher.')->group(function () {
    Route::get('dashboard', TeacherDashboardController::class)->name('dashboard');

    Route::prefix('api')->name('api.')->group(function () {
        Route::get('dashboard', TeacherDashboardSummaryController::class)->name('dashboard');
        Route::get('pending-feedback', PendingFeedbackController::class)->name('pending-feedback');
        Route::post('feedback/{feedback}/moderate', ModerateFeedbackController::class)->name('feedback.moderate');
        Route::post('reminders', ReminderController::class)->name('reminders');
        
        // Quiz review routes
        Route::get('quizzes', [TeacherQuizSessionController::class, 'index'])->name('quizzes.index');
        Route::get('quizzes/{quiz_session}', [TeacherQuizSessionController::class, 'show'])->name('quizzes.show');
        Route::post('quizzes/{quiz_session}/review', [TeacherQuizSessionController::class, 'review'])->name('quizzes.review');
        Route::get('quizzes-export', [QuizExportController::class, 'export'])->name('quizzes.export');
    });
});
