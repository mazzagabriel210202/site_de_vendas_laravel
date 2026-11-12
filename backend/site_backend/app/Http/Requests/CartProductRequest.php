<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CartProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // A autorização de acesso ao carrinho pode ser tratada via Policy ou Middleware
    }

    public function rules(): array
    {
        return [
            'product_id' => ['required', 'exists:products,id'],
            'quantidade' => ['required', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'product_id.required' => 'O ID do produto é obrigatório.',
            'product_id.exists'   => 'O produto informado não existe no catálogo.',
            'quantidade.required' => 'A quantidade é obrigatória.',
            'quantidade.integer'  => 'A quantidade deve ser um número inteiro.',
            'quantidade.min'      => 'A quantidade mínima deve ser pelo menos 1.',
        ];
    }
}