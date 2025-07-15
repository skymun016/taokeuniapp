<?php

namespace app\model;

use think\Model;

class GoodsSku extends Model
{

    protected $connection = 'mysql';

    protected $pk = 'id';

    protected $name = 'goods_sku';

    //取得商品选项
    static public function get_goods_sku($id)
    {
        $data = self::field('name,item')->where(['goods_id' => $id])
            ->order('id asc')
            ->select()->toArray();
        if (empty($data)) {
            $data = [];
        } else {
            foreach ($data as &$vo) {
                if(!empty($vo['item'])){
                    $vo['item'] = explode(",", $vo['item']);
                }
            }
        }
        return $data;
    }
}
