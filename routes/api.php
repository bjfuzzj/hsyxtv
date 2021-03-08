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

    Route::post('/login', 'TvUserController@login');
    // 收集formId
    Route::get('collect_form_id', 'WxappController@collectFormId');

//    Route::prefix('wxapp')->group(function () {
//        Route::post('/login', 'WxappController@login');
//        Route::post('/auth', 'WxappController@doAuth');
//        Route::post('/decrypt_data', 'WxappController@decryptData');
//        Route::post('subscribe_message', 'WxappController@subscribeMessage');
//    });

    Route::prefix('product')->group(function () {
        Route::get('/index', 'ProductController@index');
        Route::get('/detail', 'ProductController@getDetail');
        Route::get('/share_poster', 'ProductController@getSharePoster');
    });

    Route::prefix('order')->group(function () {
        Route::post('/confirm', 'OrderController@confirm');
        Route::post('/submit_pay', 'OrderController@submitAndPay');
        Route::get('/list', 'OrderController@getOrderList');
        Route::get('/detail', 'OrderController@getOrderDetail');
        Route::get('/send_detail', 'OrderController@getOrderSendDetail');
        Route::get('/logistics_num', 'OrderController@getOrderLogisticsNum');
        Route::post('/add_logistics', 'OrderController@addOrderLogistics');
        Route::post('/del_logistics', 'OrderController@delOrderLogistics');
    });

    Route::prefix('pay')->group(function () {
        Route::post('/wx_pay', 'PayController@wxPay');
        Route::get('/success', 'PayController@paySuccess');
    });

    Route::prefix('user')->group(function () {
        Route::get('/index', 'UserController@index');
        Route::get('/teams', 'UserController@getTeamList');
        Route::get('/coupons', 'UserController@getCouponList');
        Route::post('/replenish_idcard', 'UserController@replenishIdCard');
        Route::post('/replenish_mobile', 'UserController@replenishMobile');
    });

    Route::prefix('income')->group(function () {
        Route::get('/my', 'IncomeController@myIncome');
        Route::get('/details', 'IncomeController@getDetailList');
        Route::get('/show_cashout', 'IncomeController@showCashOut');
        Route::post('/cashout', 'IncomeController@cashOut');
        Route::get('/cashout_records', 'IncomeController@cashOutRecords');
    });

    Route::prefix('coupon')->group(function () {
        Route::post('/receive', 'CouponController@receiveCoupon');
        Route::get('/list', 'CouponController@getList');
        Route::post('/send_detail', 'CouponController@sendDetail');
        Route::post('/change_coupon', 'CouponController@changeCoupon');
    });

});
