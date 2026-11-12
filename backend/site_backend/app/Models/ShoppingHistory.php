<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShoppingHistory extends Model
{
    use HasFactory;

    protected $table = 'shopping_histories';

    protected $fillable = [
        'client_id',
        'product_id',
        'quantidade',
        'preco_unitario',
        'subtotal',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}