<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shopping_histories', function (Blueprint $table) {
            $table->id();
            // Chave estrangeira ligando ao cliente (1 para 1)
            $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');
            $table->decimal('total', 10, 2)->default(0.00); // Valor total das compras registradas
            $table->string('status')->default('concluido'); // Ex: pendente, concluido, cancelado
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shopping_histories');
    }
};