<?php

namespace app\model;

use think\Model;

class TransportExtend extends Model
{

	protected $connection = 'mysql';

	protected $pk = 'id';

	protected $name = 'transport_extend';
	public function calc_transport($buy_num, $area_id)
	{

		if (empty($area_id)) {
			return 0;
		}

		$extend_list = TransportExtend::where(['weid' =>weid()])->order('id asc')->select()->toArray();

		if (empty($extend_list)) {
			return 0;
		} else {
			return $this->calc_unit($area_id, $buy_num, $extend_list);
		}
	}

	/**
	 * 计算某个具单元的运费
	 *
	 * @param 配送地区 $area_id
	 * @param 购买数量 $num
	 * @param 运费模板内容 $extend
	 * @return number 总运费
	 */
	private function calc_unit($area_id, $num, $extend)
	{

		if (!empty($extend) && is_array($extend)) {

			$calc_total = array(
				'error' => '该地区不配送！！'
			);
			//dump($extend);
			foreach ($extend as $v) {

				/**
				 * strpos函数返回字符串在另一个字符串中第一次出现的位置，没有该字符返回false
				 * 参数1，字符串
				 * 参数2，要查找的字符
				 */
				if (strpos($v['area_id'], "," . $area_id . ",") !== false) {

					unset($calc_total['error']);

					$areaarray = Area::find($area_id);
					if (!empty($areaarray)) {
						$areaarray = $areaarray->toArray();
					}

					if ($num <= $v['snum']) {
						//在首重数量范围内
						$calc_total['price'] = $v['sprice'];
					} else {
						//超出首重数量范围，需要计算续重
						$calc_total['price'] = sprintf('%.2f', ($v['sprice'] + ceil(($num - $v['snum']) / $v['xnum']) * $v['xprice']));
					}
					$calc_total['info'] = $areaarray['area_name'] . '，首重(小于等于1千克KG) ' . $v['sprice'] . '元'
						. ' 续重(每千克KG) ' . $v['xprice'] . '元，总计 ' . $num . ' 千克KG';
					return $calc_total;
				}
			}
			//没有找到则选择默认运费选项
			if (isset($extend[0]) && is_array($extend)) {
				unset($calc_total['error']);

				$areaarray = Area::find($area_id);
				if (!empty($areaarray)) {
					$areaarray = $areaarray->toArray();
				}


				if ($num <= $extend[0]['snum']) {
					//在首重数量范围内
					$calc_total['price'] = $extend[0]['sprice'];
				} else {
					//超出首重数量范围，需要计算续重
					$calc_total['price'] = sprintf('%.2f', ($extend[0]['sprice'] + ceil(($num - $extend[0]['snum']) / $extend[0]['xnum']) * $extend[0]['xprice']));
				}
				$calc_total['info'] = $areaarray['area_name'] . '，首重(小于等于1千克KG) ' . $extend[0]['sprice'] . '元'
					. ' 续重(每千克KG) ' . $extend[0]['xprice'] . '元，总计 ' . $num . ' 千克KG';
				return $calc_total;
			}


			return $calc_total;
		}
	}
}
