<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SettlementController;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// 1. Főoldal átirányítása a városok listájára
Route::redirect('/', '/settlements');

// 2. Hitelesítés (Login / Logout)
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// 3. Városok kezelése (Settlements)

// Először a speciális útvonalak (hogy ne akadjanak össze az ID-val)
Route::get('/settlements/filter', [SettlementController::class, 'filterView'])->name('settlements.filter');
Route::get('/settlements/export/pdf', [SettlementController::class, 'exportPdf'])->name('settlements.export.pdf');
Route::get('/settlements/export/csv', [SettlementController::class, 'exportCsv'])->name('settlements.export.csv');
// Majd a CRUD műveletek (index, create, store, edit, update, destroy)
Route::resource('settlements', SettlementController::class);
Route::resource('counties', \App\Http\Controllers\CountyController::class);