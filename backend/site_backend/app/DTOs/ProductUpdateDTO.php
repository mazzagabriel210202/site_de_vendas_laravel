<?php

namespace App\DTOs;

use App\Http\Requests\ProductUpdateRequest;

class ProductUpdateDTO
{
    public function __construct(
        public readonly ?string $nome = null,
        public readonly ?string $descricao = null,
        public readonly ?float $preco = null,
        public readonly ?int $estoque = null,
        public readonly ?bool $ativo = null
    ) {}

    public static function fromRequest(ProductUpdateRequest $request): self
    {
        return new self(
            nome: $request->validated('nome'),
            descricao: $request->validated('descricao'),
            preco: $request->has('preco') ? (float) $request->validated('preco') : null,
            estoque: $request->has('estoque') ? (int) $request->validated('estoque') : null,
            ativo: $request->has('ativo') ? (bool) $request->validated('ativo') : null
        );
    }

    public function toArray(): array
    {
        // Retorna apenas os campos que foram enviados (não nulos) para evitar sobrescrever dados com null acidentalmente
        return array_filter([
            'nome'      => $this->nome,
            'descricao' => $this->descricao,
            'preco'     => $this->preco,
            'estoque'   => $this->estoque,
            'ativo'     => $this->ativo,
        ], fn ($value) => !is_null($value));
    }
}