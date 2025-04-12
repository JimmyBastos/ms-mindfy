<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Relations\Relation;
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
        Relation::enforceMorphMap([
            'category' => 'App\Models\Category',
            'content'  => 'App\Models\Content',
            'creator'  => 'App\Models\Creator',
            'media'    => 'App\Models\Media',
            'user'     => 'App\Models\User',
        ]);
    }
}
