<?php

namespace App\Providers;

use App\Models\WorkAssignment;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Schema;
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
        try {
            if (Schema::hasTable('work_assignments')) {
                WorkAssignment::completeExpiredAssignments();
            }
        } catch (QueryException) {
            // The database may not exist yet during first-time setup.
        }
    }
}
