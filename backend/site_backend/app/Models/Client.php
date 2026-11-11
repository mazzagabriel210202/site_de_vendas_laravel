<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Client extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'clients';

    protected $fillable = [
        'nome',
        'email',
        'senha',
        'cpf',
    ];

    protected $hidden = [
        'senha',
        'remember_token',
    ];

    /**
     * Informa ao Laravel que o campo de senha da tabela se chama 'senha'.
     */
    public function getAuthPassword()
    {
        return $this->senha;
    }
}