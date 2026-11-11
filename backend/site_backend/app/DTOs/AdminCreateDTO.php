<?php

namespace App\DTOs;

use App\Http\Requests\AdminCreateRequest;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class AdminCreateDTO
{
    public function __construct(
        public readonly string $nome,
        public readonly string $email,
        public readonly string $senha,
        public readonly ?string $cpf = null
    ) {}

    public static function fromRequest(AdminCreateRequest $request): self
    {
        return new self(
            nome: $request->validated('nome'),
            email: $request->validated('email'),
            senha: $request->validated('senha'),
            cpf: $request->validated('cpf')
        );
    }

    /**
     * Converte o DTO em uma instância do Model Admin pronta para salvar (com hash na senha).
     */
    public function toModel(): Admin
    {
        return new Admin([
            'nome'  => $this->nome,
            'email' => $this->email,
            'senha' => Hash::make($this->senha),
            'cpf'   => $this->cpf,
        ]);
    }
}