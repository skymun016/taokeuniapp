<?php

namespace app\model;

use think\Model;

class Feedback extends Model
{
    protected $connection = 'mysql';

    protected $pk = 'id';

    protected $name = 'feedback';

    public static function total_byuuid($uuid = '')
    {
        return self::where('technical_uuid',$uuid)->count();
    }

}
