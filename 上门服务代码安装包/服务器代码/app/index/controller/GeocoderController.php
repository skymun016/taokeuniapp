<?php

namespace app\index\controller;

use think\exception\ValidateException;
use app\model\Geocoder;
use app\model\Config;
use app\model\Address;

class GeocoderController extends Base
{
    public function address2area()
    {
        $postdata = input('post.');
        if (!empty($postdata['address'])) {
            $resarea = Address::Address2area($postdata['address']);
        }
        if ($resarea['province_name'] && $resarea['city_name'] && $resarea['district_name']) {
            $data = $resarea;
            $data['is_regex'] = 1;
        } else {
            $geodata = Geocoder::reverse_geocoding($postdata['latitude'], $postdata['longitude']);
            $data['province_name'] = $geodata['addressComponent']['province'];
            $data['city_name'] = $geodata['addressComponent']['city'];
            $data['district_name'] = $geodata['addressComponent']['district'];
        }

        return $this->json(['data' => $data]);
    }

    public function locationscope()
    {
        $postdata = input('post.');
        $data = Geocoder::reverse_geocoding($postdata['latitude'], $postdata['longitude']);

        $data['city'] = $data['addressComponent']['city'];
        $Configdata = Config::getconfig();

        if ($Configdata['locationscope'] == 3 && $data['addressComponent']['district']) {
            $data['city'] = $data['addressComponent']['district'];
        }
        return $this->json(['data' => $data]);
    }
    public function reversegeocoding()
    {
        $postdata = input('post.');
        $data = Geocoder::reverse_geocoding($postdata['latitude'], $postdata['longitude']);
        return $this->json(['data' => $data]);
    }
}
