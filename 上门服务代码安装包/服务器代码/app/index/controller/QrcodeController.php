<?php

namespace app\index\controller;

use think\exception\ValidateException;
use app\model\QrcodeMod;
use app\model\Member;

class QrcodeController extends Base
{

    public function index()
    {

        $page = input('post.page', '', 'serach_in');
        $goodsid = input('post.goodsid', '', 'serach_in');
        $msid = input('post.msid', '', 'serach_in');
        $tuanid = input('post.tuanid', '', 'serach_in');
        $orderid = input('post.orderid', '', 'serach_in');

        $QrcodeMod = new QrcodeMod;
        $Membermob = new Member;
        $memberinfo = $Membermob->getUserByWechat();

        if (!empty($goodsid)) {
            $params['goodsid'] = (int)$goodsid;
            $params['msid'] = (int) $msid;
            $params['tuanid'] = (int) $tuanid;
            $data = $QrcodeMod->getGoodsQrcode($params, $memberinfo['id'], $page);
        } elseif (!empty($orderid)) {
            $page = 'pagesA/my/admin/orderDetail';
            $data = $QrcodeMod->getOrderQrcode($orderid, $page);
        } else {
            $data = $QrcodeMod->getUserQrcode($memberinfo['id'], $page);
        }

        return $this->json(['data' => $data]);
    }
    
    public function timescard()
    {

        $orderid = input('post.orderid', '', 'serach_in');

        $QrcodeMod = new QrcodeMod;
        $page = 'pagesA/my/publicOrder/timescardDetail';
        $data = $QrcodeMod->getOrderQrcode($orderid, $page);

        return $this->json(['data' => $data]);
    }
    public function yuyue()
    {

        $orderid = input('post.orderid', '', 'serach_in');

        $QrcodeMod = new QrcodeMod;
        $page = 'pagesA/my/admin/orderDetail';
        $data = $QrcodeMod->getOrderQrcode($orderid, $page);

        return $this->json(['data' => $data]);
    }
    public function store()
    {

        $sid = input('post.sid', '', 'serach_in');
        $page = 'pages/store_details/store_details';

        $QrcodeMod = new QrcodeMod;
        $Membermob = new Member;
        $memberinfo = $Membermob->getUserByWechat();

        $data = $QrcodeMod->getWorkersQrcode($sid, $memberinfo['id'], $page);

        return $this->json(['data' => $data]);
    }
    public function technical()
    {

        $id = input('post.id', '', 'intval');
        $page = 'pages/technical/details';

        $QrcodeMod = new QrcodeMod;
        $Membermob = new Member;
        $memberinfo = $Membermob->getUserByWechat();

        $data = $QrcodeMod->getWorkersQrcode($id, $memberinfo['id'], $page);

        return $this->json(['data' => $data]);
    }
    public function tuanzhang()
    {

        $id = input('post.id', '', 'intval');
        $page = 'pages/tuanzhang/qrcodejump';
        //$page = 'pages/index/index';

        $QrcodeMod = new QrcodeMod;
        $Membermob = new Member;
        $memberinfo = $Membermob->getUserByWechat();

        $data = $QrcodeMod->getWorkersQrcode($id, $memberinfo['id'], $page);

        return $this->json(['data' => $data]);
    }
}
