<?php

namespace app\model;

use dh2y\qrcode\QRcode;
use app\samos\wechat\MiniProgram;

class QrcodeMod
{

    function getUserQrcode($user_id, $page)
    {
        $weid = weid();
        $from = input('get.from', '', 'serach_in');
        if ($from == 'mp') {
            $qrcod = new QRcode();
            $qrcodeurl = gethost() . scriptPath() .  '/h5/?i=' . $weid . '#/' . $page . '?reid=' . $user_id;
            $qrcodres = $qrcod->png($qrcodeurl, false, 10)->getPath();
            return gethost() . '/app/' . $qrcodres;
        } else {
            $scene = $user_id;
            return MiniProgram::getQrcode($scene, $page);
        }
    }
    function getGoodsQrcode($params, $user_id, $page)
    {
        $weid = weid();
        $from = input('get.from', '', 'serach_in');
        if ($from == 'mp') {
            $qrcod = new QRcode();
            $q = 'id=' . $params['goodsid'];

            if ($params['msid']) {
                $q = $q . '&msid=' . $params['msid'];
            }
            if ($params['tuanid']) {
                $q = $q . '&tuanid=' . $params['tuanid'];
            }
            $qrcodeurl = gethost() . scriptPath() .  '/h5/?i=' . $weid . '#/' . $page . '?' . $q;
            $qrcodres = $qrcod->png($qrcodeurl, false, 10)->getPath();
            return '/app/' . $qrcodres;
        } else {
            $scene = "{$params['goodsid']},{$user_id},{$params['msid']},{$params['tuanid']}";
            return MiniProgram::getQrcode($scene, $page);
        }
    }
    function getWorkersQrcode($id, $user_id, $page)
    {
        $weid = weid();
        $from = input('get.from', '', 'serach_in');
        if ($from == 'mp') {
            $qrcod = new QRcode();
            $qrcodeurl = gethost() . scriptPath() .  '/h5/?i=' . $weid . '#/' . $page . '?id=' . $id . '&reid=' . $user_id;
            $qrcodres = $qrcod->png($qrcodeurl, false, 10)->getPath();
            return gethost() . '/app/' . $qrcodres;
        } else {
            $scene = "{$id},{$user_id}";
            return MiniProgram::getQrcode($scene, $page);
        }
    }
    function getOrderQrcode($orderid, $page)
    {
        $weid = weid();
        $from = input('get.from', '', 'serach_in');
        if ($from == 'mp') {
            $qrcod = new QRcode();
            $qrcodeurl = gethost() . scriptPath() .  '/h5/?i=' . $weid . '#/' . $page . '?id=' . trim($orderid);
            $qrcodres = $qrcod->png($qrcodeurl, false, 10)->getPath();
            return gethost() . '/app/' . $qrcodres;
        } else {
            $scene = $orderid;
            return MiniProgram::getQrcode($scene, $page);
        }
    }
}
