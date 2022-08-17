<?php
/*
 * @Author: your name
 * @Date: 2020-12-02 18:06:10
 * @LastEditTime: 2022-08-17 11:57:12
 * @LastEditors: bjfuzzj
 * @Description: In User Settings Edit
 * @FilePath: /tv/app/Providers/RouteServiceProvider.php
 */

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        //

        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapApiRoutes();
        $this->mapAdminRoutes();
        //$this->mapWebRoutes();
        $this->mapFrontApiRoutes();
        
    }

    // /**
    //  * Define the "web" routes for the application.
    //  *
    //  * These routes all receive session state, CSRF protection, etc.
    //  *
    //  * @return void
    //  */
    protected function mapWebRoutes()
    {
        Route::middleware('web')
             ->domain(config('app.h5_domain'))
             ->namespace($this->namespace)
             ->group(base_path('routes/web.php'));
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
//        Route::prefix('api')
        Route::middleware('api')
            ->domain(config('app.api_domain'))
            ->middleware('api')
            ->namespace($this->namespace)
            ->group(base_path('routes/api.php'));
    }

    protected function mapAdminRoutes()
    {
        Route::middleware('admin')
            ->domain(config('app.admin_domain'))
            ->namespace($this->namespace)
            ->group(base_path('routes/admin.php'));
    }


    protected function mapFrontApiRoutes()
    {
        Route::middleware('api')
            ->domain(config('app.frontapi_domain'))
            ->middleware('api')
            ->namespace($this->namespace)
            ->group(base_path('routes/frontapi.php'));
    }
}
