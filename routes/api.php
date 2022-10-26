<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Enums\UserRole;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PublisherController;
use App\Http\Controllers\Api\AuthorController;
use App\Http\Controllers\Api\BookController;
use App\Http\Controllers\Api\DiscountController;
use App\Http\Controllers\VerificationController;
use App\Http\Controllers\Api\GenreController;
use App\Models\Genre;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group([
    'prefix' => 'auth'
], function ($router) {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
});


Route::group([
    'middleware' => ['auth:sanctum', 'role:' . UserRole::getKey(UserRole::Admin)],
], function ($router) {
    Route::apiResource('/publishers', PublisherController::class);
    Route::apiResource('/authors', AuthorController::class);
    Route::apiResource('/books', BookController::class);
    Route::apiResource('/genres', GenreController::class);
    Route::apiResource('/discounts', DiscountController::class);
});


Route::middleware(['auth:sanctum', 'active'])->group(function () {
    Route::apiResource('/publishers', PublisherController::class)->only(['show', 'index']);
});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('email/verify/{id}',  [VerificationController::class, 'verify'])->name('verification.verify'); // Make sure to keep this as your route name

    Route::get('email/resend', [VerificationController::class, 'resend'])->name('verification.resend');
});