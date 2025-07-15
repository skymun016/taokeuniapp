<?php

namespace app\model;

use think\Model;

class Printer extends Model
{

    protected $connection = 'mysql';

    protected $pk = 'id';

    protected $name = 'printer';

    public static function getPinpaiType()
    {

        return [
            'feie' => '飞鹅打印机'
        ];
    }
    public static function getPinpaiTypeName($v)
    {

        $PinpaiType = self::getPinpaiType();
        return $PinpaiType[$v];
    }

    public static function getpcarray()
    {
        $data = self::getPinpaiType();
        $i=0;
        foreach ($data as $k => $v) {
			$array[$i]['val'] = $k;
			$array[$i]['key'] = $v;
            $i++;
		}
        return $array;
    }

    
}
