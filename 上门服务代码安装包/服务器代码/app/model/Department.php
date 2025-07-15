<?php

namespace app\model;

use think\Model;

class Department extends Model
{

    protected $connection = 'mysql';

    protected $pk = 'id';

    protected $name = 'department';

    public static function get_name($id)
    {
        $weid = weid();
        $list = self::field('id,title')->where(['weid' => $weid])
            ->select()->toArray();

        foreach ($list as $k => $v) {
            $data[$v['id']] = $v;
        }

        if (!isset($data[$id]['title'])) {
            return null;
        }
        return $data[$id]['title'];

    }

    public static function getarray()
    {
        $data = self::field('id,title')->where(['weid' => weid()])->order('sort asc')->select()->toArray();
        $datalist = [];
        foreach ($data as $vo) {
            $datalist[$vo['id']] = $vo['title'];
        }
        return $datalist;
    }

    public static function getpcarray()
    {
        $data = self::field('id,title')->where(['weid' => weid()])->order('sort asc')->select()->toArray();
        $datalist = [];
        foreach ($data as $key => $vo) {
            $datalist[$key]['val'] = $vo['id'];
			$datalist[$key]['key'] = $vo['title'];
        }
        return $datalist;
    }
}
