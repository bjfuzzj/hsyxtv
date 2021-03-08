<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Menu extends Model
{
    use SoftDeletes;

    protected $table = 'op_menus';

    // 菜单权限名称前缀
    const PERMISSION_PREFIX = 'menu_';

    // 获取权限名称
    public function getPermissionName()
    {
        return self::PERMISSION_PREFIX . $this->id;
    }

    // 根据权限名称获取菜单ID
    public static function getIdByPermissionName($permissionName)
    {
        return (int)ltrim($permissionName, self::PERMISSION_PREFIX);
    }

    // 根据权限名称获取菜单Id
    public static function getIdsByPermissionNames($names)
    {
        if (!is_array($names) || empty($names)) {
            return array();
        }

        $ids = array();
        foreach ($names as $name) {
            $ids[] = (int)ltrim($name, self::PERMISSION_PREFIX);
        }
        return $ids;
    }

    // 根据菜单Id获取权限名称
    public static function getPermissionNamesByIds($ids)
    {
        if (!is_array($ids) || empty($ids)) {
            return array();
        }

        $names = array();
        foreach ($ids as $id) {
            $names[] = self::PERMISSION_PREFIX . $id;
        }
        return $names;
    }
}
