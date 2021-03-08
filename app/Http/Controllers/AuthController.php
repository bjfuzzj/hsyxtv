<?php

namespace App\Http\Controllers;

use App\Models\Operator;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{

    /**
     * Get a JWT token via given credentials.
     *
     * @param  \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        try {
            $params = $this->validate($request, [
                'user_name' => 'required|string',
                'password'  => 'required|string'
            ]);
        } catch (ValidationException $e) {
            return $this->outErrorResult(500, '账号或密码错误[-1]');
        }

        if (!$token = auth('api')->attempt($params)) {
            return $this->outErrorResult(500, '账号或密码错误[-2]');
        }

        return $this->outSuccessResult([
            'token'      => 'Bearer ' . $token,
            'expires_in' => auth('api')->factory()->getTTL() * 60
        ]);
    }

    public function refresh()
    {
        $token = auth('api')->refresh();
        return $this->outSuccessResult([
            'token'      => 'Bearer ' . $token,
            'expires_in' => auth('api')->factory()->getTTL() * 60
        ]);
    }

    public function me(Request $request)
    {
        $operator = $request->user();
        $roles = [];
        if ($operator instanceof Operator) {
            $roles    = $operator->roles()->pluck('name')->toArray();
        }
        return $this->outSuccessResult([
            'user_name' => $operator->user_name ?? '',
            'real_name' => $operator->real_name ?? '',
            'roles'     => $roles,
        ]);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth('api')->logout();
        return $this->outSuccessResult(['message' => '退出成功']);
    }

}
