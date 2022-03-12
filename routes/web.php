<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return view('/auth/login');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/client', [App\Http\Controllers\ClientController::class, 'index'])->name('client');
Route::post('/client/get_list', [App\Http\Controllers\ClientController::class, 'get_list'])->name('client/get_list');

Route::get('/product', [App\Http\Controllers\ProductController::class, 'index'])->name('product');
Route::post('/product/get_list', [App\Http\Controllers\ProductController::class, 'get_list'])->name('product/get_list');

Route::get('/order', [App\Http\Controllers\OrderController::class, 'index'])->name('order');
Route::post('/order/get_list', [App\Http\Controllers\OrderController::class, 'get_list'])->name('order/get_list');

Route::get('/delivery', [App\Http\Controllers\DeliveryController::class, 'index'])->name('delivery');
Route::post('/delivery/get_list', [App\Http\Controllers\DeliveryController::class, 'get_list'])->name('delivery/get_list');

Route::get('/helpdesk', [App\Http\Controllers\HelpdeskController::class, 'index'])->name('helpdesk');
Route::post('/helpdesk/get_list', [App\Http\Controllers\HelpdeskController::class, 'get_list'])->name('helpdesk/get_list');

Route::get('/offer', [App\Http\Controllers\OfferController::class, 'index'])->name('offer');
Route::post('/offer/get_list', [App\Http\Controllers\OfferController::class, 'get_list'])->name('offer/get_list');
