<?php

namespace app\model;

use think\Model;

class Article extends Model
{

    protected $connection = 'mysql';

    protected $pk = 'id';

    protected $name = 'article';
}
