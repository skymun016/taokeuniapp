<?php

namespace app\index\controller;

use think\exception\ValidateException;
use think\facade\Validate;
use app\model\RegisterField;
use app\model\Member;
use app\model\Technical;
use app\model\Tuanzhang;
use app\model\Agent;
use app\model\Partner;
use app\model\Category;
use app\model\Config;
use app\model\Users;
use app\model\Store;
use app\model\Operatingcity;
use app\model\AgentCode;
use app\model\Order;
use app\model\UuidRelation;

class RegisterfieldController extends Base
{

    public function list()
    {
        $ptype = input('get.ptype', '', 'serach_in');
        $update = input('get.update', '', 'serach_in');
        $orderid = input('get.orderid', '', 'serach_in');
        $technicalid = input('get.technicalid', '', 'serach_in');
        $sid = input('get.sid', '', 'serach_in');

        if (empty($ptype)) {
            $ptype = 'users';
        }
        $uid = UID();

        if ($ptype == 'agent') {
            $agent = Agent::where('uid', $uid)->find();
            if (!empty($agent)) {
                if (empty($update)) {
                    return $this->json(['is_submit' => 1]);
                }
                $infodata =  $agent->toArray();
            }
        }
        if ($ptype == 'partner') {
            $partner = Partner::where('uid', $uid)->find();
            if (!empty($partner)) {
                if (empty($update)) {
                    return $this->json(['is_submit' => 1]);
                }
                $infodata =  $partner->toArray();
            }
        }
        if ($ptype == 'technical') {
            $uuid = UuidRelation::getuuid(UID(), 'technical');
            if (!empty($technicalid)) {
                $technical = Technical::find($technicalid);
            } else if (!empty($uuid)) {
                $technical = Technical::where('uuid', $uuid)->find();
            }

            if (!empty($technical)) {
                if (empty($update)) {
                    return $this->json(['is_submit' => 1]);
                }
                $infodata =  $technical->toArray();
            }
        }
        if ($ptype == 'tuanzhang') {
            $uuid = UuidRelation::getuuid(UID(), 'tuanzhang');

            if (!empty($uuid)) {
                $tuanzhang = Tuanzhang::where('uuid', $uuid)->find();
                if (!empty($tuanzhang)) {
                    if (empty($update)) {
                        return $this->json(['is_submit' => 1]);
                    }
                    $infodata =  $tuanzhang->toArray();
                }
            }
        }
        if ($ptype == 'store') {

            if (!empty($sid)) {
                $store = Store::find($sid);
            } else {
                $store = Store::getInfobyuid(UID());
            }

            if (!empty($store)) {
                if (empty($update)) {
                    return $this->json(['is_submit' => 1]);
                }
                $infodata =  $store;
            }
        }
        if ($ptype == 'operatingcity') {
            $uuid = UuidRelation::getuuid(UID(), 'operatingcity');
            if (!empty($uuid)) {
                $Operatingcity = Operatingcity::where('uuid', $uuid)->find();
                if (!empty($Operatingcity)) {
                    if (empty($update)) {
                        return $this->json(['is_submit' => 1]);
                    }
                    $infodata =  $Operatingcity->toArray();
                }
            }
        }
        if ($ptype == 'complete') {
            $order = Order::where('id', $orderid)->find();
            if (!empty($order)) {
                $infodata['customtext'] = $order->customtext;
            }
        }

        $customtext = iunserializer($infodata['customtext']);

        $nousername = $update;

        $data = RegisterField::getfrontinputField($ptype, $nousername);

        foreach ($data as &$vo) {
            if ($vo['is_sys'] == 1) {
                $vo['fieldsvalue'] = $infodata[$vo['fieldsmingcheng']];
                if ($vo['inputtype'] == 'lbs') {
                    if (empty($vo['fieldsvalue'])) {
                        $vo['fieldsvalue'] = [];
                        $vo['fieldsvalue']['region_name'] =  $infodata['province_name'] . $infodata['city_name'] . $infodata['district_name'];
                    } else {
                        if ($vo['is_sys'] == 1) {
                            $vo['fieldsvalue'] = [];
                            $vo['fieldsvalue']['province_name'] = $infodata['province_name'];
                            $vo['fieldsvalue']['city_name'] =  $infodata['city_name'];
                            $vo['fieldsvalue']['district_name'] =  $infodata['district_name'];
                            $vo['fieldsvalue']['address'] =  $infodata['dizhi'];
                            $vo['fieldsvalue']['latitude'] =  $infodata['latitude'];
                            $vo['fieldsvalue']['longitude'] =  $infodata['longitude'];
                            $vo['fieldsvalue']['region_name'] =  $infodata['region_name'];
                        } else {
                            $vo['fieldsvalue'] = iunserializer($vo['fieldsvalue']);
                        }
                    }
                }
                if ($vo['inputtype'] == 'checkbox' && $vo['fieldsmingcheng'] == 'cate_ids') {
                    if (empty($vo['fieldsvalue'])) {
                        $vo['fieldsvalue'] = [];
                    } else {
                        $vo['fieldsvalue'] =  explode(',', $vo['fieldsvalue']);
                    }
                    if ($vo['ptype'] == 'technical') {
                        $ctopptype = 2;
                    } else {
                        $ctopptype = '';
                    }
                    $vo['selectvaluearray'] = Category::gettoparray($ctopptype);
                } elseif ($vo['fieldsmingcheng'] == 'sid') {
                    if (empty($vo['fieldsvalue'])) {
                        $vo['fieldsvalue'] = [];
                    }
                    $vo['selectvaluearray'] = Store::getpcarray();
                } else {
                    if (!empty($vo['selectvalue'])) {
                        $vo['fieldsvalue'] = [];
                        $selectvaluearray = explode(',', $vo['selectvalue']);
                        if (is_array($selectvaluearray)) {
                            foreach ($selectvaluearray as $key => $svo) {
                                $vo['selectvaluearray'][$key]['title'] = $svo;
                            }
                        }
                    }
                }
            } else {
                $vo['fieldsvalue'] = $customtext[$vo['inputtype']][$vo['id']];
            }

            if ($vo['inputtype'] == 'pics') {
                $vo['fieldsvalue1'] = $vo['fieldsvalue'];
                if (empty($vo['fieldsvalue'])) {
                    $vo['fieldsvalue'] = [];
                } else {
                    $vo['fieldsvalue'] = explode(',', $vo['fieldsvalue']);
                }
            }
        }
        return $this->json(['data' => $data]);
    }

