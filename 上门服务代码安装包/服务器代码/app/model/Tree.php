<?php
namespace app\model;

class Tree
{

    public static function title($data, $pid, $before = "")
    {
        $arr = [];
        if (empty($before)) {
            $before = "├ ";
        } else {
            $before = "│ " . $before;
        }
        if (!empty($data)) {
            foreach ($data as $iv) {
                if ($iv['pid'] == $pid) {
                    if ($before == "├ ") {
                    } else {
                        $iv['title'] = $before . $iv['title'];
                    }

                    $arr[] = $iv;
                    $arr = array_merge($arr, self::title($data, $iv["id"], $before));
                }
            }
        }

        $before = "";

        return $arr;
    }

    public static function listarray($data, $pid, $before = "")
    {
        $arr = [];
        if (!empty($data)) {
            foreach ($data as $iv) {
                if ($iv['pid'] == $pid) {
                    $arr[$iv['id']] = $iv;
                    $arr[$iv['id']]['son'] = self::listarray($data, $iv["id"], $before);
                }
            }
        }
        return $arr;
    }

    public static function treelist($data, $pid, $before = "")
    {
        $arr = [];
        if (!empty($data)) {
            foreach ($data as $key => $iv) {
                if ($iv['pid'] == $pid) {
                    $arr[$key] = $iv;
                    $arr[$key]['son'] = self::treelist($data, $iv["id"], $before);
                }
            }
        }
        //var_dump($arr);
        return $arr;
    }
}
