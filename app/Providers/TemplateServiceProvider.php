<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

class TemplateServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        Blade::directive('date', function ($date) {
            return "<?php echo commonDate($date); ?>";
        });

        Blade::if('ican', function ($permission, User $user = NULL) {
            $user || $user = auth()->user();
            return $user->ican($permission);
        });
    }
}