    public function view()
    {
        $ptype = input('get.ptype', '', 'serach_in');
        $orderid = input('get.orderid', '', 'serach_in');
        $technicalid = input('get.technicalid', '', 'serach_in');
        $sid = input('get.sid', '', 'serach_in');

        if (empty($ptype)) {
            $ptype = 'users';
        }
        $uid = UID();

        if ($ptype == 'agent') {
            $agent = Agent::where('uid', $uid)->find();
            if (!empty($agent)) {
                $infodata =  $agent->toArray();
            }
        }
        if ($ptype == 'partner') {
            $partner = Partner::where('uid', $uid)->find();
            if (!empty($partner)) {
                $infodata =  $partner->toArray();
            }
        }
        if ($ptype == 'technical') {
            $uuid = UuidRelation::getuuid(UID(), 'technical');
            if (!empty($technicalid)) {
                $technical = Technical::find($technicalid);
            } else if (!empty($uuid)) {
                $technical = Technical::where('uuid', $uuid)->find();
            }

            if (!empty($technical)) {
                $infodata =  $technical->toArray();
            }
        }
        if ($ptype == 'tuanzhang') {
            $uuid = UuidRelation::getuuid(UID(), 'tuanzhang');

            if (!empty($uuid)) {
                $tuanzhang = Tuanzhang::where('uuid', $uuid)->find();
                if (!empty($tuanzhang)) {
                    $infodata =  $tuanzhang->toArray();
                }
            }
        }
        if ($ptype == 'store') {
            if (!empty($sid)) {
                $store = Store::find($sid);
            } else {
                $uuid = UuidRelation::getuuid(UID(), 'store');
                if (!empty($uuid)) {
                    $store = Store::where('uuid', $uuid)->find();
                }
            }
            if (!empty($store)) {
                $infodata =  $store->toArray();
            }
        }
        if ($ptype == 'operatingcity') {
            $uuid = UuidRelation::getuuid(UID(), 'operatingcity');
            if (!empty($uuid)) {
                $Operatingcity = Operatingcity::where('uuid', $uuid)->find();
                if (!empty($Operatingcity)) {
                    $infodata =  $Operatingcity->toArray();
                }
            }
        }
        if ($ptype == 'complete') {
            $order = Order::where('id', $orderid)->find();
            if (!empty($order)) {
                $infodata['customtext'] = $order->customtext;
            }
        }

        $customtext = iunserializer($infodata['customtext']);

        $data = RegisterField::getfrontField($ptype);

        foreach ($data as &$vo) {
            if ($vo['is_sys'] == 1) {
                $vo['fieldsvalue'] = $infodata[$vo['fieldsmingcheng']];
                if ($vo['inputtype'] == 'lbs') {
                    if (empty($vo['fieldsvalue'])) {
                        $vo['fieldsvalue'] = [];
                        $vo['fieldsvalue']['region_name'] =  $infodata['province_name'] . $infodata['city_name'] . $infodata['district_name'];
                    } else {
                        if ($vo['is_sys'] == 1) {
                            $vo['fieldsvalue'] = [];
                            $vo['fieldsvalue']['province_name'] = $infodata['province_name'];
                            $vo['fieldsvalue']['city_name'] =  $infodata['city_name'];
                            $vo['fieldsvalue']['district_name'] =  $infodata['district_name'];
                            $vo['fieldsvalue']['address'] =  $infodata['dizhi'];
                            $vo['fieldsvalue']['latitude'] =  $infodata['latitude'];
                            $vo['fieldsvalue']['longitude'] =  $infodata['longitude'];
                            $vo['fieldsvalue']['region_name'] =  $infodata['region_name'];
                        } else {
                            $vo['fieldsvalue'] = iunserializer($vo['fieldsvalue']);
                        }
                    }
                }
                if ($vo['inputtype'] == 'checkbox' && $vo['fieldsmingcheng'] == 'cate_ids') {
                    if (empty($vo['fieldsvalue'])) {
                        $vo['fieldsvalue'] = [];
                    } else {
                        $vo['fieldsvalue'] =  explode(',', $vo['fieldsvalue']);
                    }
                    if ($vo['ptype'] == 'technical') {
                        $ctopptype = 2;
                    } else {
                        $ctopptype = '';
                    }
                    $vo['selectvaluearray'] = Category::gettoparray($ctopptype);
                } elseif ($vo['fieldsmingcheng'] == 'sid') {
                    if (empty($vo['fieldsvalue'])) {
                        $vo['fieldsvalue'] = [];
                    }
                    $vo['selectvaluearray'] = Store::getpcarray();
                } else {
                    if (!empty($vo['selectvalue'])) {
                        $vo['fieldsvalue'] = [];
                        $selectvaluearray = explode(',', $vo['selectvalue']);
                        if (is_array($selectvaluearray)) {
                            foreach ($selectvaluearray as $key => $svo) {
                                $vo['selectvaluearray'][$key]['title'] = $svo;
                            }
                        }
                    }
                }
            } else {
                $vo['fieldsvalue'] = $customtext[$vo['inputtype']][$vo['id']];
            }

            if ($vo['inputtype'] == 'pics') {
                $vo['fieldsvalue1'] = $vo['fieldsvalue'];
                if (empty($vo['fieldsvalue'])) {
                    $vo['fieldsvalue'] = [];
                } else {
                    $vo['fieldsvalue'] = explode(',', $vo['fieldsvalue']);
                }
            }
        }
        return $this->json(['data' => $data]);
    }

