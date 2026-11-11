<?php

namespace App\DTOs;

use App\Http\Requests\FormUpdateRequest;

class ClientUpdateDTO
{
    public function __construct(
        public readonly ?string $nome = null,
        public readonly ?string $email = null,
        public readonly ?string $senha = null,
        public readonly ?string $cpf = null
    ) {}

    public static function fromRequest(FormUpdateRequest $request): self
    {
        return new self(
            nome: $request->validated('nome'),
            email: $request->validated('email'),
            senha: $request->validated('senha'),
            cpf: $request->validated('cpf')
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'nome'  => $this->nome,
            'email' => $this->email,
            'senha' => $this->senha,
            'cpf'   => $this->cpf,
        ], fn ($value) => !is_null($value));
    }
}