<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Enums\UserRole;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PublisherController;
use App\Http\Controllers\Api\AuthorController;
use App\Http\Controllers\Api\BookController;
use App\Http\Controllers\Api\CheckoutController;
use App\Http\Controllers\Api\NewPasswordController;
use App\Http\Controllers\Api\EmailVerificationController;
use App\Http\Controllers\Api\GenresController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\DiscountController;
use App\Http\Controllers\Api\ShoppingCartController;
use App\Http\Controllers\Api\UserManagementController;
use App\Http\Controllers\Api\SearchController;
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

/* Auth Routes */

Route::group([
    'prefix' => 'auth'
], function () {
    Route::post('/login', [AuthController::class, 'login'])->name('auth.login');
    Route::post('/register', [AuthController::class, 'register'])->name('auth.register');
    Route::post('/forgot-password', [NewPasswordController::class, 'forgotPassword'])->name('password.email');
    Route::post('/reset-password', [NewPasswordController::class, 'resetPassword'])->name('password.update');
});
/* End of Auth Routes */
/* -------------------------------------------------------------------------- */


/* Email Verification Routes */
Route::middleware(['auth:sanctum',])->group(function () {
    Route::get(
        '/email/verify/{id}/{hash}',
        [EmailVerificationController::class, 'verify']
    )->name('verification.verify');

    Route::post(
        '/email/verification-notification',
        [EmailVerificationController::class, 'sendVerificationEmail']
    )->name('verification.send');
});
/* End of Email Verification Routes */
/* -------------------------------------------------------------------------- */


/* Admin Routes */
Route::group([
    'middleware' => ['auth:sanctum', 'role:' . UserRole::getKey(UserRole::Admin)],
    'prefix' => 'admin'
], function () {
    Route::apiResource('/publishers', PublisherController::class);
    Route::apiResource('/authors', AuthorController::class);
    Route::apiResource('/books', BookController::class);
    Route::apiResource('/genres', GenresController::class);
    Route::apiResource('/discounts', DiscountController::class);
    Route::group([
        'prefix' => 'users'
    ], function () {
        Route::get('/', [UserManagementController::class, 'getUsers']);
        Route::get('/{user}', [UserManagementController::class, 'getUser']);
        Route::put('/active', [UserManagementController::class, 'activeUser']);
        Route::put('/unactive', [UserManagementController::class, 'unactiveUser']);
        Route::post('/assign-role', [UserManagementController::class, 'assignRole']);
        Route::delete('/remove-role', [UserManagementController::class, 'removeRole']);
    });
});
/* End of Admin Routes */
/* -------------------------------------------------------------------------- */


/* User Routes */
Route::group([
    'middleware' => ['auth:sanctum', 'active'],
], function () {
    Route::group([
        'prefix' => 'user'
    ], function () {
        Route::get('/profile', [UserController::class, 'getProfile'])->name('users.getProfile');
        Route::post('/profile', [UserController::class, 'createOrUpdateProfile'])->name('users.createOrUpdateProfile');
        Route::put('/password', [UserController::class, 'updatePassword'])->name('user.password.update');
    });

    Route::group([
        'prefix' => 'books'
    ], function () {
        Route::get('/', [BookController::class, 'index'])->name('books.index');
        Route::get('/{book}', [BookController::class, 'show'])->name('books.show');
    });

    Route::group([
        'prefix' => 'authors'
    ], function () {
        Route::get('/', [AuthorController::class, 'index'])->name('authors.index');
        Route::get('/{author}', [AuthorController::class, 'show'])->name('authors.show');
    });

    Route::group([
        'prefix' => 'publishers'
    ], function () {
        Route::get('/', [PublisherController::class, 'index'])->name('publishers.index');
        Route::get('/{publisher}', [PublisherController::class, 'show'])->name('publishers.show');
    });

    Route::group([
        'prefix' => 'genres'
    ], function () {
        Route::get('/', [GenresController::class, 'index'])->name('genres.index');
        Route::get('/{genre}', [GenresController::class, 'show'])->name('genres.show');
    });

    Route::group([
        'prefix' => 'cart'
    ], function () {
        Route::get('/get', [ShoppingCartController::class, 'getCart'])->name('cart.get');
        Route::post('/add-to-cart', [ShoppingCartController::class, 'addToCart'])->name('cart.add');
        Route::put('/update', [ShoppingCartController::class, 'updateCart'])->name('cart.update');
        Route::put('/remove', [ShoppingCartController::class, 'removeFromCart'])->name('cart.remove');
        Route::put('/clear', [ShoppingCartController::class, 'clearCart'])->name('cart.clear');
        Route::put('/add-checked-item', [ShoppingCartController::class, 'addCheckedItem'])->name('cart.addCheckedItems');
        Route::put('/add-all-checked-item', [ShoppingCartController::class, 'addAllCheckedItem'])->name('cart.addAllCheckedItems');
    });

    Route::group([
        'prefix' => 'checkout'
    ], function () {
        Route::post('/payment/confirm', [CheckoutController::class, 'confirmPayment'])->name('checkout.payment.confirm');
    });
});
/* End of User Routes */
/* -------------------------------------------------------------------------- */


/* Search Routes */
Route::group([
    'prefix' => 'search'
], function () {
    Route::get('/books', [SearchController::class, 'searchBooks'])->name('search.books');
    Route::get('/authors', [SearchController::class, 'searchAuthors'])->name('search.authors');
    Route::get('/publishers', [SearchController::class, 'searchPublishers'])->name('search.publishers');
    Route::get('/genres', [SearchController::class, 'searchGenres'])->name('search.genres');
});
