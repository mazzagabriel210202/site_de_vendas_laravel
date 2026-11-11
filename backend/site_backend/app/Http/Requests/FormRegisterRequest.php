<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FormRegisterRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nome'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:clients,email'], // Assumindo a tabela clients
            'senha' => ['required', 'string', 'min:6'],
            'cpf'   => ['required', 'string', 'size:11', 'unique:clients,cpf'], // Ajuste tamanho conforme formato (com ou sem pontos/traço)
        ];
    }

    /**
     * Mensagens de erro personalizadas.
     */
    public function messages(): array
    {
        return [
            'nome.required'  => 'O campo nome é obrigatório.',
            'email.required' => 'O campo e-mail é obrigatório.',
            'email.email'    => 'Informe um endereço de e-mail válido.',
            'email.unique'   => 'Este e-mail já está cadastrado.',
            'senha.required' => 'O campo senha é obrigatório.',
            'senha.min'      => 'A senha deve ter pelo menos :min caracteres.',
            'cpf.required'   => 'O campo CPF é obrigatório.',
            'cpf.size'       => 'O CPF deve conter exatamente 11 dígitos.',
            'cpf.unique'     => 'Este CPF já está cadastrado.',
        ];
    }
}