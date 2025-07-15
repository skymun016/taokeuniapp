<?php

namespace app\model;

use think\Model;

class RegisterField extends Model
{
    protected $connection = 'mysql';

    protected $pk = 'id';

    protected $name = 'register_field';

    public static function getField($ptype)
    {
        return self::where(['weid' => weid(), 'ptype' => $ptype, 'status' => 1])->order('sort asc,id asc')->select()->toArray();
    }
    public static function getsysField($ptype)
    {
        return self::where(['weid' => weid(), 'ptype' => $ptype, 'is_sys' => 1, 'status' => 1])->order('sort asc,id asc')->select()->toArray();
    }
    public static function getsnoysField($ptype)
    {
        return self::where(['weid' => weid(), 'ptype' => $ptype, 'is_sys' => 0, 'status' => 1])->order('sort asc,id asc')->select()->toArray();
    }
    public static function getfrontField($ptype)
    {
        $query = self::where(['weid' => weid(), 'ptype' => $ptype, 'is_front' => 1, 'status' => 1]);
        return $query->order('sort asc,id asc')->select()->toArray();
    }
    public static function getfrontinputField($ptype, $nousername = '')
    {
        $query = self::where(['weid' => weid(), 'ptype' => $ptype, 'is_frontinput' => 1, 'status' => 1]);

        if (empty($query->select()->toArray())) {
            $query = self::where(['weid' => weid(), 'ptype' => $ptype, 'is_front' => 1, 'status' => 1]);
        }

        if (!empty($nousername)) {
            $query->where('fieldsmingcheng', '<>', 'username');
            $query->where('fieldsmingcheng', '<>', 'password');
        }
        return $query->order('sort asc,id asc')->select()->toArray();
    }
    public static function getimportField($ptype)
    {
        $data = self::where(['weid' => weid(), 'ptype' => $ptype, 'is_import' => 1, 'status' => 1])->order('sort asc,id asc')->select()->toArray();
        return $data;
    }
    public static function getlistViewField($ptype)
    {
        $data = self::where(['weid' => weid(), 'is_listView' => 1, 'ptype' => $ptype, 'status' => 1])->order('sort asc,id asc')->select()->toArray();
        foreach ($data as &$vo) {
            if ($vo['is_sys'] == 0) {
                $vo['fieldsmingcheng'] = $vo['inputtype'] . $vo['id'];
            }
        }
        return $data;
    }
    public static function getinputField($ptype)
    {
        return self::where(['weid' => weid(), 'is_input' => 1, 'ptype' => $ptype, 'status' => 1])->order('sort asc,id asc')->select()->toArray();
    }
    public static function conversion($vo)
    {
        $customtextarr = iunserializer($vo['customtext']);
        if (!empty($customtextarr)) {
            foreach ($customtextarr as $k => $v) {
                if (!empty($v)) {
                    foreach ($v as $kk => $vv) {
                        if ($k == 'lbs') {
                            if (!empty($vv)) {
                                $tmp = iunserializer($vv);
                                if (!empty($tmp)) {
                                    if (!empty($tmp['region_name'])) {
                                        $vo[$k . $kk] = '[' . $tmp['region_name'] . ']';
                                    }
                                    $vo[$k . $kk] =  $vo[$k . $kk] . $tmp['address'];
                                }
                            }
                        } else {
                            if (!empty($vv)) {
                                $vo[$k . $kk] = $vv;
                            }
                        }
                    }
                }
            }
        }
        return $vo;
    }


    public static function fieldToData($postdata, $terminal = "")
    {
        $registerfield = $postdata['fields'];

        if (!empty($registerfield)) {
            foreach ($registerfield as $vo) {

                if ($vo['fieldsvalue'] == null) {
                    $vo['fieldsvalue'] = "";
                }
                if ($vo['inputtype'] == 'date') {
                    $vo['fieldsvalue'] = strtotime($vo['fieldsvalue']);
                }
                if ($vo['inputtype'] == 'checkbox') {
                    if (!empty($vo['fieldsvalue'])) {
                        $vo['fieldsvalue'] = implode(',', $vo['fieldsvalue']);
                    }
                }

                if ($vo['inputtype'] == 'pics') {
                    $picsstr = '';
                    if ($vo['fieldsvalue']) {
                        if ($terminal == 'pc') {
                            foreach ($vo['fieldsvalue'] as $key => $vooo) {
                                if ($picsstr) {
                                    $picsstr = $picsstr . ',' . $vooo['url'];
                                } else {
                                    $picsstr = $vooo['url'];
                                }
                            }
                        } else {
                            foreach ($vo['fieldsvalue'] as $vooo) {
                                if ($picsstr) {
                                    $picsstr = $picsstr . ',' . $vooo;
                                } else {
                                    $picsstr = $vooo;
                                }
                            }
                        }
                    }
                    $vo['fieldsvalue'] = $picsstr;
                }

                if ($vo['inputtype'] == 'lbs') {

                    if ($vo['is_sys'] == 1) {
                        if ($terminal == 'pc') {
                            $fieldsvalue = $vo['fieldsvalue'];
                            $data['province_name'] = $fieldsvalue[0];
                            $data['city_name'] = $fieldsvalue[1];
                            $data['district_name'] = $fieldsvalue[2];
                            $vo['fieldsvalue'] = '';
                        } else {
                            $fieldsvalue = $vo['fieldsvalue'];
                            $data['province_name'] = $fieldsvalue['province_name'];
                            $data['city_name'] = $fieldsvalue['city_name'];
                            $data['district_name'] = $fieldsvalue['district_name'];
                            $data['dizhi'] = $fieldsvalue['address'];
                            $data['latitude'] = $fieldsvalue['latitude'];
                            $data['longitude'] = $fieldsvalue['longitude'];
                            $vo['fieldsvalue'] = $fieldsvalue['region_name'];
                        }
                    } else {
                        $vo['fieldsvalue'] = serialize($vo['fieldsvalue']);
                    }
                }

                if (!empty($vo['valuerules'])) {
                    if ($vo['is_sys'] == 1) {
                        $rule[$vo['fieldsmingcheng'] . '|' . $vo['viewmingcheng']] = ltrim($vo['valuerules'], "|");
                    } else {
                        $rule[$vo['inputtype'] . '_rule_' . $vo['id'] . '|' . $vo['viewmingcheng']] = ltrim($vo['valuerules'], "|");
                    }
                }

                if ($vo['is_sys'] == 1) {
                    if ($vo['fieldsmingcheng']) {
                        $data[$vo['fieldsmingcheng']] = $vo['fieldsvalue'];
                    }
                } else {
                    $data[$vo['inputtype'] . '_rule_' . $vo['id']] = $vo['fieldsvalue'];
                    if ($vo['inputtype']) {
                        $data[$vo['inputtype']][$vo['id']] = $vo['fieldsvalue'];
                    }
                    $customtext[$vo['inputtype']][$vo['id']] = $vo['fieldsvalue'];
                }
            }
            if ($terminal == 'pc') {

                $dizhi = $data['province_name'];
                if ($data['city_name']) {
                    $dizhi = $dizhi . $data['city_name'];
                }
                if ($data['district_name']) {
                    $dizhi = $dizhi . $data['district_name'];
                }
                if ($data['house_number']) {
                    $dizhi = $dizhi . $data['house_number'];
                }
                $coder = Geocoder::geocoding($dizhi);
                $data['latitude'] = $coder['latitude'];
                $data['longitude'] = $coder['longitude'];

                $data['region_name'] = $dizhi;
            }

            $data['customtext'] = serialize($customtext);
        }

        $result['rule'] = $rule;
        $result['data'] = $data;

        return  $result;
    }

