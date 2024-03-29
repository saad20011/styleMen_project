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
use App\Http\Controllers\AttributesController;
use App\Http\Controllers\CategorieController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PhoneTypeController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\CarrierController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\PaymentTypeController;
use App\Http\Controllers\PaymentMethodController;
use App\Http\Controllers\ChargeTypeController;
use App\Http\Controllers\ChargeController;
use App\Http\Controllers\AccountCarrierCity;
use App\Http\Controllers\AttributeTypesController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\SourceController;
use App\Http\Controllers\DeliveryMenController;
use App\Http\Controllers\SupplierOrderController;
use App\Http\Controllers\SupplierReceiptController;
use App\Http\Controllers\OfferController;
use App\Http\Controllers\VariationAttributesController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\SubcommentController;
use App\Http\Controllers\OrdersController;
use App\Http\Controllers\ImageController;


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
Route::post('register_new_account', [RegisterController::class, 'register_new_account']);
Route::post('login', [RegisterController::class, 'login']);
// Route::resource('phoneType', PhoneTypeController::class);

Route::middleware(['auth:api' , 'VerifyDomain'])->group( function () {
    Route::post('register_new_user', [RegisterController::class, 'register_new_user']);
    Route::resource('product', ProductController::class);
    Route::resource('roles', RoleController::class);
    Route::resource('users', UserController::class);
    Route::resource('account', AccountController::class);
    Route::resource('category', CategorieController::class);
    Route::resource('phone', PhoneController::class);
    Route::resource('region', RegionController::class);
    Route::resource('city', CityController::class);
    Route::resource('attribute', AttributesController::class);
    Route::resource('phone_type', PhoneTypeController::class);
    Route::resource('address', AddressController::class);
    Route::resource('carrier', CarrierController::class);
    Route::resource('supplier', SupplierController::class);
    Route::resource('payment_type', PaymentTypeController::class);
    Route::resource('payment_method', PaymentMethodController::class);
    Route::resource('charge_type', ChargeTypeController::class);
    Route::resource('charge', ChargeController::class);
    Route::resource('carrier_city', AccountCarrierCity::class);
    Route::resource('image', ImageController::class);
    Route::put('/carrier_cities', [AccountCarrierCity::class, 'update_carrier_cities']);
    Route::resource('attribute_types', AttributeTypesController::class);
    Route::resource('brand', BrandController::class);
    Route::resource('customer', CustomerController::class);
    Route::resource('comments', CommentController::class);
    Route::resource('orders', OrdersController::class);
    Route::resource('subcomments', SubcommentController::class);
    Route::resource('source', SourceController::class);
    Route::resource('brand_source', SourceController::class);
    Route::resource('delivery_men', DeliveryMenController::class);
    Route::resource('supplier_order', SupplierOrderController::class);
    Route::resource('supplier_receipt', SupplierReceiptController::class);
    Route::resource('offer', OfferController::class);
    Route::resource('variation', VariationAttributesController::class);


    // Route::resource('products', ProductController::class);
});