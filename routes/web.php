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

    Route::get('/finanzas', [FinanceController::class, 'summary'])
    ->middleware(['auth'])
    ->name('finances.home');

Route::get('/finanzas/ingresos', [FinanceController::class, 'incomes'])
    ->middleware(['auth'])
    ->name('finances.incomes');

Route::post('/finanzas/ingresos', [FinanceController::class, 'storeIncome'])
    ->middleware(['auth'])
    ->name('finances.incomes.store');

Route::get('/finanzas/gastos', [FinanceController::class, 'expenses'])
    ->middleware(['auth'])
    ->name('finances.expenses');

Route::post('/finanzas/gastos', [FinanceController::class, 'storeExpense'])
    ->middleware(['auth'])
    ->name('finances.expenses.store');

Route::get('/finanzas/resumen', [FinanceController::class, 'summary'])
    ->middleware(['auth'])
    ->name('finances.summary');

    Route::get('/finanzas/configuracion', [FinanceController::class, 'settings'])
    ->middleware(['auth'])
    ->name('finances.settings');

Route::post('/finanzas/configuracion/categorias', [FinanceController::class, 'storeCategory'])
    ->middleware(['auth'])
    ->name('finances.categories.store');

    Route::delete('/finanzas/configuracion/categorias/{category}', [FinanceController::class, 'destroyCategory'])
    ->middleware(['auth'])
    ->name('finances.categories.destroy');

    Route::get('/finanzas/participantes', [FinanceController::class, 'participants'])
    ->middleware(['auth'])
    ->name('finances.participants');

Route::post('/finanzas/participantes', [FinanceController::class, 'storeParticipant'])
    ->middleware(['auth'])
    ->name('finances.participants.store');

Route::delete('/finanzas/participantes/{member}', [FinanceController::class, 'destroyParticipant'])
    ->middleware(['auth'])
    ->name('finances.participants.destroy');

    Route::put('/finanzas/ingresos/{income}', [FinanceController::class, 'updateIncome'])
    ->middleware(['auth'])
    ->name('finances.incomes.update');

Route::delete('/finanzas/ingresos/{income}', [FinanceController::class, 'destroyIncome'])
    ->middleware(['auth'])
    ->name('finances.incomes.destroy');

Route::put('/finanzas/gastos/{expense}', [FinanceController::class, 'updateExpense'])
    ->middleware(['auth'])
    ->name('finances.expenses.update');

Route::delete('/finanzas/gastos/{expense}', [FinanceController::class, 'destroyExpense'])
    ->middleware(['auth'])
    ->name('finances.expenses.destroy');

    Route::post('/finanzas/configuracion/categorias-ingreso', [FinanceController::class, 'storeIncomeCategory'])
    ->middleware(['auth'])
    ->name('finances.income-categories.store');

Route::delete('/finanzas/configuracion/categorias-ingreso/{category}', [FinanceController::class, 'destroyIncomeCategory'])
    ->middleware(['auth'])
    ->name('finances.income-categories.destroy');

    require __DIR__.'/auth.php';