<?php
/*
 * @Author:bjfuzzj
 * @Date: 2019-11-28 11:20:50
 * @LastEditTime: 2021-02-09 20:34:13
 * @LastEditors: Please set LastEditors
 * @Description: In User Settings Edit
 */

namespace App\Helper;

/**
 * 缓存KEY
 */
class CacheKey
{
    const CK_WXAPP_PRODUCT_POSTER      = 'yiqitonggan:wxapp_product_poster:';       //产品海报链接缓存
    const CK_DISTRIBUTOR_CASH_OUT_LOCK = 'yiqitonggan:distributor_cash_out_lock:';  //分销员提现防止并发锁
    const CK_ABOUT_TO_EXPIRE_REMIND    = 'yiqitonggan:about_to_expire_remind:';     //订单即将过期提醒
    const CK_ZHIBO_ROOM                = 'yiqitonggan:zhibo_room:';                 //直播房间列表
    const CK_GROUP_URL = 'paota:goup_url:';//小程序团购商品二维码
    const CK_PRODUCT_URL = 'paota:product_url:';//小程序商品二维码
}