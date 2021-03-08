<?php
/*
 * @Author: your name
 * @Date: 2020-12-02 18:06:10
 * @LastEditTime: 2020-12-03 21:42:08
 * @LastEditors: your name
 * @Description: In User Settings Edit
 * @FilePath: /paota/ptweb/app/Providers/AppServiceProvider.php
 */

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        \DB::listen(function ($query) {
            $sql      = str_replace(array('%', '?'), array('&*&', '"%s"'), $query->sql);
            $bindings = [];
            foreach ($query->bindings as $key => $value) {
                if (is_numeric($key)) {
                    $bindings[] = $value;
                } else {
                    $sql = str_replace(':' . $key, '"' . $value . '"', $sql);
                }
            }
            $sql = vsprintf($sql, $bindings);
            $sql = str_replace('&*&', '%', $sql);
            $sql = str_replace("\\", '', $sql);
           // \Log::warning("time:{$query->time} sql:{$sql}");
        });
    }
}
