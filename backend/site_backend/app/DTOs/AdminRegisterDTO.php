<?php

namespace App\DTOs;

use App\Http\Requests\AdminRegisterRequest;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class AdminRegisterDTO
{
    public function __construct(
        public readonly string $nome,
        public readonly string $email,
        public readonly string $senha,
        public readonly ?string $cpf = null
    ) {}

    public static function fromRequest(AdminRegisterRequest $request): self
    {
        return new self(
            nome: $request->validated('nome'),
            email: $request->validated('email'),
            senha: $request->validated('senha'),
            cpf: $request->validated('cpf')
        );
    }

    /**
     * Transforma o DTO em uma instância não persistida do Model Admin.
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