<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AlumniController;
use App\Http\Controllers\SurveyController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\ResponseController;
use App\Http\Controllers\EmploymentDataController;
use App\Http\Controllers\ClusterController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// Routes yang hanya bisa diakses setelah login
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Routes untuk Admin
    Route::middleware(['role:admin'])->group(function () {
        // Alumni Management (Admin)
        Route::resource('alumni', AlumniController::class)->except(['create', 'store', 'edit', 'update']);
        
        // Survey Management
        Route::resource('surveys', SurveyController::class)->except(['show']);
        Route::get('/surveys/{survey}/results', [SurveyController::class, 'results'])->name('surveys.results');
        
        // Question Management
        Route::resource('surveys.questions', QuestionController::class)->except(['show']);
        Route::post('/surveys/{survey}/questions/reorder', [QuestionController::class, 'reorder'])->name('surveys.questions.reorder');
        
        // Response Management
        Route::get('/responses', [ResponseController::class, 'index'])->name('responses.index');
        Route::get('/responses/{survey}/{alumni}', [ResponseController::class, 'show'])->name('responses.show');
        Route::get('/responses/{survey}/export', [ResponseController::class, 'export'])->name('responses.export');
        Route::delete('/responses/{survey}/{alumni}', [ResponseController::class, 'destroy'])->name('responses.destroy');
        
        // Clustering
        Route::get('/clustering/analyze', [ClusterController::class, 'analyze'])->name('clustering.analyze');
        Route::post('/clustering/process', [ClusterController::class, 'process'])->name('clustering.process');
        Route::get('/clustering/results/{id}', [ClusterController::class, 'show'])->name('clustering.results');
        Route::post('/clustering/compare', [ClusterController::class, 'compare'])->name('clustering.compare');
        Route::get('/clustering/export/{id}', [ClusterController::class, 'export'])->name('clustering.export');
    });
    
    // Routes untuk Alumni
    Route::middleware(['role:alumni'])->group(function () {
        // Profile Alumni
        Route::get('/profile/create', [AlumniController::class, 'create'])->name('alumni.create');
        Route::post('/profile', [AlumniController::class, 'store'])->name('alumni.store');
        Route::get('/profile/edit', [AlumniController::class, 'edit'])->name('alumni.edit');
        Route::put('/profile', [AlumniController::class, 'update'])->name('alumni.update');
        
        // Employment Data
        Route::resource('employment-data', EmploymentDataController::class)->except(['index', 'show']);
    });
    
    // Routes Umum (untuk Admin dan Alumni)
    // Survey yang bisa dilihat semua user
    Route::get('/surveys/{survey}', [SurveyController::class, 'show'])->name('surveys.show');
    
    // Employment Data yang bisa dilihat
    Route::get('/employment-data', [EmploymentDataController::class, 'index'])->name('employment-data.index');
    Route::get('/employment-data/{employmentData}', [EmploymentDataController::class, 'show'])->name('employment-data.show');
    
    // Survey Fill (hanya untuk alumni)
    Route::get('/surveys/{survey}/fill', [SurveyController::class, 'fill'])->name('surveys.fill')->middleware('role:alumni');
    Route::post('/surveys/{survey}/submit', [SurveyController::class, 'submit'])->name('surveys.submit')->middleware('role:alumni');
});

// Auth routes
require __DIR__.'/auth.php';