<?php
/*
 * @Author: your name
 * @Date: 2020-12-02 18:06:10
 * @LastEditTime: 2020-12-03 14:22:12
 * @LastEditors: your name
 * @Description: In User Settings Edit
 * @FilePath: /paota/ptweb/routes/web.php
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
    //Route::get('/', 'IndexController@index')->name('home');
});
