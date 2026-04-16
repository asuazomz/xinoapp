<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('app');
});

Route::get('/login_1',[AuthController::class,'showLogin']);
Route::post('/login_1',[AuthController::class,'login']);

Route::get('/register_1',[AuthController::class,'showRegister']);
Route::post('/register_1',[AuthController::class,'register']);

Route::post('logout_1',[AuthController::class,'logout'])->middleware(['auth']);

Route::get('/dashboard',[DashboardController::class,'index'])->middleware('auth');

/*Route::get('/admin',[AdminController::class,'index'])->middleware(['auth','role:admin']);*/

Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->middleware(['auth', 'role:admin']);

Route::get('/user/dashboard', [DashboardController::class, 'userDashboard'])->middleware(['auth', 'role:usuario']);