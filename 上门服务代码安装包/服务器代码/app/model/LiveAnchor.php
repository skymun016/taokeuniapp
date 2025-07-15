<?php

namespace app\model;

use think\Model;

class LiveAnchor extends Model
{

    protected $connection = 'mysql';

    protected $pk = 'id';

    protected $name = 'live_anchor';

    public static function getpagearray()
    {
        $data = self::field('id,name')->where(['weid' => weid(), 'status' => 1])
            ->order('sort asc')
            ->select()->toArray();

        foreach ($data as $k => $v) {
            $array[$k]['val'] = $v['id'];
            $array[$k]['key'] = $v['name'];
        }
        return $array;
    }

    public static function getAnchor($id)
    {
        $ret = self::find($id);
        if (!empty($ret)) {
            $ret = $ret->toArray();
        }

        return $ret;
    }
}
