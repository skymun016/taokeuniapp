<?php

namespace app\model;

use think\Model;

class TextReplace extends Model
{

    protected $connection = 'mysql';

    protected $pk = 'id';

    protected $name = 'text_replace';

    public static function setreplace($textarry)
    {
        if (!empty($textarry)) {
            $isarray = 0;
            if (is_array($textarry)) {
                $isarray = 1;
                $textarry = json_encode($textarry,JSON_UNESCAPED_UNICODE);
            }
            $textReplace = self::where('status', 1)->where('weid', weid())->cache(180)->order('sort asc')->select()->toArray();
            if (!empty($textReplace)) {
                foreach ($textReplace as $vo) {
                    if ($isarray) {
                        $textarry =  str_replace(str_replace('/','\/',$vo['sourcetext']), str_replace('/','\/',$vo['replacetext']), $textarry);
                    } else {
                        $textarry =  str_replace($vo['sourcetext'], $vo['replacetext'], $textarry);
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
