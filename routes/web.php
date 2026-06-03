<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\FinanceController;

Route::get('/', function () {
    return Inertia::render('WelcomePortal');
})->name('home');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])
    ->middleware(['auth', 'role:admin'])
    ->name('admin.dashboard');

Route::get('/user/dashboard', [DashboardController::class, 'userDashboard'])
    ->middleware(['auth', 'role:usuario'])
    ->name('user.dashboard');

    Route::get('/finanzas', [FinanceController::class, 'index'])
    ->middleware(['auth'])
    ->name('finances.index');

Route::post('/finanzas/ingresos', [FinanceController::class, 'storeIncome'])
    ->middleware(['auth'])
    ->name('finances.incomes.store');

Route::post('/finanzas/gastos', [FinanceController::class, 'storeExpense'])
    ->middleware(['auth'])
    ->name('finances.expenses.store');

    require __DIR__.'/auth.php';