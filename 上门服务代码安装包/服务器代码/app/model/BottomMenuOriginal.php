<?php

namespace app\model;

use think\Model;

class BottomMenuOriginal extends Model
{

    protected $connection = 'mysql';

    protected $pk = 'id';

    protected $name = 'bottom_menu_original';

    public static function getpagearray($mo = '')
    {
        $where['weid'] = 0;

        if (config('database.app_name') == config('my.app_v2')) {
            $where['is_v2'] = 1;
		}

		if (config('database.app_name') == config('my.app_v3')) {
            $where['is_v3'] = 1;
		}

        if (config('database.app_name') == config('my.app_v6')) {
            $where['is_v6'] = 1;
		}

        if (!empty($mo)) {
            $where['module'] = $mo;
        }
        $data = self::field('url,title')->where($where)
            ->order('sort asc')
            ->select()->toArray();

        foreach ($data as $k => $v) {
            $array[$k]['val'] = $v['url'];
            $array[$k]['key'] = $v['title'];
        }
        return $array;
    }
}
