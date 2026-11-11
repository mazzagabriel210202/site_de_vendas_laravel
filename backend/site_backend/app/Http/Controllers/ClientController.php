<?php

namespace App\Http\Controllers;

use App\Http\Requests\FormCreateRequest;
use App\Http\Requests\FormUpdateRequest;
use App\DTOs\ClientCreateDTO;
use App\DTOs\ClientUpdateDTO;
use App\Services\ClientService;
use App\Models\Client;
use Illuminate\Http\JsonResponse;
use Exception;

class ClientController extends Controller
{
    public function __construct(
        protected ClientService $clientService
    ) {}

    /**
     * Lista todos os clientes.
     */
    public function index(): JsonResponse
    {
        try {
            $clients = $this->clientService->getAll();
            return response()->json([
                'success' => true,
                'data' => $clients
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cadastra um novo cliente.
     */
    public function store(FormCreateRequest $request): JsonResponse
    {
        try {
            $dto = ClientCreateDTO::fromRequest($request);
            $client = $this->clientService->create($dto);

            return response()->json([
                'success' => true,
                'message' => 'Cliente cadastrado com sucesso!',
                'data' => $client
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Exibe os detalhes de um cliente específico.
     */
    public function show(Client $client): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $client
        ], 200);
    }

    /**
     * Atualiza os dados de um cliente existente.
     */
    public function update(FormUpdateRequest $request, Client $client): JsonResponse
    {
        try {
            $dto = ClientUpdateDTO::fromRequest($request);
            $updatedClient = $this->clientService->update($client, $dto);

            return response()->json([
                'success' => true,
                'message' => 'Cliente atualizado com sucesso!',
                'data' => $updatedClient
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Remove um cliente do sistema.
     */
    public function destroy(Client $client): JsonResponse
    {
        try {
            $this->clientService->delete($client);

            return response()->json([
                'success' => true,
                'message' => 'Cliente removido com sucesso!'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}