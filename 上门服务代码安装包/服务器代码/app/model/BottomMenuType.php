<?php

namespace app\model;

use think\Model;

class BottomMenuType extends Model
{

    protected $connection = 'mysql';

    protected $pk = 'id';

    protected $name = 'bottom_menu_type';

    public static function getTitle($id)
	{
		$data = [];
		$data = self::find($id);
		if (!empty($data)) {
			$data = $data->toArray();

			$returndata = $data['title'];
		}

		return $returndata;
	}

    public static function getpagearray() {
        $data = self::field('id,title')->where(['weid'=>weid(),'status' => 1])
            ->order('sort asc')
            ->select()->toArray();

        foreach ($data as $k => $v) {
			$array[$k]['val'] = $v['id'];
			$array[$k]['key'] = $v['title'];
		}
        return $array;
    }
}
