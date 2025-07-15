<?php

namespace app\index\controller;

use think\exception\ValidateException;
use app\model\Config;
use app\model\Tongue;

class TongueController extends Base
{
    public function check()
    {
        $uid = UID();
        $weid = weid();
        /*
        $data = Tongue::where(['uid' => $uid, 'weid' => $weid])->find();
        if (!empty($data)) {
            $data = $data->toArray();
        }
*/
        return $this->json(['data' => $data]);
    }
    public function upload()
    {
        $uid = UID();
        $weid = weid();
        $tongueConfig = Config::getconfig('tongue');
        $img = input('post.img', '', 'serach_in');

        //$tongue = Tongue::where('uid', $uid)->find();
        if (empty($tongue)) {
            $tongue =  Tongue::create([
                'uid' => $uid,
                'weid' => $weid,
                'price' => $tongueConfig['price'],
                'is_pay' => 0,
                'img' => $img,
                'status' => 1
            ]);
        } else {

            Tongue::where('id', $tongue->id)->update(['img' => $img]);
        }
        $tongue = $tongue->toArray();

        return $this->json(['data' => $tongue]);
    }
    public function getaidata()
    {
        $id = (int) input('post.id', '', 'serach_in');

        $tongue = Tongue::find($id);
        if (!empty($tongue)) {
            $tongue = $tongue->toArray();
            if (empty($tongue['bodydata']) && !empty($tongue['img'])) {

                $tongueConfig = Config::getconfig('tongue');
                $url = 'http://www.aibayes.cn/api/analysis';
                $restime = date("YmdHis");
                $appid = trim($tongueConfig['app_id']);
                $key = trim($tongueConfig['apikey']);

                $postparam = [
                    'timestamp' => $restime,
                    'app_id' => $appid,
                    'version' => '1.0',
                    'method' => 'jiuti',
                    'imgpath' => $tongue['img'],
                    'sign' => strtoupper(md5(strtoupper(md5($restime)) . $key)),
                    'timeout_express' => 60
                ];
                $ret = Author()::postbyform($url, $postparam);
               
               
                if ($ret['code'] == 200) {
                    Tongue::where('id', $tongue['id'])->update(['bodydata' => ($ret['data'])]);
                }
                $data = json_decode($ret['data'], true);
            }else{
                $data = json_decode($tongue['bodydata'], true);
            }
        }

        return $this->json(['data' => $data]);
    }
}
