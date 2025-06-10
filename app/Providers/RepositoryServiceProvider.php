<?php

namespace App\Providers;

use App\Repositories\Contracts\OrderRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Eloquent\OrderRepository;
use App\Repositories\Eloquent\UserRepository;
use Illuminate\Support\ServiceProvider;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Repositories\Contracts\FlowerTypeRepositoryInterface;
use App\Repositories\Contracts\ImportReceiptRepositoryInterface;
use App\Repositories\Eloquent\CategoryRepository;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Repositories\Eloquent\FlowerTypeRepository;
use App\Repositories\Eloquent\ImportReceiptRepository;
use App\Repositories\Eloquent\ProductRepository;
class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(CategoryRepositoryInterface::class, CategoryRepository::class);
        $this->app->bind(ProductRepositoryInterface::class, ProductRepository::class);
         $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        // $this->app->bind(DiscountRepositoryInterface::class, DiscountRepository::class);
         $this->app->bind(OrderRepositoryInterface::class, OrderRepository::class);
        // $this->app->bind(OrderDetailRepositoryInterface::class, OrderDetailRepository::class);
        // $this->app->bind(InvoiceRepositoryInterface::class, InvoiceRepository::class);
        $this->app->bind(FlowerTypeRepositoryInterface::class, FlowerTypeRepository::class);
        // $this->app->bind(FlowerRepositoryInterface::class, FlowerRepository::class);
        // $this->app->bind(RecipeRepositoryInterface::class, RecipeRepository::class);
         $this->app->bind(ImportReceiptRepositoryInterface::class, ImportReceiptRepository::class);
        // $this->app->bind(ImportReceiptDetailRepositoryInterface::class, ImportReceiptDetailRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
