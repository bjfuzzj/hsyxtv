<?php
/*
 * @Author: your name
 * @Date: 2020-12-02 18:06:10
 * @LastEditTime: 2020-12-03 14:12:42
 * @LastEditors: Please set LastEditors
 * @Description: In User Settings Edit
 * @FilePath: /paota/ptweb/app/Http/Controllers/BackIndexController.php
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BackIndexController extends Controller
{
    public function index()
    {
        return view('index');
    }
}
