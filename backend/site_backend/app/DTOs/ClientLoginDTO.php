<?php

namespace App\DTOs;

use App\Http\Requests\FormLoginRequest;

class ClientLoginDTO
{
    public function __construct(
        public readonly string $email,
        public readonly string $senha
    ) {}

    /**
     * Cria uma instância do DTO a partir do FormRequest de login validado.
     */
    public static function fromRequest(FormLoginRequest $request): self
    {
        return new self(
            email: $request->validated('email'),
            senha: $request->validated('senha')
        );
    }

    /**
     * Retorna os dados em formato de array (útil para o Auth::attempt).
     */
    public function toArray(): array
    {
        return [
            'email' => $this->email,
            'password' => $this->senha, // Mapeado para 'password' caso o Auth do Laravel espere esse índice, ou ajuste para 'senha' dependendo da sua implementação
        ];
    }
}