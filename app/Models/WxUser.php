<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

/**
 * 微信用户
 * Class WxUser
 * @package App
 *
 * @property int id
 * @property string open_id 微信openid
 * @property string union_id 微信unionid
 * @property int user_id 用户ID
 * @property string source 来源 1:小程序 2:公众号
 * @property string nick_name 微信昵称
 * @property string avatar 微信头像
 * @property string gender 性别
 * @property string country 国家
 * @property string province 省
 * @property string city 城市
 * @property string district 地区
 * @property string language 语言
 * @property string reg_ip 用户IP
 * @property int is_subscribe 是否关注
 * @property string subscribe_time 关注时间
 * @property string subscribe_scene 关注公众号来源
 * @property int isold 是否之前就数据
 * @property string created_at 创建时间
 * @property string updated_at 更新时间
 */
class WxUser extends Model
{
    protected $table = 'wx_users';

    const SOURCE_WXAPP  = 1;//小程序
    const SOURCE_WECHAT = 2;//公众号

   
    // 是否之前就数据 0:不是 1:是
    const OLD_NO  = 0;
    const OLD_YES = 1;

    const TOKEN_IV        = '2f1f7afc9fa465a0d05845d4ed6702b3';
    const TOKEN_DELIMITER = '&@qw@&';

    public static function generateToken($openId, $sessionKey)
    {
        $key = config('app.key');
        return openssl_encrypt($openId . self::TOKEN_DELIMITER . $sessionKey, 'AES-256-CBC',
            $key, 0, hex2bin(self::TOKEN_IV));
    }

    public static function getOpenId($token)
    {
        $key    = config('app.key');
        $result = openssl_decrypt($token, 'AES-256-CBC', $key, 0, hex2bin(self::TOKEN_IV));
        $data   = explode(self::TOKEN_DELIMITER, $result);
        return !empty($data[0]) ? $data[0] : '';
    }

    public static function getSessionKey($token)
    {
        $key    = config('app.key');
        $result = openssl_decrypt($token, 'AES-256-CBC', $key, 0, hex2bin(self::TOKEN_IV));
        $data   = explode(self::TOKEN_DELIMITER, $result);
        return !empty($data[1]) ? $data[1] : '';
    }

    public static function add($openId, $source, $unionId = '', $sourceUserId = 0)
    {
        Log::info(__CLASS__ . '_' . __FUNCTION__, \func_get_args());
        $self           = new self();
        $self->open_id  = $openId;
        $self->union_id = $unionId;
        $self->source   = $source;
        $self->reg_ip   = request()->ip();
        $self->isold    = self::OLD_NO;
        $self->save();

        if ($sourceUserId > 0) {
            $sourceUser = User::find($sourceUserId);
            if ($sourceUser instanceof User && $sourceUser->is_distributor) {
                UserRelationship::add($self->id, $sourceUserId);
            }
        }
        return $self;
    }

    public function bindUnionId($unionId)
    {
        $this->union_id = $unionId;
        return $this->save();
    }
    public function bindUserId($userId)
    {
        $this->user_id = $userId;
        return $this->save();
    }

    public function isOld()
    {
        return self::OLD_YES == $this->isold;
    }
    public function updateData($params)
    {
        !empty($params['union_id']) && $this->union_id = $params['union_id'];
        isset($params['nick_name']) && $this->nick_name = $params['nick_name'];
        isset($params['avatar']) && $this->avatar = $params['avatar'];
        isset($params['gender']) && $this->gender = $params['gender'];
        isset($params['country']) && $this->country = $params['country'];
        isset($params['province']) && $this->province = $params['province'];
        isset($params['city']) && $this->city = $params['city'];
        isset($params['district']) && $this->district = $params['district'];
        return $this->save();
    }

}
