<?php

namespace App\Http\Controllers;

use App\Http\Requests\AdminRegisterRequest;
use App\Http\Requests\AdminLoginRequest;
use App\DTOs\AdminRegisterDTO;
use App\DTOs\AdminLoginDTO;
use App\Services\AdminAuthService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AdminAuthController extends Controller
{
    protected AdminAuthService $authService;

    public function __construct(AdminAuthService $authService)
    {
        $this->authService = $authService;
    }

    public function register(AdminRegisterRequest $request): JsonResponse
    {
        $dto = AdminRegisterDTO::fromRequest($request);
        $result = $this->authService->register($dto);

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['message']
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => $result['message'],
            'data'    => [
                'admin' => $result['admin'],
                'token' => $result['token'],
            ]
        ], 201);
    }

    public function login(AdminLoginRequest $request): JsonResponse
    {
        $dto = AdminLoginDTO::fromRequest($request);
        $result = $this->authService->login($dto);

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['message']
            ], 401);
        }

        return response()->json([
            'success' => true,
            'message' => $result['message'],
            'data'    => [
                'admin' => $result['admin'],
                'token' => $result['token'],
            ]
        ], 200);
    }

    public function logout(Request $request): JsonResponse
    {
        $admin = $request->user();

        if ($admin) {
            $this->authService->logout($admin);
        }

        return response()->json([
            'success' => true,
            'message' => 'Logout realizado com sucesso!'
        ], 200);
    }
}