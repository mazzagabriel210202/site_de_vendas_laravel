<?php

namespace App\Services;

use App\DTOs\ClientRegisterDTO;
use App\DTOs\ClientLoginDTO;
use App\Models\Client;
use Illuminate\Support\Facades\Hash;

class ClientAuthService
{
    public function register(ClientRegisterDTO $dto): array
    {
        $client = Client::create([
            'nome'  => $dto->nome,
            'email' => $dto->email,
            'senha' => Hash::make($dto->senha),
            'cpf'   => $dto->cpf,
        ]);

        $token = $client->createToken('auth_token')->plainTextToken;

        return [
            'client' => $client,
            'token'  => $token,
        ];
    }

    public function login(ClientLoginDTO $dto): ?array
    {
        $client = Client::where('email', $dto->email)->first();

        if (!$client || !Hash::check($dto->senha, $client->senha)) {
            return null;
        }

        $token = $client->createToken('auth_token')->plainTextToken;

        return [
            'client' => $client,
            'token'  => $token,
        ];
    }

    public function logout(Client $client): void
    {
        // Remove os tokens atuais do cliente de forma segura utilizando a relação do Sanctum
        $client->tokens()->delete();
    }
}