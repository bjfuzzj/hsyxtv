<?php
/*
 * @Author: your name
 * @Date: 2021-06-01 00:20:20
 * @LastEditTime: 2021-06-01 00:24:51
 * @LastEditors: Please set LastEditors
 * @Description: In User Settings Edit
 * @FilePath: /tv/app/Console/Commands/SyncMedia.php
 */

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Media;
use App\Models\VMedia;

class SyncMedia extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crontab:syncMedia';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '同步 media 到 vmedia';

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
        $allMedia = Media::all();
        foreach($allMedia as $media){
            $mediaId = $media->d_id;
            $code = $media->id_code;
            $new_media = VMedia::where('vid_code',$code)->where('vmedia_id',$mediaId)->first();
            if(!$new_media instanceof VMedia){
                $new = new VMedia();
                $new->vid_code= $code;
                $new->vmedia_id=$mediaId;
                $new->save();
            }
        }
    }
}
