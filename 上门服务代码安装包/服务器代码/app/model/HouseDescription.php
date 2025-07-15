<?php
namespace app\model;

use think\Model;

class HouseDescription extends Model
{

    protected $connection = 'mysql';

    protected $pk = 'id';

    protected $name = 'house_description';

}
