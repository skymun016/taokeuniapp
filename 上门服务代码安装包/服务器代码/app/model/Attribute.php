<?php
namespace app\model;

use think\Model;

class Attribute extends Model
{
    protected $connection = 'mysql';

    protected $pk = 'id';

    protected $name = 'attribute';

}
