<?php
/**
 * Created by PhpStorm.
 * User: yu
 * Date: 2019/3/25
 * Time: 8:07 PM
 */

namespace App\Helper;

use App\Models\Distributor;
use App\Models\Product;
use App\Models\WxUser;
use Illuminate\Support\Facades\Log;

class MakePoster
{
    //字体
    const FONT_YAHEI      = 'public/fonts/MicrosoftYaHei.ttc';  //微软雅黑常规
    const FONT_YAHEI_BOLD = 'public/fonts/MicrosoftYaHeiBold.ttc'; //微软雅黑粗体

    const SHARE_POSTER_FOOTER = 'public/img/share_poster_footer.png';
    const DEFAULT_AVATAR      = 'public/img/logo.png';

    // 生成小程序产品分享海报
    public static function productWxAppSharePoster(Product $product, WxUser $wxUser)
    {
        $productToken = Codec::encodeId($product->id);
        $userToken    = !empty($wxUser->user_id) ? Codec::encodeId($wxUser->user_id) : '';
        $scene        = "sp_{$productToken}_{$userToken}";

        $qrCodeFile = MiniProgram::getInstance()->getWxACodeUnLimit($scene, MiniProgram::PAGE_PRODUCT_HOME, ['width' => 170], false);

        $posterImg = Oss::addImageStyle(Oss::STYLE_NAME_W800, $product->share_poster);
        $tipText   = '长按识别购买';
        $shareText = '发现了好东西要与你分享';
        $referrer  = '#' . $shareText . '#';
        if (!empty($wxUser->nick_name)) {
            $nickName = self::filterContent($wxUser->nick_name);
            $referrer = '@' . mb_substr($nickName, 0, 6) . ' ' . $shareText;
        }
        $headImage = $wxUser->avatar ?? '';
        //$headImage = 'https://wx.qlogo.cn/mmopen/vi_32/DYAIOgq83epFuI7eAOcACe9Ftz4iaT63q2hshTVP1pTavU2WvM2Bc4nMFgcLibu0ykjEwJp9qS5lBFMo8MbVPLfA/132';

        //计算照片高度，并创建照片图像；
        $photoWidth  = 800;
        $photoImg    = imagecreatefromstring(file_get_contents($posterImg));
        $photoImg    = self::imageResizeByWidth($photoImg, $photoWidth);
        $photoHeight = imagesy($photoImg);

        //背景图高度
        $backHeight = $photoHeight + 200;

        //新建彩色背景图像 固定宽度：800
        $backImg = imagecreatetruecolor(800, $backHeight);

        //画布背景颜色 白色
        $bgColor = imagecolorallocate($backImg, 255, 255, 255);
        imagefill($backImg, 0, 0, $bgColor);

        //将照片拷贝到背景图上面
        imagecopy($backImg, $photoImg, 0, 0, 0, 0, $photoWidth, $photoHeight);

        //二维码 尺寸：170 * 170；
        $codeImg = imagecreatefromstring(file_get_contents($qrCodeFile));
        $codeImg = self::imageResizeByWidth($codeImg, 170);
        imagecopy($backImg, $codeImg, 45, $backHeight - 185, 0, 0, 170, 170);

        //头像 尺寸：150 * 150
        if (!empty($headImage)) {
            $avatarImg = imagecreatefromstring(self::downloadImage($headImage));
        } else {
            $avatarImg = imagecreatefromstring(file_get_contents(base_path(self::DEFAULT_AVATAR)));
        }
        $avatarImg = self::imageResizeCenterCrop($avatarImg, 150, 150);
        imagecopy($backImg, $avatarImg, 595, $backHeight - 175, 0, 0, 150, 150);

        //将底部照片拷贝到背景图上面
        $footerImg = imagecreatefromstring(file_get_contents(base_path(self::SHARE_POSTER_FOOTER)));
        imagecopy($backImg, $footerImg, 0, $backHeight - 200, 0, 0, 800, 200);

        // 推荐人和推荐语
        $fontColor = imagecolorallocate($backImg, 58, 59, 61);
        imagettftext($backImg, 15, 0, 250, $backHeight - 160, $fontColor, base_path(self::FONT_YAHEI_BOLD), $referrer);

        // 提示语
        $fontColor = imagecolorallocate($backImg, 58, 59, 61);
        imagettftext($backImg, 15, 0, 360, $backHeight - 45, $fontColor, base_path(self::FONT_YAHEI_BOLD), $tipText);

        //保存照片
        $file = base_path('public/tmp/') . uniqid($wxUser->id, false) . '.jpg';
        imagejpeg($backImg, $file);
        imagedestroy($backImg);

        // 上传到阿里云
        try {
            $oss  = new Oss(Oss::BUCKET_NAME_YIQITONGGAN);
            $info = $oss->localUpload($file, $file);
            if (empty($info['info']['url'])) {
                return '';
            }
            $posterUrl = Oss::addImageStyle(Oss::STYLE_NAME_W800, $info['info']['url']);

            unlink($file);
        } catch (\Exception $e) {
            Log::error(__CLASS__ . ':' . __FUNCTION__ . ' 上传图片失败 e:' . $e->getMessage());
            return '';
        }
        return $posterUrl;
    }

    //固定宽度缩放
    private static function imageResizeByWidth($photoImg, $width)
    {
        //获取图像尺寸信息
        $source_w = imagesx($photoImg);
        $source_h = imagesy($photoImg);

        //计算原图缩放宽度和高度
        $height = ($source_h * $width) / $source_w;

        //缩放原图
        $resize_img = imagecreatetruecolor($width, $height);
        imagecopyresampled($resize_img, $photoImg, 0, 0, 0, 0, $width, $height, $source_w, $source_h);
        return $resize_img;
    }

    private static function imageResizeCenterCrop($image, $width, $height)
    {
        //获取图像尺寸信息
        $target_w = $width;
        $target_h = $height;
        $source_w = imagesx($image);
        $source_h = imagesy($image);

        //计算原图缩放宽度和高度
        $judge    = ($source_w / $source_h) > ($target_w / $target_h);
        $resize_w = $judge ? ($source_w * $target_h) / $source_h : $target_w;
        $resize_h = !$judge ? ($source_h * $target_w) / $source_w : $target_h;

        //计算裁剪开始位置
        $start_x = $judge ? ($resize_w - $target_w) / 2 : 0;
        $start_y = !$judge ? ($resize_h - $target_h) / 2 : 0;

        //缩放原图
        $resize_img = imagecreatetruecolor($resize_w, $resize_h);
        imagecopyresampled($resize_img, $image, 0, 0, 0, 0, $resize_w, $resize_h, $source_w, $source_h);

        //裁剪图片
        $target_img = imagecreatetruecolor($target_w, $target_h);
        imagecopy($target_img, $resize_img, 0, 0, $start_x, $start_y, $resize_w, $resize_h);
        return $target_img;
    }

    //计算文字内容宽度
    private static function downloadImage($url)
    {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $image = curl_exec($curl);
        curl_close($curl);
        return $image;
    }

    private static function filterContent($content)
    {
        //将emoji表情替换为空格
        $content = preg_replace_callback(
            '/./u',
            function (array $match) {
                return strlen($match[0]) >= 4 ? '' : $match[0];
            },
            $content);

        //将换行替换为空格
        $content = str_replace(array("\r", "\n", "\r\n"), ' ', $content);
        return $content;
    }
}