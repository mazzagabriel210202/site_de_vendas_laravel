<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Cart extends Model
{
    use HasFactory;

    protected $table = 'carts';

    protected $fillable = [
        'client_id',
    ];

    /**
     * O carrinho pertence a um Client.
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Relacionamento: Um carrinho possui muitos produtos (1 para N através da pivot).
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'cart_product')
                    ->withPivot(['quantidade', 'preco_unitario'])
                    ->withTimestamps();
    }
}