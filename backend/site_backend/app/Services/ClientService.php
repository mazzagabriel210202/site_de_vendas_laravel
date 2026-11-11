<?php

namespace App\Services;

use App\DTOs\ClientCreateDTO;
use App\DTOs\ClientUpdateDTO;
use App\Models\Client;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Exception;

class ClientService
{
    /**
     * Retorna todos os clientes cadastrados.
     */
    public function getAll(): Collection
    {
        try {
            return Client::all();
        } catch (Exception $e) {
            Log::error("Erro ao listar clientes: " . $e->getMessage());
            throw new Exception("Não foi possível carregar a lista de clientes no momento.");
        }
    }

    /**
     * Busca um cliente pelo ID.
     */
    public function findById(int $id): ?Client
    {
        try {
            return Client::find($id);
        } catch (Exception $e) {
            Log::error("Erro ao buscar cliente por ID ({$id}): " . $e->getMessage());
            throw new Exception("Erro ao buscar o cliente.");
        }
    }

    /**
     * Cria um novo cliente a partir do DTO de criação.
     */
    public function create(ClientCreateDTO $dto): Client
    {
        try {
            $data = $dto->toArray();

            // Faz o hash da senha antes de persistir no banco
            if (isset($data['senha'])) {
                $data['senha'] = Hash::make($data['senha']);
            }

            return Client::create($data);
        } catch (Exception $e) {
            Log::error("Erro ao criar cliente ({$dto->email}): " . $e->getMessage());
            throw new Exception("Não foi possível cadastrar o cliente. Verifique os dados informados.");
        }
    }

    /**
     * Atualiza um cliente existente a partir do DTO de atualização.
     */
    public function update(Client $client, ClientUpdateDTO $dto): Client
    {
    // Verifique se os dados estão sendo preenchidos e salvos corretamente
    $client->update([
        'nome'  => $dto->nome,
        'email' => $dto->email,
        'cpf'   => $dto->cpf,
        // adicione 'senha' se houver alteração de senha
    ]);

    return $client;
    }
    /**
     * Remove um cliente do banco de dados.
     */
    public function delete(Client $client): bool
    {
        try {
            return $client->delete();
        } catch (Exception $e) {
            Log::error("Erro ao deletar cliente ID ({$client->id}): " . $e->getMessage());
            throw new Exception("Não foi possível remover o cliente.");
        }
    }
}