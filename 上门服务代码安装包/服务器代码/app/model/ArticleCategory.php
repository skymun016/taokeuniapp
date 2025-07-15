<?php

namespace app\model;

use think\Model;

class ArticleCategory extends Model
{

    protected $connection = 'mysql';

    protected $pk = 'id';

    protected $name = 'article_category';

    public static function datainitial()
    {
        $weid = weid();
        $data = array('关于我们', '售后流程');
        foreach ($data as $key => $vo) {
            $updata[$key] = [
                'weid' => $weid,
                'title' => $vo,
                'sort' => $key + 10,
                'status' => 1
            ];
        }
        $mod = new self;

        $mod->saveAll($updata);
    }

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
