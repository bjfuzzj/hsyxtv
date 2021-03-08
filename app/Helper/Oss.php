<?php

namespace App\Helper;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\App;
use OSS\Core\OssException;
use OSS\Core\OssUtil;
use OSS\OssClient;

/**
 * 阿里云OSS 工具类
 * Class Oss
 * @package App\Services
 */
class Oss
{
    private $ossClient;
    private $bucketName;
    private $domainUrl;

    // 存储空间
    const BUCKET_NAME_YIQITONGGAN = 'imgtravel';

    // 图片样式
    const STYLE_NAME_W300H300 = '!w300h300';
    const STYLE_NAME_W800H800 = '!w800h800';
    const STYLE_NAME_W500H400 = '!w500h400';
    const STYLE_NAME_W800     = '!w800';
    const STYLE_NAME_POSTER = '!poster';
    const STYLE_NAME_DEFAULT = '';

    /**
     * 初始化 OssClient
     * @param string $bucketName 存储空间名称
     * @throws \OSS\Core\OssException
     */
    public function __construct($bucketName)
    {
        $accessKeyId     = config('oss.accessKeyId');
        $accessKeySecret = config('oss.accessKeySecret');
        if (empty($accessKeyId) || empty($accessKeySecret)) {
            throw new OssException('AccessKey配置为空');
        }

        $buckets = config('oss.buckets');
        if (!array_key_exists($bucketName, $buckets)) {
            throw new OssException('存储空间不存在');
        }

        $bucketInfo = $buckets[$bucketName];

        $endpoint = $bucketInfo['endpoint'];
        if (App::environment('prod')) {
            $endpoint = $bucketInfo['internalEndpoint'];
        }
        $this->ossClient  = new OssClient($accessKeyId, $accessKeySecret, $endpoint);
        $this->bucketName = $bucketName;
        $this->domainUrl  = $bucketInfo['cdn'];
    }

    /**
     * post 上传文件
     * @param UploadedFile $file 文件
     * @return array
     * @throws \OSS\Core\OssException
     */
    public function postUpload(UploadedFile $file)
    {
        $fileKey = $this->genFileKey($file->getClientOriginalName()) . '.' . $file->getClientOriginalExtension();
        $rest    = $this->ossClient->uploadFile($this->bucketName, $fileKey, $file->getRealPath());
        $this->replaceUrl($rest);
        return $rest;
    }

    /**
     * 上传本地文件
     *
     * @param string $fileName 文件名（带上后缀名）
     * @param string $filePath 文件路径
     * @return array 结果
     * @throws \OSS\Core\OssException
     */
    public function localUpload($fileName, $filePath)
    {
        if (!file_exists($filePath)) {
            throw new OssException('文件不存在');
        }

        $fileKey = $this->genFileKey($fileName) . '.' . $this->getExtension($fileName);
        $rest    = $this->ossClient->uploadFile($this->bucketName, $fileKey, $filePath);
        $this->replaceUrl($rest);
        return $rest;
    }

    /**
     * 初始化一个分片上传事件
     * @param $fileName 文件名
     * @return string OSS创建的全局唯一的uploadId
     * @throws \OSS\Core\OssException
     */
    public function initMultipartUpload($fileName)
    {
        $fileKey = $this->genFileKey($fileName) . '.' . $this->getExtension($fileName);;
        $uploadId = $this->ossClient->initiateMultipartUpload($this->bucketName, $fileKey);
        return compact('fileKey', 'uploadId');
    }

    /**
     * 上传分片数据
     * @param $uploadId OSS创建的全局唯一的uploadId
     * @param $file 文件
     * @param $fileName 文件名
     * @param $partNum 分片号
     * @return 返回ETag值
     * @throws \OSS\Core\OssException
     */
    public function uploadPart($uploadId, $file, $fileName, $partNum)
    {
        $upOptions = array(
            OssClient::OSS_FILE_UPLOAD => $file,
            OssClient::OSS_PART_NUM    => $partNum,
            OssClient::OSS_LENGTH      => filesize($file),
            OssClient::OSS_CHECK_MD5   => true,
            OssClient::OSS_CONTENT_MD5 => OssUtil::getMd5SumForFile($file, 0, filesize($file))
        );
        return $this->ossClient->uploadPart($this->bucketName, $fileName, $uploadId, $upOptions);
    }

    /**
     * 完成上传
     * @param string $uploadId OSS创建的全局唯一的uploadId
     * @param string $fileName 文件名
     * @param array $uploadParts 所有分片上传的Etag
     * @return null
     * @throws \OSS\Core\OssException
     */
    public function completeMultipartUpload($uploadId, $fileName, $uploadParts)
    {
        $rest = $this->ossClient->completeMultipartUpload($this->bucketName, $fileName, $uploadId, $uploadParts);
        $this->replaceUrl($rest);
        return $rest;
    }

    /**
     * 添加图片样式
     * @param string $newStyleName 新的图片样式
     * @param array $urls 图片链接
     * @return string|array 新样式的图片链接
     */
    public static function addImageStyle($newStyleName, $urls)
    {
        $isOne = false;
        if (empty($urls)) {
            return $urls;
        }
        if (!is_array($urls)) {
            $isOne = true;
            $urls  = array($urls);
        }

        $newUrls = array();
        foreach ($urls as $url) {
            $newUrls[] = preg_replace('/!.*$/', '', $url) . $newStyleName;
        }
        return $isOne ? current($newUrls) : $newUrls;
    }

    /**
     * 生成文件唯一Key
     * @param $fileName
     * @return string
     */
    private function genFileKey($fileName)
    {
        $dateTime  = date('YmdHis');
        $machineIp = '';
        if (isset($_SERVER['SERVER_ADDR'])) {
            $machineIp = ip2long($_SERVER['SERVER_ADDR']);
        }
        $pid    = getmypid();
        $random = mt_rand(0, 999999);
        return md5($fileName . $random . $dateTime . $machineIp . $pid);
    }

    /**
     * 获取文件扩展名
     * @param $file
     * @return string
     */
    private function getExtension($file)
    {
        $arr = explode('.', $file);
        return (count($arr) === 1) ? '' : strtolower(end($arr));
    }

    /**
     * 将访问域名替换成自定义域名
     *
     * @param $rest
     * @return string
     */
    private function replaceUrl(&$rest)
    {
        if (!empty($rest['info']['url'])) {
            $rest['info']['url'] = preg_replace("/\?.*/", '', $rest['info']['url']);
            $rest['info']['url'] = preg_replace('/^(http[s]?:\/\/)?[^\/]+/i', $this->domainUrl, $rest['info']['url']);
        }
    }

    /**
     * 获取缩略图和原图
     * @param $imageList
     * @return array|mixed
     */
    public static function formatImage($imageList)
    {
        if (empty($imageList)) {
            return [];
        }
        $isOne = false;
        if (is_string($imageList)) {
            $isOne     = true;
            $imageList = [$imageList];
        }

        $data = [];
        if (is_array($imageList)) {
            foreach ($imageList as $image) {
                //$data[] = ['url' => $image, 'thumbnail' => self::addImageStyle(self::STYLE_NAME_W300H300, $image)];
                $data[] = ['url' => $image, 'thumbnail' => self::addImageStyle('', $image)];
            }
        }
        return $isOne ? current($data) : $data;
    }

}