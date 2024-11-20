<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\StaticController;
use App\Http\Controllers\UserController;


use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\AnswerController;
use App\Http\Controllers\SearchController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Home
Route::get('/', [StaticController::class, 'index'])->name('home');
Route::get('/profile', [UserController::class, 'profile'])->name('profile');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/followed-tags', [TagController::class, 'followedTags'])->name('followed-tags')->middleware('auth');
Route::get('/my-questions', [QuestionController::class, 'myQuestions'])->name('my-questions')->middleware('auth');
Route::get('/my-answers', [AnswerController::class, 'myAnswers'])->name('my-answers')->middleware('auth');
Route::get('/about', [StaticController::class, 'aboutUs'])->name('about-us');

Route::get('/profile/edit', [UserController::class, 'edit'])->name('profile.edit')->middleware('auth');
Route::put('/profile/update', [UserController::class, 'update'])->name('profile.update')->middleware('auth');
Route::delete('/profile/destroy', [UserController::class, 'destroy'])->name('profile.destroy')->middleware('auth');

// Questions
Route::controller(QuestionController::class)->group(function () {
	Route::get('/followed-questions', 'followedQuestions')->name('followed-questions')->middleware('auth');
    Route::get('/questions/create', 'showCreateForm')->middleware('auth');
	Route::post('/questions/create', 'create')->name('questions-create')->middleware('auth');
	Route::get('/questions/{id}', 'show');
});


// API
//Route::controller(CardController::class)->group(function () {
//    Route::put('/api/cards', 'create');
//    Route::delete('/api/cards/{card_id}', 'delete');
//});

//Route::controller(ItemController::class)->group(function () {
//    Route::put('/api/cards/{card_id}', 'create');
//    Route::post('/api/item/{id}', 'update');
//    Route::delete('/api/item/{id}', 'delete');
//});


// Authentication
Route::controller(LoginController::class)->group(function () {
    Route::get('/login', 'showLoginForm')->name('login');
    Route::post('/login', 'authenticate');
    Route::get('/logout', 'logout')->name('logout');
});

Route::controller(RegisterController::class)->group(function () {
    Route::get('/register', 'showRegistrationForm')->name('register');
    Route::post('/register', 'register');
});

// Search
Route::get('/search', [SearchController::class, 'index'])->name('search');