<?php

namespace App\Providers;

use App\Repositories\Contracts\AutoImportReceiptInterface;
use App\Repositories\Contracts\DiscountRepositoryInterface;
use App\Repositories\Contracts\OrderRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Eloquent\DiscountRepository;
use App\Repositories\Eloquent\OrderRepository;
use App\Repositories\Eloquent\UserRepository;
use Illuminate\Support\ServiceProvider;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Repositories\Contracts\ColorRepositoryInterface;
use App\Repositories\Contracts\FlowerTypeRepositoryInterface;
use App\Repositories\Contracts\ImportReceiptRepositoryInterface;
use App\Repositories\Contracts\ProductReportRepositoryInterface;
use App\Repositories\Eloquent\CategoryRepository;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Repositories\Eloquent\AutoImportReceiptRepository;
use App\Repositories\Eloquent\ColorRepository;
use App\Repositories\Eloquent\FlowerTypeRepository;
use App\Repositories\Eloquent\ImportReceiptRepository;
use App\Repositories\Eloquent\ProductReportRepository;
use App\Repositories\Eloquent\ProductRepository;
use Cloudinary\Transformation\Argument\Color;

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
        // $this->app->bind(OrderDetailRepositoryInterface::class, OrderDetailRepository::class);
        // $this->app->bind(InvoiceRepositoryInterface::class, InvoiceRepository::class);
        $this->app->bind(FlowerTypeRepositoryInterface::class, FlowerTypeRepository::class);
        // $this->app->bind(FlowerRepositoryInterface::class, FlowerRepository::class);
        // $this->app->bind(RecipeRepositoryInterface::class, RecipeRepository::class);
        $this->app->bind(ImportReceiptRepositoryInterface::class, ImportReceiptRepository::class);
        $this->app->bind(AutoImportReceiptInterface::class, AutoImportReceiptRepository::class);
        // $this->app->bind(ImportReceiptDetailRepositoryInterface::class, ImportReceiptDetailRepository::class);
        $this->app->bind(ProductReportRepositoryInterface::class, ProductReportRepository::class);

        $this->app->bind(ColorRepositoryInterface::class, ColorRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
