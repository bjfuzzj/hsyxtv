<?php
/*
 * @Author: bjfuzzj
 * @Date: 2021-11-29 12:07:55
 * @LastEditTime: 2021-11-29 12:31:12
 * @LastEditors: bjfuzzj
 * @Description: 
 * @FilePath: /tv/app/Console/Commands/InitPinYin.php
 * 寻找内心的安静
 */

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Overtrue\Pinyin\Pinyin;
use App\Models\Media;

class InitPinYin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'script:initPY';

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
        $pinyin = app('pinyin');
        $allMedias = Media::all();
        foreach($allMedias as $media)
        {
            if(!isset($media->search_name) || empty($media->search_name))
            // if(!empty($media->search_name))
            {
                // $newName = $pinyin->abbr($media->name, PINYIN_KEEP_ENGLISH);
                $newName = strtoupper($pinyin->abbr($media->name,PINYIN_KEEP_NUMBER));
                $media->search_name = $newName;
                $media->save();
            }
        }

        

    }
}
