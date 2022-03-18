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
use App\Http\Controllers\SavingsController;
use App\Http\Controllers\SubscriptionsController;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Request;

use function PHPSTORM_META\map;

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
Route::post('forgot-password/confirm-code', [AuthController::class, 'confirm_password_reset_code']);

Route::post('test', function () {
	$request_id = (string) Str::uuid();
	$api_version = config('mesomb.version');
	$api_url = "https://mesomb.hachther.com/api/{$api_version}/payment/online/";

	$headers = [
		'X-MeSomb-Application'      => config('mesomb.key'),
		'X-MeSomb-RequestId'        => $request_id,
		// 'X-MeSomb-OperationMode'    => 'asynchronous'
	];

	$data = [
		"service" => 'MTN',
		"amount" => 10,
		"payer" => "237672374414",
	];

	try {
		$response = Http::withHeaders($headers)->post($api_url, $data);
		return json_encode($response);
	} catch (\Throwable $th) {
		// refund the amount to payer

		return json_encode([
			"success" => false,
			"message" => $th->getMessage()
		]);
	}
});

Route::get('campay', [CampayController::class, 'collect']);
Route::get('campay/{reference}', [CampayController::class, 'checkTransactionStatus']);

Route::middleware('auth:sanctum')->group(function () {
	Route::post('logout', [AuthController::class, 'logout'])->name('logout');
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
	// Route::get('subscribers', [SubscriptionsController::class, 'index']);
});

Route::fallback(function () {
	return response()->json([
		"success" => false,
		"message" => 'Page Not Found. If error persists, contact info@website.com'
	], 404);
});
