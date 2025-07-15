<?php

namespace app\model;

use think\Model;

class AttributeValue extends Model
{
    protected $connection = 'mysql';

    protected $pk = 'id';

    protected $name = 'attribute_value';
}
