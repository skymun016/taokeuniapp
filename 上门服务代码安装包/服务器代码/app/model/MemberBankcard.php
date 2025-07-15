<?php

namespace app\model;

use think\Model;

class MemberBankcard extends Model
{

    protected $connection = 'mysql';

    protected $pk = 'id';

    protected $name = 'member_bankcard';

    public static function getbankcard($bid)
    {

        $bankcard = self::find($bid);
        if(!empty($bankcard)){
            $bankcard = $bankcard->toArray();
        }
        return $bankcard['name'] . ";" . $bankcard['accounts'] . ";" . $bankcard['bankname'] . $bankcard['branchname'];
    }
}
