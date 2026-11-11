<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule; // <-- Importe a classe Rule

class FormUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Pega o ID do cliente que está vindo na rota (ex: /api/clients/{client})
        $clientId = $this->route('client');

        return [
            'nome'  => ['sometimes', 'string', 'max:255'],
            
            // Ignora o e-mail do próprio cliente atual durante a validação unique
            'email' => [
                'sometimes', 
                'email', 
                Rule::unique('clients', 'email')->ignore($clientId)
            ],
            
            // Ignora o CPF do próprio cliente atual durante a validação unique
            'cpf'   => [
                'sometimes', 
                'string', 
                'size:11', 
                Rule::unique('clients', 'cpf')->ignore($clientId)
            ],
            
            'senha' => ['sometimes', 'string', 'min:6'],
        ];
    }
}