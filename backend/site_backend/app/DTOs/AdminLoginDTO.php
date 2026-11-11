<?php

namespace App\DTOs;

use App\Http\Requests\AdminLoginRequest;

class AdminLoginDTO
{
    public function __construct(
        public readonly string $email,
        public readonly string $senha
    ) {}

    public static function fromRequest(AdminLoginRequest $request): self
    {
        return new self(
            email: $request->validated('email'),
            senha: $request->validated('senha')
        );
    }

    /**
     * Retorna as credenciais em formato de array para verificação ou login.
     */
    public function toArray(): array
    {
        return [
            'email' => $this->email,
            'senha' => $this->senha,
        ];
    }
}