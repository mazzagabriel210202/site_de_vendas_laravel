<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; // <-- Importante

class Admin extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable; // <-- Adicionado HasApiTokens

    protected $table = 'admins';

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

    protected $casts = [
        'email_verified_at' => 'datetime',
        'senha' => 'hashed',
    ];

    /**
     * Informa ao Laravel qual campo é utilizado como senha para autenticação.
     */
    public function getAuthPasswordName()
    {
        return 'senha';
    }
}