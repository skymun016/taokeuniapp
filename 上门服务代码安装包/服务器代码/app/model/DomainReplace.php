<?php

namespace app\model;

use think\Model;

class DomainReplace extends Model
{

    protected $connection = 'mysql';

    protected $pk = 'id';

    protected $name = 'domain_replace';

    public static function setreplace($textarry)
    {
        if (!empty($textarry)) {
            $isarray = 0;
            if (is_array($textarry)) {
                $isarray = 1;
                $textarry = json_encode($textarry);
            }
            $textReplace = self::where('status', 1)->where('weid', weid())->cache(180)->select()->toArray();
            if (!empty($textReplace)) {
                foreach ($textReplace as $vo) {
                    if ($isarray) {
                        $textarry =  str_replace(str_replace('/','\/',$vo['sourceurl']), str_replace('/','\/',$vo['replaceurl']), $textarry);
                    } else {
                        $textarry =  str_replace($vo['sourceurl'], $vo['replaceurl'], $textarry);
                    }
                }
            }
            if ($isarray) {
                $textarry = json_decode($textarry, true);
            }
        }
        return $textarry;
    }
}
