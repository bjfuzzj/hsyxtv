<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Bluerhinos\phpMQTT;
use Illuminate\Support\Facades\Log;

class TestMQTT extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'script:testmq';

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
     */
    public function handle()
    {

        $result = [];
        $result['code'] = 200;
        $result['msg'] = 200;
        $msg_info = [
            [
                'id'=>'2234567890',
                'type'=>'2',
                'status'=>'1',
                'start'=>'2021-04-20 11:38:00',
                'end'=>'2021-06-29 17:15:00',
                'duration'=>'10',
                'content'=>'报告大王，我是游字，我是游字！',
                'url'=>'https://v.static.yiqiqw.com/pic/e2d8dc9fddabe93286ca7508320e83b0.jpg',
            ],
            [
                'id'=>'2234567891',
                'type'=>'2',
                'status'=>'1',
                'start'=>'2021-04-20 11:38:00',
                'end'=>'2021-06-29 17:15:00',
                'duration'=>'10',
                'content'=>'报告大王，我是游字，我是游字！',
                'url'=>'https://v.static.yiqiqw.com/pic/785d9ddb8c551e7e9bd770070b4cad24.jpg',
            ],
            [
                'id'=>'2234567892',
                'type'=>'2',
                'status'=>'1',
                'start'=>'2021-04-20 11:38:00',
                'end'=>'2021-06-29 17:15:00',
                'duration'=>'10',
                'content'=>'报告大王，我是游字，我是游字！',
                'url'=>'https://v.static.yiqiqw.com/pic/3975bba6654ff3c7d0540d94273ec137.jpg',
            ]
        ];

        $result['data'] = $msg_info;
        $this->sendEmqxMsg("SYSORDER", json_encode($result, 320)); 
    }


    public function sendEmqxMsg($topic, $msg)
    {
        //发送mqtt消息
        $server = env('MQTT_HOST', '39.97.164.69');
        $port = env('MQTT_PORT', '1883');
        $username = env('MQTT_ADMIN', 'admin');
        $password = env('MQTT_PASSWORD', '');
        $client_id = uniqid('mqtt_');
        $mqtt = new phpMQTT($server, $port, $client_id);
        //如果创建链接成功
        if ($mqtt->connect(true, null, $username, $password)) {
            Log::info('链接成功=====topic=>'.$topic);
            Log::info(print_r($mqtt,1));
            $res = $mqtt->publish($topic, $msg, 1);
            Log::info(print_r($mqtt,1));
            $mqtt->close();    //发送后关闭链接
        } else {
            Log::debug(json_encode($mqtt, '320'));
        }
    }
}