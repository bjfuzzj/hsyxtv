<?php
/*
 * @Author: your name
 * @Date: 2020-12-02 18:15:38
 * @LastEditTime: 2021-02-09 20:33:10
 * @LastEditors: Please set LastEditors
 * @Description: In User Settings Edit
 * @FilePath: /paota/ptweb/routes/admin.php
 */

use Illuminate\Http\Request;

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
Route::middleware(['admin'])->group(function () {
    Route::post('auth/login', 'AuthController@login');
    Route::get('/', 'BackIndexController@index')->name('home');
    Route::post('upload_image', 'UploadController@uploadImage');
    Route::get('exportexcel', 'BackOrderController@exportExcel');
    Route::get('exportorderexcel', 'BackOrderController@exportOrderExcel');
    Route::post('upload_excel', 'UploadController@uploadExcel');
    Route::get('cash_out/export_excel', 'BackCashOutController@exportExcel');
    Route::get('user/export', 'BackUserController@exportExcel');
    Route::get('user/teamexport', 'BackUserController@exportTeamExcel');
    Route::get('user/incomeexport', 'BackUserController@exportIncomeExcel');
});

Route::middleware(['admin', 'refresh.token', 'check.role:admin,vip'])->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('refresh', 'AuthController@refresh');
        Route::post('logout', 'AuthController@logout');
        Route::post('me', 'AuthController@me');
    });

    Route::prefix('operator')->group(function () {
        Route::get('index', 'OperatorController@index');
        Route::get('info', 'OperatorController@info');
        Route::post('add', 'OperatorController@add');
        Route::post('edit', 'OperatorController@edit');
        Route::post('do_start', 'OperatorController@doStart');
        Route::post('do_stop', 'OperatorController@doStop');
        Route::post('init_password', 'OperatorController@initPassword');
    });

    Route::prefix('product')->group(function () {
        Route::get('index', 'BackProductController@index');
        Route::get('edit_detail', 'BackProductController@getEditDetail');
        Route::post('save', 'BackProductController@save');
        Route::post('offline', 'BackProductController@OffLine');
        Route::post('online', 'BackProductController@Online');
        Route::post('get_img', 'BackProductController@getImage');
    });

    Route::prefix('order')->group(function () {
        Route::get('index', 'BackOrderController@index');
        Route::any('detail', 'BackOrderController@detail');
        Route::post('markSend', 'BackOrderController@markSend');
        Route::post('doSave', 'BackOrderController@doSave');
        Route::post('markNoSend', 'BackOrderController@markNoSend');
        Route::post('doDeleteAddress', 'BackOrderController@doDeleteAddress');
        Route::post('doRefundByAdmin', 'BackOrderController@doRefundByAdmin');
    });

    Route::prefix('user')->group(function () {
        Route::get('index', 'BackUserController@index');
        Route::get('detail', 'BackUserController@detail');
        Route::get('team', 'BackUserController@team');
        Route::post('doSave', 'BackUserController@doSave');
    });

    Route::prefix('cash_out')->group(function () {
        Route::get('index', 'BackCashOutController@index');
        Route::get('calc_person_taxes', 'BackCashOutController@calcPersonTaxes');
        Route::post('cash_out', 'BackCashOutController@cashOut');
        Route::post('do_refuse', 'BackCashOutController@refuseCashOut');
    });


    Route::prefix('banner')->group(function () {
        Route::get('index', 'BackBannerController@index');
        Route::post('save', 'BackBannerController@doSave');
        Route::get('detail', 'BackBannerController@detail');
        Route::post('offline', 'BackBannerController@OffLine');
        Route::post('online', 'BackBannerController@Online');
    });

      //优惠券
      Route::prefix('coupon')->group(function () {
        Route::get('index', 'BackCouponController@index');
        Route::post('save', 'BackCouponController@doSave');
        Route::get('edit', 'BackCouponController@edit');
        Route::post('detail', 'BackCouponController@detail');
        Route::get('log', 'BackCouponController@log');
        Route::post('change_status', 'BackCouponController@changeStatus');
    });

    //活动管理
    Route::prefix('activity')->group(function () {
        Route::get('index', 'BackGroupController@index');
        Route::post('save', 'BackGroupController@doSave');
        Route::get('edit', 'BackGroupController@edit');
        Route::post('change_status', 'BackGroupController@changeStatus');
    });
     //拼团管理
     Route::prefix('group')->group(function () {
        Route::post('list', 'BackGroupController@list');
        Route::post('team', 'BackGroupController@getTeam');
        Route::post('get_img', 'BackGroupController@getImage');
        Route::get('log', 'BackGroupController@log');
        
    });
});