    public static function setdata($data, $ptype)
    {
        $weid = weid();
        if (!empty($data)) {
            foreach ($data as &$vo) {
                $vo['weid'] = $weid;
                $vo['ptype'] = $ptype;
                if ($vo['is_sys'] === 0) {
                    $vo['is_sys'] = 0;
                } else {
                    $vo['is_sys'] = 1;
                }
                $vo['sort'] = 100;
                $vo['status'] = 1;
            }
        }
        return $data;
    }

    public static function createdata($data)
    {
        if (!empty($data)) {
            foreach ($data as $vo) {
                if (empty(self::where(['fieldsmingcheng' => $vo['fieldsmingcheng'], 'ptype' => $vo['ptype'], 'weid' => $vo['weid']])->find())) {
                    self::create($vo);
                }
            }
        }
        return $data;
    }

    public static function importData($Mod, $ptype, $data)
    {
        $data = $data;
        $ptype = $ptype;
        $weid = weid();
        $list = [];

        $importField = RegisterField::getimportField($ptype);

        foreach ($data as $k => $v) {

            $info['weid'] = $weid;

            foreach ($importField as $key => $fvo) {

                if ($fvo['inputtype'] == 'text' || $fvo['inputtype'] == 'textarea') {
                    if ($fvo['is_sys'] == 1) {
                        $info[$fvo['fieldsmingcheng']] = $v[$fvo['viewmingcheng']];
                    } else {
                        $customtext['text'][$fvo['id']] = $v[$fvo['viewmingcheng']];
                    }
                } elseif ($fvo['inputtype'] == 'select') {
                    if ($fvo['is_sys'] == 1) {
                        $info[$fvo['fieldsmingcheng']] = $v[$fvo['viewmingcheng']];
                    } else {
                        $customtext['select'][$fvo['id']] = $v[$fvo['viewmingcheng']];
                    }
                } elseif ($fvo['inputtype'] == 'radio') {
                    if ($fvo['is_sys'] == 1) {
                        if ($fvo['fieldsmingcheng'] == "sex") {
                            if ($v[$fvo['viewmingcheng']] == '保密') {
                                $info[$fvo['fieldsmingcheng']] = 0;
                            } elseif ($v[$fvo['viewmingcheng']] == '男') {
                                $info[$fvo['fieldsmingcheng']] = 1;
                            } elseif ($v[$fvo['viewmingcheng']] == '女') {
                                $info[$fvo['fieldsmingcheng']] = 2;
                            }
                        }
                    } else {
                        $customtext['radio'][$fvo['id']] = $v[$fvo['viewmingcheng']];
                    }
                } elseif ($fvo['inputtype'] == 'radio') {
                    if ($fvo['is_sys'] == 1) {
                        $info[$fvo['fieldsmingcheng']] = $v[$fvo['viewmingcheng']];
                    } else {
                        $customtext['radio'][$fvo['id']] = $v[$fvo['viewmingcheng']];
                    }
                }
            }

            $info['customtext'] = serialize($customtext);

            $info['regdate'] = time();
            $info['lastdate'] = time();

            $is_repeat = 0;

            if (empty($is_repeat)) {
                $Mod->create($info);
            }
        }
    }

