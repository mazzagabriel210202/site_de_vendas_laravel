<?php

namespace App\Providers;

use App\Models\Product;
use App\Models\Cart;
use App\Policies\ProductPolicy;
use App\Policies\CartPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Registro explícito das Policies da aplicação
        Gate::policy(Product::class, ProductPolicy::class);
        Gate::policy(Cart::class, CartPolicy::class);
    }
}