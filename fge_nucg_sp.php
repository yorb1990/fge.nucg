<?php
namespace fge\nucg;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
class fge_nucg_sp extends ServiceProvider
{
    protected $namespace = 'fge\nucg\controller';
    
    public function map()
    {        
        // Route::prefix('fge-tok')
        //      ->namespace($this->namespace)
        //      ->group(__DIR__.'/routes/api.php');
    }
    public function boot()
    {
        
        // $this->loadMigrationsFrom(__DIR__.'/database/migrations');
        
        // Para ejecutar las semillas es con el comando php artisan db:seed
        // if ($this->app->runningInConsole()) {
        //     $this->registerSeedsFrom(__DIR__.'/database/seed');
        // }

        parent::boot();
    }

    protected function registerSeedsFrom($path)
    {
        foreach (glob("$path/*.php") as $filename)
        {
            include $filename;
            $classes = get_declared_classes();
            $class = end($classes);
            $command = \Request::server('argv', null);

            if (is_array($command)) {
                $command = implode(' ', $command);
                if ($command == "artisan db:seed") {
                    \Artisan::call('db:seed', ['--class' => $class]);
                }
            }
        }
    }
}