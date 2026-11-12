<?php

namespace App\Policies;

use App\Models\Cart;
use App\Models\Client;

class CartPolicy
{
    /**
     * Determina se o cliente pode ver o carrinho.
     */
    public function view(Client $client, Cart $cart): bool
    {
        return $client->id === $cart->client_id;
    }

    /**
     * Determina se o cliente pode gerenciar/adicionar itens ao carrinho.
     */
    public function update(Client $client, Cart $cart): bool
    {
        return $client->id === $cart->client_id;
    }

    /**
     * Determina se o cliente pode realizar a compra (buy) do carrinho.
     */
    public function buy(Client $client, Cart $cart): bool
    {
        return $client->id === $cart->client_id;
    }
}