<?php

use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', [UserController::class, 'createForm'])->name('register');
Route::get('/login-form', [UserController::class, 'loginForm'])->name('loginForm');
Route::post('/login', [UserController::class, 'login'])->name('login');
Route::post('/users', [UserController::class, 'storeUser'])->name('users');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [UserController::class, 'dashboard'])->name('dashboard');
    Route::get('/deposited-transactions', [TransactionController::class, 'showDepositedTransactions'])->name('deposited_transactions');
    Route::get('/withdrawal-transactions', [TransactionController::class, 'withdrawalTransactions'])->name('withdrawal_transactions');
    Route::post('/deposit', [TransactionController::class, 'depositMoney'])->name('deposit');
    Route::post('/withdraw', [TransactionController::class, 'withdrawMoney'])->name('withdraw');
});

Route::post('/logout', function () {
    Auth::logout();
    return redirect('/');
})->name('logout');