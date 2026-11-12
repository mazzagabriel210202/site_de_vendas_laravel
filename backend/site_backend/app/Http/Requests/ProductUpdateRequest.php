<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nome'        => ['sometimes', 'required', 'string', 'max:255'],
            'descricao'   => ['nullable', 'string'],
            'preco'       => ['sometimes', 'required', 'numeric', 'min:0'],
            'estoque'     => ['sometimes', 'required', 'integer', 'min:0'],
            'ativo'       => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'nome.required'   => 'O nome do produto é obrigatório.',
            'nome.max'        => 'O nome não pode ter mais de 255 caracteres.',
            'preco.required'  => 'O preço do produto é obrigatório.',
            'preco.numeric'   => 'O preço deve ser um valor numérico válido.',
            'preco.min'       => 'O preço não pode ser negativo.',
            'estoque.required' => 'A quantidade em estoque é obrigatória.',
            'estoque.integer' => 'O estoque deve ser um número inteiro.',
            'estoque.min'     => 'O estoque não pode ser negativo.',
        ];
    }
}