<?php

namespace App\Services;

use App\DTOs\ProductCreateDTO;
use App\DTOs\ProductUpdateDTO;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Exception;

class ProductService
{
    /**
     * Retorna todos os produtos cadastrados.
     */
    public function getAllProducts(): Collection
    {
        return Product::all();
    }

    /**
     * Cria um novo produto no banco de dados.
     */
    public function createProduct(ProductCreateDTO $dto): array
    {
        try {
            DB::beginTransaction();

            $product = Product::create($dto->toArray());

            DB::commit();

            return [
                'success' => true,
                'message' => 'Produto criado com sucesso!',
                'data'    => $product,
            ];
        } catch (Exception $e) {
            DB::rollBack();

            return [
                'success' => false,
                'message' => 'Erro ao criar produto: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Encontra um produto específico pelo ID.
     */
    public function getProductById(int $id): ?Product
    {
        return Product::find($id);
    }

    /**
     * Atualiza os dados de um produto existente.
     */
    public function updateProduct(Product $product, ProductUpdateDTO $dto): array
    {
        try {
            DB::beginTransaction();

            // Atualiza apenas os campos enviados no DTO (filtrados pelo array_filter)
            $product->update($dto->toArray());

            DB::commit();

            return [
                'success' => true,
                'message' => 'Produto atualizado com sucesso!',
                'data'    => $product->fresh(),
            ];
        } catch (Exception $e) {
            DB::rollBack();

            return [
                'success' => false,
                'message' => 'Erro ao atualizar produto: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Exclui um produto do catálogo.
     */
    public function deleteProduct(Product $product): array
    {
        try {
            DB::beginTransaction();

            $product->delete();

            DB::commit();

            return [
                'success' => true,
                'message' => 'Produto excluído com sucesso!',
            ];
        } catch (Exception $e) {
            DB::rollBack();

            return [
                'success' => false,
                'message' => 'Erro ao excluir produto: ' . $e->getMessage(),
            ];
        }
    }
}