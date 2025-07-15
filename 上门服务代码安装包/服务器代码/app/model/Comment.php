<?php

namespace app\model;

use think\Model;

class Comment extends Model
{
    protected $connection = 'mysql';

    protected $pk = 'id';

    protected $name = 'comment';

    public static function total_byuuid($uuid = '')
    {
        return self::where('technical_uuid',$uuid)->count();
    }

}
