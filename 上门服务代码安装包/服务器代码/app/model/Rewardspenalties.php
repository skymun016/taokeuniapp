<?php

namespace app\model;

use think\Model;

class Rewardspenalties extends Model
{
    protected $connection = 'mysql';

    protected $pk = 'id';

    protected $name = 'rewardspenalties';

    public static function total_byuuid($uuid = '')
    {
        return self::where('technical_uuid',$uuid)->count();
    }

}
