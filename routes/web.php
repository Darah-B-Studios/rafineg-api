<?php

use App\Http\Controllers\Admin\AdminUsersController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SavingsController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::apiResource('savings', SavingsController::class);

Route::prefix('admin')->group(function() {
    Route::resource('manage-users', AdminUsersController::class);
});

Route::get('/', function () {
    return view('welcome');
});
