<?php

namespace App\Http\Controllers;

use App\Http\Requests\AdminCreateRequest;
use App\Http\Requests\AdminUpdateRequest;
use App\DTOs\AdminCreateDTO;
use App\DTOs\AdminUpdateDTO;
use App\Services\AdminService;
use App\Models\Admin;
use Illuminate\Http\JsonResponse;

class AdminController extends Controller
{
    protected AdminService $adminService;

    public function __construct(AdminService $adminService)
    {
        $this->adminService = $adminService;
    }

    /**
     * Lista todos os administradores.
     */
    public function index(): JsonResponse
    {
        $admins = $this->adminService->getAll();

        return response()->json([
            'success' => true,
            'data'    => $admins
        ], 200);
    }

    /**
     * Cria um novo administrador.
     */
    public function store(AdminCreateRequest $request): JsonResponse
    {
        $dto = AdminCreateDTO::fromRequest($request);
        $result = $this->adminService->create($dto);

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['message']
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => $result['message'],
            'data'    => $result['admin']
        ], 201);
    }

    /**
     * Exibe um administrador específico.
     */
    public function show(Admin $admin): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data'    => $admin
        ], 200);
    }

    /**
     * Atualiza um administrador existente.
     */
    public function update(AdminUpdateRequest $request, Admin $admin): JsonResponse
    {
        $dto = AdminUpdateDTO::fromRequest($request);
        $result = $this->adminService->update($admin, $dto);

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['message']
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => $result['message'],
            'data'    => $result['admin']
        ], 200);
    }

    /**
     * Remove um administrador.
     */
    public function destroy(Admin $admin): JsonResponse
    {
        $result = $this->adminService->delete($admin);

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['message']
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => $result['message']
        ], 200);
    }
}