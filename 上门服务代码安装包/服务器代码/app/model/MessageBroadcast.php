<?php
namespace app\model;

use think\Model;

class MessageBroadcast extends Model
{

	protected $connection = 'mysql';

	protected $pk = 'id';

	protected $name = 'message_broadcast';

}
