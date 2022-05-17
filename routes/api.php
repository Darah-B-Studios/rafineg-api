<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CampayController;
use App\Http\Controllers\ContractsController;
use App\Http\Controllers\NjangiGroupController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\PackagesController;
use App\Http\Controllers\ProfilesController;
use App\Http\Controllers\ReferalController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use App\Http\Controllers\RegistrationFeesController;
use App\Http\Controllers\ReportsControler;
use App\Http\Controllers\SavingsController;
use App\Http\Controllers\SubscriptionsController;
use Illuminate\Support\Facades\Http;


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

Route::post('signup', [AuthController::class, 'signup']);
Route::post('signin', [AuthController::class, 'signin']);
Route::post('reset-password', [AuthController::class, 'reset_password']);
Route::post('verify/${method}', [AuthController::class, 'verify_client']);
Route::post('forgot-password', [AuthController::class, 'forgot_password']);
Route::post('forgot-password/{code}', [AuthController::class, 'confirm_password_reset_code']);

// Campay endpoints


Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('cashbox', [UsersController::class, 'cashbox']);
    Route::apiResource('users', UsersController::class);
    Route::apiResource('packages', PackagesController::class);
    Route::apiResource('profiles', ProfilesController::class);
    Route::apiResource('contracts', ContractsController::class);
    Route::apiResource('referals', ReferalController::class);
    Route::apiResource('registration', RegistrationFeesController::class);
    Route::apiResource('savings', SavingsController::class);
    Route::apiResource('njangi-groups', NjangiGroupController::class)->except(['create', 'show', 'edit']);
    Route::apiResource('cash-transfers', NjangiGroupController::class)->except(['create', 'edit']);

    Route::post('subscribe/{package_id}', [SubscriptionsController::class, 'subscribe']);
    Route::post('unsubscribe/{package_id}', [SubscriptionsController::class, 'unsubscribe']);
    Route::get('subscribers/{package_id}', [SubscriptionsController::class, 'subscribers']);
    Route::get('subscriptions', [SubscriptionsController::class, 'index']);
    Route::get('subscriptions/user/{user_id}', [SubscriptionsController::class, 'user_subscriptions']);

    // reports
    Route::get('transactions/report', [ReportsControler::class, 'transaction_report']);

    // Campay endpoints
    Route::post('campay/withdraw', [CampayController::class, 'withdraw'])->name('campay.withdraw');
    Route::post('campay/collect', [CampayController::class, 'collect'])->name('campay.collect');
    Route::get('campay/status/{reference}', [CampayController::class, 'checkTransactionStatus'])->name('campay.transaction.status');
    Route::get('campay/user-transactions/{id?}', [CampayController::class, 'userTransactions'])->name('campay.user.transactions');
    Route::get('campay/balance', [CampayController::class, 'balance'])->name('campay.balance');
    Route::get('campay/callback', [CampayController::class, 'callback'])->name('campay.callback');
});

Route::fallback(function () {
    return response()->json([
        "success" => false,
        "message" => 'Page Not Found. If error persists, contact info@website.com'
    ], 404);
});