<?php

namespace app\index\controller;

use think\exception\ValidateException;
use app\model\MemberBankcard;

class MemberbankcardController extends Base
{
    public function add()
    {

        $postdata = array_filter($this->getdata());
        if (empty($postdata['ptype'])) {
            $postdata['ptype'] = 1;
        }

        $postdata['weid'] = weid();

        $mb =  MemberBankcard::create($postdata);
        $data['id'] = $mb->id;

        return $this->json(['data' => $data]);
    }

    public function update()
    {

        $postdata = $this->getdata();
        $id = (int) input('post.id', '', 'serach_in');
        if (empty($postdata['ptype'])) {
            $postdata['ptype'] = 1;
        }

        unset($postdata['weid']);
        unset($postdata['uid']);
        unset($postdata['isDefault']);
        $postdata['id'] = $id;
        if (!empty($id)) {
            MemberBankcard::update($postdata);
        }
        $data['id'] = $id;
        return $this->json(['data' => $data]);
    }

    public function delete()
    {
        $idx =  $this->request->post('id', '', 'serach_in');
        if (!$idx) throw new ValidateException('参数错误');
        MemberBankcard::destroy(['id' => explode(',', $idx)], true);
        return $this->json(['msg' => '操作成功']);
    }

    public function getdata()
    {
        $postdata['weid'] = weid();
        $postdata['uid'] = UID();
        $postdata['name'] = input('post.name', '', 'serach_in');
        $postdata['ptype'] = input('post.ptype', '', 'serach_in');
        $postdata['accounts'] = input('post.accounts', '', 'serach_in');
        $postdata['bankname'] = input('post.bankname', '', 'serach_in');
        $postdata['branchname'] = input('post.branchname', '', 'serach_in');
        if ($postdata['branchname'] == 'undefined') {
            $postdata['branchname'] = '';
        }
        $postdata['isDefault'] = input('post.isDefault', '', 'serach_in');

        return $postdata;
    }

    public function setdefault()
    {
        $id = (int) input('post.id', '', 'serach_in');
        $postdata['isDefault'] = 1;

        if (!empty($id)) {
            MemberBankcard::where(['weid' => weid(), 'uid' => UID()])->update(['isDefault' => 0]);
            $postdata['id'] = $id;
            MemberBankcard::update($postdata);
        }
        $data['id'] = $id;
        return $this->json(['data' => $data]);
    }

    public function detail()
    {
        $id = (int) input('get.id', '', 'serach_in');
        if (!empty($id)) {
            $where['weid'] = weid();
            $where['uid'] = UID();
            $where['id'] = $id;
            $data = MemberBankcard::where($where)->find();
            if (!empty($data)) {
                $data = $data->toArray();
            }
        }
        return $this->json(['data' => $data]);
    }

    public function default()
    {

        $where['weid'] = weid();
        $where['uid'] = UID();

        $data = MemberBankcard::where($where)->order('isDefault desc')->find();
        if (!empty($data)) {
            $data = $data->toArray();
        }

        return $this->json(['data' => $data]);
    }

    public function list()
    {
        $where['weid'] = weid();
        $where['uid'] = UID();
        $data = MemberBankcard::where($where)
            ->order('isDefault desc')
            ->select()
            ->toArray();

        return $this->json(['data' => $data]);
    }
    public function listname()
    {
        $data = getcollect_type();
        return $this->json(['data' => $data]);
    }
}
