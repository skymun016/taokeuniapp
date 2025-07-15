<?php
namespace app\model;

use think\Model;

class StoreLevel extends Model
{

    protected $connection = 'mysql';

    protected $pk = 'id';

    protected $name = 'store_level';

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

    public static function getpcarray() {
        $data = self::field('id,title')->where(['weid'=>weid(),'status' => 1])
            ->order('sort asc')
            ->select()->toArray();

        foreach ($data as $k => $v) {
			$array[$k]['val'] = $v['id'];
			$array[$k]['key'] = $v['title'];
		}
        return $array;
    }
    public static function getPercent($id = '')
	{
		$data = self::where(['id' => $id])->find();
		if(!empty($data)){
			$data = $data->toArray();
		}
		return $data['return_percent'];
	}
}
