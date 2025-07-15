<?php

namespace app\model;

use think\Model;

class RotarytablePrize extends Model
{
    protected $connection = 'mysql';

    protected $pk = 'id';

    protected $name = 'rotarytable_prize';

    public static function datainitial()
    {
        $data = [
            [
                'title' => '0.1元现金红包',
                'ptype' => 2,
                'quantity' => 10,
                'probability' => 100,
                'price' => 0.1,
            ], [
                'title' => '30积分',
                'ptype' => 4,
                'quantity' => 10,
                'probability' => 200,
                'points' => 30,
            ], [
                'title' => '谢谢参与',
                'ptype' => 1,
                'quantity' => 100,
                'probability' => 500,
            ],  [
                'title' => '0.1元现金红包',
                'ptype' => 2,
                'quantity' => 10,
                'probability' => 100,
                'price' => 0.1,
            ], [
                'title' => '30积分',
                'ptype' => 4,
                'quantity' => 10,
                'probability' => 200,
                'points' => 30,
            ], [
                'title' => '谢谢参与',
                'ptype' => 1,
                'quantity' => 100,
                'probability' => 500,
            ], [
                'title' => '30积分',
                'ptype' => 4,
                'quantity' => 10,
                'probability' => 200,
                'points' => 30,
            ], [
                'title' => '谢谢参与',
                'ptype' => 1,
                'quantity' => 100,
                'probability' => 500,
            ],
        ];

        $data =  self::setdata($data);
        self::createdata($data);
    }
    public static function setdata($data)
    {
        $weid = weid();
        if (!empty($data)) {
            foreach ($data as &$vo) {
                $vo['weid'] = $weid;
                $vo['rid'] = 0;
                $vo['status'] = 1;
                if (empty($vo['sort'])) {
                    $vo['sort'] = 100;
                }
            }
        }
        return $data;
    }
    public static function createdata($data)
    {
        $count = self::where(['weid' => weid()])->count();
        $insertcount = 8 - $count;
        if ($insertcount > 0) {
            for ($i = 0; $i < $insertcount; $i++) {
                self::create($data[7 - $i]);
            }
        }
        return $data;
    }
}
