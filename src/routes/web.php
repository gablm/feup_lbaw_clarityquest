<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\StaticController;
use App\Http\Controllers\UserController;


use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\AnswerController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\NotificationController;

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

// Static
Route::controller(StaticController::class)->group(function () {
	Route::get('/', 'index')->name('home');
	Route::get('/about', 'aboutUs')->name('about-us');
	Route::get('/contacts', 'contacts')->name('contacts');
	Route::get('/search', 'search')->name('search'); 
	Route::get('/admin', 'admin')->name('admin');
});

// Comments
Route::controller(CommentController::class)->group(function () {
	Route::put('/comments', 'create')->middleware('auth');
	
	Route::delete('/comments/{id}', 'delete')->middleware('auth');
	Route::patch('/comments/{id}', 'update')->middleware('auth');
});

// Tags
Route::controller(TagController::class)->group(function () {
	Route::get('/followed-tags', 'followedTags')->name('followed-tags')->middleware('auth');
	Route::get('/tags/{id}', 'show')->middleware('auth');

	Route::put('/tags', 'create')->middleware('auth');
	Route::delete('/tags/{id}', 'delete')->middleware('auth');
	Route::patch('/tags/{id}', 'update')->middleware('auth');
});

// Answers
Route::controller(AnswerController::class)->group(function () {
	Route::get('/my-answers', 'myAnswers')->name('my-answers')->middleware('auth');

	Route::put('/answers', 'create')->middleware('auth');
	Route::delete('/answers/{id}', 'delete')->middleware('auth');
	Route::patch('/answers/{id}', 'update')->middleware('auth');
});

// Questions
Route::controller(QuestionController::class)->group(function () {
	Route::get('/followed-questions', 'followedQuestions')->name('followed-questions')->middleware('auth');
	Route::get('/my-questions', 'myQuestions')->name('my-questions')->middleware('auth');

    Route::get('/questions/create', 'showCreateForm')->middleware('auth');
	Route::post('/questions/create', 'create')->name('questions-create')->middleware('auth');

	Route::get('/questions/{id}', 'show');
	Route::delete('/questions/{id}', 'delete')->middleware('auth');
	Route::patch('/questions/{id}', 'update')->middleware('auth');
});

// Posts
Route::controller(PostController::class)->group(function () {
	Route::post('/posts/{id}', 'vote')->middleware('auth');
});

// Users
Route::controller(UserController::class)->group(function () {
	Route::get('/profile', 'profile')->name('profile');
	Route::get('/profile/edit', 'edit')->name('profile.edit')->middleware('auth');
	
	Route::get('/users/{id}', 'showPublicProfile')->name('public.profile');
	Route::patch('/users/{id}', 'update')->middleware('auth');
	Route::delete('/users/{id}', 'delete')->middleware('auth');
	
});

// Authentication
Route::controller(LoginController::class)->group(function () {
    Route::get('/login', 'showLoginForm')->name('login');
    Route::post('/login', 'authenticate');
    Route::post('/logout', 'logout')->name('logout');
});

Route::controller(RegisterController::class)->group(function () {
    Route::get('/register', 'showRegistrationForm')->name('register');
    Route::post('/register', 'register');
});

// Notifications
Route::controller(NotificationController::class)->group(function () {
    Route::get('/notifications', 'index')->name('pages.notifications')->middleware('auth');
	Route::delete('/notifications/{id}/delete', [NotificationController::class, 'delete'])->middleware('auth');
});