<?php

namespace app\model;

use think\Model;

class Broadcast extends Model
{

    protected $connection = 'mysql';

    protected $pk = 'id';

    protected $name = 'broadcast';

    public static function neworder($order)
    {
        $data['weid'] = weid();
        $member =  Member::find($order['uid']);
        if (!empty($member)) {
            $data['username'] = $member->nickname;
            $data['content'] = '刚刚下了订单';
            $data['touxiang'] = $member->userpic;
            $res = self::create($data);
        }
    }

    public static function staff($order_staff)
    {
        $data['weid'] = weid();
        $Technical =  Technical::getInfo($order_staff['uuid']);
        if (!empty($Technical)) {
            $data['username'] = $Technical['title'];
            $data['content'] = '刚刚接到平台的派单';
            $data['touxiang'] = $Technical['touxiang'];
            $res = self::create($data);
        }
    }
}
