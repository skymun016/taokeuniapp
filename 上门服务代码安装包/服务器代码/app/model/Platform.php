<?php

namespace app\model;

use think\Model;
use think\facade\Db;

class Platform extends Model
{

    protected $connection = 'mysql';

    protected $pk = 'id';

    protected $name = 'platform';

    public static function datainitial()
    {
        $data =  self::create([
            'title' => '默认平台',
            'endtime' => 0,
            'sort' => 100,
            'status' => 1
        ]);

        $data->id;
    }

    public static function getInfo($id)
    {
        $data = self::find($id);
        if ($data) {
            $data = $data->toArray();
            $data['pic'] = toimg($data['pic']);
            $data['endtime'] = time_format($data['endtime']);
        }
        return $data;
    }
    public static function getScheme($id)
    {
        $data = self::find($id);
        if ($data) {
            $data = $data->toArray();
            if ($data['scheme_id']) {
                $PlatformScheme = PlatformScheme::find($data['scheme_id']);
                if ($PlatformScheme) {
                    if ($PlatformScheme->is_allrole == 1) {
                        $ret = 'all';
                    } else {
                        return $PlatformScheme->access;
                    }
                }
            } else {
                $ret = 'all';
            }
        }
        return $ret;
    }
}
