<?php

namespace app\model;

use think\Model;

class Withdraw extends Model
{

    protected $connection = 'mysql';

    protected $pk = 'id';

    protected $name = 'withdraw';

    static public function audit($id)
    {
        $data = Withdraw::find($id);
        $res['code'] = false;

        if (!empty($data)) {
            $data = $data->toArray();
            unset($data['create_time']);
            $data['status'] = 1;

            $MemberBankcard = MemberBankcard::find($data['bid']);
            if (!empty($MemberBankcard)) {
                $MemberBankcard = $MemberBankcard->toArray();
                if ($MemberBankcard['ptype'] == 3) {
                    if ($data['pay_from'] == 'mp') {
                        $Openid = Openid::getMpOpenidbyuid($MemberBankcard['uid']);
                    } else {
                        $Openid = Openid::getWxappOpenidbyuid($MemberBankcard['uid']);
                    }
                    if (!empty($Openid)) {
                        $wxpay_settings = Paymethod::getwx_settings();
                        $actual_amounts = round($data['actual_amounts'], 2);
                        $actual_amounts = intval($actual_amounts * 100);
                        if ($wxpay_settings['transfertype'] == '1') {
                            $payment = \app\samos\wechat\WxPaymethod::makepay($data['pay_from']);
                            

                            $result = $payment->transfer->toBalance([
                                'partner_trade_no' => $data['withdraw_sn'], // 商户订单号，需保持唯一性(只能是字母或者数字，不能包含有符号)
                                'openid' => $Openid,
                                'check_name' => 'FORCE_CHECK', // NO_CHECK：不校验真实姓名, FORCE_CHECK：强校验真实姓名
                                're_user_name' => $MemberBankcard['name'], // 如果 check_name 设置为FORCE_CHECK，则必填用户真实姓名
                                'amount' => $actual_amounts, // 企业付款金额，单位为分
                                'desc' => '提现', // 企业付款操作说明信息。必填
                            ]);

                            if ($result['return_code'] === 'SUCCESS' && $result['result_code'] === 'SUCCESS') {
                                $res['code'] = Withdraw::update($data);
                            } else {
                                $res['code'] = false;
                                $res['return_msg'] = $result['return_msg'];
                                $res['err_code_des'] = $result['err_code_des'];
                            }
                        } elseif ($wxpay_settings['transfertype'] == '2') {
                            $payment = \app\samos\wechat\WxPaymethod::makepayv3($data['pay_from']);

                            $result = $payment->batchs([
                                'out_batch_no'         => 'plfk' . $data['withdraw_sn'],
                                'batch_name'           => '用户佣金提现',
                                'batch_remark'         => '用户佣金提现',
                                'total_amount'         => $actual_amounts,
                                'transfer_detail_list' => [
                                    [
                                        'out_detail_no'   => $data['withdraw_sn'],
                                        'transfer_amount' => $actual_amounts,
                                        'transfer_remark' => '用户佣金提现',
                                        'openid'          => $Openid,
                                        'user_name'       => ''
                                    ]
                                ]
                            ]);
                            if (isset($result['code']) && isset($result['message'])) {
                                $res['code'] = false;
                                $res['return_msg'] = $result['message'];
                                $res['err_code_des'] = $result['err_code_des'];
                            }else{
                                $res['code'] = Withdraw::update($data);
                            }
                        } else {
                            $res['code'] = Withdraw::update($data);
                        }
                    }
                } else {
                    $res['code'] = Withdraw::update($data);
                }
            }
        }

        return $res;
    }
}
