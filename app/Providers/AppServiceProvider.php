<?php

namespace App\Providers;

use App\Interfaces\AuthServiceInterface;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Repositories\EloquentProductRepository;
use App\Services\AuthService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ProductRepositoryInterface::class, EloquentProductRepository::class);
        $this->app->bind(AuthServiceInterface::class, AuthService::class);

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
