<?php

namespace app\model;

use think\Model;

class Invitecode extends Model
{


    protected $connection = 'mysql';

    protected $pk = 'id';

    protected $name = 'invite_code';
}
