<?php

namespace App\Imports;

use App\Models\SubMedia;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\Models\Media;
use App\Helper\Codec;

class MediaImport implements ToCollection, WithHeadingRow
{
    /**
     * @param Collection $collection
     */
    public function collection(Collection $rows)
    {
        $tvs      = [];
        $newTv    = [];
        $isHeadTV = false;
        foreach ($rows as $i => $row) {

            if (!empty($row['name'])) {
                if ($isHeadTV) {
                    array_push($tvs, $newTv);
                    $isHeadTV = false;
                }
                $newTv            = [];
                $newTv['name']    = $row['name'];
                $newTv['kind']    = Media::KIND_TV;
                $newTv['id_code'] = '';
                $newTv['subs']    = [];
                $sub              = [];
                $sub['sub_name']  = $row['sub_name'];
                $sub['number']    = $row['number'];
                $sub['md5']       = $row['md5'];
                $sub['kong']      = $row['kong'];
                array_push($newTv['subs'], $sub);
                $isHeadTV = true;
            } else {
                $sub             = [];
                $sub['sub_name'] = $row['sub_name'];
                $sub['number']   = $row['number'];
                $sub['md5']      = $row['md5'];
                $sub['kong']     = $row['kong'];
                array_push($newTv['subs'], $sub);
            }
        }
        if ($isHeadTV) {
            array_push($tvs, $newTv);
        }

        //开始操作

        foreach ($tvs as $tv) {
            $createTv             = new Media();
            $createTv->name       = $tv['name'];
            $createTv->kind       = $tv['kind'];
            $num                  = count($tv['subs']);
            $createTv->newest_num = $num;
            $createTv->total_num  = $num;
            $createTv->save();
            $d_id              = $createTv->d_id;
            $createTv->id_code = Codec::encodeId($d_id);
            $createTv->url_1   = "/show/" . $createTv->id_code . ".html";
            $createTv->save();

            foreach ($tv['subs'] as $key => $subTv) {
                $subCreateTv           = new SubMedia();
                $subCreateTv->media_id = $d_id;
                $subCreateTv->name     = $subTv['sub_name'];
                $subCreateTv->md5      = $subTv['md5'];

                $info                  = explode('.', $subTv['number']);
                $sourceId              = $info[0];
                $extension             = $info[1];
                $kong                  = $subTv['kong'];
                $subCreateTv->sourceid = $sourceId;
                $subCreateTv->now_num  = ($key + 1);
                $subCreateTv->save();
                $idCode               = Codec::encodeId($subCreateTv->d_id);
                $subCreateTv->id_code = $idCode;
                $subCreateTv->url     = 'https://v.static.yiqiqw.com/hsyx/' . $kong . '/' . $idCode . "." . $extension;
                $subCreateTv->save();
            }

        }
    }

    public function headingRow(): int
    {
        return 1;
    }
}
