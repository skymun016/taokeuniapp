<?php

namespace app\index\controller;

use think\exception\ValidateException;
use app\model\Broadcast;

class BroadcastController extends Base
{

    public function index()
    {
        $currentid = input('post.currentid', '', 'serach_in');
        $where['weid'] = weid();

        $Broadcast =  Broadcast::where($where);

        if (!empty($currentid)) {
            $Broadcast->where('id', '<', $currentid);
        }

        $data = $Broadcast->order('id desc')->find();
        if (!empty($data)) {
            $data = $data->toArray();
            $data['content'] = $data['username'] .' '. $data['content'];
        }

        return $this->json(['data' => $data]);
    }
}
