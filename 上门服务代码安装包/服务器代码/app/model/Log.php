<?php 

namespace app\model;
use think\Model;

class Log extends Model {


	protected $connection = 'mysql';

 	protected $pk = 'id';

 	protected $name = 'sys_log';

}

