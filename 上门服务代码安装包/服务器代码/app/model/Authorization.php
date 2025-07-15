<?php

namespace app\model;

use think\Model;

class Authorization extends Model
{

    protected $connection = 'mysql';

    protected $pk = 'id';

    protected $name = 'authorization';
    
}
