<?php

namespace app\model;

use think\Model;

class Category extends Model
{
	protected $connection = 'mysql';

	protected $pk = 'id';

	protected $name = 'category';

	public function parent()
	{
		return $this->belongsTo(Category::class, 'pid', 'id');
	}
	// 递归获取所有父节点的ID
	public static function getParentIds($nodeId)
	{
		$parentIds = [];
		$node = self::find($nodeId);

		if ($node && $node->pid != 0) {
			$parentIds[] = $node->pid;
			$parentIds = array_merge($parentIds, self::getParentIds($node->pid));
		}
		return $parentIds;
	}

	public static function getParentIdsstr($nodeId)
	{
		$pidarry = self::getParentIds($nodeId);
		$returndata = $nodeId;
		foreach ($pidarry as $parentId) {
			$returndata = $returndata . "," . $parentId;
		}
		return $returndata;
	}

	public static function getsonid($id)
	{
		//加空判定
		if (empty($id)) {
			return '';
		}
		$data = self::where(['weid' => weid(), 'pid' => $id])
			->select()->toArray();
		$returndata = $id;
		if (!empty($data)) {
			foreach ($data as $vo) {
				$returndata = $returndata . "," . $vo['id'];
			}
		}
		return explode(',', $returndata);
	}

	public static function getidssonid($ids)
	{

		if (!empty($ids)) {
			$returndata = '';
			$idsarray =	explode(',', $ids);
			foreach ($idsarray as $vo) {
				$sonid = '';
				$sonid = self::getsonid($vo);
				if (empty($returndata)) {
					if (!empty($sonid)) {
						$returndata = implode(',', $sonid);
					}
				} else {
					if (!empty($sonid)) {
						$returndata = $returndata . "," . implode(',', $sonid);
					}
				}
			}
		}

		return $returndata;
	}

	public static function getlist($params = ['status' => ''])
	{
		$where['weid'] = weid();
		$where['pid'] = (int) $params['pid'];
		if (!empty($params['ptype'])) {
			$where['ptype'] = $params['ptype'];
		}
		if ($params['status'] !== '') {
			$where['status'] = $params['status'];
		}

		$data = Category::where($where)
			->order('sort asc')
			->select()
			->toArray();

		return $data;
	}

	public static function getmultiple($ids)
	{
		if (!empty($ids)) {
			$data = self::where(['weid' => weid()])->where('id', 'in', $ids)
				->select()->toArray();

			if (!empty($data)) {
				foreach ($data as $vo) {
					if (empty($returndata)) {
						$returndata =  $vo['title'];
					} else {
						$returndata = $returndata . "," . $vo['title'];
					}
				}
			}
			return $returndata;
		}
	}

	public static function getTreeparray()
	{
		$return = [];
		$data = self::where(['weid' => weid()])
			->order('sort asc')
			->select()
			->toArray();

		$returndata = Tree::title($data, 0);
		if (!empty($returndata)) {
			foreach ($returndata as $vo) {
				$return[$vo['id']] = $vo['title'];
			}
		}
		return $return;
	}

	public static function getparray()
	{
		$data = self::where(['weid' => weid(), 'pid' => 0])
			->order('sort asc')
			->select()
			->toArray();
		$returndata[0] = "顶级分类";
		if (!empty($data)) {
			foreach ($data as $vo) {
				$returndata[$vo['id']] = $vo['title'];
			}
		}

		return $returndata;
	}

	public static function getTitle($id)
	{
		$data = [];
		$data = Category::find($id);
		if (!empty($data)) {
			$data = $data->toArray();

			$returndata = $data['title'];
			if ($data['pid'] > 0) {
				//$returndata = Category::getTitle($data['pid']) . " / " . $returndata;
			}
		}

		return $returndata;
	}
	public static function getImage($id)
	{
		$data = [];
		$data = Category::find($id);
		if (!empty($data)) {
			$data = $data->toArray();
			return  toimg($data['image']);
		}
	}


	function getdatalist()
	{

		$ptypeArray = $this->where(['weid' => weid()])
			->orderBy('sort asc')
			->select()
			->toArray();

		$arr = Tree::title($ptypeArray, 0);

		return $arr;
	}

	public static function getcatapiall($pid = 0, $ptype = '')
	{

		$where['weid'] = weid();
		$where['pid'] = $pid;
		$where['status'] = 1;

		if (!empty($ptype)) {
			$where['ptype'] = $ptype;
		}
		$data = self::where($where)
			->order('sort asc')
			->select()
			->toArray();
		if (!empty($data)) {
			foreach ($data as &$vo) {
				if ($son = self::getcatapiall($vo['id'])) {
					$vo['son'] = $son;
				} else {
					$vo['son'] = [];
				}
				$vo['icon'] = toimg($vo['image']);
			}
		}

		return $data;
	}

	public static function getpcarray($ptype = '')
	{
		$where['weid'] = weid();
		$where['status'] = 1;
		if (!empty($ptype)) {
			$where['ptype'] = $ptype;
		}

		$list =	self::field('id,title,pid')->where($where)
			->order('sort asc')
			->select()
			->toArray();
		$array = [];
		foreach ($list as $k => $v) {
			$array[$k]['val'] = $v['id'];
			$array[$k]['key'] = $v['title'];
			$array[$k]['pid'] = $v['pid'];
		}
		return $array;
	}
	public static function gettoparray($ptype = '')
	{

		$where['pid'] = 0;
		$where['weid'] = weid();
		$where['status'] = 1;
		if (!empty($ptype)) {
			$where['ptype'] = $ptype;
		}

		$list =	self::field('id,title,pid')
			->where($where)
			->order('sort asc')
			->select()
			->toArray();
		$array = [];
		foreach ($list as $k => $v) {
			$array[$k]['val'] = $v['id'];
			$array[$k]['key'] = $v['title'];
			$array[$k]['pid'] = $v['pid'];
		}
		return $array;
	}
}
