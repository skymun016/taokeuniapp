<?php
namespace app\model;
use think\Model;

class OrderRefund extends Model
{

    protected $connection = 'mysql';

    protected $pk = 'id';

    protected $name = 'order_refund';
}
