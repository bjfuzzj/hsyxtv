<?php
/*
 * @Author: your name
 * @Date: 2020-12-02 18:06:10
 * @LastEditTime: 2021-01-16 23:12:33
 * @LastEditors: Please set LastEditors
 * @Description: In User Settings Edit
 * @FilePath: /paota/ptweb/routes/api.php
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

//微信支付回调
//Route::any('wx_pay_back', 'PayController@wxPayBack');
//Route::any('test', 'ProductController@test');
//Route::any('/wx_refund_back', 'PayController@wxRefundBack');


Route::middleware(['api'])->group(function () {

    Route::get('/login', 'TvUserController@login');
    Route::get('/upgrade', 'TvUserController@upgrade');
    Route::get('/cache', 'TvUserController@cache');

    Route::get('/time_task', 'TvUserController@timeTask');
    // 增加观看记录
    Route::get('/add_watch', 'TvUserController@addWatchHistory');
    Route::get('/del_watch', 'TvUserController@delWatchHistory');
    Route::get('/get_watch', 'TvUserController@getWatchHistory');

//    Route::prefix('wxapp')->group(function () {
//        Route::post('/login', 'WxappController@login');
//        Route::post('/auth', 'WxappController@doAuth');
//        Route::post('/decrypt_data', 'WxappController@decryptData');
//        Route::post('subscribe_message', 'WxappController@subscribeMessage');
//    });


//    Route::prefix('pay')->group(function () {
//        Route::post('/wx_pay', 'PayController@wxPay');
//        Route::get('/success', 'PayController@paySuccess');
//    });


});
