<?php

namespace app\model;

use think\Model;

class Ad extends Model
{

    protected $connection = 'mysql';

    protected $pk = 'id';

    protected $name = 'ad';
}
