<?php 

namespace app\model;
use think\Model;

class AdminToken extends Model {


	protected $connection = 'mysql';

 	protected $pk = 'id';

 	protected $name = 'admin_token';
 
}