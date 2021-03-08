<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;

/**
 * 操作员
 * Class Operator
 * @package App\Models
 *
 * @property int id
 * @property string user_name 用户名
 * @property string password 密码
 * @property string real_name 真实姓名
 * @property string mobile 手机号
 * @property string email 邮箱
 * @property string status 状态 1:启用 2:停用
 * @property int creator_id 创建者ID
 * @property string creator_name 创建者姓名
 * @property string created_at 创建时间
 * @property string updated_at 更新时间
 */
class Operator extends Authenticatable implements JWTSubject
{
    use Notifiable;
    use HasRoles;

    protected $table = 'operators';

    protected $hidden = [
        'password'
    ];

    protected $guard_name = 'web';

    // 状态 1:启用 2:停用
    const STATUS_START = 1;
    const STATUS_STOP  = 2;
    public static $statusOptions = [
        ['value' => self::STATUS_START, 'label' => '启用'],
        ['value' => self::STATUS_STOP, 'label' => '停用'],
    ];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public static function doAdd($params)
    {
        $self               = new self();
        $self->user_name    = $params['user_name'];
        $self->password     = Hash::make($params['password']);
        $self->real_name    = $params['real_name'] ?? '';
        $self->mobile       = $params['mobile'] ?? '';
        $self->email        = $params['email'] ?? '';
        $self->creator_id   = $params['creator_id'];
        $self->creator_name = $params['creator_name'];
        $self->status       = self::STATUS_START;
        $self->save();
        return $self;
    }

    public function doUpdate($params)
    {
        $this->real_name = $params['real_name'];
        $this->mobile    = $params['mobile'];
        $this->email     = $params['email'];
        return $this->save();
    }

    public function initialPassword()
    {
        $password = env('INITIAL_PASSWORD', 'paota.123456');

        $this->password = Hash::make($password);
        return $this->save();
    }

    public function doStart() {
        $this->status = self::STATUS_START;
        return $this->save();
    }

    public function doStop() {
        $this->status = self::STATUS_STOP;
        return $this->save();
    }

}
