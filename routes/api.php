<?php

use Illuminate\Support\Facades\Route;
use App\Enums\UserRole;

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\EmailVerificationController;

use App\Http\Controllers\Api\AuthorController;
use App\Http\Controllers\Api\BookController;
use App\Http\Controllers\Api\CheckoutController;
use App\Http\Controllers\Api\GenresController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\DiscountController;
use App\Http\Controllers\Api\ShoppingCartController;
use App\Http\Controllers\Api\UserManagementController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\ShippingController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\PublisherController;
use App\Http\Controllers\Api\CityController;
use App\Http\Controllers\Api\AddressController;
use App\Http\Controllers\Api\StatisticsController;
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
    Route::get('/google/url', [GoogleController::class, 'loginUrl'])->name('auth.google.url');
    Route::get('/google/callback', [GoogleController::class, 'loginCallback'])->name('auth.google.callback');
});


/* End of Auth Routes */
/* -------------------------------------------------------------------------- */


/* Email Verification Routes */
Route::group(
    ['middleware' => ['auth:sanctum']],
    function () {
        Route::get(
            '/email/verify/{id}/{hash}',
            [EmailVerificationController::class, 'verify']
        )->name('verification.verify');

        Route::post(
            '/email/verification-notification',
            [EmailVerificationController::class, 'sendVerificationEmail']
        )->name('verification.send');
    }
);
/* End of Email Verification Routes */
/* -------------------------------------------------------------------------- */


/* Admin Routes */
Route::group([
    'middleware' => ['auth:sanctum', 'role:' . UserRole::getKey(UserRole::Admin)],
    'prefix' => 'admin'
], function () {
    Route::apiResource('/publishers', PublisherController::class);
    Route::apiResource('/authors', AuthorController::class);
    Route::group(['prefix' => 'books'], function () {
        Route::get('/', [BookController::class, 'getBooks'])->name('books.getBooks');
        Route::get('/{book}', [BookController::class, 'getBook'])->name('books.getBook');
        Route::post('/', [BookController::class, 'createBook'])->name('books.createBook');
        Route::post('/{book}', [BookController::class, 'updateBook'])->name('books.updateBook');
        Route::delete('/{book}', [BookController::class, 'deleteBook'])->name('books.deleteBook');
    });
    Route::group(['prefix' => 'orders'], function () {
        Route::get('/', [OrderController::class, 'allOrders'])->name('orders.all');
        Route::post('/update/{order}', [OrderController::class, 'updateStatus'])->name('orders.update');
        Route::delete('/{order}', [OrderController::class, 'destroy'])->name('orders.destroy');
    });
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
        Route::put('/remove-role', [UserManagementController::class, 'removeRole']);
    });
    Route::get('/statistics', [StatisticsController::class, 'getStatistics']);
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

    Route::group([
        'prefix' => 'shipping'
    ], function () {
        Route::get('/{order}', [ShippingController::class, 'getShipping'])->name('shipping.get');
        Route::post('/{order}', [ShippingController::class, 'store'])->name('shipping.store');
        Route::put('/{order}', [ShippingController::class, 'update'])->name('shipping.update');
        Route::put('/{order}', [ShippingController::class, 'updateShippingOn'])->name('shipping.update.shippingon');
    });

    Route::group([
        'prefix' => 'addresses'
    ], function () {
        Route::get('/', [AddressController::class, 'index'])->name('address.get');
        Route::post('/', [AddressController::class, 'store'])->name('address.store');
        Route::get('/{address}', [AddressController::class, 'show'])->name('address.show');
        Route::put('/{address}', [AddressController::class, 'update'])->name('address.update');
        Route::delete('/{address}', [AddressController::class, 'destroy'])->name('address.remove');
        Route::put('/set-default/{address}', [AddressController::class, 'setDefault'])->name('address.update.default');
    });
    Route::group([
        'prefix' => 'orders'
    ], function () {
        Route::get('/', [OrderController::class, 'index'])->name('orders.index');
        Route::get('/{order}', [OrderController::class, 'show'])->name('orders.show');
    });
});
/* End of User Routes */
/* -------------------------------------------------------------------------- */

/* Guest Routes */
Route::group([
    'prefix' => 'books'
], function () {
    Route::get('/', [BookController::class, 'getBooks'])->name('books.getBooks');
    Route::get('/{book}', [BookController::class, 'getBook'])->name('books.getBook');
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
    'prefix' => 'reviews'
], function () {
    Route::get('/', [ReviewController::class, 'getReviews']);
    Route::get('/{book}', [ReviewController::class, 'getReviewsByBook']);
    Route::post('/{book}', [ReviewController::class, 'createOrUpdateReview']);
    Route::delete('/{review}', [ReviewController::class, 'deleteReview']);
});
/* End of Guest Routes */
/* -------------------------------------------------------------------------- */

Route::group([
    'prefix' => 'cities'
], function () {
    Route::get('/province', [CityController::class, 'getAllProvince']);
    Route::get('/province/{province}', [CityController::class, 'getCityFromProvince']);
});
