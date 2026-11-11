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
        'total',
        'status',
    ];

    /**
     * Relacionamento inverso: O histórico de compras pertence a um Client.
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}