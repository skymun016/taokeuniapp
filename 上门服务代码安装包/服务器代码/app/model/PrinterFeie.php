<?php

//===========添加打印机接口（支持批量）=============
//***接口返回值说明***
//正确例子：{"msg":"ok","ret":0,"data":{"ok":["sn#key#remark#carnum","316500011#abcdefgh#快餐前台"],"no":["316500012#abcdefgh#快餐前台#13688889999  （错误：识别码不正确）"]},"serverExecutedTime":3}
//错误：{"msg":"参数错误 : 该帐号未注册.","ret":-2,"data":null,"serverExecutedTime":37}
//打开注释可测试
//提示：打印机编号(必填) # 打印机识别码(必填) # 备注名称(选填) # 流量卡号码(选填)，多台打印机请换行（\n）添加新打印机信息，每次最多100行(台)。
//$snlist = "sn1#key1#remark1#carnum1\nsn2#key2#remark2#carnum2";
//addprinter($snlist);
//==================方法1.打印订单==================
//***接口返回值说明***
//正确例子：{"msg":"ok","ret":0,"data":"316500004_20160823165104_1853029628","serverExecutedTime":6}
//错误：{"msg":"错误信息.","ret":非零错误码,"data":null,"serverExecutedTime":5}
//标签说明：
//单标签:
//"<BR>"为换行,"<CUT>"为切刀指令(主动切纸,仅限切刀打印机使用才有效果)
//"<LOGO>"为打印LOGO指令(前提是预先在机器内置LOGO图片),"<PLUGIN>"为钱箱或者外置音响指令
//成对标签：
//"<CB></CB>"为居中放大一倍,"<B></B>"为放大一倍,"<C></C>"为居中,<L></L>字体变高一倍
//<W></W>字体变宽一倍,"<QR></QR>"为二维码,"<BOLD></BOLD>"为字体加粗,"<RIGHT></RIGHT>"为右对齐
//拼凑订单内容时可参考如下格式
//根据打印纸张的宽度，自行调整内容的格式，可参考下面的样例格式
//打开注释可测试
//wp_print(SN,$orderInfo,1);
//===========方法2.查询某订单是否打印成功=============
//***接口返回值说明***
//正确例子：
//已打印：{"msg":"ok","ret":0,"data":true,"serverExecutedTime":6}
//未打印：{"msg":"ok","ret":0,"data":false,"serverExecutedTime":6}
//打开注释可测试
//$orderid = "xxxxxxxx_xxxxxxxxxx_xxxxxxxx";//订单ID，从方法1返回值中获取
//queryOrderState($orderid);
//===========方法3.查询指定打印机某天的订单详情============
//***接口返回值说明***
//正确例子：{"msg":"ok","ret":0,"data":{"print":6,"waiting":1},"serverExecutedTime":9}
//打开注释可测试
//$date = "2017-04-02";//注意时间格式为"yyyy-MM-dd",如2016-08-27
//queryOrderInfoByDate(SN,$date);
//===========方法4.查询打印机的状态==========================
//***接口返回值说明***
//正确例子：
//{"msg":"ok","ret":0,"data":"离线","serverExecutedTime":9}
//{"msg":"ok","ret":0,"data":"在线，工作状态正常","serverExecutedTime":9}
//{"msg":"ok","ret":0,"data":"在线，工作状态不正常","serverExecutedTime":9}
//打开注释可测试
//queryPrinterStatus(SN);

namespace app\model;

use think\exception\ValidateException;

class PrinterFeie
{

    private $user;  //*必填*：飞鹅云后台注册账号
    private $ukey;  //*必填*: 飞鹅云注册账号后生成的UKEY
    private $sn;  //打印机编号
    private $times;  //打印联数
    //以下参数不需要修改
    private $ip = 'api.feieyun.cn'; //接口IP或域名
    private $port = 80;  //接口IP端口
    private $path = '/Api/Open/';   //接口路径

    public function __construct($sid = 0)
    {
        $Printer = Printer::where(['weid' => weid(), 'sid' => (int) $sid])->order('id desc')->find();
        if (!empty($Printer->settings)) {

            $settings = iunserializer($Printer->settings);
            $this->user = $settings['user'];
            $this->ukey = $settings['ukey'];
            $this->sn = $settings['sn'];
            $this->times = (int) $settings['times'];
            if (empty($this->times)) {
                $this->times = 1;
            }
        }
    }

