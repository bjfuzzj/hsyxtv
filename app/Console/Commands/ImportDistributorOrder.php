<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\UserRelationship;
use Illuminate\Console\Command;
use App\Models\WxUser;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ImportDistributorOrder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'script:import_distributor_order';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     * @throws \InvalidArgumentException
     */
    public function handle()
    {
        ini_set('memory_limit', '128M');

        $orderArr = DB::table('orders as o')->join('users as u', 'o.user_id', '=', 'u.id')
            ->selectRaw('o.user_id, u.nick_name, sum(o.buy_num) as buy_cnt')
            ->whereIn('status', [20, 30, 40])->groupBy('o.user_id')->get();


        $relationshipArr = DB::table('user_relationships as r')->join('users as u', 'r.user_id', '=', 'u.id')
            ->selectRaw('r.parent_id, r.user_id, u.nick_name')
            ->whereRaw('r.user_id > r.parent_id')->where('r.user_id', '<>', 0)->where('r.parent_id', '<>', 0)
            ->orderBy('r.parent_id', 'desc')->get();

        $oldRelationshipArr = DB::table('user_relationships as r')->join('users as u', 'r.user_id', '=', 'u.id')
            ->selectRaw('r.parent_id, r.user_id, u.nick_name')
            ->whereRaw('r.parent_id > r.user_id')->where('r.user_id', '<>', 0)->where('r.parent_id', '<>', 0)
            ->orderBy('r.parent_id', 'desc')->get();

        $orderList = [];
        foreach ($orderArr as $order) {
            $orderList[$order->user_id] = $order;
        }

        $data = [];
        foreach ($orderArr as $order) {
            $data[$order->user_id] = [
                'user_id'   => $order->user_id,
                'nick_name' => $order->nick_name,
                'buy_cnt'   => $order->buy_cnt,
                'child_ids' => [$order->user_id]
            ];
        }

        foreach ($relationshipArr as $relationship) {
            $userId   = $relationship->user_id;
            $parentId = $relationship->parent_id;
            if (empty($data[$userId])) {
                continue;
            }

            if (isset($data[$parentId])) {
                $data[$parentId]['buy_cnt']     = round($data[$parentId]['buy_cnt'] + $data[$userId]['buy_cnt'], 2);
                $data[$parentId]['child_ids'][] = $userId;
            } else {
                $data[$parentId] = [
                    'user_id'   => $userId,
                    'nick_name' => $relationship->nick_name,
                    'buy_cnt'   => $data[$userId]['buy_cnt'],
                    'child_ids' => [$userId]
                ];
            }
        }

        foreach ($oldRelationshipArr as $relationship) {
            $userId   = $relationship->user_id;
            $parentId = $relationship->parent_id;
            if (empty($data[$userId])) {
                continue;
            }

            if (isset($data[$parentId])) {
                $data[$parentId]['buy_cnt']     = round($data[$parentId]['buy_cnt'] + $data[$userId]['buy_cnt'], 2);
                $data[$parentId]['child_ids'][] = $userId;
            } else {
                $data[$parentId] = [
                    'user_id'   => $userId,
                    'nick_name' => $relationship->nick_name,
                    'buy_cnt'   => $data[$userId]['buy_cnt'],
                    'child_ids' => [$userId]
                ];
            }
        }

        $orderData = [];
        $orderData[] = ['用户ID', '用户昵称', '销售总量', '销售团队'];
        foreach ($data as $item) {
            if ($item['buy_cnt'] < 500) {
                continue;
            }

            $childData = [];
            foreach ($item['child_ids'] as $childId) {
                if ($item['user_id'] == $childId && !empty($orderList[$childId])) {
                    $childData[] = "{$orderList[$childId]->nick_name}:{$orderList[$childId]->buy_cnt}";
                } else if (!empty($data[$childId]['nick_name']) && !empty($data[$childId]['buy_cnt'])) {
                    $childData[] = "{$data[$childId]['nick_name']}:{$data[$childId]['buy_cnt']}";
                }
            }

            $orderData[] = [
                $item['user_id'],
                $item['nick_name'],
                $item['buy_cnt'],
                implode(';;', $childData)
            ];
        }

        $fp      = fopen('public/order.csv', 'a');
        $content = '';
        foreach ($orderData as $v) {
            $content .= implode(',', $v) . PHP_EOL;
        }
        fwrite($fp, $content);
        fclose($fp);
    }
}
