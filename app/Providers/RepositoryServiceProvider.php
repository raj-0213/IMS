<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Interfaces\UserRepositoryInterface;
use App\Repositories\UserRepository;
use App\Interfaces\DraftProductsRepositoryInterface;
use App\Interfaces\CategoryRepositoryInterface;
use App\Repositories\DraftProductsRepository;
use App\Repositories\CategoryRepository;
use App\Interfaces\MoleculeRepositoryInterface;
use App\Repositories\MoleculeRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(CategoryRepositoryInterface::class, CategoryRepository::class);
        $this->app->bind(DraftProductsRepositoryInterface::class, DraftProductsRepository::class);
        $this->app->bind(MoleculeRepositoryInterface::class, MoleculeRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
