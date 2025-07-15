<?php
namespace app\model;

use think\Model;

class TuanGoodsSkuValue extends Model
{

    protected $connection = 'mysql';

    protected $pk = 'id';

    protected $name = 'tuan_goods_sku_value';

    //取得商品选项
    static public function get_goods_sku_value($id)
    {
        $data = self::where(['goods_id' => $id])->order('id asc')->select()->toArray();
        if (empty($data)) {
            $data = [];
        }else{
            foreach($data as &$vo){
                $vo['number'] = 1;
            } 
        }
        return $data;
    }

}
