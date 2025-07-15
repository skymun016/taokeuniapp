<?php
namespace app\model;

use think\Model;

class MemberWishlist extends Model
{

	protected $connection = 'mysql';

	protected $pk = 'id';

	protected $name = 'member_wishlist';

}
