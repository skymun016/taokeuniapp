<?php

namespace app\model;

use think\Model;
use think\Cache;

class Area extends Model
{

    protected $connection = 'mysql';

    protected $pk = 'id';

    protected $name = 'area';

    public static function getTree($pid = 0)
    {
        $arr = [];
        $data = self::field('id,area_name')->where(['area_parent_id' => $pid])
            ->select()->toArray();

        foreach ($data as $key => $vo) {
            $arr[$key]['id'] = $vo['id'];
            $arr[$key]['text'] = $vo['area_name'];
            $arr[$key]['children'] = self::getTree($vo["id"]);
        }
        return $arr;
    }

    public static function getpcTree($pid = 0)
    {
        $arr = [];
        $children = [];
        $data = self::field('id,area_name')->where(['area_parent_id' => $pid])
            ->select()->toArray();

        foreach ($data as $key => $vo) {
            $arr[$key]['value'] = $vo['area_name'];
            $arr[$key]['label'] = $vo['area_name'];
            $children = self::getpcTree($vo["id"]);
            if (!empty($children)) {
                $arr[$key]['children'] = $children;
            }
        }
        return $arr;
    }

    public static function getpcTreeCache()
    {
        $pcTree = cache('pcTree');
        if (empty($pcTree)) {
            $pcTree = self::getpcTree();
            cache('pcTree', $pcTree, 3600);
        }
        return  $pcTree;
    }

    //通过地区的id取地区的名称
    public static function get_area_name($id)
    {
        $id = (int) $id;
        //加上空判断
        if (empty($id)) {
            return '';
        }

        $data = self::find($id);

        if (empty($data)) {
            return null;
        }

        return $data->area_name;
    }
    //通过地区的id取地区的名称
    public static function get_area_id($area_name)
    {
        if (!empty($area_name)) {
            $data = self::where('area_name', $area_name)->find();
            if (empty($data)) {
                return null;
            }

            return $data->id;
        }
    }

    //通过地区的id取地区上级id
    public static function get_pid($id)
    {

        $data = self::find($id);

        if (empty($data)) {
            return null;
        }

        return $data->area_parent_id;
    }

    //取省区
    public static function get_province()
    {
        $list = self::where(['area_parent_id' => 0, 'area_deep' => 1])
            ->select()->toArray();
        return $list;
    }

    public static function get_province_byname()
    {
        $listdata = self::get_province();

        foreach ($listdata as $vo) {
            $data[$vo['id']] = $vo['area_name'];
        }

        return $data;
    }

    public static function get_child($pid)
    {

        $list = self::where(['area_parent_id' => $pid])
            ->select()->toArray();

        return $list;
    }

    public static function get_child_byname($pid)
    {
        if (!empty($pid)) {
            $listdata = self::get_child($pid);
            if (!empty($listdata)) {
                foreach ($listdata as $vo) {
                    $data[$vo['id']] = $vo['area_name'];
                }
            }
        } else {
            $data[0] = "";
        }
        return $data;
    }
}
