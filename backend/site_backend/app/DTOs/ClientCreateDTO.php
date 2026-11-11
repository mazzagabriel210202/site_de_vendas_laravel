<?php

namespace App\DTOs;

use App\Http\Requests\FormCreateRequest;

class ClientCreateDTO
{
    public function __construct(
        public readonly string $nome,
        public readonly string $email,
        public readonly string $senha,
        public readonly string $cpf
    ) {}

    public static function fromRequest(FormCreateRequest $request): self
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
        return [
            'nome'  => $this->nome,
            'email' => $this->email,
            'senha' => $this->senha,
            'cpf'   => $this->cpf,
        ];
    }
}