<?php

namespace app\model;

use think\Model;

class GoodsQuantityUnit extends Model
{
    protected $connection = 'mysql';

    protected $pk = 'id';

    protected $name = 'goods_quantity_unit';

    public static function getpcarray($ptype = 1)
    {
        $data = self::field('id,title')->where(['weid' => weid(), 'ptype' => $ptype])->select()->toArray();

        $datalist = [];
        foreach ($data as $key => $vo) {
            $datalist[$key]['val'] = $vo['title'];
            $datalist[$key]['key'] = $vo['title'];
        }
        return $datalist;
    }

    public static function datainitial($ptype = 1)
    {
        $weid = weid();

        $data[1] = [
            [
                'title' => '件',
            ], [
                'title' => '套',
            ]
        ];

        $data[2] = [
            [
                'title' => '分钟',
            ], [
                'title' => '小时',
            ], [
                'title' => '平方米',
            ], [
                'title' => '台',
            ], [
                'title' => '套',
            ], [
                'title' => '米',
            ], [
                'title' => '个',
            ], [
                'title' => '张',
            ], [
                'title' => '双',
            ], [
                'title' => '件',
            ]
        ];

        $datalist = self::where(['weid' => $weid, 'ptype' => $ptype])->select()->toArray();

        if (empty($datalist)) {
            $data =  self::setdata($data[$ptype], $ptype);
            self::createdata($data);
        }
    }

    public static function setdata($data, $ptype)
    {
        $weid = weid();
        if (!empty($data)) {
            foreach ($data as &$vo) {
                $vo['weid'] = $weid;
                $vo['ptype'] = $ptype;
            }
        }
        return $data;
    }

    public static function createdata($data)
    {
        if (!empty($data)) {
            foreach ($data as $vo) {
                self::create($vo);
            }
        }
        return $data;
    }
}
