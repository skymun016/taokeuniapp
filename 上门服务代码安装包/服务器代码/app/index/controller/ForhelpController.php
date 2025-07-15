<?php

namespace app\index\controller;

use think\exception\ValidateException;
use app\model\Forhelp;
use app\model\Technical;
use app\model\Geocoder;
use app\model\UuidRelation;

class ForhelpController extends Base
{
    public function add()
    {

        $uuid = UuidRelation::getuuid(UID(), 'technical');
        $data = input('post.');

        $data['weid'] = weid();
        $data['uuid'] = $uuid;
        $data['username'] = Technical::getTitle($uuid);
        $data['tel'] = Technical::getTel($uuid);
        $data['title'] = $data['username'] . '发送了求助';

        $reverse = Geocoder::reverse_geocoding($data['latitude'], $data['longitude']);
        $data['province_name'] = $reverse['addressComponent']['province'];
        $data['city_name'] = $reverse['addressComponent']['city'];
        $data['district_name'] = $reverse['addressComponent']['district'];
        $data['dizhi'] = $reverse['formatted_address'];
        try {
            $res = Forhelp::create($data);
        } catch (\Exception $e) {
            throw new ValidateException($e->getMessage());
        }
        return $this->json(['msg' => '求助发送成功']);
    }
}
