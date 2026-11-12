<?php

namespace App\DTOs;

use App\Http\Requests\ProductCreateRequest;

class ProductCreateDTO
{
    public function __construct(
        public readonly string $nome,
        public readonly ?string $descricao,
        public readonly float $preco,
        public readonly int $estoque,
        public readonly bool $ativo = true
    ) {}

    public static function fromRequest(ProductCreateRequest $request): self
    {
        return new self(
            nome: $request->validated('nome'),
            descricao: $request->validated('descricao'),
            preco: (float) $request->validated('preco'),
            estoque: (int) $request->validated('estoque'),
            ativo: (bool) $request->validated('ativo', true)
        );
    }

    public function toArray(): array
    {
        return [
            'nome'      => $this->nome,
            'descricao' => $this->descricao,
            'preco'     => $this->preco,
            'estoque'   => $this->estoque,
            'ativo'     => $this->ativo,
        ];
    }
}