<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products';

    protected $fillable = [
        'nome',
        'descricao',
        'preco',
    ];

    /**
     * Relacionamento: Um produto pode estar em vários carrinhos.
     */
    public function carts(): BelongsToMany
    {
        return $this->belongsToMany(Cart::class, 'cart_product')
                    ->withPivot('quantidade')
                    ->withTimestamps();
    }
}