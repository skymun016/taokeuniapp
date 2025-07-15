<?php
namespace app\model;

use think\Model;

class OrderAddress extends Model
{

    protected $connection = 'mysql';

    protected $pk = 'id';

    protected $name = 'order_address';

}
