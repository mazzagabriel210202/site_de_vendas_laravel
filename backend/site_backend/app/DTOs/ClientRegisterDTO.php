<?php

namespace App\DTOs;

use App\Http\Requests\FormRegisterRequest;

class ClientRegisterDTO
{
    public function __construct(
        public readonly string $nome,
        public readonly string $email,
        public readonly string $senha,
        public readonly string $cpf
    ) {}

    /**
     * Cria uma instância do DTO diretamente a partir do FormRequest já validado.
     */
    public static function fromRequest(FormRegisterRequest $request): self
    {
        return new self(
            nome: $request->validated('nome'),
            email: $request->validated('email'),
            senha: $request->validated('senha'),
            cpf: $request->validated('cpf')
        );
    }

    /**
     * Converte os dados do DTO em um array (útil para passar direto para o Eloquent::create).
     */
    public function toArray(): array
    {
        return [
            'nome' => $this->nome,
            'email' => $this->email,
            'senha' => $this->senha, // Lembre-se de criptografar (Hash::make) antes de salvar no banco
            'cpf' => $this->cpf,
        ];
    }
}