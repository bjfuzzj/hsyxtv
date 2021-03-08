<?php

namespace App\Http\Controllers;

use App\Http\Resources\OperatorResource;
use App\Models\Operator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;

class OperatorController extends Controller
{

    // 操作员列表
    public function index(Request $request)
    {
        $status   = $request->input('status', Operator::STATUS_START);
        $userName = $request->input('user_name', '');
        $size     = $request->input('size', 20);

        $query = Operator::where('status', $status);
        if (!empty($userName)) {
            $query->where(function ($query) use ($userName) {
                $query->where('user_name', 'like', "%{$userName}%")
                    ->orWhere('real_name', 'like', "%{$userName}%");
            });
        }
        $queryRes = $query->orderByDesc('id')->paginate($size);

        return $this->outSuccessResult([
            'status_options' => Operator::$statusOptions,
            'list'           => OperatorResource::collection($queryRes),
            'current_page'   => $queryRes->currentPage(),
            'total'          => $queryRes->total(),
        ]);
    }

    public function info(Request $request)
    {
        $params = $this->validate($request, [
            'id' => 'required|integer',
        ], [
            'id.*' => '加载数据失败[-1]',
        ]);

        $operator = Operator::find($params['id']);
        $info     = [];
        if ($operator instanceof Operator) {
            $info = OperatorResource::make($operator);
        }
        return $this->outSuccessResult($info);
    }

    // 添加操作员操作
    public function add(Request $request)
    {
        $params = $this->validate($request, [
            'user_name' => 'required|alpha_num|min:2|max:20',
            'password'  => 'required|alpha_dash|min:6|max:32',
            'real_name' => 'nullable|string',
            'mobile'    => 'nullable|regex:/^1[3456789][0-9]{9}$/',
            'email'     => 'nullable|email',
        ], [
            'user_name.required'  => '请填写用户名',
            'user_name.alpha_num' => '用户名只能填写字母和数字',
            'user_name.min'       => '用户名至少2个字符长度',
            'user_name.max'       => '用户名最多20个字符长度',
            'password.required'   => '请输入密码',
            'password.alpha_dash'  => '密码只能填写字母和数字',
            'password.min'        => '密码至少6个字符长度',
            'password.max'        => '密码最多32个字符长度',
            'real_name.*'         => '请填写姓名',
            'mobile.regex'        => '手机号格式不正确',
            'email.email'         => '请输入正确的邮箱地址',
        ]);
        if (Operator::where('user_name', $params['user_name'])->exists()) {
            throw ValidationException::withMessages(['user_name' => '用户名已经注册']);
        }
        $params['creator_id']   = $request->user()->id;
        $params['creator_name'] = $request->user()->real_name;

        Operator::doAdd($params);
        return $this->outSuccessResult();
    }

    // 编辑操作员操作
    public function edit()
    {
        $params = $this->validate(request(), [
            'id'        => 'required|integer',
            'real_name' => 'nullable|string',
            'mobile'    => 'nullable|regex:/^1[3456789][0-9]{9}$/',
            'email'     => 'nullable|email',
        ], [
            'real_name.*'    => '请填写姓名',
            'mobile.regex'   => '手机号格式不正确',
            'email.required' => '请输入邮箱地址',
            'email.email'    => '请输入正确的邮箱地址',
        ]);

        $operator = Operator::find($params['id']);
        if (!$operator instanceof Operator) {
            throw ValidationException::withMessages(['id' => '操作员不存在']);
        }
        unset($params['id']);

        $operator->doUpdate($params);
        return $this->outSuccessResult();
    }

    // 启用账号
    public function doStart()
    {
        $params   = $this->validate(request(), [
            'id' => 'required|integer'
        ]);
        $operator = Operator::find($params['id']);
        if (!$operator instanceof Operator) {
            throw ValidationException::withMessages(['id' => '操作员不存在']);
        }
        $operator->doStart();
        return $this->outSuccessResult();
    }

    // 停用账号
    public function doStop()
    {
        $params   = $this->validate(request(), [
            'id' => 'required|integer'
        ]);
        $operator = Operator::find($params['id']);
        if (!$operator instanceof Operator) {
            throw ValidationException::withMessages(['id' => '操作员不存在']);
        }
        $operator->doStop();
        return $this->outSuccessResult();
    }

    // 初始化密码
    public function initPassword()
    {
        $params   = $this->validate(request(), [
            'id' => 'required|integer'
        ]);
        $operator = Operator::find($params['id']);
        if (!$operator instanceof Operator) {
            throw ValidationException::withMessages(['id' => '操作员不存在']);
        }

        $operator->initialPassword();
        return $this->outSuccessResult();
    }

    // 绑定角色页面
    //    public function roles()
    //    {
    //        $params   = $this->validate(request(), [
    //            'id' => 'required|integer'
    //        ]);
    //        $operator = Operator::find($params['id']);
    //        if (!$operator instanceof Operator) {
    //            throw ValidationException::withMessages(['id' => '操作员不存在']);
    //        }
    //
    //        // 所有角色
    //        $roles = Role::all();
    //
    //        // 获取当前角色的权限
    //        $myRoles = $operator->roles;
    //        return view('/system/operator_role', compact('roles', 'myRoles'));
    //    }
    //
    //    // 绑定角色操作
    //    public function bindRole()
    //    {
    //        $params   = $this->validate(request(), [
    //            'id'    => 'required|integer',
    //            'roles' => 'required'
    //        ]);
    //        $operator = Operator::find($params['id']);
    //        if (!$operator instanceof Operator) {
    //            throw ValidationException::withMessages(['id' => '操作员不存在']);
    //        }
    //
    //        $roleIds = $params['roles'];
    //        $operator->syncRoles($roleIds);
    //        return redirect('/system/operators');
    //    }
}
