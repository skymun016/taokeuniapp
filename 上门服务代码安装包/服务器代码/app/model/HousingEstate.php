<?php

namespace app\model;

use think\Model;

class HousingEstate extends Model
{
    protected $connection = 'mysql';

    protected $pk = 'id';

    protected $name = 'housing_estate';

    public static function getTitle($id = '')
    {
        $mo = self::find($id);
        return $mo->title;
    }

    public static function getCityname($id = '')
    {
        $mo = self::find($id);
        return $mo->city_name;
    }
}
