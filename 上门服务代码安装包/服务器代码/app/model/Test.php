<?php

namespace app\model;

use think\Model;

class Test extends Model
{

    protected $connection = 'mysql';

    protected $pk = 'id';

    protected $name = 'test';
}
