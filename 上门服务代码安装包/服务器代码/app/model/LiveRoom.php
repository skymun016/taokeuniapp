<?php

namespace app\model;

use think\Model;

class LiveRoom extends Model
{

    protected $connection = 'mysql';

    protected $pk = 'id';

    protected $name = 'live_room';
}
