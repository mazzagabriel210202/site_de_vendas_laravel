<?php

namespace App\DTOs;

use App\Http\Requests\CartProductRequest;

class CartProductDTO
{
    public function __construct(
        public readonly int $productId,
        public readonly int $quantidade
    ) {}

    /**
     * Cria o DTO diretamente a partir do FormRequest validado.
     */
    public static function fromRequest(CartProductRequest $request): self
    {
        return new self(
            productId: (int) $request->input('product_id'),
            quantidade: (int) $request->input('quantidade')
        );
    }

    /**
     * Converte o DTO para array (útil para operações de banco/mass assignment).
     */
    public function toArray(): array
    {
        return [
            'product_id' => $this->productId,
            'quantidade' => $this->quantidade,
        ];
    }
}