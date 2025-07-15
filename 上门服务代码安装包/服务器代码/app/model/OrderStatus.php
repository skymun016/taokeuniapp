<?php

namespace app\model;

use think\Model;

class OrderStatus extends Model
{

    protected $connection = 'mysql';

    protected $pk = 'id';

    protected $name = 'order_status';

    public static function listname($ptype)
    {
        $weid = weid();
        if ($ptype == 1) {
            $ptype = 'goodsuser';
        }
        if ($ptype == 2) {
            $ptype = 'serviceuser';
        }
        $where['weid'] = $weid;
        $where['status'] = 1;
        $where['ptype'] = $ptype;
        $OrderStatus = OrderStatus::where($where)
            ->order('sort asc')
            ->select()
            ->toArray();

        $data[0]['val'] = 0;
        $data[0]['weid'] = $weid;
        $data[0]['name'] = '全部';
        $data[0]['status'] = 1;

        $data = array_merge($data, $OrderStatus);

        foreach ($data as &$vo) {
            $vo['id'] = $vo['val'];
        }

        return $data;
    }

    public static function get_order_status_name($id, $ptype = "goods")
    {
        $weid = weid();
        $ret = self::where('weid', $weid)->where('val', $id)->where('ptype', $ptype)->find();
        if (!empty($ret)) {
            $ret = $ret->toArray();
            return $ret['name'];
        }
    }

    public static function get_order_status($id, $ptype = "goods")
    {
        $weid = weid();
        $ret = self::where('weid', $weid)->where('val', $id)->where('ptype', $ptype)->find();
        if (!empty($ret)) {
            $ret = $ret->toArray();
            return $ret;
        }
    }

    public static function getpcarray($ptype = "goods")
    {
        $weid = weid();
        $data = self::where('weid', $weid)->field('id,name')->where('ptype', $ptype)->select()->toArray();
        $datalist = [];
        foreach ($data as $key => $vo) {
            $datalist[$key]['val'] = $vo['val'];
            $datalist[$key]['key'] = $vo['name'];
        }
        return $datalist;
    }

    public static function datainitial($ptype)
    {
        $data['goods'] = [
            [
                'val' => 1,
                'name' => '待付款',
                'sort' => 10,
                'status' => 1,
            ], [
                'val' => 2,
                'name' => '待发货',
                'sort' => 20,
                'status' => 1,
            ], [
                'val' => 3,
                'name' => '待收货',
                'sort' => 30,
                'status' => 1,
            ], [
                'val' => 4,
                'name' => '已取消',
                'sort' => 99,
                'status' => 1,
            ], [
                'val' => 5,
                'name' => '已收货',
                'sort' => 50,
                'status' => 1,
            ], [
                'val' => 6,
                'name' => '售后',
                'sort' => 60,
                'status' => 1,
            ], [
                'val' => 7,
                'name' => '已完成',
                'sort' => 70,
                'status' => 1,
            ]
        ];

        $data['goodsuser'] = [
            [
                'val' => 1,
                'name' => '待付款',
                'sort' => 10,
                'status' => 1,
            ], [
                'val' => 2,
                'name' => '待发货',
                'sort' => 20,
                'status' => 1,
            ], [
                'val' => 3,
                'name' => '待收货',
                'sort' => 30,
                'status' => 1,
            ], [
                'val' => 4,
                'name' => '已取消',
                'sort' => 99,
                'status' => 1,
            ], [
                'val' => 5,
                'name' => '已收货',
                'sort' => 50,
                'status' => 1,
            ], [
                'val' => 6,
                'name' => '售后',
                'sort' => 60,
                'status' => 1,
            ], [
                'val' => 7,
                'name' => '已完成',
                'sort' => 70,
                'status' => 0,
            ]
        ];

        $data['service'] = [
            [
                'val' => 1,
                'name' => '待付款',
                'sort' => 10,
                'status' => 1,
            ], [
                'val' => 2,
                'name' => '待指派',
                'sort' => 20,
                'status' => 1,
            ], [
                'val' => 3,
                'name' => '待服务',
                'sort' => 30,
                'status' => 1,
            ], [
                'val' => 8,
                'name' => '服务中',
                'sort' => 32,
                'status' => 1,
            ], [
                'val' => 4,
                'name' => '已取消',
                'sort' => 99,
                'status' => 1,
            ], [
                'val' => 5,
                'name' => '已服务',
                'sort' => 50,
                'status' => 1,
            ], [
                'val' => 6,
                'name' => '售后',
                'sort' => 60,
                'status' => 1,
            ], [
                'val' => 7,
                'name' => '已完成',
                'sort' => 70,
                'status' => 1,
            ]
        ];
        $data['storeservice'] = [
            [
                'val' => 1,
                'name' => '待付款',
                'sort' => 10,
                'status' => 1,
            ], [
                'val' => 2,
                'name' => '已付款',
                'sort' => 20,
                'status' => 1,
            ], [
                'val' => 3,
                'name' => '待服务',
                'sort' => 30,
                'status' => 1,
            ], [
                'val' => 8,
                'name' => '服务中',
                'sort' => 32,
                'status' => 1,
            ], [
                'val' => 4,
                'name' => '已取消',
                'sort' => 99,
                'status' => 1,
            ], [
                'val' => 5,
                'name' => '已服务',
                'sort' => 50,
                'status' => 1,
            ], [
                'val' => 6,
                'name' => '售后',
                'sort' => 60,
                'status' => 1,
            ], [
                'val' => 7,
                'name' => '已完成',
                'sort' => 70,
                'status' => 1,
            ]
        ];

        $data['serviceuser'] = [
            [
                'val' => 1,
                'name' => '待付款',
                'sort' => 10,
                'status' => 1,
            ], [
                'val' => 2,
                'name' => '待指派',
                'sort' => 20,
                'status' => 0,
            ], [
                'val' => 3,
                'name' => '待服务',
                'sort' => 30,
                'status' => 1,
            ], [
                'val' => 4,
                'name' => '已取消',
                'sort' => 99,
                'status' => 1,
            ], [
                'val' => 5,
                'name' => '已服务',
                'sort' => 50,
                'status' => 1,
            ], [
                'val' => 6,
                'name' => '售后',
                'sort' => 60,
                'status' => 1,
            ], [
                'val' => 7,
                'name' => '已完成',
                'sort' => 70,
                'status' => 1,
            ]
        ];

        $data =  self::setdata($data[$ptype], $ptype);
        self::createdata($data);
    }
    public static function setdata($data, $ptype)
    {
        $weid = weid();
        if (!empty($data)) {
            foreach ($data as &$vo) {
                $vo['weid'] = $weid;
                $vo['ptype'] = $ptype;
                if ($vo['is_sys'] === 0) {
                    $vo['is_sys'] = 0;
                } else {
                    $vo['is_sys'] = 1;
                }
                if (empty($vo['sort'])) {
                    $vo['sort'] = 100;
                }
            }
        }
        return $data;
    }

    public static function createdata($data)
    {
        if (!empty($data)) {
            foreach ($data as $vo) {
                if (empty(self::where(['val' => $vo['val'], 'ptype' => $vo['ptype'], 'weid' => $vo['weid']])->find())) {
                    self::create($vo);
                }
            }
        }
        return $data;
    }
}
