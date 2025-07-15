<?php
namespace app\model;
use think\Model;

class OrderTotal extends Model
{

    protected $connection = 'mysql';

    protected $pk = 'id';

    protected $name = 'order_total';

}