    public function update()
    {
        $uuid = uniqid(rand(1, 10000));
        $update = input('post.update', '', 'serach_in');
        $postdata['fields'] =  json_decode(input('post.registerfield'), true);
        $registerfield =  $postdata['fields'];
        $regdata = RegisterField::fieldToData($postdata);
        $data = $regdata['data'];
        $uid = UID();

        $validate = Validate::rule($regdata['rule']);
        if (!$validate->check($data)) {
            return $this->json(['errno' => 1, 'msg' => $validate->getError()]);
        }
        if (empty($update)) {
            $data['uuid'] = $uuid;
        }

        $usersdata['password'] = $data['password'];
        unset($data['password']);
        if (empty($id)) {
            $usersdata['username'] = trim($data['username']);
        }
        unset($data['username']);

        if ($usersdata['username']) {
            if (Users::where('username', $usersdata['username'])->find()) {
                throw new ValidateException('用户名已被占用');
            }
        }

        $data['weid'] = weid();
        unset($data['create_time']);
        try {
            if ($registerfield[0]['ptype'] == 'member') {
                $Configdata = Config::getconfig('member');
                if ($Configdata['reg_check'] == 1) {
                    $data['status'] = 0;
                } else {
                    $data['status'] = 1;
                }
                $data['id'] = $uid;
                Member::update($data);
            }
            if ($registerfield[0]['ptype'] == 'technical') {

                if ($data['photoalbum']) {
                    $data['touxiang'] = explode(',', $data['photoalbum'])[0];
                }

                if (empty($update)) {
                    $data['status'] = 0;
                    $data['sort'] = 100;
                    $res = Technical::create($data);
                } else {
                    $uuid = UuidRelation::getuuid($uid, 'technical');
                    Technical::where('uuid', $uuid)->update($data);
                }
            }
            if ($registerfield[0]['ptype'] == 'tuanzhang') {
                if (empty($update)) {
                    $data['status'] = 0;
                    $data['sort'] = 100;
                    $res = Tuanzhang::create($data);
                } else {
                    $uuid = UuidRelation::getuuid($uid, 'tuanzhang');
                    Tuanzhang::where('uuid', $uuid)->update($data);
                }
            }
            if ($registerfield[0]['ptype'] == 'agent') {
                unset($data['uuid']);
                $Configdata = Config::getconfig('agent');
                if ($Configdata['share_condition'] == 2) {
                    $data['status'] = 0;
                } else if ($Configdata['share_condition'] == 3) {
                    $data['status'] = 1;
                }
                if (!empty($data['pid_code'])) {
                    $puid = AgentCode::getuid($data['pid_code']);
                    if (!empty($puid)) {
                        Member::where('id', $uid)->update(['pid' => $puid]);
                    }
                }
                $data['uid'] = $uid;
                Agent::create($data);
            }
            if ($registerfield[0]['ptype'] == 'partner') {
                unset($data['uuid']);
                $Configdata = Config::getconfig('partner');
                $data['uid'] = UID();
                Partner::create($data);
            }
            if ($registerfield[0]['ptype'] == 'store') {
                if (empty($update)) {
                    $data['status'] = 0;
                    $data['sort'] = 100;
                    $res = Store::create($data);
                } else {
                    $uuid = UuidRelation::getuuid($uid, 'store');
                    Store::where('uuid', $uuid)->update($data);
                }
            }

            if ($registerfield[0]['ptype'] == 'operatingcity') {
                if (empty($update)) {
                    $data['status'] = 0;
                    $data['sort'] = 100;
                    $res = Operatingcity::create($data);
                } else {
                    $uuid = UuidRelation::getuuid($uid, 'operatingcity');
                    Operatingcity::where('uuid', $uuid)->update($data);
                }
            }

            if ($registerfield[0]['ptype'] == 'store' || $registerfield[0]['ptype'] == 'tuanzhang' || $registerfield[0]['ptype'] == 'technical' || $registerfield[0]['ptype'] == 'operatingcity') {

                if ($res->id && !empty($usersdata['username'])) {
                    if (!empty($usersdata['password'])) {
                        $usersdata["salt"] = substr(md5(uniqid()), 8, 8);
                        $usersdata['password'] = pass_hash($usersdata['password'], $usersdata["salt"]);
                    } else {
                        unset($usersdata['password']);
                    }
                    if ($registerfield[0]['ptype'] == 'store') {
                        $usersdata['sid'] = $res->id;
                    }
                    if ($registerfield[0]['ptype'] == 'operatingcity') {
                        $usersdata['ocid'] = $res->id;
                    }

                    if ($registerfield[0]['ptype'] == 'tuanzhang') {
                        $usersdata['tzid'] = $res->id;
                    }

                    if ($registerfield[0]['ptype'] == 'technical') {
                        $usersdata['tid'] = $res->id;
                    }

                    $usersdata['weid'] = weid();
                    $usersdata['uuid'] = $data['uuid'];
                    $usersdata['w7uid'] = 0;
                    Users::create($usersdata);
                    UuidRelation::create(['weid' => weid(), 'ptype' => $registerfield[0]['ptype'], 'uuid' => $res['uuid'], 'uid' => $uid]);
                }
            }
        } catch (\Exception $e) {
            throw new ValidateException($e->getMessage());
        }



        $msg = '提交成功';
        return $this->json(['msg' => $msg, 'data' => $data]);
    }
}
