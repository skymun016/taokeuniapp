<?php

namespace app\model;

use think\Model;

class TuanFound extends Model
{
    protected $connection = 'mysql';

    protected $pk = 'id';

    protected $name = 'tuan_found';

    static public function add_found($tuanid)
    {
        $TuanGoods = TuanGoods::find($tuanid);
        $Membermob = new Member;
        $Member = $Membermob->getUserByWechat();
        $data['weid'] = weid();
        $data['sn'] = build_order_no();
        $data['found_time'] = time();
        $data['found_end_time'] = strtotime("+" . $TuanGoods->time_limit . " hours");
        $data['tuan_end_time'] = $data['found_end_time'];
        $data['uid'] = $Member['id'];
        $data['tuan_id'] = $TuanGoods->id;
        $data['nickname'] = $Member['nickname'];
        $data['avatar'] = $Member['userpic'];
        $data['join'] = 0;
        $data['need'] = $TuanGoods->people_num;

        $res = self::create($data);
        return $res->id;
    }

    public static function getTuanJoinTuanId($tuanid)
    {
        $tuangoods = TuanGoods::find($tuanid);
        if (!empty($tuangoods)) {
            if ($tuangoods->auto_initiate == 1) {
                $TuanFound = self::where('status', 0)->where('found_end_time', '>', time())->where('join', '<', (int) ($tuangoods->people_num - $tuangoods->robot_num))->find();
                if ($TuanFound) {
                    return $TuanFound->id;
                } else {
                    return self::add_found($tuanid);
                }
            } else {
                return;
            }
        }
        return;
    }
}
