<?php

namespace app\model;

use think\Model;

class Address extends Model
{

    protected $connection = 'mysql';

    protected $pk = 'id';

    protected $name = 'address';

    public static function getthefare($address_id, $technicalId)
    {
        $shipping = Address::find($address_id);
        $technical =  Technical::getInfo($technicalId);
        $from['longitude'] = $technical['longitude'];
        $from['latitude'] = $technical['latitude'];
        $to['longitude'] = $shipping['longitude'];
        $to['latitude'] = $shipping['latitude'];
        if ($from['longitude'] && $from['latitude'] && $to['longitude'] && $to['latitude']) {
            $data["distance"] = Geocoder::get_distance($from, $to);
            if ($data["distance"]) {
                $Configthefare = Config::getconfig('thefare');

                if ($data["distance"] < (int)$Configthefare['startkm']) {
                    $data["thefare"] = round($Configthefare['startat'], 2);
                } else {
                    $data["thefare"] = $Configthefare['startat'];
                    $data["thefare"] =   round($data["thefare"] + (($data["distance"] - $Configthefare['startkm']) * $Configthefare['mileageprice']), 2);
                }
            }
        }
        return $data;
    }
    
    public static function geterrands($address_id, $take_address_id)
    {
        $addressdata = Address::find($address_id);
        if (!empty($addressdata)) {
            $addressdata = $addressdata->toArray();
        }
        $takeaddressdata = Address::find($take_address_id);
        if (!empty($takeaddressdata)) {
            $takeaddressdata = $takeaddressdata->toArray();
        }

        if ($addressdata['longitude'] && $addressdata['latitude'] && $takeaddressdata['longitude'] && $takeaddressdata['latitude']) {
            $from['longitude'] = $addressdata['longitude'];
            $from['latitude'] = $addressdata['latitude'];
            $to['longitude'] = $takeaddressdata['longitude'];
            $to['latitude'] = $takeaddressdata['latitude'];

            $data["distance"] = Geocoder::get_distance($from, $to);
        }

        if ($data["distance"]) {
            $errands = Config::getconfig('errands');

            if ($data["distance"] < (int) $errands['startkm']) {
                $data["amountTotle"] = round($errands['startat'], 2);
            } else {
                $data["amountTotle"] =  $errands['startat'];

                $data["amountTotle"] =   round($data["amountTotle"] + (($data["distance"] - $errands['startkm']) * $errands['mileageprice']), 2);
            }
        }
        return $data;
    }

    public static function Address2area($address)
    {
        preg_match('/(.*?(省|自治区|北京市|天津市|重庆市|上海市|香港特别行政区|澳门特别行政区))/', $address, $matches);
        if (count($matches) > 1) {
            $province = $matches[count($matches) - 2];
            $address = str_replace($province, '', $address);
        }
        preg_match('/(.*?(市|自治州|地区|区划))/', $address, $matches);
        if (count($matches) > 1) {
            $city = $matches[count($matches) - 2];
            $address = str_replace($city, '', $address);
        }
        preg_match('/(.*?(市|区|县))/', $address, $matches);
        if (count($matches) > 1) {
            $area = $matches[count($matches) - 2];
            $address = str_replace($area, '', $address);
        }
        return ['province_name' => isset($province) ? $province : '', 'city_name' => isset($city) ? $city : '', 'district_name' => isset($area) ? $area : '',];
    }
}
