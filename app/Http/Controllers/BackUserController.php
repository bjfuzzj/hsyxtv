<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\DistributorDetail;
use App\Exports\UserExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\UserTeamExport;
use App\Exports\UserIncomeExport;

class BackUserController extends Controller
{
    public function index(Request $request)
    {
        $nickName = $request->input('user_name', '');
        $type     = $request->input('type', '');
        $level    = $request->input('level', '');
        $size     = $request->input('size', 50);
        $page     = $request->input('page', 1);

        $query = DB::table('wx_users as wu')
            ->leftJoin('users as u', 'u.id', '=', 'wu.user_id')
            ->leftJoin('user_relationships as uship', 'wu.id', '=', 'uship.wx_user_id')
            ->leftJoin('users as dwu', 'uship.parent_id', '=', 'dwu.id')
            ->leftJoin('distributor_incomes as di', 'u.id', '=', 'di.user_id')
            ->selectRaw('dwu.nick_name as d_nick_name,dwu.real_name as d_real_name,wu.*, u.real_name, u.mobile, u.is_distributor, u.is_advance_sale, u.advance_sale_level,
             u.distributor_level, di.income_total, di.income_used, di.income_usable, di.income_freeze, di.person_taxes');
        if (!empty($nickName)) {
            $query->where(function ($query) use ($nickName) {
                $query->where('u.nick_name', 'like', '%' . $nickName . '%')
                    ->orWhere('u.real_name', 'like', '%' . $nickName . '%');
            });
        }
        if (!empty($type)) {
            $query->where('u.is_distributor', User::DISTRIBUTOR_YES);
            $query->where('u.distributor_level', $type);
        }
        // if (1 == $type) {
        //     $query->where('u.is_advance_sale', User::ADVANCE_SALE_YES);
        //     if (!empty($level)) {
        //         $query->where('u.advance_sale_level', $level);
        //     }
        // } else if (2 == $type) {
        //     $query->where('u.is_distributor', User::DISTRIBUTOR_YES);
        //     if (!empty($level)) {
        //         $query->where('u.distributor_level', $level);
        //     }
        // }

        $queryRes = $query->paginate($size, ['*'], 'page', $page);
        $userList = [];
        foreach ($queryRes->items() as $item) {
            $userList[] = [
                'id'                     => $item->id,
                'uid'                    => $item->user_id,
                'nick_name'              => $item->nick_name,
                'real_name'              => $item->real_name,
                'head_image'             => $item->avatar,
                'mobile'                 => $item->mobile,
                'is_distributor'         => $item->is_distributor,
                'distributor_level_desc' => User::$distributorLevelDesc[$item->distributor_level] ?? '',
                'distributor_level'      => $item->distributor_level,
                'is_advance_sale'        => $item->is_advance_sale,
                'advance_sale_level'     => User::$advanceSaleLevelDesc[$item->advance_sale_level] ?? '',
                'income_total'           => $item->income_total,
                'income_used'            => $item->income_used,
                'income_usable'          => $item->income_usable,
                'income_freeze'          => $item->income_freeze,
                'person_taxes'           => $item->person_taxes,
                'created_at'             => $item->created_at,
                'd_nick_name'            => $item->d_nick_name,
                'd_real_name'            => $item->d_real_name,
            ];
        }

        // $userTypeOptions = [
        //     ['value' => 0, 'label' => '所有用户', 'level_options' => []],
        //     ['value' => 1, 'label' => '预售用户', 'level_options' => User::$advanceSaleLevelOptions],
        //     ['value' => 2, 'label' => '分享用户', 'level_options' => User::$distributorLevelOptions],
        // ];
        $userTypeOptions = User::$distributorLevelOptions;
        return $this->outSuccessResult([
            'user_list'    => $userList,
            'type_options' => $userTypeOptions,
            'current_page' => $queryRes->currentPage(),
            'per_page'     => $queryRes->perPage(),
            'total'        => $queryRes->total(),
        ]);
    }


    public function detail(Request $request)
    {
        $params = $this->validate($request, [
            'userId' => 'nullable|integer',
            'page'   => 'nullable|integer',
            'size'   => 'nullable|integer',
        ]);
        $page   = $params['page'] ?? 1;
        $size   = $params['size'] ?? 50;

        $query = DB::table('distributor_details as d')
            ->join('users as u', 'd.user_id', '=', 'u.id')
            ->join('products as p', 'p.id', '=', 'd.product_id')
            ->join('orders as o', 'o.id', '=', 'd.order_id')
            ->join('users as ou', 'o.user_id', '=', 'ou.id')
            ->join('order_logistics as ol', 'o.id', '=', 'ol.order_id')
            ->selectRaw('d.user_id as uid,u.nick_name,p.title as product_title, o.amount_paid, o.paid_time as order_time, d.buy_num, 
                d.rebate_amount, d.status, d.invalid_reason, ol.receiver_name, ol.receiver_mobile,
                ou.id as ouid,ou.nick_name as ounick_name')
            ->where('d.user_id', $params['userId'])
            ->orderByDesc('d.id');
        if (!empty($params['status'])) {
            $query->where('d.status', $params['status']);
        }
        $queryRes   = $query->paginate($size, ['*'], 'page', $page);
        $incomeList = [];
        foreach ($queryRes->items() as $row) {
            $item                = collect($row)->toArray();
            $orderTime           = Carbon::parse($row->order_time);
            $item['month']       = $orderTime->format('m');
            $item['order_time']  = $orderTime->format('Y-m-d H:i');
            $item['amount_paid'] = round($row->amount_paid, 2);
            $item['status_desc'] = DistributorDetail::$statusDesc[$row->status] ?? '';
            if (!empty($row->receiver_mobile)) {
                $item['receiver_mobile'] = substr_replace($row->receiver_mobile, '****', 3, 4);
            }
            unset($item['status']);
            $incomeList[] = $item;
        }

        return $this->outSuccessResult([
            'records'      => $incomeList,
            'current_page' => $queryRes->currentPage(),
            'per_page'     => $queryRes->perPage(),
            'total'        => $queryRes->total(),
        ]);

    }


    public function team(Request $request)
    {
        $params = $this->validate($request, [
            'userId' => 'nullable|integer',
            'page'   => 'nullable|integer',
            'size'   => 'nullable|integer',
        ]);
        $userId = $params['userId'];
        $type   = $params['type'] ?? 'all';
        $page   = $params['page'] ?? 1;
        $size   = $params['size'] ?? 20;

        $query = DB::table('user_relationships as ur')
            ->leftJoin('distributor_details as d', function ($join) {
                $join->where('ur.user_id', '>', 0)->on('ur.parent_id', '=', 'd.user_id')->on('ur.user_id', '=', 'd.source_user_id');
            })
            ->selectRaw('ur.id, sum(d.rebate_amount) as total_rebate')
            ->where('ur.parent_id', $userId);
        if ('month' == $type) {
            $beginDate = date('Y-m-01');
            $endDate   = today()->addMonth()->format('Y-m-01');
            $query->where('d.created_at', '>=', $beginDate)->where('d.created_at', '<', $endDate);
        }
        $paginateRes = $query->groupBy('ur.id')->orderByDesc('total_rebate')->orderByDesc('id')->paginate($size, ['*'], 'page', $page);

        //$queryRes   = $query->paginate($size, ['*'], 'page', $page);
        $rebateAmounts = [];
        foreach ($paginateRes->items() as $item) {
            $rebateAmounts[$item->id] = $item->total_rebate;
        }
        if (empty($rebateAmounts)) {
            return $this->outSuccessResult([
                'records'      => [],
                'current_page' => 0,
                'per_page'     => 0,
                'total'        => 0,
            ]);
        }

        $queryRes = DB::table('user_relationships as ur')
            ->join('wx_users as wu', 'ur.wx_user_id', '=', 'wu.id')
            ->selectRaw('ur.id, wu.nick_name, wu.avatar, ur.created_at')
            ->whereIn('ur.id', array_keys($rebateAmounts))
            ->get();
        $result   = [];
        foreach ($queryRes as $item) {
            $result[$item->id] = collect($item)->toArray();
        }

        $teamList = [];
        foreach ($rebateAmounts as $id => $totalRebate) {
            if (!isset($result[$id])) {
                continue;
            }

            $teamList[] = [
                'nick_name'    => $result[$id]['nick_name'],
                'head_image'   => $result[$id]['avatar'],
                'total_rebate' => $totalRebate,
                'join_time'    => Carbon::parse($result[$id]['created_at'])->format('Y-m-d H:i')
            ];
        }

        return $this->outSuccessResult([
            'records'      => $teamList,
            'current_page' => $paginateRes->currentPage(),
            'per_page'     => $paginateRes->perPage(),
            'total'        => $paginateRes->total(),
        ]);

    }


    public function exportExcel(Request $request){
        ini_set('memory_limit','3000M');
        $nickName = $request->input('user_name', '');
        $type     = $request->input('type', '');
        $level    = $request->input('level', '');

        $query = DB::table('wx_users as wu')
            ->leftJoin('users as u', 'u.id', '=', 'wu.user_id')
            ->leftJoin('user_relationships as uship', 'wu.id', '=', 'uship.wx_user_id')
            ->leftJoin('users as dwu', 'uship.parent_id', '=', 'dwu.id')
            ->leftJoin('distributor_incomes as di', 'u.id', '=', 'di.user_id')
            ->selectRaw('dwu.nick_name as d_nick_name,dwu.real_name as d_real_name,wu.*, u.real_name, u.mobile, u.is_distributor, u.is_advance_sale, u.advance_sale_level,
             u.distributor_level, di.income_total, di.income_used, di.income_usable, di.income_freeze, di.person_taxes');
        if (!empty($nickName)) {
            $query->where(function ($query) use ($nickName) {
                $query->where('u.nick_name', 'like', '%' . $nickName . '%')
                    ->orWhere('u.real_name', 'like', '%' . $nickName . '%');
            });
        }
        if (1 == $type) {
            $query->where('u.is_advance_sale', User::ADVANCE_SALE_YES);
            if (!empty($level)) {
                $query->where('u.advance_sale_level', $level);
            }
        } else if (2 == $type) {
            $query->where('u.is_distributor', User::DISTRIBUTOR_YES);
            if (!empty($level)) {
                $query->where('u.distributor_level', $level);
            }
        }

        //$queryRes = $query->paginate(10000, ['*'], 'page', 1);
        $queryRes = $query->get();
        $userList = [];
        foreach ($queryRes as $item) {
            $userList[] = [
                'id'                 => $item->id,
                'uid'                => $item->user_id,
                'nick_name'          => $item->nick_name,
                'real_name'          => $item->real_name,
                'head_image'         => $item->avatar,
                'mobile'             => $item->mobile,
                'is_distributor'     => $item->is_distributor,
                'distributor_level'  => User::$distributorLevelDesc[$item->distributor_level] ?? '',
                'is_advance_sale'    => $item->is_advance_sale,
                'advance_sale_level' => User::$advanceSaleLevelDesc[$item->advance_sale_level] ?? '',
                'income_total'       => $item->income_total,
                'income_used'        => $item->income_used,
                'income_usable'      => $item->income_usable,
                'income_freeze'      => $item->income_freeze,
                'person_taxes'       => $item->person_taxes,
                'created_at'         => $item->created_at,
                'd_nick_name'        => $item->d_nick_name,
                'd_real_name'        => $item->d_real_name,
            ];
        }
        return Excel::download(new UserExport($userList), '客户记录.xlsx');
    }


    public function exportTeamExcel(Request $request){
        $params = $this->validate($request, [
            'userId' => 'nullable|integer',
            'page'   => 'nullable|integer',
            'size'   => 'nullable|integer',
        ]);
        $userId = $params['userId'];
        $type   = $params['type'] ?? 'all';
        $page   = $params['page'] ?? 1;
        $size   = $params['size'] ?? 10000;

        $query = DB::table('user_relationships as ur')
            ->leftJoin('distributor_details as d', function ($join) {
                $join->where('ur.user_id', '>', 0)->on('ur.parent_id', '=', 'd.user_id')->on('ur.user_id', '=', 'd.source_user_id');
            })
            ->selectRaw('ur.id, sum(d.rebate_amount) as total_rebate')
            ->where('ur.parent_id', $userId);
        if ('month' == $type) {
            $beginDate = date('Y-m-01');
            $endDate   = today()->addMonth()->format('Y-m-01');
            $query->where('d.created_at', '>=', $beginDate)->where('d.created_at', '<', $endDate);
        }
        $paginateRes = $query->groupBy('ur.id')->orderByDesc('total_rebate')->orderByDesc('id')->paginate($size, ['*'], 'page', $page);

        //$queryRes   = $query->paginate($size, ['*'], 'page', $page);
        $rebateAmounts = [];
        foreach ($paginateRes->items() as $item) {
            $rebateAmounts[$item->id] = $item->total_rebate;
        }
        if (empty($rebateAmounts)) {
            return $this->outSuccessResult([
                'records'      => [],
                'current_page' => 0,
                'per_page'     => 0,
                'total'        => 0,
            ]);
        }

        $queryRes = DB::table('user_relationships as ur')
            ->join('wx_users as wu', 'ur.wx_user_id', '=', 'wu.id')
            ->selectRaw('ur.id, wu.nick_name, wu.avatar, ur.created_at')
            ->whereIn('ur.id', array_keys($rebateAmounts))
            ->get();
        $result   = [];
        foreach ($queryRes as $item) {
            $result[$item->id] = collect($item)->toArray();
        }

        $teamList = [];
        foreach ($rebateAmounts as $id => $totalRebate) {
            if (!isset($result[$id])) {
                continue;
            }

            $teamList[] = [
                'nick_name'    => $result[$id]['nick_name'],
                'head_image'   => $result[$id]['avatar'],
                'total_rebate' => $totalRebate,
                'join_time'    => Carbon::parse($result[$id]['created_at'])->format('Y-m-d H:i')
            ];
        }
        return Excel::download(new UserTeamExport($teamList), '团队记录.xlsx');
    }

    public function exportIncomeExcel(Request $request){
        $params = $this->validate($request, [
            'userId' => 'nullable|integer',
            'page'   => 'nullable|integer',
            'size'   => 'nullable|integer',
        ]);
        $page   = $params['page'] ?? 1;
        $size   = $params['size'] ?? 10000;

        $query = DB::table('distributor_details as d')
            ->join('users as u', 'd.user_id', '=', 'u.id')
            ->join('products as p', 'p.id', '=', 'd.product_id')
            ->join('orders as o', 'o.id', '=', 'd.order_id')
            ->join('users as ou', 'o.user_id', '=', 'ou.id')
            ->join('order_logistics as ol', 'o.id', '=', 'ol.order_id')
            ->selectRaw('d.user_id as uid,u.nick_name,p.title as product_title, o.amount_paid, o.paid_time as order_time, d.buy_num, 
                d.rebate_amount, d.status, d.invalid_reason, ol.receiver_name, ol.receiver_mobile,
                ou.id as ouid,ou.nick_name as ounick_name')
            ->where('d.user_id', $params['userId'])
            ->orderByDesc('d.id');
        if (!empty($params['status'])) {
            $query->where('d.status', $params['status']);
        }
        $queryRes   = $query->paginate($size, ['*'], 'page', $page);
        $incomeList = [];
        foreach ($queryRes->items() as $row) {
            $item                = collect($row)->toArray();
            $orderTime           = Carbon::parse($row->order_time);
            $item['month']       = $orderTime->format('m');
            $item['order_time']  = $orderTime->format('Y-m-d H:i');
            $item['amount_paid'] = round($row->amount_paid, 2);
            $item['status_desc'] = DistributorDetail::$statusDesc[$row->status] ?? '';
            if (!empty($row->receiver_mobile)) {
                $item['receiver_mobile'] = substr_replace($row->receiver_mobile, '****', 3, 4);
            }
            unset($item['status']);
            $incomeList[] = $item;
        }
        return Excel::download(new UserIncomeExport($incomeList), '团队收入记录.xlsx');
    }

    public function doSave(Request $request){
        $params = $this->validate($request, [
            'uid'               => 'required|integer',
            'distributor_level' => 'required|integer',
        ],[
            '.*' => '保存数据失败[-1]',
        ]);
        $user = User::find($params['uid']);
        if (!$user instanceof User) {
            return $this->outErrorResult(-1, '保存数据失败[-2]');
        }
        if(!$user->setDistributorLevel($params['distributor_level'])) {
            return $this->outErrorResult(-1, '保存数据失败[-3]');
        }
        return $this->outSuccessResult(['id'=>$user->id,'distributor_level'=>$user->distributor_level]);
        
    }
}
