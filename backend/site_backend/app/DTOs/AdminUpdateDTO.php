<?php

namespace App\DTOs;

use App\Http\Requests\AdminUpdateRequest;
use Illuminate\Support\Facades\Hash;

class AdminUpdateDTO
{
    public function __construct(
        public readonly ?string $nome = null,
        public readonly ?string $email = null,
        public readonly ?string $senha = null,
        public readonly ?string $cpf = null
    ) {}

    public static function fromRequest(AdminUpdateRequest $request): self
    {
        return new self(
            nome: $request->validated('nome'),
            email: $request->validated('email'),
            senha: $request->validated('senha'),
            cpf: $request->validated('cpf')
        );
    }

    /**
     * Retorna um array contendo apenas os campos preenchidos, tratando a senha com hash se informada.
     */
    public function toArray(): array
    {
        $data = [];

        if ($this->nome !== null) {
            $data['nome'] = $this->nome;
        }

        if ($this->email !== null) {
            $data['email'] = $this->email;
        }

        if ($this->senha !== null && trim($this->senha) !== '') {
            $data['senha'] = Hash::make($this->senha);
        }

        if ($this->cpf !== null) {
            $data['cpf'] = $this->cpf;
        }

        return $data;
    }
}