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

Route::get('/followed-tags', [TagController::class, 'followedTags'])->name('followed-tags')->middleware('auth');

// Static
Route::controller(StaticController::class)->group(function () {
	Route::get('/', 'index')->name('home');
	Route::get('/about', 'aboutUs')->name('about-us');
	Route::get('/contacts', 'contacts')->name('contacts');
});

// Answers
Route::controller(AnswerController::class)->group(function () {
	Route::get('/my-answers', 'myAnswers')->name('my-answers')->middleware('auth');
	Route::put('/answers', 'create');
});

// Questions
Route::controller(QuestionController::class)->group(function () {
	Route::get('/followed-questions', 'followedQuestions')->name('followed-questions')->middleware('auth');
	Route::get('/my-questions', 'myQuestions')->name('my-questions')->middleware('auth');

    Route::get('/questions/create', 'showCreateForm')->middleware('auth');
	Route::post('/questions/create', 'create')->name('questions-create')->middleware('auth');

	Route::get('/questions/{id}', 'show');
	Route::delete('/questions/{id}', 'delete');
	Route::patch('/questions/{id}', 'update');
});

// Users
Route::controller(UserController::class)->group(function () {
	Route::get('/profile', 'profile')->name('profile');
	Route::get('/profile/edit', 'edit')->name('profile.edit')->middleware('auth');
	Route::put('/profile', 'update')->name('profile.update')->middleware('auth');
	Route::delete('/profile', 'destroy')->name('profile.destroy')->middleware('auth');
});

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

// Public User Profile

Route::get('/user/{id}', [UserController::class, 'showPublicProfile'])->name('public.profile');