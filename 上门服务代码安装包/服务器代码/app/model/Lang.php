<?php

namespace app\model;

use think\Model;

class Lang extends Model
{
    protected $connection = 'mysql';

    protected $pk = 'id';

    protected $name = 'lang';

    public static function getLang()
    {

        $where['weid'] = weid();

        $where['status'] = 1;
        $listdata = Lang::where($where)
            ->select()
            ->toArray();

        foreach ($listdata as $key => $vo) {
            $data[$vo['item']] = $vo['title'];
        }
        if(empty($data)){
            $data = [];
        }
        
        return $data;
    }
    public static function getzhLang()
    {
        return self::where(['weid' => weid(), 'locale' => 'zh', 'status' => 1])->order('sort asc,id asc')->select()->toArray();
    }

    public static function datainitial()
    {
        $data = [
            [
                'item' => 'technical',
                'title' => '师傅',
                'shuoming' => '师傅',
            ],
            [
                'item' => 'store',
                'title' => '商家',
                'shuoming' => '商家',
            ],
            [
                'item' => 'tuanzhang',
                'title' => '团长',
                'shuoming' => '团长',
            ],
            [
                'item' => 'communitytuanzhang',
                'title' => '社区/团长',
                'shuoming' => '社区/团长',
            ],
            [
                'item' => 'points',
                'title' => '积分',
                'shuoming' => '积分',
            ]
        ];

        $data =  self::setdata($data);
        self::createdata($data);
    }

    public static function setdata($data)
    {
        $weid = weid();
        if (!empty($data)) {
            foreach ($data as &$vo) {
                $vo['weid'] = $weid;
                $vo['locale'] = 'zh';
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
                if (empty(self::where(['locale' => $vo['locale'], 'item' => $vo['item'], 'weid' => $vo['weid']])->find())) {
                    self::create($vo);
                }
            }
        }
        return $data;
    }
}
