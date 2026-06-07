<?php

namespace App\Providers;

use App\Support\SchoolConfig;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $school = SchoolConfig::all();

        config(['school' => $school]);

        View::share('school', $school);
    }
}