    public static function dumpdata($query, $ptype, $page)
    {
        $limit = config('my.dumpsize') ? config('my.dumpsize') : 1000;

        $count = $query->count();
        $res = $query->order('id desc')
            ->limit(($page - 1) * $limit, $limit)
            ->select()
            ->toArray();

        foreach ($res as $key => $val) {
            $res[$key]['sex'] = getItemVal($val['sex'], '[{"key":"男","val":"1","label_color":""},{"key":"女","val":"2","label_color":""}]');
            $res[$key]['status'] = getItemVal($val['status'], '[{"key":"开启","val":"1"},{"key":"关闭","val":"0"}]');
        }
        $importField = RegisterField::getimportField($ptype);
        $datalist = [];
        if (!empty($res)) {

            foreach ($res as &$voo) {
                if ($ptype == "member") {
                    $voo = Member::conversion($voo);
                }
                if ($ptype == "agent") {
                    $voo = Agent::conversion($voo);
                }
                if ($ptype == "technical") {
                    $voo = Technical::conversion($voo);
                }
                if ($ptype == "store") {
                    $voo = Store::conversion($voo);
                }
                if ($ptype == "tuanzhang") {
                    $voo = Tuanzhang::conversion($voo);
                }
            }

            foreach ($res as $k => $vo) {

                foreach ($importField as $key => $fvo) {

                    if ($fvo['inputtype'] == 'text' || $fvo['inputtype'] == 'textarea') {
                        if ($fvo['is_sys'] == 1) {
                            $datalist[$k][$key] = $vo[$fvo['fieldsmingcheng']];
                        } else {
                            $datalist[$k][$key] = iunserializer($vo['customtext'])[$fvo['inputtype']][$fvo['id']];
                        }
                    } elseif ($fvo['inputtype'] == 'select') {
                        if ($fvo['is_sys'] == 1) {

                            if ($fvo['fieldsmingcheng'] == 'city_id') {
                                $datalist[$k][$key] =  Area::get_area_name($vo['city_id']);
                            } elseif ($fvo['fieldsmingcheng'] == 'country_id') {
                                $datalist[$k][$key] =  Area::get_area_name($vo['country_id']);
                            } elseif ($fvo['fieldsmingcheng'] == 'province_id') {
                                $datalist[$k][$key] =  Area::get_area_name($vo['province_id']);
                            }
                        } else {
                            $datalist[$k][$key] = iunserializer($vo['customtext'])[$fvo['inputtype']][$fvo['id']];
                        }
                    } elseif ($fvo['inputtype'] == 'lbs') {
                        if ($fvo['is_sys'] == 1) {
                            if ($fvo['fieldsmingcheng'] == 'city_id') {
                                $datalist[$k][$key] =  Area::get_area_name($vo['city_id']);
                            } elseif ($fvo['fieldsmingcheng'] == 'country_id') {
                                $datalist[$k][$key] =  Area::get_area_name($vo['country_id']);
                            } elseif ($fvo['fieldsmingcheng'] == 'province_id') {
                                $datalist[$k][$key] =  Area::get_area_name($vo['province_id']);
                            }
                        } else {
                            $tmp = iunserializer(iunserializer($vo['customtext'])[$fvo['inputtype']][$fvo['id']]);
                            if (!empty($tmp)) {
                                if (!empty($tmp['region_name'])) {
                                    $datalist[$k][$key] = '[' . $tmp['region_name'] . ']';
                                }
                                $datalist[$k][$key] = $datalist[$k][$key] . $tmp['address'];
                            } else {
                                $datalist[$k][$key] = '';
                            }
                        }
                    } elseif ($fvo['inputtype'] == 'radio') {
                        if ($fvo['is_sys'] == 1) {
                            if ($fvo['fieldsmingcheng'] == "sex") {
                                if (empty($vo[$fvo['fieldsmingcheng']])) {
                                    $datalist[$k][$key] = '保密';
                                } elseif ($vo[$fvo['fieldsmingcheng']] == '1') {
                                    $datalist[$k][$key] = '男';
                                } elseif ($vo[$fvo['fieldsmingcheng']] == '2') {
                                    $datalist[$k][$key] = '女';
                                }
                            } else {
                                $datalist[$k][$key] = $vo[$fvo['fieldsmingcheng']];
                            }
                        } else {
                            $datalist[$k][$key] = iunserializer($vo['customtext'])[$fvo['inputtype']][$fvo['id']];
                        }
                    } elseif ($fvo['inputtype'] == 'checkbox') {
                        if ($fvo['is_sys'] == 1) {
                            $datalist[$k][$key] = $vo[$fvo['fieldsmingcheng']];
                        } else {
                            $datalist[$k][$key] = iunserializer($vo['customtext'])[$fvo['inputtype']][$fvo['id']];
                        }
                    } elseif ($fvo['inputtype'] == 'date') {
                        $datalist[$k][$key] = time_format($vo[$fvo['fieldsmingcheng']]);
                    }

                    if (empty($datalist[$k][$key])) {
                        $datalist[$k][$key] = '';
                    }
                    //$ii = $key;
                }
            }
        }

        foreach ($importField as $key => $vo) {
            $data['header'][$key] = $vo['viewmingcheng'];
        }
        //var_dump($datalist);
        //var_dump($res);

        $data['percentage'] = ceil($page * 100 / ceil($count / $limit));
        $data['filename'] = '数据.' . config('my.dump_extension');
        $data['data'] = $datalist;
        return $data;
    }

