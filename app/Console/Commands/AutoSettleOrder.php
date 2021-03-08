<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\OrderLog;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AutoSettleOrder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crontab:auto_settle_order';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        Log::info('===系统自动结算 start===');

        $time = now()->addDays(-3)->toDateTimeString();

        $results = DB::select("select user_id, sum(rebate_amount) as amount  from distributor_details
where order_id in (select id from orders where product_type = 1 and status in (30, 40) and updated_at < :time) and status = 1 group by user_id", ['time' => $time]);

        if (!empty($results)) {
            DB::update("update distributor_incomes i
            inner join (
                select user_id, sum(rebate_amount) as amount  from distributor_details
                where order_id in (select id from orders where product_type  = 1 and status in (30, 40)  and updated_at < :time) and status = 1 group by user_id
            ) t on i.`user_id` = t.user_id set i.income_usable = (i.income_usable + t.amount), i.`income_freeze` = (i.income_freeze - t.amount)", ['time' => $time]);

            DB::update("update distributor_details set status = 2 where `order_id` in (select id from orders where `product_type`  = 1 and `status` in (30, 40)  and updated_at < :time) and status = 1", ['time' => $time]);
        }

        Log::info('===系统自动确认完成 end===');
    }
}
