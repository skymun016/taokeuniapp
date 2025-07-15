<?php
namespace app\model;

use think\Model;
use think\facade\Db;

class MiaoshaGoods extends Model
{
    protected $connection = 'mysql';

    protected $pk = 'id';

    protected $name = 'miaosha_goods';

    public function goods()
    {
        return $this->hasOne(Goods::class, 'id', 'goods_id')->bind([
            'district_name',
            'city_name',
            'province_name'
        ]);
    }
    public static function setGoodslist($goods)
    {
        if (!empty($goods)) {
            foreach ($goods as &$vo) {
                $goodsdata = Goods::find($vo['goods_id']);
                if (!empty($goodsdata)) {
                    $vo['goods'] = $goodsdata->toArray();
                    $vo['image'] = toimg($vo['goods']['image']);
                    $vo['ptype'] = $vo['goods']['ptype'];
                    $vo['time_amount'] = $vo['goods']['time_amount'];
                    $vo['quantity_unit'] = $vo['goods']['quantity_unit'];
                    if (empty($vo['quantity_unit'] && $vo['is_times'] != 3)) {
                        $vo['quantity_unit'] = '次';
                    }
                    $vo['is_timer'] = $vo['goods']['is_timer'];

                    $vo['is_times'] = $vo['goods']['is_times'];
                    $vo['timesmum'] = $vo['goods']['timesmum'];

                    //$vo['price'] = floatval($vo['price']);
                    $vo['price'] = floatval(Goods::setPrice($vo)["price"]);
                }
            }
        }
        return $goods;
    }
    public static function getDiyMiaoshaGoods($count = 4, $goodsSort = "all", $ocid = 0)
    {

        $Configdata = Config::getconfig();        
        $query = Db::name('miaosha_goods')
			->alias('mg')
			->leftJoin('goods', 'mg.goods_id = goods.id')
			->field('mg.*, goods.district_name, goods.city_name, goods.province_name');

        $query->where('mg.weid', weid());
        $query->where('mg.status', 1);
        if (empty($Configdata['show_storegoods'])) {
            $query->where('mg.sid', 0);
        }

        //$query = MiaoshaGoods::where($where);
        /*
        $withJoin = [
            'goods' => ['weid'=>'goodsweid','district_name', 'city_name', 'province_name'],
        ];
        $query->withJoin($withJoin, 'left');
*/

        if ($ocid) {
            $query->where(function ($q) use ($ocid) {
                $Operatingcity = Operatingcity::find($ocid);
                if (!empty($Operatingcity)) {
                    $Operatingcity = $Operatingcity->toArray();
                    if (empty($Operatingcity['areatype'])) {
                        $Operatingcity['areatype'] = 3;
                    }
                    if ($Operatingcity['areatype'] == 3) {
                        $q->where('goods.district_name', $Operatingcity['district_name'])->whereOr('goods.district_name', '');
                    } elseif ($Operatingcity['areatype'] == 2) {
                        $q->where('goods.city_name', $Operatingcity['city_name'])->whereOr('goods.city_name', '');
                    } elseif ($Operatingcity['areatype'] == 1) {
                        $q->where('province_name', 'like', '%' . $Operatingcity['province_name'] . '%' )->whereOr('goods.province_name', '');
                    }
                } else {
                    $q->where('goods.city_name', '');
                }
            });
        } else {
            $query->where('goods.city_name', '');
        }

        if ($goodsSort == "all") {
            $Sort = 'mg.sort asc,mg.id desc';
        } elseif ($goodsSort == "sales") {
            $Sort = 'mg.sale_count desc';
        } elseif ($goodsSort == "price") {
            $Sort = 'mg.price asc';
        }

        $data = $query->limit((int) $count)
            ->order($Sort)->select()->toArray();

        //$sql = $query->getLastSql();
        $retdata = Goods::setGoodslist($data);
        //$retdata['sql'] = $sql;
        return $retdata;
    }
}
