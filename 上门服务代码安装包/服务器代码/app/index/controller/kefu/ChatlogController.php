<?php

namespace app\index\controller\kefu;

use think\exception\ValidateException;
use app\model\kefu\Seating;
use app\model\kefu\Chatlog;
use app\model\kefu\Chat;
use app\model\Config;

class ChatlogController extends Base
{
    function add()
    {

        $weid = weid();
        $iskf = input('post.iskf', '', 'serach_in');
        $is_group = input('post.is_group', '', 'serach_in');
        $chattype = intval(input('post.chattype', '', 'serach_in'));
        $contentType = input('post.contentType', '', 'serach_in');
        $fromclient = input('post.fromclient', '', 'serach_in');
        $toid = input('post.toid', '', 'serach_in');
        $content = trim(input('post.content', '', 'serach_in'));
        $toname = trim(input('post.toname', '', 'serach_in'));
        $chatgroupid = trim(input('post.chatgroupid', '', 'serach_in'));

        if (is_numeric($toid)) {
            $is_group = 1;
        }

        $kefuconfig = \app\model\Config::getconfig('kefu');
        $messagetpl = \app\model\Config::getconfig('messagetpl');

        if (empty($chattype)) {
            $chattype = 'user';
        }

        $data['toid'] = $toid;
        $data['toname'] = $toname;
        $data['fromclient'] = $fromclient;
        $data['time'] = time();
        $data['contentType'] = $contentType;
        $data['content'] = $content;
        $data['weid'] = $weid;
        $data['chattype'] = $chattype;
        $data['reading'] = 0;

        if (empty($iskf)) {

            $data['fromid'] = $this->userInfo['openid'];
            $data['fromname'] = empty($this->userInfo['nickname']) ? '匿名用户' :  $this->userInfo['nickname'];
            $data['fromavatar'] = empty($this->userInfo['avatar']) ? $kefuconfig['defaultavatar'] :  $this->userInfo['avatar'];

            if (empty($data['toname'])) {
                $seating  = Seating::field('uid,chatid,title,touxiang')->where('chatid', $data['toid'])->find();
                $data['toname'] = $seating['title'];
            }
        } else {
            $fromid = input('post.fromid', '', 'serach_in');
            $seating  = Seating::field('uid,chatid,title,touxiang')->where('chatid', $fromid)->find();
            $data['fromid'] = $fromid;
            $data['fromname'] = $seating['title'];
            $data['fromavatar'] = empty($seating['touxiang']) ? $kefuconfig['defaultavatar'] : $seating['touxiang'];
        }

        if ($is_group == 1 || is_numeric($data['toid'])) {
            $chatgroupid = $data['toid'];
            $grouptopid = Chat::getcrowd($data['toid']);
            $grouptopid = str_replace($data['fromid'] . ',', "", $grouptopid);
            $grouptopid = str_replace(',' . $data['fromid'], "", $grouptopid);
            $grouptopid = str_replace($data['fromid'], "", $grouptopid);

            $grouptopid = explode(",", $grouptopid);
        }

        if (empty($chatgroupid)) {
            $chatgroupid = Chat::getchatid($data['fromid'], $data['toid']);
        }

        $data['chatgroupid'] = $chatgroupid;

        $ret =  Chatlog::create($data);

        $url = gethost() . scriptPath() . '/public/h5-im/?openid=' . $data['toid'] . '&toid=' . $data['fromid'] . '&xmtoken=xmtokenvalue';

        $app = \app\samos\wechat\Wechatmp::makemp();

        if (!empty($app)) {
            $kefudata['username'] = $data['fromname'];
            $kefudata['kefu'] = $data['toname'];
            $kefudata['time'] = date('Y-m-d H:i:s', $data['time']);
            $kefudata['content'] = $content;

            $message['data']['thing3']['value'] = $kefudata['username'];
            $message['data']['thing2']['value'] = mb_substr($kefudata['content'], 0, 20);

            $app->template_message->send([
                'touser' =>  $data['toid'],
                'template_id' => trim($messagetpl['kefu_tpl']),
                'url' => $url,
                'data' => $message['data'],
            ]);

            //$ret = $ret->toArray();
            $ret['grouptopid'] = $grouptopid;
            $ret['is_group'] = $is_group;
        }
        return $this->json(['data' => $ret]);
    }

