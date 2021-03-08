<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Permission;

class MenuController extends Controller
{
    // 菜单列表
    public function index(Request $request)
    {
        $parentId = $request->input('parent_id', 0);
        $curMenu  = $menus = [];
        if (!empty($parentId)) {
            $curMenu = Menu::where('id', $parentId);
        }
        $menus = Menu::where('parent_id', $parentId)->get();

        return view('/system/menu_list', [
            'curMenu' => $curMenu,
            'menus'   => $menus,
            'query'   => $params
        ]);
    }

    // 添加菜单操作，并关联对应权限
    public function add(Request $request)
    {
        $data              = $this->validate(request(), [
            'name'      => 'required|string',
            'method'    => 'required|in:' . implode(',', Menu::$methods),
            'is_nav'    => 'required|in:' . implode(',', Menu::$navigations),
            'url'       => 'string|nullable',
            'parent_id' => 'integer',
        ], [
            'name.required'   => '请填写菜单名',
            'name.string'     => '请填写菜单名',
            'method.required' => '请选择请求方式',
            'method.in'       => '请选择请求方式',
            'is_nav.required' => '请选择是否是左侧菜单',
            'is_nav.in'       => '请选择是否是左侧菜单',
        ]);
        $data['url']       = isset($data['url']) ? $data['url'] : '';
        $data['parent_id'] = isset($data['parent_id']) ? $data['parent_id'] : 0;
        if (!empty($data['url'])) {
            $isExists = Menu::where('url', $data['url'])->where('method', $data['method'])->exists();
        } else {
            $isExists = Menu::where('name', $data['name'])->where('parent_id', $data['parent_id'])->exists();
        }
        if ($isExists) {
            throw ValidationException::withMessages(['name' => '菜单已经添加']);
        }

        $data['level'] = Menu::LEVEL_FIRST;
        if ($data['parent_id']) {
            $parentLevel   = Menu::where('id', $data['parent_id'])->value('level');
            $data['level'] = $parentLevel + 1;
        }
        if (Menu::IS_NAV_YES == $data['is_nav'] && $data['level'] > Menu::NAV_LEVEL_MAX) {
            throw ValidationException::withMessages(['is_nav' => '导航菜单最大' . Menu::NAV_LEVEL_MAX . '级别']);
        }

        $curOperator          = $request->user();
        $data['creator_id']   = $curOperator->id;
        $data['creator_name'] = $curOperator->real_name;

        DB::beginTransaction();
        try {
            $menu = Menu::create($data);
            Permission::create(['name' => $menu->getPermissionName()]);
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
        return redirect('/system/menus?id=' . $data['parent_id']);
    }

    // 编辑菜单页面
    public function edit()
    {
        $data = $this->validate(request(), [
            'id' => 'required|integer',
        ]);

        $menu = Menu::find($data['id']);
        if (!$menu instanceof Menu) {
            throw ValidationException::withMessages(['id' => '菜单不存在']);
        }
        $parentName = '';
        if (!empty($menu->parent_id)) {
            $parentName = Menu::where('id', $menu->parent_id)->pluck('name')->first();
        }
        return view('/system/menu_edit', [
            'methods'    => Menu::$methods,
            'menu'       => $menu,
            'parentName' => $parentName
        ]);
    }

    // 编辑菜单操作
    public function doEdit()
    {
        $data = $this->validate(request(), [
            'id'     => 'required|integer',
            'name'   => 'required|string',
            'method' => 'required|in:' . implode(',', Menu::$methods),
            'is_nav' => 'required|in:' . implode(',', Menu::$navigations),
            'url'    => 'string|nullable',
        ], [
            'name.required'   => '请填写菜单名',
            'name.string'     => '请填写菜单名',
            'method.required' => '请选择请求方式',
            'method.in'       => '请选择请求方式',
            'is_nav.required' => '请选择是否是左侧菜单',
            'is_nav.in'       => '请选择是否是左侧菜单',
        ]);

        $menu = Menu::find($data['id']);
        if (!$menu instanceof Menu) {
            throw ValidationException::withMessages(['id' => '菜单不存在']);
        }
        unset($menu['id']);
        $menu->update($data);
        return redirect('/system/menus?id=' . $menu->parent_id);
    }

    // 删除操作，并删除对应的权限
    public function doDelete()
    {
        $data = $this->validate(request(), [
            'id' => 'required|integer',
        ]);

        $menu = Menu::find($data['id']);
        if (!$menu instanceof Menu) {
            throw ValidationException::withMessages(['id' => '菜单不存在']);
        }

        $parent_id = $menu->parent_id;
        DB::beginTransaction();
        try {
            Permission::findByName($menu->getPermissionName())->delete();
            $menu->delete();
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
        return redirect('/system/menus?parent_id=' . $parent_id);
    }
}
