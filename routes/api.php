<?php

use App\Http\Controllers\Api\V1;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware('throttle:api')->group(function () {
    Route::get('/stories', [V1\StoryController::class, 'index']);
    Route::get('/stories/{story}', [V1\StoryController::class, 'show']);
    Route::get('/comments', [V1\CommentController::class, 'index']);
    Route::get('/comments/{comment}', [V1\CommentController::class, 'show']);
    Route::get('/users/{user}', [V1\UserController::class, 'show']);
    Route::get('/vote-reasons', [V1\VoteController::class, 'reasons']);

    Route::post('/tokens', [V1\TokenController::class, 'store'])->middleware('throttle:login');

    Route::middleware(['auth:sanctum', 'not-banned'])->group(function () {
        Route::get('/me', [V1\UserController::class, 'me']);
        Route::delete('/tokens/current', [V1\TokenController::class, 'destroy']);

        Route::post('/stories', [V1\StoryController::class, 'store'])->middleware('throttle:submit');
        Route::patch('/stories/{story}', [V1\StoryController::class, 'update']);
        Route::delete('/stories/{story}', [V1\StoryController::class, 'destroy']);

        Route::post('/stories/{story}/comments', [V1\CommentController::class, 'store'])->middleware('throttle:comment');
        Route::patch('/comments/{comment}', [V1\CommentController::class, 'update']);
        Route::delete('/comments/{comment}', [V1\CommentController::class, 'destroy']);

        Route::post('/stories/{story}/vote', [V1\VoteController::class, 'story'])->middleware('throttle:vote');
        Route::post('/comments/{comment}/vote', [V1\VoteController::class, 'comment'])->middleware('throttle:vote');
    });
});
