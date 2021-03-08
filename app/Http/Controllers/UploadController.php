<?php

namespace App\Http\Controllers;

use App\Helper\AliyunOSS;
use App\Helper\FFMpegTool;
use App\Helper\Oss;
use App\Models\MultipartUpload;
use App\Models\MultipartUploadRecord;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use App\Imports\OrderImport;
use Maatwebsite\Excel\Facades\Excel;

class UploadController extends Controller
{
    /**
     * @api {post} /upload_image 上传图片
     * @apiName uploadImage
     * @apiGroup Upload
     *
     * @apiParam {Object} file 文件流
     *
     * @apiSuccess {Integer} errorCode 操作返回code 成功为0,其他均为接口错误
     * @apiSuccess {String} errorMsg 操作返回消息
     * @apiSuccess {Object} data 返回数据
     * @apiSuccess {String} data.url 图片链接
     * @apiSuccess {String} data.thumbnail 缩略图
     * @apiSuccess {Object} data.extra 额外参数，用于前端使用
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function uploadImage(Request $request)
    {
        $params = $this->validate($request, [
            'file'  => 'required|file',
            'style' => 'nullable|string',
        ], [
            '*' => '上传图片失败[-1]'
        ]);

        $file = $params['file'];
        if (!$file instanceof UploadedFile || !$file->isValid()) {
            return $this->outErrorResult(-1, '无效文件');
        }
        if ($file->getSize() > 15728640) {
            return $this->outErrorResult(-1, '您上传的文件太大了');
        }
        $mimeType = $file->getMimeType();
        if (false === strpos($mimeType, 'image')) {
            return $this->outErrorResult(-1, '图片格式不正确');
        }

        try {
            $oss  = new Oss(Oss::BUCKET_NAME_YIQITONGGAN);
            $rest = $oss->postUpload($file);
        } catch (\Throwable $e) {
            Log::info(print_r($e->getMessage(),1));
            return $this->outErrorResult(-1, '上传图片失败[-2]');
        }
        if (empty($rest['info']['url'])) {
            return $this->outErrorResult(-1, '上传图片失败[-3]');
        }
        $style = $params['style'] ?? Oss::STYLE_NAME_W300H300;
        $data  = array(
            'url'       => $rest['info']['url'],
            'thumbnail' => Oss::addImageStyle($style, $rest['info']['url'])
        );

        // 额外参数，用于前端使用
        if ($request->has('extra')) {
            $data['extra'] = $request->input('extra');
        }
        return $this->outSuccessResult($data);
    }



    /**
     * @api {post} /upload_excel 上传excel
     * @apiName uploadImage
     * @apiGroup Upload
     *
     * @apiParam {Object} file 文件流
     *
     * @apiSuccess {Integer} errorCode 操作返回code 成功为0,其他均为接口错误
     * @apiSuccess {String} errorMsg 操作返回消息
     * @apiSuccess {Object} data 返回数据
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function uploadExcel(Request $request)
    {
        $params = $this->validate($request, [
            'file'  => 'required|file',
            'style' => 'nullable|string',
        ], [
            '*' => '上传图片失败[-1]'
        ]);

        $file = $params['file'];
        if (!$file instanceof UploadedFile || !$file->isValid()) {
            return $this->outErrorResult(-1, '无效文件');
        }
        if ($file->getSize() > 15728640) {
            return $this->outErrorResult(-1, '您上传的文件太大了');
        }
        /*
        $mimeType = $file->getMimeType();
        if (false === strpos($mimeType, 'excel')) {
            return $this->outErrorResult(-1, 'excel格式不正确');
        }
        */

        //$file_path = $file->getRealPath();

        Excel::import(new OrderImport(), request()->file('file'));

        return $this->outSuccessResult([]);
    }

}
