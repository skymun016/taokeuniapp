<?php

namespace app\model;

use think\Model;

class BottomMenu extends Model
{

	protected $connection = 'mysql';

	protected $pk = 'id';

	protected $name = 'bottom_menu';

	public static function datainitial($mo, $tid = 0)
	{
		$weid = weid();

		$datalist = BottomMenu::where(['weid' => $weid, 'tid' => (int)$tid, 'module' => $mo])->select()->toArray();

		if (empty($datalist)) {

			$query = BottomMenuOriginal::where(['weid' => '0', 'tid' => (int)$tid, 'is_base' => 1, 'module' => $mo]);

			if (config('database.app_name') == config('my.app_v2')) {
				$query->where('is_v2', 1);
			}

			if (config('database.app_name') == config('my.app_v3')) {
				$query->where('is_v3', 1);
			}

			if (config('database.app_name') == config('my.app_v6')) {
				$query->where('is_v6', 1);
			}

			$list = $query->order('sort asc')->select()->toArray();

			if (!empty($list)) {
				foreach ($list as &$vo) {
					unset($vo['id']);
					$vo['weid'] = $weid;
					$vo['tid'] = (int) $tid;
				}
				$BottomMenu = new BottomMenu;
				$BottomMenu->saveAll($list);
			}
		}
	}

	static function getBottommenu($userInfo)
	{
		$mo = input('post.mo', '', 'serach_in');
		if (empty($mo)) {
			$mo = 'bottom';
		}

		$pageid = input('post.pageid', '', 'serach_in');

		if ($pageid == '-1') {
			$Bottommenutype = $userInfo['Bottommenutype'];
		} else {
			$diywhere['weid'] = weid();
			$diywhere['version'] = '2';
			if (!empty($pageid)) {
				$diywhere['id'] = $pageid;
			}
			$DiyPage = DiyPage::where($diywhere)->order('is_index desc')->find();
			if (!empty($DiyPage)) {
				$DiyPage = $DiyPage->toArray();
				$data['pagebase'] = iunserializer($DiyPage['pagebase'])[0];
				if ($mo == 'bottom') {
					$Bottommenutype = $data['pagebase']['base']['Bottommenutype'];
				}
			}
		}

		$where['weid'] = weid();
		$where['module'] = $mo;
		$where['tid'] = (int) $Bottommenutype;

		if (\app\model\Uploadminiprogram::getaudit(input('get.v', '', 'serach_in'))) {
			$where['is_submitaudit'] = 0;
		}

		$where['status'] = 1;
		$listdata = BottomMenu::where($where)
			->order('sort asc')
			->select()
			->toArray();

		$config = Config::getconfig('pagestyle');

		if (!empty(trim($config['bottommenucolor']))) {
			$data['color'] = trim($config['bottommenucolor']);
		} else {
			$data['color'] = '#999';
		}

		if (!empty(trim($config['bottommenuselectedColor']))) {
			$data['selectedColor'] = trim($config['bottommenuselectedColor']);
		} else {
			$data['selectedColor'] = '#EB0909';
		}

		if (!empty(trim($config['bottommenubackgroundColor']))) {
			$data['backgroundColor'] = trim($config['bottommenubackgroundColor']);
		} else {
			$data['backgroundColor'] = '#fff';
		}

		$data['borderStyle'] = '#fff';

		$data['thisurl'] = input('post.thisurl');

		foreach ($listdata as $key => $vo) {

			$list[$key]['iconPath'] = toimg($vo['icon']);
			$list[$key]['selectedIconPath'] = toimg($vo['iconactive']);
			$list[$key]['ptype'] = $vo['url'];
			$list[$key]['zdyLinktype'] = $vo['zdyLinktype'];
			$list[$key]['zdyappid'] = $vo['zdyappid'];
			$list[$key]['hump'] = $vo['hump'];
			if ($vo['url'] == 'customurl') {
				$list[$key]['pagePath'] = $vo['customurl'];
			} else {
				if (!empty($vo['url'])) {
					$list[$key]['pagePath'] = $vo['url'];
					if ($vo['params']) {
						$tmppath = explode('?', $vo['url']);
						if ($tmppath[1]) {
							$list[$key]['pagePath'] = $list[$key]['pagePath'] . '&' . $vo['params'];
						} else {
							$list[$key]['pagePath'] = $list[$key]['pagePath'] . '?' . $vo['params'];
						}
					}
				}
			}
			$list[$key]['text'] = $vo['title'];
		}

		$data['list'] = $list;
		$data['lan'] = Author()::getlan();
		return  $data;
	}
}
