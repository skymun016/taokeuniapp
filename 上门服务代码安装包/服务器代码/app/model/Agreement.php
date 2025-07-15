<?php

namespace app\model;

use think\Model;

class Agreement extends Model
{

    protected $connection = 'mysql';

    protected $pk = 'id';

    protected $name = 'agreement';

    public static function datainitial()
    {
        $weid = weid();
        $data[1] = [
            'weid' => $weid,
            'code' => 'userstobuy',
            'name' => '用户购买协议',
            'title' => '用户购买协议',
            'content' => '',
            'status' => 1,
            'is_v2' => 1,
            'is_v3' => 1,
            'is_v6' => 1,
            'sort' => 10,
        ];
        $data[2] = [
            'weid' => $weid,
            'code' => 'technicaljoin',
            'name' => '师傅入驻协议及隐私政策',
            'title' => '师傅入驻协议及隐私政策',
            'content' => '',
            'status' => 1,
            'is_v2' => 0,
            'is_v3' => 1,
            'is_v6' => 1,
            'sort' => 20,
        ];
        $data[3] = [
            'weid' => $weid,
            'code' => 'storejoin',
            'name' => '商家入驻协议及隐私政策',
            'title' => '商家入驻协议及隐私政策',
            'content' => '',
            'status' => 1,
            'is_v2' => 1,
            'is_v3' => 1,
            'is_v6' => 0,
            'sort' => 30,
        ];
        $data[4] = [
            'weid' => $weid,
            'code' => 'operatingcityjoin',
            'name' => '城市代理入驻协议及隐私政策',
            'title' => '城市代理入驻协议及隐私政策',
            'content' => '',
            'status' => 1,
            'is_v2' => 1,
            'is_v3' => 1,
            'is_v6' => 1,
            'sort' => 40,
        ];
        $data[5] = [
            'weid' => $weid,
            'code' => 'communityjoin',
            'name' => '社区入驻协议及隐私政策',
            'title' => '社区代理入驻协议及隐私政策',
            'content' => '',
            'status' => 1,
            'is_v2' => 0,
            'is_v3' => 1,
            'is_v6' => 0,
            'sort' => 50,
        ];
        $data[6] = [
            'weid' => $weid,
            'code' => 'tuanzhangjoin',
            'name' => '团长入驻协议及隐私政策',
            'title' => '团长代理入驻协议及隐私政策',
            'content' => '',
            'status' => 1,
            'is_v2' => 1,
            'is_v3' => 0,
            'is_v6' => 0,
            'sort' => 50,
        ];
        $data[7] = [
            'weid' => $weid,
            'code' => 'agentjoin',
            'name' => '分销申请协议及隐私政策',
            'title' => '分销申请协议及隐私政策',
            'content' => '',
            'status' => 1,
            'is_v2' => 1,
            'is_v3' => 1,
            'is_v6' => 1,
            'sort' => 50,
        ];

        foreach ($data as $vo) {
            if (!(self::where(['weid' => $weid, 'code' => $vo['code']])->find())) {
                self::create($vo);
            }
        }
    }
}
