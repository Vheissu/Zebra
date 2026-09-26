<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\StoryController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VoteController;
use Illuminate\Support\Facades\Route;

// Reading
Route::get('/', [StoryController::class, 'index'])->name('home');
Route::get('/newest', [StoryController::class, 'newest'])->name('newest');
Route::get('/ask', [StoryController::class, 'ask'])->name('ask');
Route::get('/from/{domain}', [StoryController::class, 'domain'])->where('domain', '[A-Za-z0-9.-]+')->name('domain');
Route::get('/comments', [CommentController::class, 'index'])->name('comments.index');
Route::get('/s/{story}/{slug?}', [StoryController::class, 'show'])
    ->whereNumber('story')->where('slug', '(?!edit$)[A-Za-z0-9-]+')->name('stories.show');
Route::get('/c/{comment}', [CommentController::class, 'show'])->whereNumber('comment')->name('comments.show');
Route::get('/u/{user}', [UserController::class, 'show'])->name('users.show');
Route::get('/u/{user}/submissions', [UserController::class, 'stories'])->name('users.stories');
Route::get('/u/{user}/comments', [UserController::class, 'comments'])->name('users.comments');

// Accounts
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->middleware('throttle:login');
    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store'])->middleware('throttle:register');

    Route::get('/forgot-password', [PasswordResetController::class, 'request'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetController::class, 'email'])
        ->middleware('throttle:password-reset')->name('password.email');
    Route::get('/reset-password/{token}', [PasswordResetController::class, 'edit'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'update'])->name('password.update');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

    Route::get('/submit', [StoryController::class, 'create'])->name('stories.create');
    Route::post('/submit', [StoryController::class, 'store'])->middleware('throttle:submit')->name('stories.store');
    Route::get('/s/{story}/edit', [StoryController::class, 'edit'])->name('stories.edit');
    Route::put('/s/{story}', [StoryController::class, 'update'])->name('stories.update');
    Route::delete('/s/{story}', [StoryController::class, 'destroy'])->name('stories.destroy');

    Route::post('/s/{story}/comments', [CommentController::class, 'store'])
        ->middleware('throttle:comment')->name('comments.store');
    Route::get('/c/{comment}/edit', [CommentController::class, 'edit'])->name('comments.edit');
    Route::put('/c/{comment}', [CommentController::class, 'update'])->name('comments.update');
    Route::delete('/c/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');

    Route::post('/vote/{type}/{id}', [VoteController::class, 'store'])
        ->whereIn('type', ['story', 'comment'])->whereNumber('id')
        ->middleware('throttle:vote')->name('vote');

    Route::get('/settings', [SettingsController::class, 'edit'])->name('settings');
    Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');
    Route::put('/settings/password', [SettingsController::class, 'password'])->name('settings.password');
    Route::post('/settings/tokens', [SettingsController::class, 'createToken'])->name('settings.tokens.store');
    Route::delete('/settings/tokens/{token}', [SettingsController::class, 'destroyToken'])->name('settings.tokens.destroy');
});

// Moderation
Route::middleware(['auth', 'can:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', Admin\DashboardController::class)->name('dashboard');

    Route::get('/stories', [Admin\StoryController::class, 'index'])->name('stories');
    Route::delete('/stories/{story}', [Admin\StoryController::class, 'destroy'])->name('stories.destroy');
    Route::post('/stories/{id}/restore', [Admin\StoryController::class, 'restore'])->name('stories.restore');

    Route::get('/from/{domain}', [StoryController::class, 'domain'])->where('domain', '[A-Za-z0-9.-]+')->name('domain');
    Route::get('/comments', [Admin\CommentController::class, 'index'])->name('comments');
    Route::delete('/comments/{comment}', [Admin\CommentController::class, 'destroy'])->name('comments.destroy');
    Route::post('/comments/{id}/restore', [Admin\CommentController::class, 'restore'])->name('comments.restore');

    Route::get('/users', [Admin\UserController::class, 'index'])->name('users');
    Route::post('/users/{user}/ban', [Admin\UserController::class, 'ban'])->name('users.ban');
    Route::post('/users/{user}/unban', [Admin\UserController::class, 'unban'])->name('users.unban');
    Route::post('/users/{user}/admin', [Admin\UserController::class, 'toggleAdmin'])->name('users.admin');
});
