<?php

namespace app\model;

use think\Model;

class OrderCount extends Model
{

    protected $connection = 'mysql';

    protected $pk = 'id';

    protected $name = 'order_count';

    public static function createuserdata($orderInfo)
    {
        if (!empty($orderInfo['order_status_id'])) {
            $data = ['ptype' => 'user', 'uid' => $orderInfo['uid'], 'order_id' => $orderInfo['id'], 'order_status_id' => $orderInfo['order_status_id']];
            if (empty(self::where($data)->find())) {
                self::where(['ptype' => 'user', 'uid' => $orderInfo['uid'], 'order_id' => $orderInfo['id']])->update(['is_read' => 1]);
                $data['is_read'] = 0;
                self::create($data);
            }
        }
        return $data;
    }

    public static function getUserCount($uid, $order_status_id)
    {
        return self::where('uid', $uid)->where('ptype', 'user')->where('is_read', 0)->where('order_status_id',  $order_status_id)->count();
    }

    public static function upread($uid, $order_status_id)
    {
        $where['uid'] = $uid;
        $where['ptype'] = 'user';
        $where['is_read'] = 0;
        if (!empty($order_status_id)) {
            $where['order_status_id'] = $order_status_id;
        }
        //var_dump($where);
        return self::where($where)->update(['is_read' => 1]);
    }
}
