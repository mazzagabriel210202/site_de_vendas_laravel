<?php

namespace App\Http\Controllers;

use App\Http\Requests\CartProductRequest;
use App\DTOs\CartProductDTO;
use App\Services\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CartController extends Controller
{
    public function __construct(
        private readonly CartService $cartService
    ) {}

    /**
     * Exibe o carrinho atual do cliente autenticado.
     */
    public function index(Request $request): JsonResponse
    {
        $client = $request->user();
        $cart = $this->cartService->getOrCreateCart($client->id);

        // Autoriza usando o Gate explicitamente
        Gate::authorize('view', $cart);

        $result = $this->cartService->getCartDetails($client->id);

        return response()->json($result, 200);
    }

    /**
     * Adiciona ou atualiza um produto no carrinho.
     */
    public function store(CartProductRequest $request): JsonResponse
    {
        $client = $request->user();
        $cart = $this->cartService->getOrCreateCart($client->id);

        Gate::authorize('update', $cart);

        $dto = CartProductDTO::fromRequest($request);
        $result = $this->cartService->addProduct($client->id, $dto);

        if (!$result['success']) {
            return response()->json($result, 400);
        }

        return response()->json($result, 200);
    }

    /**
     * Remove um produto específico do carrinho.
     */
    public function destroy(Request $request, int $productId): JsonResponse
    {
        $client = $request->user();
        $cart = $this->cartService->getOrCreateCart($client->id);

        Gate::authorize('update', $cart);

        $result = $this->cartService->removeProduct($client->id, $productId);

        if (!$result['success']) {
            return response()->json($result, 404);
        }

        return response()->json($result, 200);
    }

    /**
     * Esvazia completamente o carrinho.
     */
    public function clear(Request $request): JsonResponse
    {
        $client = $request->user();
        $cart = $this->cartService->getOrCreateCart($client->id);

        Gate::authorize('update', $cart);

        $result = $this->cartService->clearCart($client->id);

        return response()->json($result, 200);
    }

    /**
     * Realiza a compra dos itens do carrinho (Checkout), enviando para o histórico.
     */
    public function buy(Request $request): JsonResponse
    {
        $client = $request->user();
        $cart = $this->cartService->getOrCreateCart($client->id);

        Gate::authorize('buy', $cart);

        $result = $this->cartService->buy($client->id);

        if (!$result['success']) {
            return response()->json($result, 400);
        }

        return response()->json($result, 200);
    }
}