<?php

namespace app\model;

use think\Model;

class House extends Model
{

    protected $connection = 'mysql';

    protected $pk = 'id';

    protected $name = 'house';

    public static function getInfo($id)
    {
        $data = self::find($id);
        if (!empty($data)) {
            $data = $data->toArray();
        }

        return $data;
    }
}
