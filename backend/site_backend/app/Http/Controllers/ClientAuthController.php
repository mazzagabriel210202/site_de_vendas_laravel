<?php

namespace App\Http\Controllers;

use App\Http\Requests\FormRegisterRequest;
use App\Http\Requests\FormLoginRequest;
use App\DTOs\ClientRegisterDTO;
use App\DTOs\ClientLoginDTO;
use App\Services\ClientAuthService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ClientAuthController extends Controller
{
    protected ClientAuthService $authService;

    public function __construct(ClientAuthService $authService)
    {
        $this->authService = $authService;
    }

    public function register(FormRegisterRequest $request): JsonResponse
    {
        $dto = ClientRegisterDTO::fromRequest($request);
        $result = $this->authService->register($dto);

        return response()->json([
            'success' => true,
            'message' => 'Cliente cadastrado com sucesso!',
            'data'    => [
                'client' => $result['client'],
                'token'  => $result['token'],
            ]
        ], 201);
    }

    public function login(FormLoginRequest $request): JsonResponse
    {
        $dto = ClientLoginDTO::fromRequest($request);
        $result = $this->authService->login($dto);

        if (!$result) {
            return response()->json([
                'success' => false,
                'message' => 'Credenciais inválidas.'
            ], 401);
        }

        return response()->json([
            'success' => true,
            'message' => 'Login realizado com sucesso!',
            'data'    => [
                'client' => $result['client'],
                'token'  => $result['token'],
            ]
        ], 200);
    }

    public function logout(Request $request): JsonResponse
    {
        $client = $request->user();

        if ($client) {
            $this->authService->logout($client);
        }

        return response()->json([
            'success' => true,
            'message' => 'Logout realizado com sucesso!'
        ], 200);
    }
}