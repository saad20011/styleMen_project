<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\RegisterController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PhoneController;
use App\Http\Controllers\RegionController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\SizeController;
use App\Http\Controllers\CategorieController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PhoneTypeController;
use App\Http\Controllers\AdresseController;
use App\Http\Controllers\CarrierController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\PaymentTypeController;
use App\Http\Controllers\PaymentMethodController;
use App\Http\Controllers\ChargeTypeController;
use App\Http\Controllers\ChargeController;
use App\Http\Controllers\Accounts_CarriersCities;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
Route::post('register', [RegisterController::class, 'register']);
Route::post('login', [RegisterController::class, 'login']);
// Route::resource('phoneType', PhoneTypeController::class);

Route::middleware('auth:api')->group( function () {
    Route::resource('product', ProductController::class);
    Route::resource('roles', RoleController::class);
    Route::resource('users', UserController::class);
    Route::resource('account', AccountController::class);
    Route::resource('category', CategorieController::class);
    Route::resource('phone', PhoneController::class);
    Route::resource('region', RegionController::class);
    Route::resource('city', CityController::class);
    Route::get('/cities', [CityController::class, 'edit_all_cities']);
    Route::put('/cities', [CityController::class, 'update_all_cities']);
    Route::resource('size', SizeController::class);
    Route::resource('phone_type', PhoneTypeController::class);
    Route::resource('adresse', AdresseController::class);
    Route::resource('carrier', CarrierController::class);
    Route::resource('supplier', SupplierController::class);
    Route::resource('payment_type', PaymentTypeController::class);
    Route::resource('payment_method', PaymentMethodController::class);
    Route::resource('charge_type', ChargeTypeController::class);
    Route::resource('charge', ChargeController::class);
    Route::resource('carrier_city', Accounts_CarriersCities::class);
    Route::put('/carrier_cities', [Accounts_CarriersCities::class, 'update_all_carrier_cities']);


    // Route::resource('products', ProductController::class);
});