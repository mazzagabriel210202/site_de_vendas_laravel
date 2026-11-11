<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\ClientAuthController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Aqui você registra as rotas da API da sua aplicação.
|
*/

// --- Rotas Públicas de Autenticação de Cliente ---
Route::post('/register', [ClientAuthController::class, 'register']);
Route::post('/login', [ClientAuthController::class, 'login']);

// --- Rotas Públicas de Autenticação de Administrador ---
Route::post('/admin/register', [AdminAuthController::class, 'register']);
Route::post('/admin/login', [AdminAuthController::class, 'login']);

// --- Rotas Protegidas pelo Sanctum ---
Route::middleware('auth:sanctum')->group(function () {
    // Autenticação / Sessão atual do Cliente
    Route::post('/logout', [ClientAuthController::class, 'logout']);
    Route::get('/me', function (Request $request) {
        return response()->json($request->user());
    });

    // Autenticação / Sessão atual do Administrador
    Route::post('/admin/logout', [AdminAuthController::class, 'logout']);
    Route::get('/admin/me', function (Request $request) {
        return response()->json($request->user());
    });

    // --- Rotas de CRUD do Cliente (ClientController) ---
    Route::get('/clients', [ClientController::class, 'index']);          // Listar todos
    Route::post('/clients', [ClientController::class, 'store']);         // Criar novo
    Route::get('/clients/{client}', [ClientController::class, 'show']);    // Exibir um específico
    Route::put('/clients/{client}', [ClientController::class, 'update']);  // Atualizar
    Route::delete('/clients/{client}', [ClientController::class, 'destroy']); // Deletar

    // --- Rotas de CRUD do Administrador (AdminController) ---
    Route::get('/admins', [AdminController::class, 'index']);          // Listar todos
    Route::post('/admins', [AdminController::class, 'store']);         // Criar novo
    Route::get('/admins/{admin}', [AdminController::class, 'show']);    // Exibir um específico
    Route::put('/admins/{admin}', [AdminController::class, 'update']);  // Atualizar
    Route::delete('/admins/{admin}', [AdminController::class, 'destroy']); // Deletar
});