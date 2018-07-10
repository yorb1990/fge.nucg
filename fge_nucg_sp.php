<?php
namespace fge\nucg;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
class fge_nucg_sp extends ServiceProvider
{
    protected $namespace = 'fge\nucg\controller';
    public function map()
    {
        Route::prefix('fge-tok')
             ->namespace($this->namespace)
             ->group(__DIR__.'/routes/api.php');
    }
    public function boot()
    {        
        parent::boot();
    }
}