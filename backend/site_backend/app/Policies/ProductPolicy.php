<?php

namespace App\Policies;

use App\Models\Admin;
use App\Models\Product;
use Illuminate\Foundation\Auth\User;

class ProductPolicy
{
    /**
     * Determina se o usuário pode criar produtos.
     * Apenas administradores autenticados podem criar.
     */
    public function create(User $user): bool
    {
        return $user instanceof Admin;
    }

    /**
     * Determina se o usuário pode atualizar o produto.
     * Apenas administradores autenticados podem atualizar.
     */
    public function update(User $user, Product $product): bool
    {
        return $user instanceof Admin;
    }

    /**
     * Determina se o usuário pode excluir o produto.
     * Apenas administradores autenticados podem excluir.
     */
    public function delete(User $user, Product $product): bool
    {
        return $user instanceof Admin;
    }
}