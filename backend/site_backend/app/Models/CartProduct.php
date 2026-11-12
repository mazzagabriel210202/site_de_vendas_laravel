<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartProduct extends Pivot
{
    protected $table = 'cart_product';

    protected $fillable = [
        'cart_id',
        'product_id',
        'quantidade',
        'preco_unitario',
    ];

    /**
     * O item da pivot pertence a um Carrinho.
     */
    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    /**
     * O item da pivot pertence a um Produto.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}