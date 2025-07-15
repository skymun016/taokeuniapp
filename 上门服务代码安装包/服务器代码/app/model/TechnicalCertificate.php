<?php

namespace app\model;

use think\Model;

class TechnicalCertificate extends Model
{

    protected $connection = 'mysql';

    protected $pk = 'id';

    protected $name = 'technical_certificate';


    function getdatalist()
    {
        $ptypeArray =  $this->where(['weid' => weid()])->select()->toArray();
        return $ptypeArray;
    }

    public static function getTitle($id = '')
    {
        $ret = self::find($id);
        if (!empty($ret)) {
            $ret = $ret->toArray();
        }
        return $ret['title'];
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
    public static function getarray()
    {
        $data = self::where(['weid' => weid()])->order('id desc')->select()->toArray();
        if (!empty($data))
            foreach ($data as $vo) {
                $datalist[$vo['id']] = $vo['title'];
            }
        else
            $datalist['0'] = '认证';

        return $datalist;
    }

    public static function getpcarray()
    {
        $data = self::field('id,title')->where(['weid' => weid()])->select()->toArray();
        $datalist = [];
        foreach ($data as $key => $vo) {
            $datalist[$key]['val'] = $vo['id'];
			$datalist[$key]['key'] = $vo['title'];
        }
        return $datalist;
    }

    public static function getone()
    {
        $ret = self::where(['weid' => weid()])->order('id asc')->find();

		if (!empty($ret)) {
			$ret = $ret->toArray();
		}

        return $ret;
    }
}
