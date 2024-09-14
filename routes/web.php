<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaypalController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


Route::get('/',[PaypalController::class, 'index'])->name('home');
Route::post('/paypal',[PaypalController::class, 'paypal'])->name('paypal');
Route::get('/success',[PaypalController::class, 'success'])->name('success');
Route::get('/cancel',[PaypalController::class, 'cancel'])->name('cancel');
Route::get('/details/{capture_id}',[PaypalController::class, 'details'])->name('details');
Route::get('/refund/{capture_id}',[PaypalController::class, 'refund'])->name('refund');
Route::get('/refund-details/{refund_id}',[PaypalController::class, 'refundDetails'])->name('refunddetails');