    function log()
    {
        $toid = input('post.toid', '', 'serach_in');

        $fromid = $this->userInfo['openid'];

        $chatgroupid =   Chat::getchatid($fromid, $toid);

        if (!empty($chatgroupid)) {
            $chantcon = Chatlog::where(['chatgroupid' => $chatgroupid])->select();
            $msgList = [];
            foreach ($chantcon as $kk => $vo) {
                $msgList[$kk]['id'] = $vo['id'];
                $msgList[$kk]['avatarUrl'] = $vo['fromavatar'];
                $msgList[$kk]['fromid'] = $vo['fromid'];
                $msgList[$kk]['fromname'] = $vo['fromname'];
                $msgList[$kk]['chattype'] = $vo['chattype'];
                $msgList[$kk]['content'] = $vo['content'];
                $msgList[$kk]['contentType'] = $vo['contentType'];
                $msgList[$kk]['createTime'] =  date('Y-m-d H:i', $vo['time']);

                if ($vo['fromid'] == $fromid) {
                    $msgList[$kk]['role'] = 'right';
                } else {
                    $msgList[$kk]['role'] = 'left';
                }
            }
        }
        $data['msgList'] = $msgList;

        return $this->json(['data' => $data]);
    }

    function chatlist()
    {
        $fromid = input('post.fromid', '', 'serach_in');
        $data = [];

        if (!empty($fromid)) {

            $mychatlist = Chat::field('id,crowd,title,is_group,time')->where(['weid' => weid()])->where('crowd', 'find in set', $fromid)->select();
            if (!empty($mychatlist)) {
                $mychatlist = $mychatlist->toArray();
                $k = 0;
                foreach ($mychatlist as  $v) {
                    $crowd = explode(',', $v['crowd']);

                    $chantcon = [];
                    if (!empty($crowd[1])) {
                        $chantcon = [];
                        $chantcon = Chatlog::where(['chatgroupid' => $v['id']])->select()->toArray();
                        if (!empty($chantcon)) {
                            $data[$k]['is_group'] = 0;
                            $msgList = [];
                            foreach ($chantcon as $kk => $vo) {
                                $msgList[$kk]['avatarUrl'] = $vo['fromavatar'];
                                $msgList[$kk]['content'] = $vo['content'];
                                $msgList[$kk]['contentType'] = $vo['contentType'];
                                $msgList[$kk]['createTime'] =  date('Y-m-d H:i', $vo['time']);

                                $data[$k]['accessTime'] = date('Y-m-d H:i', $vo['time']);
                                $data[$k]['inputContent'] = '';
                                $data[$k]['isFollow'] = false;
                                $data[$k]['lastMsgContent'] = $vo['content'];
                                $data[$k]['lastMsgShowTime'] = date('Y-m-d H:i', $vo['time']);
                                $data[$k]['lastMsgTime'] = date('Y-m-d H:i', $vo['time']);
                                $data[$k]['state'] = 'on';
                                $data[$k]['newMsgCount'] = '';

                                if ($vo['fromid'] == $fromid) {
                                    $msgList[$kk]['role'] = 'server';
                                    $data[$k]['clientChatId'] = $vo['toid'];
                                    $data[$k]['clientChatName'] = $vo['toname'];
                                } else {
                                    $msgList[$kk]['role'] = 'client';
                                    $data[$k]['clientChatId'] = $vo['fromid'];
                                    $data[$k]['clientChatName'] = $vo['fromname'];
                                }

                                $data[$k]['msgList'] = $msgList;
                            }
                            if ($v['is_group'] == 1) {
                                $data[$k]['accessTime'] = date('Y-m-d H:i', $v['time']);
                                $data[$k]['clientChatId'] = $v['id'];
                                $data[$k]['is_group'] = 1;
                                $data[$k]['clientChatName'] = $v['title'];
                                $data[$k]['inputContent'] = '';
                                $data[$k]['isFollow'] = false;
                                $data[$k]['lastMsgContent'] = '';
                                $data[$k]['lastMsgShowTime'] = date('Y-m-d H:i', $v['time']);
                                $data[$k]['lastMsgTime'] = date('Y-m-d H:i', $v['time']);
                                $data[$k]['msgList'] = $msgList;
                                $data[$k]['state'] = 'on';
                                $data[$k]['newMsgCount'] = '';
                            }
                            $k++;
                        }
                    }
                }
            }
        }

        return $this->json(['data' => $data]);
    }
}
