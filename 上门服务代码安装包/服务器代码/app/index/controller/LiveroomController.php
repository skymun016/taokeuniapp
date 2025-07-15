<?php

namespace app\index\controller;

use think\exception\ValidateException;
use app\model\LiveRoom;
use app\model\LiveAnchor;

class LiveroomController extends Base
{
    public function index()
    {
        $weid = weid();
        $serach['keyword'] = input('post.keyword', '', 'serach_in');

        $query = LiveRoom::where(['weid' => $weid]);

        if (!empty($serach['keyword'])) {
            $query->where('name', 'like', '%' . $serach['keyword'] . '%');
        }

        $res = $query->paginate(getpage())->toArray();

        foreach ($res['data'] as &$vo) {
            $vo['share_img'] = toimg($vo['share_img']);
            $vo['anchor'] = LiveAnchor::getAnchor($vo['anchor_id']);
			$vo['start_time'] = time_format($vo['start_time']);
        }

        $data['data'] = $res;

        return $this->json($data);
    }
}
