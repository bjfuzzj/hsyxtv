<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    // 角色列表
    public function index()
    {
        $roles = Role::paginate(20);
        return view('/system/role_list', compact('roles'));
    }

    // 添加角色页面
    public function add()
    {
        return view('/system/role_add');
    }

    // 添加角色操作
    public function doAdd()
    {
        $data = $this->validate(request(), [
            'name' => 'required|string',
        ], [
            'name.required' => '请输入角色名',
            'name.string'   => '请输入角色名',
        ]);
        Role::create($data);
        return redirect('/system/roles');
    }

    // 添加角色页面
    public function delete()
    {
        $params = $this->validate(request(), [
            'id' => 'required|integer',
        ]);

        $role = Role::findById($params['id']);
        if (!$role instanceof Role) {
            throw ValidationException::withMessages(['id' => '角色不存在']);
        }
        $role->delete();
        return redirect('/system/roles');
    }

    // 绑定权限页面
    public function permission()
    {
        $params = $this->validate(request(), [
            'id' => 'required|integer',
        ]);

        $role = Role::findById($params['id']);

        // 所有菜单
        $menus = Menu::orderBy('level')->get();
        $trees = $this->getMenuTree($menus);

        // 当前角色的菜单
        $checkedIds    = [];
        $myPermissions = $role->getAllPermissions();
        foreach ($myPermissions as $permission) {
            $checkedIds[] = Menu::getIdByPermissionName($permission->name);
        }

        $this->getCheckedLeafNode($trees, $checkedIds);
        $checkedIds = array_values($checkedIds);

        return view('/system/role_permission', [
            'roleId'     => $params['id'],
            'trees'      => $trees,
            'checkedIds' => $checkedIds
        ]);
    }

    // 菜单树
    private function getMenuTree($menus, $pId = 0)
    {
        $trees = [];
        foreach ($menus as $key => $menu) {
            if ($pId == $menu->parent_id) {
                $trees[] = array(
                    'id'       => $menu->id,
                    'name'     => $menu->name,
                    'children' => $this->getMenuTree($menus, $menu->id)
                );
                unset($menus[$key]);
            }
        }
        return $trees;
    }

    // 获取选中的叶子节点
    private function getCheckedLeafNode($trees, &$checkedIds)
    {
        foreach ($trees as $tree) {
            if (!empty($tree['children'])) {
                $checkedIds = array_diff($checkedIds, [$tree['id']]);
                $this->getCheckedLeafNode($tree['children'], $checkedIds);
            }
        }
    }

    // 绑定权限操作
    public function bindPermission()
    {
        $params = $this->validate(request(), [
            'id'      => 'required|integer',
            'menuIds' => 'required|string',
        ]);

        $role = Role::findById($params['id']);
        if (!$role instanceof Role) {
            throw ValidationException::withMessages(['id' => '角色不存在']);
        }
        $permissionNames = Menu::getPermissionNamesByIds(explode(',', $params['menuIds']));
        $role->syncPermissions($permissionNames);
        return redirect('/system/roles');
    }
}
