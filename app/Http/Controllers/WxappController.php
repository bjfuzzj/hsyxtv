<?php

namespace App\Http\Controllers;

use App\Helper\Codec;
use App\Helper\IPUtil;
use App\Helper\MiniProgram;
use App\Models\OrderGen;
use App\Models\SubscribeMessage;
use App\Models\UserRelationship;
use App\Models\WxappFormId;
use App\Models\WxappUser;
use App\Models\User;
use App\Models\WxUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class WxappController extends Controller
{

    /**
     * @api {post} /api/wxapp/login 登录
     * @apiName login
     * @apiGroup Wxapp
     *
     * @apiParam {String} code 登录凭证code
     * @apiParam {String} source_token 来源用户的user_token，点哪个用户推广链接进来的
     *
     * @apiSuccess {String} errorCode 操作返回code 0为正常，其他则为异常
     * @apiSuccess {String} errorMsg 操作返回消息
     * @apiSuccess {Object} data 返回数据
     * @apiSuccess {String} data.token 用户身份token
     * @apiSuccess {String} data.user_token 用户的user_token
     * @throws \Illuminate\Validation\ValidationException
     */
    public function login(Request $request)
    {
        $params = $this->validate($request, [
            'code'         => 'required|string',
            'source_token' => 'nullable|string',
        ], [
            '*' => '登录失败，请重试[-1]'
        ]);

        $sourceUserId = 0;
        if (!empty($params['source_token'])) {
            $sourceUserId = Codec::decodeId($params['source_token']);
        }

        $result = MiniProgram::getInstance()->login($params['code']);
        if (empty($result['openid']) || empty($result['session_key'])) {
            return $this->outErrorResultApi(500, '登录失败，请重试[1]');
        }
        $openId     = $result['openid'];
        $sessionKey = $result['session_key'];
        $unionId    = $result['unionid'] ?? '';

        $wxUser = WxUser::where('open_id', $openId)->first();
        if (!$wxUser instanceof WxUser) {
            $wxUser = WxUser::add($openId, WxUser::SOURCE_WXAPP, $unionId, $sourceUserId);
        } else if (empty($wxUser->unionid) && !empty($unionId)) {
            $wxUser->bindUnionId($unionId);
        }


        // 微信绑定user_id
        if (empty($wxUser->user_id) && !empty($unionId)) {
            $user = User::where('union_id', $unionId)->first();
            if (!$user instanceof User) {
                $user = User::createByWxUser($wxUser);
            }
                $wxUser->bindUserId($user->id);
        }

        //旧数据处理
        // if ($wxUser->isOld() && !empty($wxUser->user_id) && !empty($unionId)) {
        //     $oldUser = User::find($wxUser->user_id);
        //     if (empty($oldUser->union_id)) {
        //         $oldUser->union_id = $unionId;
        //         $oldUser->save();
        //     }
        // }

        $data = [
            'token' => WxUser::generateToken($openId, $sessionKey)
        ];
        if (!empty($wxUser->user_id) && !empty($wxUser->nick_name)) {
            $data['user_token'] = Codec::encodeId($wxUser->user_id);
        }
        return $this->outSuccessResultApi($data);
    }

    /**
     * @api {post} /api/wxapp/auth 授权
     * @apiName doAuth
     * @apiGroup Wxapp
     *
     * @apiParam {String} _token 用户身份token
     * @apiParam {String} encryptedData 用户信息的加密数据
     * @apiParam {String} iv 加密算法的初始向量
     *
     * @apiSuccess {String} errorCode 操作返回code 0为正常，其他则为异常
     * @apiSuccess {String} errorMsg 操作返回消息
     * @apiSuccess {Object} data 返回数据
     * @apiSuccess {String} data.user_token 用户的user_token
     * @throws \Illuminate\Validation\ValidationException
     */
    public function doAuth(Request $request)
    {
        $params = $this->validate($request, [
            '_token'        => 'required|string',
            'encryptedData' => 'required|string',
            'iv'            => 'required|string'
        ], [
            '*' => '授权登录失败，请重试[-1]'
        ]);
        $token  = $params['_token'];

        $sessionKey = WxUser::getSessionKey($token);
        if (empty($sessionKey)) {
            return $this->outErrorResultApi(501, '授权登录失败，请重试[-2]');
        }
        $result = MiniProgram::getInstance()->decryptData($sessionKey, $params['iv'], $params['encryptedData']);
        if (empty($result['openId'])) {
            return $this->outErrorResultApi(501, '授权登录失败，请重试[-3]');
        }
        $wxUser = WxUser::where('open_id', $result['openId'])->first();
        if (!$wxUser instanceof WxUser) {
            return $this->outErrorResultApi(501, '授权登录失败，请重试[-4]');
        }

        $data = [
            'nick_name' => $result['nickName'] ?? '',
            'gender'    => $result['gender'] ?? '',
            'avatar'    => $result['avatarUrl'] ?? '',
            'district'  => $result['district'] ?? '',
            'city'      => $result['city'] ?? '',
            'province'  => $result['province'] ?? '',
            'country'   => $result['country'] ?? '',
            'language'  => $request['language'] ?? '',
            'union_id'  => $result['unionId'] ?? '',
        ];
        $rest = $wxUser->updateData($data);
        if (!$rest) {
            return $this->outErrorResultApi(500, '登录失败，请重试[-5]');
        }

        // 创建user或更新user
        if (empty($wxUser->user_id)) {
            $user = User::createByWxUser($wxUser);
            $wxUser->bindUserId($user->id);
        } else {
            $user = User::find($wxUser->user_id);
            if ($user instanceof User) {
                $user->updateByWxUser($wxUser);
            }
        }
        return $this->outSuccessResultApi([
            'user_token' => Codec::encodeId($user->id)
        ]);
    }

    /**
     * @api {post} /api/collect_form_id 收集FormId
     * @apiName collectFormId
     * @apiGroup Wxapp
     *
     * @apiParam {String} _token 用户身份token
     * @apiParam {String} form_id 表单ID
     *
     * @apiSuccess {String} errorCode 操作返回code 0为正常，其他则为异常
     * @apiSuccess {String} errorMsg 操作返回消息
     * @apiSuccess {Object} data 返回数据
     * @throws \Illuminate\Validation\ValidationException
     */
    public function collectFormId(Request $request)
    {
        $params = $this->validate($request, [
            '_token'  => 'required|string',
            'form_id' => 'required|string',
        ], [
            '*' => '收集失败[1]'
        ]);

        // 过滤掉开发工具生成的formId
        if ('the formId is a mock one' == $params['form_id']) {
            return $this->outSuccessResultApi();
        }

        $openId = WxUser::getOpenId($params['_token']);
        if (empty($openId)) {
            return $this->outErrorResultApi(500, '收集失败[2]');
        }
        WxappFormId::add($openId, $params['form_id'], WxappFormId::TYPE_FORM);
        return $this->outSuccessResultApi();
    }

    /**
     * @api {post} /api/wxapp/decrypt_data 微信解密数据
     * @apiName decryptData
     * @apiGroup Wxapp
     *
     * @apiParam {String} _token 用户身份token
     * @apiParam {String} encryptedData 加密数据
     * @apiParam {String} iv 加密算法的初始向量
     *
     * @apiSuccess {String} errorCode 操作返回code 0为正常，其他则为异常
     * @apiSuccess {String} errorMsg 操作返回消息
     * @apiSuccess {Object} data 返回数据 解密数据
     * @throws \Illuminate\Validation\ValidationException
     */
    public function decryptData(Request $request)
    {
        $params = $this->validate($request, [
            '_token'        => 'required|string',
            'encryptedData' => 'required|string',
            'iv'            => 'required|string'
        ], [
            '*' => '解密失败[1]'
        ]);
        $token  = $params['_token'];

        $sessionKey = WxUser::getSessionKey($token);
        if (empty($sessionKey)) {
            return $this->outErrorResultApi(500, '解密失败[2]');
        }
        $result = MiniProgram::getInstance()->decryptData($sessionKey, $params['iv'], $params['encryptedData']);
        if (empty($result)) {
            return $this->outErrorResultApi(500, '解密失败[3]');
        }
        return $this->outSuccessResultApi($result);
    }

    /**
     * @api {post} /api/wxapp/subscribe_message 添加订阅消息
     * @apiName subscribeMessage
     * @apiGroup Wxapp
     *
     * @apiParam {String} _token 用户身份token
     * @apiParam {String} tmplIds 模板id的集合
     *
     * @apiSuccess {String} errorCode 操作返回code 0为正常，其他则为异常
     * @apiSuccess {String} errorMsg 操作返回消息
     * @apiSuccess {Object} data 返回数据
     * @throws \Illuminate\Validation\ValidationException
     */
    public function subscribeMessage(Request $request)
    {
        $params = $this->validate($request, [
            '_token'  => 'required|string',
            'tmplIds' => 'required|array',
        ], [
            '*' => '收集失败[1]'
        ]);

        $openId = WxUser::getOpenId($params['_token']);
        if (empty($openId)) {
            return $this->outErrorResultApi(500, '收集失败[2]');
        }

        SubscribeMessage::batchAdd($openId, $params['tmplIds']);
        return $this->outSuccessResultApi();
    }

}
