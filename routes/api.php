<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\VeiculoController;

Route::get('/users', [AuthController::class, 'users']);

Route::post('/register', [AuthController::class, 'register']);

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(
    function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/veiculos', [VeiculoController::class, 'store']);
        Route::get('/veiculo/{id}', [VeiculoController::class, 'show']);
        Route::get('/veiculos/status{status}', [VeiculoController::class, 'status']);
    }
);