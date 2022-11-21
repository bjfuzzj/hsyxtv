<?php
/*
 * @Author: your name
 * @Date: 2020-12-02 18:06:10
 * @LastEditTime: 2022-11-21 19:02:08
 * @LastEditors: bjfuzzj
 * @Description: In User Settings Edit
 * @FilePath: /tv/routes/web.php
 */

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// 授权登录之后
Route::middleware(['web'])->group(function () {
    Route::get('/showCode', 'IndexController@showCode')->name('showCode');
    Route::get('/user/{tv_user_id}/liveindex', 'IndexController@liveIndex')->name('liveIndex');
    Route::get('/user/{tv_user_id}/livecompany', 'IndexController@liveCompany')->name('liveCompany');
});
