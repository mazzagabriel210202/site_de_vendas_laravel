<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductCreateRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\DTOs\ProductCreateDTO;
use App\DTOs\ProductUpdateDTO;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ProductController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        private readonly ProductService $productService
    ) {}

    /**
     * Lista todos os produtos (público ou autenticado).
     */
    public function index(): JsonResponse
    {
        $products = $this->productService->getAllProducts();

        return response()->json([
            'success' => true,
            'data'    => $products
        ], 200);
    }

    /**
     * Cadastra um novo produto (Restrito a Admin via Policy).
     */
    public function store(ProductCreateRequest $request): JsonResponse
    {
        $this->authorize('create', Product::class);

        $dto = ProductCreateDTO::fromRequest($request);
        $result = $this->productService->createProduct($dto);

        if (!$result['success']) {
            return response()->json($result, 500);
        }

        return response()->json($result, 201);
    }

    /**
     * Exibe os detalhes de um produto específico.
     */
    public function show(int $id): JsonResponse
    {
        $product = $this->productService->getProductById($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Produto não encontrado.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $product
        ], 200);
    }

    /**
     * Atualiza um produto existente (Restrito a Admin via Policy).
     */
    public function update(ProductUpdateRequest $request, int $id): JsonResponse
    {
        $product = $this->productService->getProductById($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Produto não encontrado.'
            ], 404);
        }

        $this->authorize('update', $product);

        $dto = ProductUpdateDTO::fromRequest($request);
        $result = $this->productService->updateProduct($product, $dto);

        if (!$result['success']) {
            return response()->json($result, 500);
        }

        return response()->json($result, 200);
    }

    /**
     * Remove um produto do catálogo (Restrito a Admin via Policy).
     */
    public function destroy(int $id): JsonResponse
    {
        $product = $this->productService->getProductById($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Produto não encontrado.'
            ], 404);
        }

        $this->authorize('delete', $product);

        $result = $this->productService->deleteProduct($product);

        if (!$result['success']) {
            return response()->json($result, 500);
        }

        return response()->json($result, 200);
    }
}