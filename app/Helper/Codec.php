<?php
/*
 * @Author: your name
 * @Date: 2020-12-02 18:06:10
 * @LastEditTime: 2020-12-02 21:06:56
 * @LastEditors: Please set LastEditors
 * @Description: In User Settings Edit
 * @FilePath: /paota/ptweb/app/Helper/Codec.php
 */

namespace App\Helper;

/**
 * 利用62进制对数字ID进行编码/解码
 * Class Codec
 */
class Codec
{
    private static $scramble = 'faiuwbGkAxntR4cM6QzpYsVX3eoKPEOLgC8m7WBqvlJFT21NrjHD9h5dIZyU0S';
    private static $prefix   = '20';
    private static $suffix   = '6';

    public static function encodeId($id)
    {
        if (empty($id) || !is_numeric($id) || strpos($id, '.')) {
            return $id;
        }
        $id = self::$prefix . $id . self::$suffix;
        $out         = '';
        $idTemp      = self::descId($id);
        $scrambleLen = strlen(self::$scramble);
        while ($idTemp > $scrambleLen - 1) {
            $out    = substr(self::$scramble, fmod($idTemp, $scrambleLen), 1) . $out;
            $idTemp = floor($idTemp / $scrambleLen);
        }
        $out = substr(self::$scramble, $idTemp, 1) . $out;
        return $out;
    }

    public static function decodeId($id)
    {
        $idLen       = strlen($id) - 1;
        $idArr       = str_split($id);
        $out         = strpos(self::$scramble, array_pop($idArr));
        $scrambleLen = strlen(self::$scramble);
        foreach ($idArr as $i => $char) {
            $num = strpos(self::$scramble, $char);
            if ($num === false) {
                throw new \RuntimeException("invalid char ($char)", 500);
            }
            $out += $num * pow($scrambleLen, $idLen - $i);
        }

        $out = self::descId($out);
        $out = substr($out, strlen(self::$prefix));
        $out = substr($out, 0, -strlen(self::$suffix));
        return (string)$out;
    }

    //id倒序
    private static function descId($id)
    {
        $idLen  = strlen($id);
        $new_id = '';
        for ($i = $idLen - 1; $i >= 0; $i--) {
            $new_id .= substr($id, $i, 1);;
        }
        return $new_id;
    }
}