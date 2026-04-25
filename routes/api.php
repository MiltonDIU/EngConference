<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\Admin\PaymentApiController;
use App\Http\Controllers\Api\V1\Admin\HotelsApiController;
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
Route::post('success/payment',[PaymentApiController::class,'successPayment'])->name('successPayment');
Route::post('check/payment',[PaymentApiController::class,'checkPayment'])->name('checkPayment');
Route::get('status/payment',[PaymentApiController::class,'statusPayment'])->name('statusPayment');
Route::get('diu-jobs',[HotelsApiController::class,'career'])->name('career');
//Route::get('success/payment',[PaymentApiController::class,'successPayment'])->name('successPayment');
//Route::get('fail/payment',[PaymentApiController::class,'failPayment'])->name('failPayment');


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
