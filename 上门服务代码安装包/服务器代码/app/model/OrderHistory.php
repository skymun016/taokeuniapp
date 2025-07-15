<?php
namespace app\model;
use think\Model;

class OrderHistory extends Model
{

    protected $connection = 'mysql';

    protected $pk = 'id';

    protected $name = 'order_history';

}