    //飞鹅打印机
    public function printer($data)
    {

        $order = $data['orderInfo'];
        $addtime = $order['create_time'];

        $content = "";
        $content .= "<CB></CB><BR>";

        $content .= "订单号：{$order['order_num_alias']}<BR>";
        $content .= "支付方式：" . paymentCode($order['payment_code']) . "<BR>";
        $content .= "订单状态：" . OrderStatus::get_order_status_name($order['order_status_id']) . "<BR>";
        $content .= "发货方式：" . $order['shipping_method'] . "<BR>";
        $content .= "下单时间：{$addtime}<BR>";

        $content .= "<BR>";
        $content .= "-------------订单商品-----------<BR>";
        $content .= "<BR>";
        if (isset($data['goods']) && is_array($data['goods'])) {
            foreach ($data['goods'] as $goods) {
                $content .= "商品名称：{$goods['name']}<BR>";
                $content .= "规格：{$goods['label']}<BR>";
                $content .= "单价：{$goods['price']}元<BR>";
                $content .= "数量：{$goods['quantity']}<BR>";
                $content .= "<BR>";
            }
        }

        $content .= "--------------------------------<BR>";
        if ($order['express_price']) {
            $content .= "运费：{$order['express_price']}元<BR>";
        }
        $content .= "总计：{$order['total']}元<BR>";
        if (isset($order['user_coupon_id'])) {
            $content .= "优惠券优惠：{$order['coupon_sub_price']}元<BR>";
        }
        if (isset($order['integral'])) {
            $integral = json_decode($order['integral'], true);
            if ($integral['forehead'] != 0) {
                $content .= "积分抵扣：{$integral['forehead']}元<BR>";
            }
        }
        if (isset($order['discount']) && $order['discount'] < 10) {
            $content .= "会员折扣：{$order['discount']}折<BR>";
        }

        //$content .= "实付：{$order['pay_price']}元<BR>";

        if (!empty($order['shipping_name'])) {
            $content .= "联系人：{$order['shipping_name']}<BR>";
        } else {
            $content .= "联系人：{$order['member']['nickname']}<BR>";
        }
        if (!empty($order['shipping_tel'])) {
            $content .= "联系电话：{$order['shipping_tel']}<BR>";
        }else{
            $content .= "联系电话：{$order['member']['telephone']}<BR>";
        }

        $content .= "地址：{$order['shipping_province']}{$order['shipping_city']}{$order['shipping_district']}{$order['address']['address']}<BR>";
        $content .= "--------------------------------<BR>";

        if ($order['content']) {
            $content .= "备注：{$order['content']}<BR>";
        }

        $resule = $this->wp_print($content);

        //$resule = iunserializer($resule);
        $result = json_decode($resule, true);
        //var_dump($result);
        if (!empty($result['ret'])) {
            //throw new ValidateException($result['msg']);
            Test::create(['title' => '小票打印机错误记录', 'info' => serialize($result)]);
        }
        return $result;
    }

    function addprinter($snlist)
    {
        $time = time();       //请求时间
        $content = array(
            'user' => $this->user,
            'stime' => $time,
            'sig' => $this->signature($time),
            'apiname' => 'Open_printerAddlist',
            'printerContent' => $snlist
        );

        $client = new PrinterFeieHttpClient($this->ip, $this->port);
        if (!$client->post($this->path, $content)) {
            echo 'error';
        } else {
            echo $client->getContent();
        }
    }

    /*
     *  方法1
      拼凑订单内容时可参考如下格式
      根据打印纸张的宽度，自行调整内容的格式，可参考下面的样例格式
     */

    function wp_print($orderInfo)
    {
        $time = time();       //请求时间

        $content = array(
            'user' => $this->user,
            'stime' => $time,
            'sig' => $this->signature($time),
            'apiname' => 'Open_printMsg',
            'sn' => $this->sn,
            'content' => $orderInfo,
            'times' => $this->times   //打印次数
        );

        $client = new PrinterFeieHttpClient($this->ip, $this->port);
        if (!$client->post($this->path, $content)) {
            return 'error';
        } else {
            //服务器返回的JSON字符串，建议要当做日志记录起来
            return $client->getContent();
        }
    }

    /*
     *  方法2
      根据订单索引,去查询订单是否打印成功,订单索引由方法1返回
     */

    function queryOrderState($index)
    {
        $time = time();       //请求时间
        $msgInfo = array(
            'user' => $this->user,
            'stime' => $time,
            'sig' => $this->signature($time),
            'apiname' => 'Open_queryOrderState',
            'orderid' => $index
        );

        $client = new PrinterFeieHttpClient($this->ip, $this->port);
        if (!$client->post($this->path, $msgInfo)) {
            echo 'error';
        } else {
            $result = $client->getContent();
            echo $result;
        }
    }

    /*
     *  方法3
      查询指定打印机某天的订单详情
     */

    function queryOrderInfoByDate($printer_sn, $date)
    {
        $time = time();       //请求时间
        $msgInfo = array(
            'user' => $this->user,
            'stime' => $time,
            'sig' => $this->signature($time),
            'apiname' => 'Open_queryOrderInfoByDate',
            'sn' => $printer_sn,
            'date' => $date
        );

        $client = new PrinterFeieHttpClient($this->ip, $this->port);
        if (!$client->post($this->path, $msgInfo)) {
            echo 'error';
        } else {
            $result = $client->getContent();
            echo $result;
        }
    }

    /*
     *  方法4
      查询打印机的状态
     */

    function queryPrinterStatus($printer_sn)
    {
        $time = time();       //请求时间
        $msgInfo = array(
            'user' => $this->user,
            'stime' => $time,
            'sig' => $this->signature($time),
            'apiname' => 'Open_queryPrinterStatus',
            'sn' => $printer_sn
        );

        $client = new PrinterFeieHttpClient($this->ip, $this->port);
        if (!$client->post($this->path, $msgInfo)) {
            echo 'error';
        } else {
            $result = $client->getContent();
            echo $result;
        }
    }

    //生成签名
    function signature($time)
    {
        return sha1($this->user . $this->ukey . $time); //公共参数，请求公钥
    }
}
