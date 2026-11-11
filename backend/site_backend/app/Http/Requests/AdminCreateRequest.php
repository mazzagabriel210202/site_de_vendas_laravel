<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AdminCreateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nome'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('admins', 'email')],
            'senha' => ['required', 'string', 'min:6'],
            'cpf'   => ['nullable', 'string', 'size:11', Rule::unique('admins', 'cpf')],
        ];
    }

    public function messages(): array
    {
        return [
            'nome.required'  => 'O campo nome é obrigatório.',
            'nome.max'       => 'O nome não pode ter mais de 255 caracteres.',
            'email.required' => 'O campo e-mail é obrigatório.',
            'email.email'    => 'Informe um endereço de e-mail válido.',
            'email.unique'   => 'Este e-mail já está cadastrado no sistema.',
            'senha.required' => 'O campo senha é obrigatório.',
            'senha.min'      => 'A senha deve conter pelo menos :min caracteres.',
            'cpf.unique'     => 'Este CPF já está cadastrado no sistema.',
            'cpf.size'       => 'O CPF deve conter exatamente 11 dígitos.',
        ];
    }
}