<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\QuizController;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/quiz', [QuizController::class, 'index'])->name('quiz.index');
Route::post('/quiz/{questionIndex}/answer', [QuizController::class, 'storeAnswer'])->name('quiz.storeAnswer');
