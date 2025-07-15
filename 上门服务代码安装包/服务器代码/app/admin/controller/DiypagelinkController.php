<?php

namespace app\admin\controller;

use think\exception\ValidateException;
use app\model\BottomMenuOriginal;
use app\model\Goods;
use app\model\Category;
use app\model\Technical;
use app\model\Article;
use app\model\ArticleCategory;
use app\model\DiyPageLink;
use app\model\DiyPage;
use app\model\Tree;

class DiypagelinkController extends Base
{
	function getModel()
	{

		$ptype = input('post.ptype', '', 'serach_in');
		if ($ptype == 'pageslist') {
			return new BottomMenuOriginal;
		} else if ($ptype == 'diypageslist') {
			return new DiyPage;
		} else if ($ptype == 'servicelist' || $ptype == 'goodslist') {
			return new Category;
		} else if ($ptype == 'serviceDetail' || $ptype == 'goodsDetail') {
			return new Goods;
		} else if ($ptype == 'technicalDetail') {
			return new Technical;
		} else if ($ptype == 'articlelist') {
			return new ArticleCategory;
		} else if ($ptype == 'articleDetail') {
			return new Article;
		}
	}

	function index()
	{
		$page = input('post.page', 1, 'intval');
		$ptype = input('post.ptype', '', 'serach_in');
		$weid = weid();

		if ($ptype != 'custom') {
			$query = $this->setSearch();

			if ($ptype == 'servicelist' || $ptype == 'goodslist') {
				$res = $query->order('sort asc')->select()->toArray();
			} else {
				$res = $query->paginate(getpage())->toArray();
			}

			if (!empty($res['data'])) {
				foreach ($res['data'] as &$vo) {
					$vo['image'] = toimg($vo['image']);
					if ($ptype == 'serviceDetail') {
						$vo['cat_id'] = Category::getTitle($vo['cat_id']);
					}
					if ($ptype == 'articleDetail') {
						$vo['cid'] = ArticleCategory::getTitle($vo['cid']);
					}
				}
			}
			if ($ptype == 'servicelist' || $ptype == 'goodslist') {

				$data['data']['data'] = Tree::title($res, 0);
			} else {
				$data['data'] = $res;
			}

			if ($ptype == 'diypageslist') {
				if (!empty($data['data']['data'])) {
					foreach ($data['data']['data'] as &$vo) {
						$vo['url'] = "/pages/index/index?id=" . $vo['id'];
					}
				}
			}

			$data['field_data']['Fields'] = DiyPageLink::getFields($ptype);
		}

		return $this->json($data);
	}
	function setSearch()
	{
		$ptype = input('post.ptype', '', 'serach_in');
		$keyword = trim(input('post.keyword', '', 'serach_in'));
		$weid = weid();
		if ($ptype == 'pageslist') {
			$plwhere['weid'] = 0;
			if (config('database.app_name') == config('my.app_v2')) {
				$plwhere['is_v2'] = 1;
			}

			if (config('database.app_name') == config('my.app_v3')) {
				$plwhere['is_v3'] = 1;
			}
			if (config('database.app_name') == config('my.app_v6')) {
				$plwhere['is_v6'] = 1;
			}
			$query = $this->getModel()->where($plwhere)->order('id asc');
		} else {
			$query = $this->getModel()->where(['weid' => $weid])->order('id desc');
		}

		if ($ptype == 'goodslist') {
			$query->where('ptype', 1);
		}
		if ($ptype == 'servicelist') {
			$query->where('ptype', 2);
		}

		if (!empty($keyword)) {
			if ($ptype == 'serviceDetail' || $ptype == 'goodsDetail') {
				$query->where('name', 'like', '%' . $keyword . '%');
			} else {
				$query->where('title', 'like', '%' . $keyword . '%');
			}
		}
		return $query;
	}
	function linklist()
	{
		$linklist = DiyPageLink::linklist();
		$i = 0;
		foreach ($linklist as $key => $vo) {
			if ($vo['v3'] == 1 && config('database.app_name') != config('my.app_v3')) {
			} else {
				$vo['ptype'] = $key;
				$list[$i] = $vo;
				$i++;
			}
		}
		$data['data'] = $list;

		return $this->json($data);
	}
}
