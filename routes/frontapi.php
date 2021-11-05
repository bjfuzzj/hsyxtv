<?php
/*
 * @Author: your name
 * @Date: 2020-12-02 18:06:10
 * @LastEditTime: 2021-11-05 11:32:14
 * @LastEditors: bjfuzzj
 * @Description: In User Settings Edit
 * @FilePath: /tv/routes/frontapi.php
 */

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware(['api'])->prefix('media')->group(function () {
        Route::post('getList', 'MediaController@getList');
        Route::get('getDetail', 'MediaController@getDetail');
        Route::get('getSubDetail', 'MediaController@getSubDetail');
        Route::get('getSubDetailByNum', 'MediaController@getSubDetailByNum');
        Route::get('getRecommend', 'MediaController@getRecommend');
        Route::post('getWatchList', 'MediaController@getWatchList');
});
