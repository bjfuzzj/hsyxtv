<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Banner extends Model
{
    protected $table = 'banner';

    const TYPE_NORMAL = 'index_banner';

    
    const CLICK_TYPE_0   = 0;//无点击效果
    const CLICK_TYPE_1   = 1;//点击跳转图片
    const CLICK_TYPE_2   = 2;//点击跳转链接
    const CLICK_TYPE_3   = 3;//点击跳转到直播
    const CLICK_TYPE_4   = 4;//点击跳转拼团产品页
    const CLICK_TYPE_5   = 5;//点击跳转普通产品页
    public static $clickTypeDesc = [
        self::CLICK_TYPE_0  => '无点击效果',
        self::CLICK_TYPE_1  => '点击跳转图片',
        self::CLICK_TYPE_2  => '点击跳转链接',
        self::CLICK_TYPE_3  => '点击跳转直播',
        self::CLICK_TYPE_4  => '点击跳转拼团产品',
        self::CLICK_TYPE_5  => '点击跳转普通产品',
    ];
    public static $clickTypeOptions = [
        ['value' => self::CLICK_TYPE_0, 'label' => '无点击效果','tip'=>'(纯展示，点击没效果)'],
        ['value' => self::CLICK_TYPE_1, 'label' => '点击跳转图片','tip'=>'(点击查看图片)'],
        ['value' => self::CLICK_TYPE_2, 'label' => '点击跳转链接','tip'=>'(点击跳转到页面链接)'],
        ['value' => self::CLICK_TYPE_3, 'label' => '点击跳转直播','tip'=>'(点击跳转到直播)'],
        ['value' => self::CLICK_TYPE_4, 'label' => '点击跳转拼团产品','tip'=>'(点击跳转到拼团产品)'],
        ['value' => self::CLICK_TYPE_5, 'label' => '点击跳转到普通产品','tip'=>'(点击跳转到普通产品)'],
    ];

    //状态 0:下线 1:上线
    const STATUS_OFFLINE = 0;
    const STATUS_ONLINE  = 1;
    public static $statusDesc    = [
        self::STATUS_OFFLINE    => '已下线',
        self::STATUS_ONLINE  => '已上线',
    ];

    public static function getList($params)
    {
        $sortColumn = $params['sort_column'] ?? 'id';
        $sortType   = $params['sort_type'] ?? 'desc';
        $size       = $params['size'] ?? 20;

        $query = DB::table('banner as ba')
            ->selectRaw('*');
        $queryRes = $query->orderBy("ba.{$sortColumn}", $sortType)->paginate($size);

        $list = [];
        foreach ($queryRes->items() as $item) {
            $itemArr                = collect($item)->toArray();
            $itemArr['clicktype_desc'] = self::$clickTypeDesc[$item->clicktype] ?? '';
            $itemArr['status_desc'] = self::$statusDesc[$item->status] ?? '';
            $list[]                 = $itemArr;
        }

        return [
            'list'         => $list,
            'current_page' => $queryRes->currentPage(),
            'total'        => $queryRes->total(),
        ];
    }


    public static function add($data)
    {
        DB::beginTransaction();
        try {
            $self = new self();
            $self->setData($data);
            $self->save();
            DB::commit();
            return $self;
        } catch (\Throwable $e) {
            DB::rollBack();
            return null;
        }
    }

    public function updateData($data)
    {
        DB::beginTransaction();
        try {
            $this->setData($data);
            $this->save();
            DB::commit();
            return $this;
        } catch (\Throwable $e) {
            DB::rollBack();
            return null;
        }
    }

    private function setData($data)
    {
        $this->type           = $data['type'] ?? self::TYPE_NORMAL;
        $this->content          = $data['content'] ?? '';
        $this->clicktype         = $data['clicktype'] ?? self::CLICK_TYPE_0;
        $this->clickcontent  = $data['clickcontent'] ?? '';
    }

    public function setOffLine(){
        if($this->isOffline()) {
            return false;
        }
        $this->status = self::STATUS_OFFLINE;
        return $this->save();
    }

    public function setOnLine(){
        if($this->isOnline()) {
            return false;
        }
        $this->status = self::STATUS_ONLINE;
        return $this->save();
    }

    private function isOnline()
    {
        return self::STATUS_ONLINE == $this->status;
    }

    private function isOffline()
    {
        return self::STATUS_OFFLINE == $this->status;
    }
}

