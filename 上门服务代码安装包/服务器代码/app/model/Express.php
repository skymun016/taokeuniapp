<?php
namespace app\model;
use think\Model;

class Express extends Model
{

    protected $connection = 'mysql';

    protected $pk = 'id';

    protected $name = 'express';

    public static function getExname($code)
    {
        $area = self::field('code,name')->where(['code' => $code])->find();
        if($area){
            $area = $area->toArray();
        }
 
        return $area['name'];
    }
    public static function getpcarray()
    {
        $data = self::field('code,name')->order('sort asc')->select()->toArray();
        $datalist = [];
        foreach ($data as $key => $vo) {
            $datalist[$key]['val'] = $vo['code'];
			$datalist[$key]['key'] = $vo['name'];
        }
        return $datalist;
    }
}
