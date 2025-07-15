<?php
namespace app\model;

class Kdniaoapi {
//调用查询物流轨迹
//---------------------------------------------
//$logisticResult =getOrderTracesByJson();
//echo logisticResult;
//---------------------------------------------

    /**
     *  查询订单物流轨迹 返回数组
     */
    function getOrderTracesByArray($data) {

        $arraydata = json_decode($this->getOrderTracesByJson($data), true);
        //var_dump($arraydata);
        if (!empty($arraydata['Traces'])) {
            $arraydata['Traces'] = array_reverse($arraydata['Traces']);
        }

        return $arraydata;
    }

    /**
     * Json方式 查询订单物流轨迹
     */
    function getOrderTracesByJson($data) {

        $ReqURL = 'http://api.kdniao.com/Ebusiness/EbusinessOrderHandle.aspx';
        $requestData = "{'OrderCode':'" . $data['OrderCode'] . "','ShipperCode':'" . $data['ShipperCode'] . "','LogisticCode':'" . $data['LogisticCode'] . "'}";

        $datas = array(
            'EBusinessID' => trim(Config::getconfig('transport')['kdniao_id']),
            'RequestType' => '1002',
            'RequestData' => urlencode($requestData),
            'DataType' => '2',
        );
        $datas['DataSign'] = $this->encrypt($requestData, trim(Config::getconfig('transport')['kdniao_apikey']));
        $result = $this->sendPost($ReqURL, $datas);

//根据公司业务处理返回的信息......

        return $result;
    }

    /**
     *  post提交数据 
     * @param  string $url 请求Url
     * @param  array $datas 提交的数据 
     * @return url响应返回的html
     */
    function sendPost($url, $datas) {
        $temps = array();
        foreach ($datas as $key => $value) {
            $temps[] = sprintf('%s=%s', $key, $value);
        }
        if(!empty($temps)){
            $post_data = implode('&', $temps);
        }
        
        $url_info = parse_url($url);
        if (empty($url_info['port'])) {
            $url_info['port'] = 80;
        }
        $httpheader = "POST " . $url_info['path'] . " HTTP/1.0\r\n";
        $httpheader .= "Host:" . $url_info['host'] . "\r\n";
        $httpheader .= "Content-Type:application/x-www-form-urlencoded\r\n";
        $httpheader .= "Content-Length:" . strlen($post_data) . "\r\n";
        $httpheader .= "Connection:close\r\n\r\n";
        $httpheader .= $post_data;
        $fd = fsockopen($url_info['host'], $url_info['port']);
        fwrite($fd, $httpheader);
        $gets = "";
        $headerFlag = true;
        while (!feof($fd)) {
            if (($header = @fgets($fd)) && ($header == "\r\n" || $header == "\n")) {
                break;
            }
        }
        while (!feof($fd)) {
            $gets .= fread($fd, 128);
        }
        fclose($fd);

        return $gets;
    }

    /**
     * 电商Sign签名生成
     * @param data 内容   
     * @param appkey Appkey
     * @return DataSign签名
     */
    function encrypt($data, $appkey) {
        return urlencode(base64_encode(md5($data . $appkey)));
    }

}