    public static function datainitial($ptype)
    {
        $weid = weid();
        $data['member'] = [
            [
                'fieldsmingcheng' => 'userpic',
                'viewmingcheng' => '头像',
                'inputtype' => 'pic',
                'is_front' => 1,
                'is_frontinput' => 1,
                'is_input' => 1,
                'is_import' => 1,
                'is_listView' => 1
            ],
            [
                'fieldsmingcheng' => 'nickname',
                'viewmingcheng' => '昵称',
                'inputtype' => 'text',
                'valuerules' => 'require',
                'is_front' => 1,
                'is_frontinput' => 1,
                'is_input' => 1,
                'is_import' => 1,
                'is_listView' => 1,
            ], [
                'fieldsmingcheng' => 'password',
                'viewmingcheng' => '密码',
                'inputtype' => 'text',
                'valuerules' => 'require',
                'is_front' => 0,
                'is_frontinput' => 1,
                'is_input' => 1,
                'is_import' => 1,
                'is_listView' => 0,
            ], [
                'fieldsmingcheng' => 'gid',
                'viewmingcheng' => '会员等级',
                'inputtype' => 'select',
                'is_front' => 0,
                'is_frontinput' => 0,
                'is_input' => 1,
                'is_import' => 1,
                'is_listView' => 1
            ],
            [
                'fieldsmingcheng' => 'pid',
                'viewmingcheng' => '推荐人',
                'inputtype' => 'text',
                'is_front' => 0,
                'is_input' => 0,
                'is_import' => 1,
                'is_listView' => 1
            ], [
                'fieldsmingcheng' => 'telephone',
                'viewmingcheng' => '手机号',
                'valuerules' => 'require|mobile',
                'inputtype' => 'text',
                'is_front' => 1,
                'is_frontinput' => 1,
                'is_input' => 1,
                'is_import' => 1,
                'is_listView' => 1
            ], [
                'fieldsmingcheng' => 'address',
                'viewmingcheng' => '地址',
                'inputtype' => 'text',
                'is_front' => 1,
                'is_frontinput' => 1,
                'is_input' => 1,
                'is_import' => 1
            ],  [
                'fieldsmingcheng' => 'balance',
                'viewmingcheng' => '余额',
                'inputtype' => 'text',
                'is_input' => 1,
                'is_listView' => 1
            ], [
                'fieldsmingcheng' => 'points',
                'viewmingcheng' => '积分',
                'inputtype' => 'text',
                'is_input' => 1,
                'is_import' => 1,
                'is_listView' => 1,
            ], [
                'fieldsmingcheng' => 'regdate',
                'viewmingcheng' => '注册时间',
                'inputtype' => 'text',
                'is_input' => 0,
                'is_listView' => 1
            ], [
                'fieldsmingcheng' => 'lastdate',
                'viewmingcheng' => '最后登录',
                'inputtype' => 'text',
                'is_input' => 0,
                'is_import' => 1,
                'is_listView' => 1
            ], [
                'fieldsmingcheng' => 'email',
                'viewmingcheng' => '邮箱',
                'inputtype' => 'text',
                'is_import' => 1,
                'is_input' => 1
            ], [
                'fieldsmingcheng' => 'status',
                'viewmingcheng' => '是否审核',
                'is_import' => 1,
                'inputtype' => 'switch',
                'is_input' => 1
            ]

        ];

        $data['agent'] = [
            [
                'fieldsmingcheng' => 'title',
                'viewmingcheng' => '姓名',
                'inputtype' => 'text',
                'valuerules' => 'require',
                'is_front' => 1,
                'is_frontinput' => 1,
                'is_input' => 1,
                'is_import' => 1,
                'is_listView' => 1,
            ], [
                'fieldsmingcheng' => 'tel',
                'viewmingcheng' => '手机号',
                'inputtype' => 'text',
                'valuerules' => 'require|mobile',
                'is_front' => 1,
                'is_frontinput' => 1,
                'is_input' => 1,
                'is_import' => 1,
                'is_listView' => 1
            ], [
                'fieldsmingcheng' => 'agent_level',
                'viewmingcheng' => '分销等级',
                'inputtype' => 'select',
                'is_front' => 0,
                'is_input' => 1,
                'is_import' => 1,
                'is_listView' => 1
            ], [
                'fieldsmingcheng' => 'touxiang',
                'viewmingcheng' => '头像',
                'inputtype' => 'pic',
                'is_front' => 1,
                'is_frontinput' => 1,
                'is_input' => 1,
                'is_import' => 1,
                'is_listView' => 1
            ], [
                'fieldsmingcheng' => 'pid_code',
                'viewmingcheng' => '邀请码',
                'inputtype' => 'text',
                'is_sys' => 0,
                'is_front' => 0,
                'is_input' => 0,
                'is_import' => 0,
                'is_listView' => 0
            ], [
                'fieldsmingcheng' => 'total_income',
                'viewmingcheng' => '总收入',
                'inputtype' => 'text',
                'is_front' => 0,
                'is_input' => 1,
                'is_import' => 1,
                'is_listView' => 1
            ], [
                'fieldsmingcheng' => 'income',
                'viewmingcheng' => '收入',
                'inputtype' => 'text',
                'is_front' => 0,
                'is_input' => 1,
                'is_import' => 1,
                'is_listView' => 1,
            ], [
                'fieldsmingcheng' => 'cash',
                'viewmingcheng' => '已提现',
                'inputtype' => 'text',
                'is_front' => 0,
                'is_input' => 1,
                'is_import' => 1,
                'is_listView' => 1,
            ], [
                'fieldsmingcheng' => 'no_cash',
                'viewmingcheng' => '未提现',
                'inputtype' => 'text',
                'is_front' => 0,
                'is_input' => 1,
                'is_import' => 1,
                'is_listView' => 1
            ], [
                'fieldsmingcheng' => 'create_time',
                'viewmingcheng' => '加入时间',
                'inputtype' => 'text',
                'is_front' => 0,
                'is_input' => 0,
                'is_import' => 1,
                'is_listView' => 1
            ], [
                'fieldsmingcheng' => 'email',
                'viewmingcheng' => '邮箱',
                'inputtype' => 'text',
                'is_front' => 0,
                'is_import' => 1,
                'is_input' => 1
            ], [
                'fieldsmingcheng' => 'status',
                'viewmingcheng' => '是否审核',
                'inputtype' => 'switch',
                'is_front' => 0,
                'is_input' => 1,
                'is_import' => 1
            ]

        ];

        $data['partner'] = [
            [
                'fieldsmingcheng' => 'title',
                'viewmingcheng' => '姓名',
                'inputtype' => 'text',
                'valuerules' => 'require',
                'is_front' => 1,
                'is_frontinput' => 1,
                'is_input' => 1,
                'is_import' => 1,
                'is_listView' => 1,
            ], [
                'fieldsmingcheng' => 'level',
                'viewmingcheng' => '等级',
                'inputtype' => 'select',
                'is_front' => 0,
                'is_input' => 1,
                'is_import' => 1,
                'is_listView' => 1
            ], [
                'fieldsmingcheng' => 'tel',
                'viewmingcheng' => '手机号',
                'inputtype' => 'text',
                'valuerules' => 'require|mobile',
                'is_front' => 1,
                'is_frontinput' => 1,
                'is_input' => 1,
                'is_import' => 1,
                'is_listView' => 1
            ], [
                'fieldsmingcheng' => 'touxiang',
                'viewmingcheng' => '头像',
                'inputtype' => 'pic',
                'is_front' => 1,
                'is_frontinput' => 1,
                'is_input' => 1,
                'is_import' => 1,
                'is_listView' => 1
            ], [
                'fieldsmingcheng' => 'total_income',
                'viewmingcheng' => '总收入',
                'inputtype' => 'text',
                'is_front' => 0,
                'is_input' => 1,
                'is_import' => 1,
                'is_listView' => 1
            ], [
                'fieldsmingcheng' => 'income',
                'viewmingcheng' => '收入',
                'inputtype' => 'text',
                'is_front' => 0,
                'is_input' => 1,
                'is_import' => 1,
                'is_listView' => 1,
            ], [
                'fieldsmingcheng' => 'cash',
                'viewmingcheng' => '已提现',
                'inputtype' => 'text',
                'is_front' => 0,
                'is_input' => 1,
                'is_import' => 1,
                'is_listView' => 1,
            ], [
                'fieldsmingcheng' => 'no_cash',
                'viewmingcheng' => '未提现',
                'inputtype' => 'text',
                'is_front' => 0,
                'is_input' => 1,
                'is_import' => 1,
                'is_listView' => 1
            ], [
                'fieldsmingcheng' => 'create_time',
                'viewmingcheng' => '加入时间',
                'inputtype' => 'text',
                'is_front' => 0,
                'is_input' => 0,
                'is_import' => 1,
                'is_listView' => 1
            ], [
                'fieldsmingcheng' => 'email',
                'viewmingcheng' => '邮箱',
                'inputtype' => 'text',
                'is_front' => 0,
                'is_import' => 1,
                'is_input' => 1
            ], [
                'fieldsmingcheng' => 'status',
                'viewmingcheng' => '是否审核',
                'inputtype' => 'switch',
                'is_front' => 0,
                'is_input' => 1,
                'is_import' => 1
            ]

        ];

        $data['complete'] = [
            [
                'fieldsmingcheng' => 'process',
                'viewmingcheng' => '施工过程',
                'inputtype' => 'pics',
                'is_sys' => 0,
                'is_front' => 1,
                'is_frontinput' => 1,
                'is_input' => 1,
                'is_import' => 0,
                'is_listView' => 1
            ], [
                'fieldsmingcheng' => 'acceptance',
                'viewmingcheng' => '验收现场',
                'inputtype' => 'pics',
                'is_sys' => 0,
                'is_front' => 1,
                'is_frontinput' => 1,
                'is_input' => 1,
                'is_import' => 0,
                'is_listView' => 1
            ], [
                'fieldsmingcheng' => 'customeracceptance',
                'viewmingcheng' => '顾客验收表',
                'inputtype' => 'pics',
                'is_sys' => 0,
                'is_front' => 1,
                'is_frontinput' => 1,
                'is_input' => 1,
                'is_import' => 0,
                'is_listView' => 1
            ]

        ];

        $data['technical'] = [
            [
                'fieldsmingcheng' => 'username',
                'viewmingcheng' => '帐号',
                'inputtype' => 'text',
                'valuerules' => 'require',
                'is_front' => 0,
                'is_frontinput' => 1,
                'is_input' => 1,
                'is_import' => 1,
                'is_listView' => 1,
            ], [
                'fieldsmingcheng' => 'password',
                'viewmingcheng' => '密码',
                'inputtype' => 'text',
                'valuerules' => 'require',
                'is_front' => 0,
                'is_frontinput' => 1,
                'is_input' => 1,
                'is_import' => 1,
                'is_listView' => 0,
            ], [
                'fieldsmingcheng' => 'title',
                'viewmingcheng' => '姓名',
                'valuerules' => 'require',
                'inputtype' => 'text',
                'is_front' => 1,
                'is_frontinput' => 1,
                'is_input' => 1,
                'is_import' => 1,
                'is_listView' => 1,
            ], [
                'fieldsmingcheng' => 'level',
                'viewmingcheng' => '等级',
                'inputtype' => 'select',
                'is_front' => 0,
                'is_input' => 1,
                'is_import' => 1,
                'is_listView' => 1
            ], [
                'fieldsmingcheng' => 'certificate_ids',
                'viewmingcheng' => '师傅认证',
                'inputtype' => 'checkbox',
                'is_front' => 0,
                'is_frontinput' => 1,
                'is_input' => 1,
                'is_import' => 1,
                'is_listView' => 1
            ], [
                'fieldsmingcheng' => 'tel',
                'viewmingcheng' => '手机号',
                'inputtype' => 'text',
                'valuerules' => 'require|mobile',
                'is_front' => 0,
                'is_frontinput' => 1,
                'is_input' => 1,
                'is_import' => 1,
                'is_listView' => 1
            ], [
                'fieldsmingcheng' => 'touxiang',
                'viewmingcheng' => '头像',
                'inputtype' => 'pic',
                'is_front' => 0,
                'is_frontinput' => 0,
                'is_input' => 0,
                'is_import' => 1,
                'is_listView' => 1
            ], [
                'fieldsmingcheng' => 'photoalbum',
                'viewmingcheng' => '相册',
                'inputtype' => 'pics',
                'is_front' => 1,
                'is_frontinput' => 1,
                'is_input' => 1,
                'is_import' => 1,
                'is_listView' => 0
            ], [
                'fieldsmingcheng' => 'videourl',
                'viewmingcheng' => '视频',
                'inputtype' => 'video',
                'is_front' => 0,
                'is_frontinput' => 1,
                'is_input' => 1,
                'is_import' => 0,
                'is_listView' => 0
            ], [
                'fieldsmingcheng' => 'idpic1',
                'viewmingcheng' => '身份证正面',
                'inputtype' => 'pic',
                'is_sys' => 0,
                'is_front' => 0,
                'is_frontinput' => 1,
                'is_import' => 1,
                'is_input' => 1
            ], [
                'fieldsmingcheng' => 'idpic2',
                'viewmingcheng' => '身份证背面',
                'inputtype' => 'pic',
                'is_sys' => 0,
                'is_front' => 0,
                'is_frontinput' => 1,
                'is_import' => 1,
                'is_input' => 1
            ], [
                'fieldsmingcheng' => 'workunits',
                'viewmingcheng' => '工作单位',
                'inputtype' => 'text',
                'is_front' => 0,
                'is_frontinput' => 0,
                'is_input' => 1,
                'is_import' => 1,
                'is_listView' => 0
            ], [
                'fieldsmingcheng' => 'region_name',
                'viewmingcheng' => '地理位置',
                'inputtype' => 'lbs',
                'valuerules' => 'require',
                'is_sys' => 1,
                'is_front' => 0,
                'is_frontinput' => 1,
                'is_import' => 1,
                'is_input' => 1,
                'is_listView' => 1
            ], [
                'fieldsmingcheng' => 'house_number',
                'viewmingcheng' => '详细地址',
                'inputtype' => 'text',
                'is_sys' => 1,
                'is_front' => 0,
                'is_frontinput' => 1,
                'is_import' => 1,
                'is_input' => 1,
                'is_import' => 1,
                'is_listView' => 0
            ], [
                'fieldsmingcheng' => 'cate_ids',
                'viewmingcheng' => '可接的服务',
                'inputtype' => 'checkbox',
                'valuerules' => 'require',
                'is_front' => 0,
                'is_frontinput' => 1,
                'is_input' => 1,
                'is_import' => 1,
                'is_listView' => 1
            ], [
                'fieldsmingcheng' => 'sid',
                'viewmingcheng' => '所属店铺',
                'inputtype' => 'select',
                'is_front' => 0,
                'is_frontinput' => 1,
                'is_input' => 1,
                'is_import' => 1,
                'is_listView' => 1
            ], [
                'fieldsmingcheng' => 'introduction',
                'viewmingcheng' => '擅长与简介',
                'inputtype' => 'textarea',
                'is_front' => 1,
                'is_frontinput' => 1,
                'is_input' => 1,
                'is_import' => 1,
                'is_listView' => 0
            ], [
                'fieldsmingcheng' => 'total_income',
                'viewmingcheng' => '总收入',
                'inputtype' => 'text',
                'is_input' => 1,
                'is_import' => 1,
                'is_listView' => 1
            ], [
                'fieldsmingcheng' => 'income',
                'viewmingcheng' => '收入',
                'inputtype' => 'text',
                'is_input' => 1,
                'is_import' => 1,
                'is_listView' => 1,
            ], [
                'fieldsmingcheng' => 'points',
                'viewmingcheng' => '积分',
                'inputtype' => 'text',
                'is_input' => 1,
                'is_import' => 1,
                'is_listView' => 1
            ], [
                'fieldsmingcheng' => 'service_times_base',
                'viewmingcheng' => '服务次数基数',
                'inputtype' => 'text',
                'is_input' => 1,
                'is_import' => 1,
                'is_listView' => 0
            ], [
                'fieldsmingcheng' => 'comment_base',
                'viewmingcheng' => '评价基数',
                'inputtype' => 'text',
                'is_input' => 1,
                'is_import' => 1,
                'is_listView' => 0
            ], [
                'fieldsmingcheng' => 'viewed_base',
                'viewmingcheng' => '人气基数',
                'inputtype' => 'text',
                'is_input' => 1,
                'is_import' => 1,
                'is_listView' => 0
            ], [
                'fieldsmingcheng' => 'end_time',
                'viewmingcheng' => '到期时间',
                'inputtype' => 'date',
                'is_input' => 1,
                'is_import' => 1,
                'is_listView' => 1
            ], [
                'fieldsmingcheng' => 'create_time',
                'viewmingcheng' => '加入时间',
                'inputtype' => 'text',
                'is_input' => 0,
                'is_import' => 1,
                'is_listView' => 1
            ], [
                'fieldsmingcheng' => 'email',
                'viewmingcheng' => '邮箱',
                'inputtype' => 'text',
                'is_import' => 1,
                'is_input' => 1
            ], [
                'fieldsmingcheng' => 'sort',
                'viewmingcheng' => '排序',
                'inputtype' => 'text',
                'is_import' => 1,
                'is_input' => 1,
                'is_listView' => 1
            ], [
                'fieldsmingcheng' => 'status',
                'viewmingcheng' => '是否审核',
                'inputtype' => 'switch',
                'is_input' => 1,
                'is_import' => 1
            ]

        ];

        $data['tuanzhang'] = [
            [
                'fieldsmingcheng' => 'username',
                'viewmingcheng' => '帐号',
                'inputtype' => 'text',
                'valuerules' => 'require',
                'is_front' => 0,
                'is_frontinput' => 1,
                'is_input' => 0,
                'is_import' => 1,
                'is_listView' => 0,
            ], [
                'fieldsmingcheng' => 'password',
                'viewmingcheng' => '密码',
                'inputtype' => 'text',
                'valuerules' => 'require',
                'is_front' => 0,
                'is_frontinput' => 1,
                'is_input' => 0,
                'is_import' => 1,
                'is_listView' => 0,
            ], [
                'fieldsmingcheng' => 'community_title',
                'viewmingcheng' => '社区名称',
                'inputtype' => 'text',
                'valuerules' => 'require',
                'is_front' => 1,
                'is_frontinput' => 1,
                'is_input' => 1,
                'is_import' => 1,
                'is_listView' => 1,
            ], [
                'fieldsmingcheng' => 'title',
                'viewmingcheng' => '团长姓名',
                'inputtype' => 'text',
                'valuerules' => 'require',
                'is_front' => 1,
                'is_frontinput' => 1,
                'is_input' => 1,
                'is_import' => 1,
                'is_listView' => 1,
            ], [
                'fieldsmingcheng' => 'level',
                'viewmingcheng' => '等级',
                'inputtype' => 'select',
                'is_front' => 0,
                'is_input' => 1,
                'is_import' => 1,
                'is_listView' => 1
            ], [
                'fieldsmingcheng' => 'tel',
                'viewmingcheng' => '手机号',
                'inputtype' => 'text',
                'valuerules' => 'require|mobile',
                'is_front' => 1,
                'is_frontinput' => 1,
                'is_input' => 1,
                'is_import' => 1,
                'is_listView' => 1
            ], [
                'fieldsmingcheng' => 'touxiang',
                'viewmingcheng' => '头像',
                'inputtype' => 'pic',
                'is_front' => 1,
                'is_frontinput' => 1,
                'is_input' => 1,
                'is_import' => 1,
                'is_listView' => 1
            ], [
                'fieldsmingcheng' => 'idpic1',
                'viewmingcheng' => '身份证正面',
                'inputtype' => 'pic',
                'is_sys' => 0,
                'is_front' => 1,
                'is_frontinput' => 1,
                'is_import' => 1,
                'is_input' => 1
            ], [
                'fieldsmingcheng' => 'idpic2',
                'viewmingcheng' => '身份证背面',
                'inputtype' => 'pic',
                'is_sys' => 0,
                'is_front' => 1,
                'is_frontinput' => 1,
                'is_import' => 1,
                'is_input' => 1
            ], [
                'fieldsmingcheng' => 'region_name',
                'viewmingcheng' => '地理位置',
                'inputtype' => 'lbs',
                'valuerules' => 'require',
                'is_sys' => 1,
                'is_front' => 1,
                'is_frontinput' => 1,
                'is_import' => 1,
                'is_input' => 1,
                'is_listView' => 1
            ], [
                'fieldsmingcheng' => 'house_number',
                'viewmingcheng' => '详细地址',
                'inputtype' => 'text',
                'valuerules' => 'require',
                'is_sys' => 1,
                'is_front' => 1,
                'is_frontinput' => 1,
                'is_import' => 1,
                'is_input' => 1,
                'is_import' => 1,
                'is_listView' => 0
            ], [
                'fieldsmingcheng' => 'introduction',
                'viewmingcheng' => '简介',
                'inputtype' => 'textarea',
                'is_front' => 1,
                'is_frontinput' => 1,
                'is_input' => 1,
                'is_import' => 1,
                'is_listView' => 0
            ], [
                'fieldsmingcheng' => 'total_income',
                'viewmingcheng' => '总收入',
                'inputtype' => 'text',
                'is_input' => 1,
                'is_import' => 1,
                'is_listView' => 1
            ], [
                'fieldsmingcheng' => 'income',
                'viewmingcheng' => '收入',
                'inputtype' => 'text',
                'is_input' => 1,
                'is_import' => 1,
                'is_listView' => 1,
            ], [
                'fieldsmingcheng' => 'points',
                'viewmingcheng' => '积分',
                'inputtype' => 'text',
                'is_input' => 1,
                'is_import' => 1,
                'is_listView' => 1
            ], [
                'fieldsmingcheng' => 'create_time',
                'viewmingcheng' => '加入时间',
                'inputtype' => 'text',
                'is_input' => 0,
                'is_import' => 1,
                'is_listView' => 1
            ], [
                'fieldsmingcheng' => 'email',
                'viewmingcheng' => '邮箱',
                'inputtype' => 'text',
                'is_import' => 1,
                'is_input' => 1
            ], [
                'fieldsmingcheng' => 'sort',
                'viewmingcheng' => '排序',
                'inputtype' => 'text',
                'is_import' => 1,
                'is_input' => 1,
                'is_listView' => 1
            ], [
                'fieldsmingcheng' => 'status',
                'viewmingcheng' => '是否审核',
                'inputtype' => 'switch',
                'is_input' => 1,
                'is_import' => 1
            ]

        ];

        $data['operatingcity'] = [
            [
                'fieldsmingcheng' => 'username',
                'viewmingcheng' => '帐号',
                'inputtype' => 'text',
                'valuerules' => 'require',
                'is_front' => 0,
                'is_frontinput' => 1,
                'is_input' => 0,
                'is_import' => 1,
                'is_listView' => 0,
            ], [
                'fieldsmingcheng' => 'password',
                'viewmingcheng' => '密码',
                'inputtype' => 'text',
                'valuerules' => 'require',
                'is_front' => 0,
                'is_frontinput' => 1,
                'is_input' => 0,
                'is_import' => 1,
                'is_listView' => 0,
            ], [
                'fieldsmingcheng' => 'title',
                'viewmingcheng' => '名称',
                'inputtype' => 'text',
                'valuerules' => 'require',
                'is_front' => 1,
                'is_frontinput' => 1,
                'is_input' => 1,
                'is_import' => 1,
                'is_listView' => 1,
            ], [
                'fieldsmingcheng' => 'level',
                'viewmingcheng' => '等级',
                'inputtype' => 'select',
                'is_front' => 0,
                'is_input' => 1,
                'is_import' => 1,
                'is_listView' => 1
            ], [
                'fieldsmingcheng' => 'areatype',
                'viewmingcheng' => '类型',
                'inputtype' => 'radio',
                'is_front' => 1,
                'is_frontinput' => 1,
                'is_input' => 1,
                'is_import' => 1,
                'is_listView' => 1
            ], [
                'fieldsmingcheng' => 'region_name',
                'viewmingcheng' => '地理位置',
                'inputtype' => 'lbs',
                'valuerules' => 'require',
                'is_sys' => 1,
                'is_front' => 1,
                'is_frontinput' => 1,
                'is_import' => 1,
                'is_input' => 1,
                'is_listView' => 1
            ], [
                'fieldsmingcheng' => 'house_number',
                'viewmingcheng' => '详细地址',
                'inputtype' => 'text',
                'valuerules' => 'require',
                'is_sys' => 1,
                'is_front' => 1,
                'is_frontinput' => 1,
                'is_import' => 1,
                'is_input' => 1,
                'is_import' => 1,
                'is_listView' => 0
            ],
            [
                'fieldsmingcheng' => 'total_income',
                'viewmingcheng' => '总收入',
                'inputtype' => 'text',
                'is_input' => 1,
                'is_import' => 1,
                'is_listView' => 1
            ], [
                'fieldsmingcheng' => 'income',
                'viewmingcheng' => '收入',
                'inputtype' => 'text',
                'is_input' => 1,
                'is_import' => 1,
                'is_listView' => 1,
            ], [
                'fieldsmingcheng' => 'cate_ids',
                'viewmingcheng' => '经营类目',
                'inputtype' => 'checkbox',
                'valuerules' => 'require',
                'is_front' => 1,
                'is_frontinput' => 1,
                'is_input' => 1,
                'is_import' => 1,
                'is_listView' => 1
            ], [
                'fieldsmingcheng' => 'create_time',
                'viewmingcheng' => '加入时间',
                'inputtype' => 'text',
                'is_input' => 0,
                'is_listView' => 1
            ], [
                'fieldsmingcheng' => 'end_time',
                'viewmingcheng' => '到期时间',
                'inputtype' => 'date',
                'is_input' => 1,
                'is_import' => 1,
                'is_listView' => 1
            ], [
                'fieldsmingcheng' => 'status',
                'viewmingcheng' => '是否审核',
                'inputtype' => 'switch',
                'is_input' => 1,
                'is_import' => 1
            ]

        ];

        $data['store'] = [
            [
                'fieldsmingcheng' => 'username',
                'viewmingcheng' => '帐号',
                'inputtype' => 'text',
                'valuerules' => 'require',
                'is_front' => 0,
                'is_frontinput' => 1,
                'is_input' => 0,
                'is_import' => 1,
                'is_listView' => 0,
            ], [
                'fieldsmingcheng' => 'password',
                'viewmingcheng' => '密码',
                'inputtype' => 'text',
                'valuerules' => 'require',
                'is_front' => 0,
                'is_frontinput' => 1,
                'is_input' => 0,
                'is_import' => 1,
                'is_listView' => 0,
            ], [
                'fieldsmingcheng' => 'title',
                'viewmingcheng' => '店名',
                'inputtype' => 'text',
                'valuerules' => 'require',
                'is_front' => 1,
                'is_frontinput' => 1,
                'is_input' => 1,
                'is_import' => 1,
                'is_listView' => 1,
            ], [
                'fieldsmingcheng' => 'level',
                'viewmingcheng' => '等级',
                'inputtype' => 'select',
                'is_front' => 0,
                'is_input' => 1,
                'is_import' => 1,
                'is_listView' => 1
            ], [
                'fieldsmingcheng' => 'store_logo',
                'viewmingcheng' => '店LOGO',
                'inputtype' => 'pic',
                'is_front' => 1,
                'is_frontinput' => 1,
                'is_input' => 1,
                'is_import' => 1,
                'is_listView' => 1
            ], [
                'fieldsmingcheng' => 'license',
                'viewmingcheng' => '营业执照',
                'inputtype' => 'pic',
                'is_front' => 1,
                'is_frontinput' => 1,
                'is_input' => 1,
                'is_import' => 1,
                'is_listView' => 1
            ], [
                'fieldsmingcheng' => 'tel',
                'viewmingcheng' => '电话',
                'inputtype' => 'text',
                'valuerules' => 'require|mobile',
                'is_front' => 1,
                'is_frontinput' => 1,
                'is_input' => 1,
                'is_import' => 1,
                'is_listView' => 1,
            ], [
                'fieldsmingcheng' => 'content',
                'viewmingcheng' => '店铺简介',
                'inputtype' => 'textarea',
                'is_sys' => 1,
                'is_front' => 1,
                'is_frontinput' => 1,
                'is_import' => 1,
                'is_input' => 1,
                'is_import' => 1,
                'is_listView' => 0
            ], [
                'fieldsmingcheng' => 'region_name',
                'viewmingcheng' => '地理位置',
                'inputtype' => 'lbs',
                'valuerules' => 'require',
                'is_sys' => 1,
                'is_front' => 1,
                'is_frontinput' => 1,
                'is_import' => 1,
                'is_input' => 1,
                'is_listView' => 1
            ], [
                'fieldsmingcheng' => 'house_number',
                'viewmingcheng' => '详细地址',
                'inputtype' => 'text',
                'is_sys' => 1,
                'is_front' => 1,
                'is_frontinput' => 1,
                'is_import' => 1,
                'is_input' => 1,
                'is_import' => 1,
                'is_listView' => 0
            ],
            [
                'fieldsmingcheng' => 'total_income',
                'viewmingcheng' => '总收入',
                'inputtype' => 'text',
                'is_input' => 1,
                'is_import' => 1,
                'is_listView' => 1
            ], [
                'fieldsmingcheng' => 'income',
                'viewmingcheng' => '收入',
                'inputtype' => 'text',
                'is_input' => 1,
                'is_import' => 1,
                'is_listView' => 1,
            ], [
                'fieldsmingcheng' => 'cate_ids',
                'viewmingcheng' => '可接的服务',
                'inputtype' => 'checkbox',
                'is_front' => 1,
                'is_frontinput' => 1,
                'is_input' => 1,
                'is_import' => 1,
                'is_listView' => 1
            ], [
                'fieldsmingcheng' => 'create_time',
                'viewmingcheng' => '加入时间',
                'inputtype' => 'text',
                'is_input' => 0,
                'is_listView' => 1
            ], [
                'fieldsmingcheng' => 'end_time',
                'viewmingcheng' => '到期时间',
                'inputtype' => 'date',
                'is_input' => 1,
                'is_import' => 1,
                'is_listView' => 1
            ], [
                'fieldsmingcheng' => 'sort',
                'viewmingcheng' => '排序',
                'inputtype' => 'text',
                'is_import' => 1,
                'is_input' => 1,
                'is_listView' => 1
            ], [
                'fieldsmingcheng' => 'status',
                'viewmingcheng' => '是否审核',
                'inputtype' => 'switch',
                'is_input' => 1,
                'is_import' => 1
            ]

        ];

        $data =  self::setdata($data[$ptype], $ptype);
        self::createdata($data);
    }
}
