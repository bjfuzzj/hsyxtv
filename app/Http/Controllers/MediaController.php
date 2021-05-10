<?php

namespace App\Http\Controllers;

use App\Models\Media;
use Illuminate\Http\Request;

class MediaController extends Controller
{
    public function getDetail(Request $request)
    {
        $id = $request->input('id',0);
        if (empty($id)) {
            return $this->outErrorResultApi(500, '内部错误[1]');
        }
        $media = Media::find($id);
        if(!$media instanceof Media){
            return $this->outErrorResultApi(500, '内部错误[2]');
        }
        return $this->outSuccessResultApi($media);

    }
}
