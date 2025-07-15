<?php

namespace app\index\controller;

use think\exception\ValidateException;
use app\model\DiyPage;
use app\model\Coupon;
use app\model\CouponReceive;
use app\model\Config;
use app\model\Goods;
use app\model\TuanGoods;
use app\model\MiaoshaGoods;
use app\model\Technical;
use app\model\Tuanzhang;
use app\model\Store;
use app\model\DiyPageLink;

class DiypageController extends Base
{

    public function indexv2()
    {

        $id = input('get.id', '', 'intval');
        $where['weid'] = weid();
        $where['version'] = '2';
        $Configdata = Config::getconfig();
        $Configdata['kefu'] = Config::getconfig('kefu');
        $Configdata['tuanzhang'] = Config::getconfig('tuanzhang');
        $pagestyleconfig = Config::getconfig('pagestyle');

        $ocid = $this->userInfo['cityinfo']['ocid'];

        if (\app\model\Uploadminiprogram::getaudit(input('get.v', '', 'serach_in'))) {
            $DiyPage = DiyPage::where($where)->order('is_submitaudit desc')->find();
        }

        if (empty($DiyPage)) {
            if (!empty($id)) {
                $where['id'] = $id;
            }
            $DiyPage = DiyPage::where($where)->order('is_index desc')->find();
        }

        if (!empty($DiyPage)) {
            $DiyPage = $DiyPage->toArray();
            $uid = UID();
            $data['pagebase'] = iunserializer($DiyPage['pagebase'])[0];

            if ($data['pagebase']['base']['Bottommenutype']) {
                $this->userInfo['Bottommenutype'] = $data['pagebase']['base']['Bottommenutype'];
                $this->setAppToken($this->userInfo, $this->getAppToken());
            } else {
                $this->userInfo['Bottommenutype'] = '';
                $this->setAppToken($this->userInfo, $this->getAppToken());
            }
            $data['modulelist'] = iunserializer($DiyPage['modulelist']);

            if ($data['pagebase']['base']['bg'] == 1) {
                $data['pagebase']['base']['bgstyle'] = 'background:#f7f7f7;';
            } elseif ($data['pagebase']['base']['bg'] == 2) {
                $data['pagebase']['base']['bgstyle'] = 'background:none;';
            } elseif ($data['pagebase']['base']['bg'] == 3) {
                if ($data['pagebase']['base']['bt'] == 1) {
                    $data['pagebase']['base']['bgstyle'] = 'background:' . $data['pagebase']['base']['bc'] . ';';
                } else if ($data['pagebase']['base']['bt'] == 2 && $data['pagebase']['base']['bi'] != '') {
                    $data['pagebase']['base']['bgstyle'] = 'background:url(' . $data['pagebase']['base']['bi'] . ') no-repeat 0px 0px;background-size:100%;';
                }
            }
            $data['pagebase']['sdv'] = Author()::getlan();

            if ($data['pagebase']['base']['titleBackground']['bg'] == 1) {
                $data['pagebase']['base']['titleBackground']['bgstyle'] = 'background:#f7f7f7;';
            } elseif ($data['pagebase']['base']['titleBackground']['bg'] == 2) {
                $data['pagebase']['base']['titleBackground']['bgstyle'] = 'background:none;';
            } elseif ($data['pagebase']['base']['titleBackground']['bg'] == 3) {
                if ($data['pagebase']['base']['titleBackground']['bt'] == 1) {
                    $data['pagebase']['base']['titleBackground']['bgstyle'] = 'background:' . $data['pagebase']['base']['titleBackground']['bc'] . ';';
                } else if ($data['pagebase']['base']['titleBackground']['bt'] == 2 && $data['pagebase']['base']['titleBackground']['bi'] != '') {
                    $data['pagebase']['base']['titleBackground']['bgstyle'] = 'background:url(' . $data['pagebase']['base']['titleBackground']['bi'] . ') no-repeat 0px 0px;background-size:100%;';
                }
            }
            $data['pagebase']['sdvc'] = Author()::getlan();

            foreach ($data['modulelist'] as &$vo) {

                $vo = Author()::setdiymodulebase($vo);

                if ($vo['type'] == 'banner') {
                    $vo['swiperHeight'] = 123;
                    foreach ($vo['list'] as &$vvo) {
                        $vvo['img'] = toimg($vvo['img']);
                    }
                }
                if ($vo['type'] == 'navBar') {
                    foreach ($vo['list'] as &$vvo) {
                        $vvo['icon']['custompic'] = toimg($vvo['icon']['custompic']);
                    }
                }

                if ($vo['type'] == 'window' && $vo['style']['layout'] == '-1') {
                    $ii = 0;
                    foreach ($vo['list'] as $vvo) {
                        $windowdata[$ii] = $vvo;
                        $ii++;
                    }
                    $vo['list'] = $windowdata;
                }

                if ($vo['type'] == 'goods') {

                    if (!empty($vo['title']['link'])) {
                        $vo['title']['link'] = DiyPageLink::setlink($vo['title']['link']);
                    }

                    $vo = Author()::setdiymodulecolumn($vo, $pagestyleconfig);

                    if ($vo['base']['source'] == 'auto') {
                        if (!empty($vo['base']['is_area'])) {
                            $goodsocid = $ocid;
                        }
                        $vo['list'] = Goods::getGoodsBycat([
                            'cat' => $vo['base']['auto']['category'],
                            'count' => $vo['base']['auto']['showNum'],
                            'goodsSort' => $vo['base']['auto']['goodsSort'],
                            'ptype' => $vo['base']['auto']['ptype'],
                            'ocid' => $goodsocid
                        ]);
                    } else {
                        foreach ($vo['list'] as &$vvo1) {
                            $gdata =  Goods::where(['weid' => weid()])->where('ptype','>', 0)->where('status', 1)->where('id', $vvo1['cm']['id'])->select()->toArray()[0];
                            if (empty($gdata)) {
                                $vvo1 = [];
                            } else {
                                $vvo1 = $gdata;
                            }
                        }
                        $vo['list'] = Goods::setGoodslist($vo['list']);
                    }
                }

                if ($vo['type'] == 'tuan') {

                    $vo = Author()::setdiymodulecolumn($vo, $pagestyleconfig);
                    if ($vo['base']['source'] == 'auto') {
                        if (!empty($vo['base']['is_area'])) {
                            $tuanocid = $ocid;
                        }

                        $vo['list'] = TuanGoods::getDiyTuanGoods($vo['base']['auto']['showNum'], $vo['base']['auto']['goodsSort'], $tuanocid);
                    } else {
                        foreach ($vo['list'] as &$vvo1) {
                            $gdata = TuanGoods::where(['weid' => weid()])->where('status', 1)->where('id', $vvo1['cm']['id'])->select()->toArray()[0];
                            if (empty($gdata)) {
                                $vvo1 = [];
                            } else {
                                $vvo1 = $gdata;
                            }
                        }
                    }

                    $vo['list'] = TuanGoods::setGoodslist($vo['list']);
                }

                if ($vo['type'] == 'miaosha') {
                    $vo = Author()::setdiymodulecolumn($vo, $pagestyleconfig);
                    if ($vo['base']['source'] == 'auto') {
                        if (!empty($vo['base']['is_area'])) {
                            $miaoshaocid = $ocid;
                        }
                        $vo['list'] = MiaoshaGoods::getDiyMiaoshaGoods($vo['base']['auto']['showNum'], $vo['base']['auto']['goodsSort'], $miaoshaocid);
                    } else {
                        foreach ($vo['list'] as &$vvo1) {
                            $gdata = MiaoshaGoods::where(['weid' => weid()])->where('status', 1)->where('id', $vvo1['cm']['id'])->select()->toArray()[0];
                            if (empty($gdata)) {
                                $vvo1 = [];
                            } else {
                                $vvo1 = $gdata;
                            }
                        }
                    }

                    $vo['list'] = MiaoshaGoods::setGoodslist($vo['list']);
                }

                if ($vo['type'] == 'goodscard') {
                    $vo = Author()::setdiymodulecolumn($vo, $pagestyleconfig);
                    $tzid = tzid();
                    if ($tzid) {
                        $sid = Store::getidbytzid($tzid);
                        if (!empty($sid)) {
                            $vo['list'] = Goods::where(['weid' => weid(), 'is_times' => 3, 'sid' => $sid])->where('status', 1)->limit($vo['base']['auto']['showNum'])->select()->toArray();
                            $vo['list'] = Goods::setGoodslist($vo['list']);
                        }
                    }
                }

                if ($vo['type'] == 'technical') {

                    $vo = Author()::setdiymoduletechcolumn($vo, $pagestyleconfig);

                    if ($vo['base']['source'] == 'auto') {
                        if (!empty($vo['base']['is_area'])) {
                            $technicalocid = $ocid;
                        }
                        $vo['list'] = Technical::getdiy_bycat($vo['base']['auto']['category'], $vo['base']['auto']['showNum'], $vo['base']['auto']['Sort'], $technicalocid);
                    } else {
                        foreach ($vo['list'] as &$vvo1) {
                            $gdata = Technical::where(['weid' => weid()])->where('status', 1)->where('id', $vvo1['cm']['id'])->select()->toArray()[0];
                            if (empty($gdata)) {
                                $vvo1 = [];
                            } else {
                                $vvo1 = $gdata;
                            }
                        }
                    }

                    foreach ($vo['list'] as &$vvo) {
                        if ($vvo['touxiang']) {
                            $vvo['touxiang'] = toimg($vvo['touxiang']);
                        }
                    }
                }

                if ($vo['type'] == 'store') {

                    $vo = Author()::setdiymoduletechcolumn($vo, $pagestyleconfig);

                    if ($vo['base']['source'] == 'auto') {
                        if (!empty($vo['base']['is_area'])) {
                            $storeocid = $ocid;
                        }
                        $vo['list'] = Store::getdiy_bycat($vo['base']['auto']['category'], $vo['base']['auto']['showNum'], $vo['base']['auto']['Sort'], $storeocid);
                    } else {
                        foreach ($vo['list'] as &$vvo1) {
                            $gdata = Store::where(['weid' => weid()])->where('status', 1)->where('id', $vvo1['cm']['id'])->select()->toArray()[0];
                            if (empty($gdata)) {
                                $vvo1 = [];
                            } else {
                                $vvo1 = $gdata;
                            }
                        }
                    }

                    foreach ($vo['list'] as &$vvo) {
                        if ($vvo['touxiang']) {
                            $vvo['touxiang'] = toimg($vvo['touxiang']);
                        }
                    }
                }

                if ($vo['type'] == 'search') {
                    if (!empty($vo['params']['hotkey'])) {
                        $vo['params']['hotkey'] = explode(" ", $vo['params']['hotkey']);
                    }

                    if ($data['items']['page']['style']['titleBackgroundColor'] == '#ffffff') {
                        $vo['style']['background'] = '#f7f7f7';
                        $vo['style']['hotkeytxtColor'] = '#666666';
                    } else {
                        $vo['style']['background'] = $data['items']['page']['style']['titleBackgroundColor'];
                        $vo['style']['hotkeytxtColor'] = '#ffffff';
                    }
                }

                if ($vo['base']['paddingTop']) {
                    $vo['base']['bgstyle'] .= 'padding-top:' . $vo['base']['paddingTop'] . 'px;';
                }
                if ($vo['base']['paddingBottom']) {
                    $vo['base']['bgstyle'] .= 'padding-bottom:' . $vo['base']['paddingBottom'] . 'px;';
                }

                if ($vo['base']['paddingLeft']) {
                    $vo['base']['bgstyle'] .= 'padding-left:' . $vo['base']['paddingLeft'] . 'px;' . 'padding-right:' . $vo['base']['paddingLeft'] . 'px;';
                }

                if ($vo['base']['marginTop']) {
                    $vo['base']['bgstyle'] .= 'margin-top:' . $vo['base']['marginTop'] . 'px;';
                }
                if ($vo['base']['marginBottom']) {
                    $vo['base']['bgstyle'] .= 'margin-bottom:' . $vo['base']['marginBottom'] . 'px;';
                }

                if ($vo['type'] == 'coupon') {

                    $coupondata = Coupon::where(['weid' => weid(), 'ptype' => 1])
                        ->limit($vo['max'])
                        ->select()
                        ->toArray();

                    foreach ($coupondata as $cvo) {
                        $cdata[$cvo['id']]['coupon_id'] = $cvo['id'];
                        $cdata[$cvo['id']]['color'] = $cvo['color'];
                        $cdata[$cvo['id']]['coupon_type'] = $cvo['coupon_type'];
                        $cdata[$cvo['id']]['discount'] = $cvo['discount'];
                        $cdata[$cvo['id']]['reduce_price'] = $cvo['reduce_price'];
                        $cdata[$cvo['id']]['min_price'] = $cvo['min_price'];
                        $cdata[$cvo['id']]['state'] = CouponReceive::getReceiveState($cvo['id'], $uid);
                    }
                    $vo['list'] = $cdata;
                }

                if (!empty($vo['list'])) {
                    foreach ($vo['list'] as &$vvo1) {
                        if (!empty($vvo1['link'])) {
                            $vvo1['link'] = DiyPageLink::setlink($vvo1['link']);
                        }
                    }
                }

                if (!empty($vo['link'])) {
                    $vo['link'] = DiyPageLink::setlink($vo['link']);
                }
            }
        }
        $Configdata['SITE_ICON'] = toimg($data['SITE_ICON']);
        $Configdata['poster'] = toimg($data['poster']);
        $Configdata['applypic'] = toimg($data['applypic']);
        $data['config'] =  $Configdata;
        $data['ocid'] =  $ocid;
        return $this->json(['data' => $data]);
    }
}
