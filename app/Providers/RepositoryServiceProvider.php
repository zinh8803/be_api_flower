<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Contracts\AutoImportReceiptInterface;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Repositories\Contracts\ColorRepositoryInterface;
use App\Repositories\Contracts\DiscountRepositoryInterface;
use App\Repositories\Contracts\FlowerLogRepositoryInterface;
use App\Repositories\Contracts\FlowerRepositoryInterface;
use App\Repositories\Contracts\FlowerTypeRepositoryInterface;
use App\Repositories\Contracts\ImportReceiptDetailRepositoryInterface;
use App\Repositories\Contracts\ImportReceiptRepositoryInterface;
use App\Repositories\Contracts\InvoiceRepositoryInterface;
use App\Repositories\Contracts\OrderDetailRepositoryInterface;
use App\Repositories\Contracts\OrderRepositoryInterface;
use App\Repositories\Contracts\ProductReportRepositoryInterface;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Repositories\Contracts\ProductSizeRepositoryInterface;
use App\Repositories\Contracts\RecipeRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;

use App\Repositories\Eloquent\AutoImportReceiptRepository;
use App\Repositories\Eloquent\CategoryRepository;
use App\Repositories\Eloquent\ColorRepository;
use App\Repositories\Eloquent\DiscountRepository;
use App\Repositories\Eloquent\FlowerLogRepository;
use App\Repositories\Eloquent\FlowerRepository;
use App\Repositories\Eloquent\FlowerTypeRepository;
use App\Repositories\Eloquent\ImportReceiptDetailRepository;
use App\Repositories\Eloquent\ImportReceiptRepository;
use App\Repositories\Eloquent\InvoiceRepository;
use App\Repositories\Eloquent\OrderDetailRepository;
use App\Repositories\Eloquent\OrderRepository;
use App\Repositories\Eloquent\ProductReportRepository;
use App\Repositories\Eloquent\ProductRepository;
use App\Repositories\Eloquent\ProductSizeRepository;
use App\Repositories\Eloquent\RecipeRepository;
use App\Repositories\Eloquent\UserRepository;

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
        $this->app->bind(DiscountRepositoryInterface::class, DiscountRepository::class);
        $this->app->bind(OrderRepositoryInterface::class, OrderRepository::class);
        $this->app->bind(OrderDetailRepositoryInterface::class, OrderDetailRepository::class);
        $this->app->bind(InvoiceRepositoryInterface::class, InvoiceRepository::class);
        $this->app->bind(FlowerTypeRepositoryInterface::class, FlowerTypeRepository::class);
        $this->app->bind(FlowerRepositoryInterface::class, FlowerRepository::class);
        $this->app->bind(FlowerLogRepositoryInterface::class, FlowerLogRepository::class);
        $this->app->bind(RecipeRepositoryInterface::class, RecipeRepository::class);
        $this->app->bind(ImportReceiptRepositoryInterface::class, ImportReceiptRepository::class);
        $this->app->bind(AutoImportReceiptInterface::class, AutoImportReceiptRepository::class);
        $this->app->bind(ImportReceiptDetailRepositoryInterface::class, ImportReceiptDetailRepository::class);
        $this->app->bind(ProductReportRepositoryInterface::class, ProductReportRepository::class);
        $this->app->bind(ColorRepositoryInterface::class, ColorRepository::class);
        $this->app->bind(ProductSizeRepositoryInterface::class, ProductSizeRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
