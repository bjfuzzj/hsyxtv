<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\TvUser;
use App\Models\DGroup;


class changeOlStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'script:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '将物流表的第一条记录改成默认地址';

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
        $id     = 2;
        $dgroup = DGroup::find($id);
        var_dump($dgroup);


    }
}
