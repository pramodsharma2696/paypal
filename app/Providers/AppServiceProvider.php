<?php

namespace App\Providers;

use App\Repositories\TestRepository;
use Illuminate\Support\ServiceProvider;
use App\Repositories\TestRepository\Interfaces\TestRepositoryInterface;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
       
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
