<?php

namespace app\model;

use think\Model;

class DiyPage extends Model
{

    protected $connection = 'mysql';

    protected $pk = 'id';

    protected $name = 'diy_page';

   static function getpagearray()
    {
        $weid = weid();
        $diypage = [];
        $diypagearray =  DiyPage::where(['weid' => $weid])->order('id asc')->select()->toArray();
        if (!empty($diypagearray)) {
            foreach ($diypagearray as $key => $vo) {
                $diypage[$key]['key'] = $vo['title'];
                $diypage[$key]['val'] = "/pages/index/index?id=" . $vo['id'];
            }
        }
        return $diypage;
    }
}
