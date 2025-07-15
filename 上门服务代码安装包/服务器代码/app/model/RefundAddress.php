<?php

namespace app\model;

use think\Model;

class RefundAddress extends Model
{

    protected $connection = 'mysql';

    protected $pk = 'id';

    protected $name = 'refund_address';
}
