<?php
/*
 * @Author: your name
 * @Date: 2020-12-02 18:06:10
 * @LastEditTime: 2020-12-03 17:00:44
 * @LastEditors: Please set LastEditors
 * @Description: In User Settings Edit
 * @FilePath: /paota/ptweb/config/oss.php
 */

return [
    'accessKeyId'     => env('OSS_ACCESSKEYID', ''),
    'accessKeySecret' => env('OSS_ACCESSKEYSECRET', ''),
    'buckets'         => [
        'imgtravel' => [
            'endpoint'         => 'http://oss-cn-beijing.aliyuncs.com',
            'internalEndpoint' => 'http://oss-cn-beijing-internal.aliyuncs.com',
            'cdn'              => 'https://img.66gou8.com'
        ],
    ],

];