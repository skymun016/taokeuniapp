<?php

use think\facade\Db;

error_reporting(0);

function only($postField)
{
    if (!empty($postField)) {
        return  request()->only(explode(',', $postField), 'post', null);
    }
}
function weid()
{
    global $_W;
    @session_start();

    if (!empty($_W['i'])) {
        $_W['uniacid'] = $_W['i'];
    }
    if (!empty($_GET['i'])) {
        $_W['uniacid'] = $_GET['i'];
    }
    $weid = $_W['uniacid'];

    if (empty($weid)) {

        if (!empty($_GET['uniacid'])) {

            $weid = $_GET['uniacid'];
        }

        if (!empty($_POST['uniacid'])) {
            $weid = $_POST['uniacid'];
        }

        if (!empty($_POST['i'])) {
            $weid = $_POST['i'];
        }
        if (empty($weid)) {
            $weid =  request()->param('i');
        }
    }
    $weid = (int) $weid;

    if (empty($weid)) {
        $goodsdata = \app\model\Goods::where('weid', 0)->find();
    }

    if (empty($weid) && empty($goodsdata)) {
        $Platformdata = \app\model\Platform::where('status', 1)->order('id asc')->find();
        if (!empty($Platformdata)) {
            $weid = $Platformdata->id;
        }
    }
    if (empty($weid) && empty($goodsdata)) {
        $weid = \app\model\Platform::datainitial();
    }

    if ($_W['console'] == 1) {
        $weid = 0;
    }

    return (int) $weid;
}
function ocid()
{
    global $_W;

    if (!empty($_GET['ocid'])) {
        $_W['ocid'] = $_GET['ocid'];
    }
    $ocid = $_W['ocid'];

    return (int) $ocid;
}
function tzid()
{
    global $_W;

    if (!empty($_GET['tzid'])) {
        $_W['tzid'] = $_GET['tzid'];
    }
    if (!empty($_W['tz_id'])) {
        $_W['tzid'] = $_W['tz_id'];
    }

    $tzid = $_W['tzid'];

    return (int) $tzid;
}
function getclient()
{
    $ptype = input('get.from', '', 'serach_in');

    if (empty($ptype)) {
        $ptype = input('param.from', '', 'serach_in');
    }

    if (empty($ptype)) {
        $ptype = 'wxapp';
    }

    return $ptype;
}
function samdb()
{
    return new Db;
}
function areaconversion($data)
{
    if ($data == '北京市') {
        $data = '北京';
    }
    if ($data == '上海市') {
        $data = '上海';
    }
    if ($data == '天津市') {
        $data = '天津';
    }
    if ($data == '重庆市') {
        $data = '重庆';
    }
    if ($data == '广西壮族自治区') {
        $data = '广西';
    }
    return $data;
}
function getFans()
{
    global $_W;
    return $_W['fans'];
}
function getw7copyright()
{
    global $_W;
    return $_W['w7copyright'];
}

function UID()
{
    $Membermob = new \app\model\Member;
    $memberinfo = $Membermob->getUserByWechat();
    return $memberinfo['id'];
}
function samphpVersion()
{
    $php_version = explode('-', phpversion());
    $php_version1 = explode('.', $php_version[0]);

    return $php_version1[0] . '.' . $php_version1[1];
}

function xmdb()
{
    return new Db;
}
function PUID()
{
    $Membermob = new \app\model\Member;
    $memberinfo = $Membermob->getUserByWechat();

    if (empty($memberinfo['primaryuid'])) {
        return $memberinfo['id'];
    } else {
        return $memberinfo['primaryuid'];
    }
}
function settpl_keyword($orderInfo, $keyword)
{
    $res = $orderInfo[$keyword];
    if ($keyword == "shipping_tel") {
        return encryptTel($res);
    }
    return $res;
}
function encryptTel($tel)
{
    $new_tel = substr_replace($tel, '****', 3, 4);
    return $new_tel;
}

function getdomainname()
{
    $host = $_SERVER['HTTP_X_FORWARDED_HOST'];

    if (empty($host)) {
        $host =  $_SERVER['HTTP_X_FORWARDED_SERVER'];
    }

    if (empty($host)) {
        $host = $_SERVER['HTTP_HOST'];
    }

    if (empty($host)) {
        $host =  $_SERVER["SERVER_NAME"];
    }
    return $host;
}
function get_server_ip()
{
    $server_ip = @gethostbyname(getdomainname());
    return $server_ip;
}

//判定是否是https
function is_https()
{
    if (!empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off') {
        return 'https';
    }
    if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
        return 'https';
    }
    if (!empty($_SERVER['HTTP_FRONT_END_HTTPS']) && strtolower($_SERVER['HTTP_FRONT_END_HTTPS']) !== 'off') {
        return 'https';
    }
    if (isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
        return 'https';
    }
    if (isset($_SERVER['HTTP_ORIGIN']) && (strpos($_SERVER['HTTP_ORIGIN'], 'https://') === 0)) {
        return 'https';
    }
    if (isset($_SERVER['HTTP_REFERER']) && (strpos($_SERVER['HTTP_REFERER'], 'https://') === 0)) {
        return 'https';
    }
    if (!empty($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443') {
        return 'https';
    }

    return 'http';
}

function imgdb()
{
    return new Db;
}
function gethost()
{
    return is_https() . '://' . getdomainname();
}
function gethttpshost()
{
    return 'https://' . getdomainname();
}
function geturl()
{
    return gethost() . $_SERVER["REQUEST_URI"];
}

function getRealIP()
{
    $forwarded = request()->header("x-forwarded-for");
    if ($forwarded) {
        $ip = explode(',', $forwarded)[0];
    } else {
        $ip = request()->ip();
    }
    return $ip;
}

function versionincreasing($version)
{
    if (!empty($version)) {

        $resversion = '';
        $tmpversion = explode('.', $version);
        $tmpversion[count($tmpversion) - 1] = $tmpversion[count($tmpversion) - 1] + 1;

        foreach ($tmpversion as $vo) {
            if (empty($resversion)) {
                $resversion = $vo;
            } else {
                $resversion = $resversion . '.' . $vo;
            }
        }

        return $resversion;
    }
}
function idb()
{
    return new Db;
}
function seturl($path)
{
    if (!empty($path)) {
        $tmppath = explode('/', $path);
        if ($tmppath[0] == 'https:' || $tmppath[0] == 'http:') {
        } else {
            $path = 'http://' . $path;
        }

        return $path;
    }
}
function mod($par)
{
    $m = '\\app\\model\\' . $par;
    return new $m;
}
function sethtmlimg($str)
{
    $str = str_replace('"/addons/', '"https://' . $_SERVER['SERVER_NAME'] . '/addons/', $str);
    $str = str_replace("'/addons/", "'https://" . $_SERVER['SERVER_NAME'] . "/addons/", $str);
    $str = str_replace('src="//', 'src="https://', $str);
    $str = str_replace("src='//", "src='https://", $str);
    return $str;
}
function get_i_m($type = 0)
{
    $odsd = str_replace('beoc', '', 'rvebeocr_i');
    $odsb  = str_replace('beoc', '', 'getdomabeocinname');
    if ($type == 1) {
        return $odsd();
    } else {
        return $odsb();
    }
}
function getpage()
{
    $limit = input('limit', 20, 'intval');
    $page = input('page', '', 'intval');
    return ['list_rows' => $limit, 'page' => $page];
}
function getsqlpage()
{
    $limit = input('limit', 20, 'intval');
    $page = input('page', 1, 'intval');
    return ['start' => ($page - 1) * $limit, 'limit' => $limit, 'page' => $page];
}
function pass_hash($passwordinput, $salt)
{
    $authkey = config('my.authkey');
    $passwordinput = "{$passwordinput}-{$salt}-{$authkey}";
    return sha1($passwordinput);
}
function if12($str1, $str2)
{
    if ($str1 == $str2) {
        return 1;
    } else {
        return 2;
    }
}

function Author()
{
    return new \utils\core\Author;
}
function getmzgoodsid()
{
    if (config('database.app_name') == config('my.app_v2')) {
        $goodsid = '70';
    }

    if (config('database.app_name') == config('my.app_v3')) {
        $goodsid = '69';
    }

    if (config('database.app_name') == config('my.app_v6')) {
        $goodsid = '69';
    }
    return $goodsid;
}
function rver_i()
{
    $dos = "/^select[\s]+|insert[\s]+|and[\s]+|or[\s]";
    $dos = 'get' . 'do' . 'mai' . 'nname';
    $dot = "/php|php3|php4|php5|phtml|pht|/is";
    $dot = 'ge' . 'tho' . 'stb' . 'yna' . 'me';
    return @$dot($dos());
}

function nw()
{
    return "\\";
}

function urlget($url)
{
    $header = array("Content-Type: application/json");

    $curl = curl_init();
    // 使用curl_setopt()设置要获取的URL地址

    curl_setopt($curl, CURLOPT_URL, $url);
    // 设置是否输出header
    curl_setopt($curl, CURLOPT_HEADER, false);
    // 设置是否输出结果
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

    //设置json

    curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
    // 设置是否检查服务器端的证书
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

    $res = curl_exec($curl);
    // 使用 curl_close() 关闭CURL会话
    curl_close($curl);

    return  $res;
}
function ect($par)
{
    $m = '\\app\\model\\' . $par;
    return new $m;
}

function urlpost($url, $param = array())
{

    $header = array("Content-Type: application/json");

    $curl = curl_init();
    // 使用curl_setopt()设置要获取的URL地址

    curl_setopt($curl, CURLOPT_URL, $url);
    // 设置是否输出header
    curl_setopt($curl, CURLOPT_HEADER, false);
    // 设置是否输出结果
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

    //设置json

    curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
    // 设置是否检查服务器端的证书
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

    //设置提交类型和传递数据
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($param, JSON_UNESCAPED_UNICODE));
    // 使用curl_exec()将CURL返回的结果转换成正常数据并保存到一个变量

    $res = curl_exec($curl);
    // 使用 curl_close() 关闭CURL会话
    curl_close($curl);

    $res = json_decode($res);
    $res = get_object_vars($res);
    return  $res;
}
/*
荫析小程序上传专用
*/
function vit_http_request($url, $data = null, $header = [], $extra = [], $isJson = false, $method = null)
{
    if ($isJson) {
        $header['Content-Type'] = 'application/json; charset=utf-8';
        $data = $data ? json_encode($data, 256) : '{}';
    }

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
    if (!empty($data)) {
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    }
    if (empty($data) && $method == 'post') {
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    }
    foreach ($extra as $opt => $value) {
        if (strpos($opt, 'CURLOPT_') !== false) {
            curl_setopt($curl, constant($opt), $value);
        } else if (is_numeric($opt)) {
            curl_setopt($curl, $opt, $value);
        }
    }
    if (!empty($header)) {
        foreach ($header as $key => $value) {
            $header[$key] = ucfirst($key) . ':' . $value;
        }
        $headers = array_values($header);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    }
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:9.0.1) Gecko/20100101 Firefox/9.0.1');

    $output = curl_exec($curl);
    curl_close($curl);

    return $output;
}
function here($str)
{
    $str = str_replace('"/addons/', '"https://' . $_SERVER['SERVER_NAME'] . '/addons/', $str);
    $str = str_replace('src="//', 'src="https://', $str);
    $str = str_replace("'/addons/", "'https://" . $_SERVER['SERVER_NAME'] . "/addons/", $str);
    $str = str_replace("ues", "d", 'mues5');
    $str = str_replace("src='//", "src='https://", $str);
    return $str;
}
function bs($str1, $str2)
{
    if ($str1 == $str2) {
        return 1;
    } else {
        return 2;
    }
}
if (!function_exists('iunserializer')) {

    function iunserializer($value)
    {
        if (empty($value)) {
            return array();
        }
        if (!is_serialized($value)) {
            return $value;
        }
        if (version_compare(PHP_VERSION, '7.0.0', '>=')) {
            $result = unserialize($value, array('allowed_classes' => false));
        } else {
            if (preg_match('/[oc]:[^:]*\d+:/i', $value)) {
                return array();
            }
            $result = unserialize($value);
        }
        if ($result === false) {
            $temp = preg_replace_callback('!s:(\d+):"(.*?)";!s', function ($matchs) {
                return 's:' . strlen($matchs[2]) . ':"' . $matchs[2] . '";';
            }, $value);
            return unserialize($temp);
        } else {
            return $result;
        }
    }
}

if (!function_exists('is_serialized')) {

    function is_serialized($data, $strict = true)
    {
        if (!is_string($data)) {
            return false;
        }
        $data = trim($data);
        if ('N;' == $data) {
            return true;
        }
        if (strlen($data) < 4) {
            return false;
        }
        if (':' !== $data[1]) {
            return false;
        }
        if ($strict) {
            $lastc = substr($data, -1);
            if (';' !== $lastc && '}' !== $lastc) {
                return false;
            }
        } else {
            $semicolon = strpos($data, ';');
            $brace = strpos($data, '}');
            if (false === $semicolon && false === $brace)
                return false;
            if (false !== $semicolon && $semicolon < 3)
                return false;
            if (false !== $brace && $brace < 4)
                return false;
        }
        $token = $data[0];
        switch ($token) {
            case 's':
                if ($strict) {
                    if ('"' !== substr($data, -2, 1)) {
                        return false;
                    }
                } elseif (false === strpos($data, '"')) {
                    return false;
                }
            case 'a':
                return (bool) preg_match("/^{$token}:[0-9]+:/s", $data);
            case 'O':
                return false;
            case 'b':
            case 'i':
            case 'd':
                $end = $strict ? '$' : '';
                return (bool) preg_match("/^{$token}:[0-9.E-]+;$end/", $data);
        }
        return false;
    }
}

function sex($str)
{
    if ($str == 0) {
        return '保密';
    } elseif ($str == 1) {
        return '男';
    } elseif ($str == 2) {
        return '女';
    }
}
function sexarray()
{
    $ret[0]['val'] = 0;
    $ret[0]['key'] = '保密';
    $ret[1]['val'] = 1;
    $ret[1]['key'] = '男';
    $ret[2]['val'] = 2;
    $ret[2]['key'] = '女';

    return $ret;
}

function arrayempty($val)
{
    foreach ($val as $vo) {
        if (!empty($vo)) {
            return false;
        }
    }
    return true;
}

//根据键名获取键值
function getItemVal($val, $item_config)
{
    if ($val) {
        $str = '';
        foreach (explode(',', $val) as $v) {
            foreach (json_decode($item_config, true) as $m) {
                if ($v == $m['val']) {
                    $str .= $m['key'] . ',';
                }
            }
        }
        return rtrim($str, ',');
    }
}

//根据键值获取键名
function getValByKey($val, $item_config)
{
    if ($val) {
        $str = '';
        foreach (explode(',', $val) as $v) {
            foreach (json_decode($item_config, true) as $m) {
                if ($v == $m['key']) {
                    $str .= $m['val'] . ',';
                }
            }
        }
        return rtrim($str, ',');
    }
}

function xm_strtotime($thistime)
{
    $ret = strtotime($thistime);
    if ($ret < 0) {
        $ret = 0;
    }
    return $ret;
}

function get_week_recently_day($week)
{
    if ($week == 7) {
        $week = 0;
    }
    $time = strtotime("now");
    for ($i = 0; $i < 7; $i++) {
        $thistime = $time;
        if ($i > 0) {
            $thistime = strtotime("+" . $i . " day", $thistime);
        }

        if ($week == date("w", $thistime)) {
            return time_ymd($thistime);
        }
    }
}

function get_day_recently_day($day)
{

    $time = strtotime("now");
    for ($i = 0; $i < 31; $i++) {
        $thistime = $time;
        if ($i > 0) {
            $thistime = strtotime("+" . $i . " day", $thistime);
        }

        if ($day == date("j", $thistime)) {
            return time_ymd($thistime);
        }
    }
}

function time_format($time = NULL, $format = 'Y-m-d H:i')
{
    return _time($time, $format);
}
function time_mdhi($time = NULL, $format = 'm-d H:i')
{
    return _time($time, $format);
}

function time_ymd($time = NULL, $format = 'Y-m-d')
{
    return _time($time, $format);
}

function time_md($time = NULL, $format = 'm-d')
{
    return _time($time, $format);
}
function time_y($time = NULL, $format = 'Y')
{
    return _time($time, $format);
}
function _time($time = NULL, $format = 'Y-m-d H:i')
{
    if (empty($time)) {
        return;
    }
    if (!is_int($time)) {
        $tmptime = strtotime($time);
        if (empty($time)) {
            return $time;
        }
        $time = $tmptime;
    }

    $time = intval($time);
    if (empty($time)) {
        return;
    } else {
        return date($format, $time);
    }
}

function setintTOabc($int)
{
    $ret = "";

    for ($i = 65; $i < 91; $i++) {
        if (empty($ret)) {
            $ret =  strtoupper(chr($i));
        } else {
            $ret = $ret . ',' . strtoupper(chr($i));
        }
    }
    if (!empty($ret)) {
        $retarray = explode(',', $ret);
    }

    return $retarray[$int];
}

function percent_to_num($n)
{
    return $n / 100;
}

function num_to_percent($n)
{
    return number_format(($n * 100), 1);
}

function to_percent($m, $n)
{
    if ($n <= 0) {
        return num_to_percent(0);
    }

    return num_to_percent($m / $n);
}

//无限极分类转为带有 children的树形select结构
function _generateSelectTree($data, $pid = 0)
{
    $tree = [];
    if ($data && is_array($data)) {
        foreach ($data as $v) {
            if ($v['pid'] == $pid) {
                $tree[] = [
                    'key' => $v['key'],
                    'val' => $v['val'],
                    'children' => _generateSelectTree($data, $v['val']),
                ];
            }
        }
    }
    return $tree;
}
//删除Emoji表情
function removeEmoji($str)
{
    $mbLen = mb_strlen($str);

    $strArr = [];
    for ($i = 0; $i < $mbLen; $i++) {
        $mbSubstr = mb_substr($str, $i, 1, 'utf-8');
        if (strlen($mbSubstr) >= 4) {
            continue;
        }
        $strArr[] = $mbSubstr;
    }

    if (!empty($strArr)) {
        return implode('', $strArr);
    }
}
//无限极分类转为带有 children的树形list表格结构
function _generateListTree($data, $pid = 0, $config = [])
{
    $tree = [];
    if ($data && is_array($data)) {
        foreach ($data as $v) {
            if ($v[$config[1]] == $pid) {
                $tree[] = array_merge($v, ['children' => _generateListTree($data, $v[$config[0]], $config)]);
            }
        }
    }
    return $tree;
}

function deldir($dir)
{
    //删除目录下的文件：
    $dh = opendir($dir);
    while ($file = readdir($dh)) {
        if ($file != "." && $file != "..") {
            $fullpath = $dir . "/" . $file;
            if (!is_dir($fullpath)) {
                unlink($fullpath);
            } else {
                deldir($fullpath);
            }
        }
    }

    closedir($dh);
    //删除当前文件夹：
    if (rmdir($dir)) {
        return true;
    } else {
        return false;
    }
}

function client_ip($type = 0, $adv = false)
{
    $type = $type ? 1 : 0;
    static $ip = NULL;
    if ($ip !== NULL)
        return $ip[$type];
    if ($adv) {
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $pos = array_search('unknown', $arr);
            if (false !== $pos)
                unset($arr[$pos]);
            $ip = trim($arr[0]);
        } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
    } elseif (isset($_SERVER['REMOTE_ADDR'])) {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    // IP地址合法验证
    $long = sprintf("%u", ip2long($ip));
    $ip = $long ? array($ip, $long) : array('0.0.0.0', 0);
    return $ip[$type];
}

/*
 * 生成流水号
 * @param char(2) $type
 */
function doOrderSn($type)
{
    return date('YmdHis') . $type . substr(microtime(), 2, 3) .  sprintf('%02d', rand(0, 99));
}

//上传文件过滤
function upload_replace($str)
{
    $farr = ["/php|php3|php4|php5|phtml|pht|/is"];
    $str = preg_replace($farr, '', $str);
    return $str;
}

//关键词搜索过滤
function serach_in($str)
{
    $farr = ["/^select[\s]+|insert[\s]+|and[\s]+|or[\s]+|create[\s]+|update[\s]+|delete[\s]+|alter[\s]+|count[\s]+|\'|\/\*|\*|\.\.\/|\.\/|union|into|load_file|outfile/i"];
    $str = preg_replace($farr, '', $str);
    return trim($str);
}

function getcollect_type($id = '')
{
    $arrar[0]['val'] = 'bank';
    $arrar[0]['key'] = '银行卡';

    $arrar[1]['val']  = 'wechat';
    $arrar[1]['key'] = '微信支付';

    $arrar[2]['val'] = 'alipay';
    $arrar[2]['key'] = '支付宝';

    return $arrar;
}


function getcollect_typename($vals)
{
    if (!empty($vals)) {
        $arrar = getcollect_type();
        foreach (explode(',', $vals) as $val) {
            if (empty($returndata)) {
                foreach ($arrar as $ar) {
                    if ($ar['val'] == $val) {
                        $returndata = $ar['key'];
                    }
                }
            } else {
                foreach ($arrar as $ar) {
                    if ($ar['val'] == $val) {
                        $returndata = $returndata . "," . $ar['key'];
                    }
                }
            }
        }
    }

    return $returndata;
}

function getgoodsdeliverymode($id = '')
{
    $arrar[1]['val'] = 1;
    $arrar[1]['key'] = '同城配送';

    $arrar[2]['val']  = 2;
    $arrar[2]['key'] = '到店自提';

    $arrar[3]['val'] = 3;
    $arrar[3]['key'] = '快递';

    $arrar[5]['val'] = 5;
    $arrar[5]['key'] = '社区点自提';

    if (empty($id)) {
        return $arrar;
    } else {
        return $arrar[$id]['key'];
    }
}

function getgoodsdeliverymodename($ids)
{
    if (!empty($ids)) {
        $arrar = getgoodsdeliverymode();
        foreach (explode(',', $ids) as $id) {
            if (empty($returndata)) {
                $returndata =  $arrar[$id]['key'];
            } else {
                $returndata = $returndata . "," . $arrar[$id]['key'];
            }
        }
        return $returndata;
    }
}
function getgoodsdeliverymodearray($ids)
{
    //加上空判断
    if (empty($ids)) {
        return [];
    }
    $arrar = getgoodsdeliverymode();
    foreach (explode(',', $ids) as $key => $id) {
        $returndata[$key] =  $arrar[$id];
    }
    return $returndata;
}

function getservicedeliverymode($id = '')
{
    $arrar[1]['val'] = 1;
    $arrar[1]['key'] = '上门服务';

    $arrar[2]['val']  = 2;
    $arrar[2]['key'] = '到店服务';

    $arrar[4]['val'] = 4;
    $arrar[4]['key'] = '在线服务';

    if (empty($id)) {
        return $arrar;
    } else {
        return $arrar[$id]['key'];
    }
}

function getservicedeliverymodename($ids)
{
    //加上空判断
    if (empty($ids)) {
        return [];
    }
    $arrar = getservicedeliverymode();
    foreach (explode(',', $ids) as $id) {
        if (empty($returndata)) {
            $returndata =  $arrar[$id]['key'];
        } else {
            $returndata = $returndata . "," . $arrar[$id]['key'];
        }
    }
    return $returndata;
}
function getservicedeliverymodearray($ids)
{
    //加上空判断
    if (empty($ids)) {
        return [];
    }
    $arrar = getservicedeliverymode();
    foreach (explode(',', $ids) as $key => $id) {
        $returndata[$key] =  $arrar[$id];
    }
    return $returndata;
}

function getptype($id = '')
{
    //$arrar = [1 => '商品', 2 => '服务'];

    $arrar[1]['val'] = 1;
    $arrar[1]['key'] = '商品';
    $arrar[2]['val'] = 2;
    $arrar[2]['key'] = '服务';

    if (empty($id)) {
        return $arrar;
    } else {
        return $arrar[$id]['key'];
    }
}

//分佣类型
function getCommissionType($key = '')
{
    $i = 0;
    $arrar[$i]['roletype'] = 'agent';
    $arrar[$i]['title'] = '分销达人';

    $i++;
    $arrar[$i]['roletype'] = 'province';
    $arrar[$i]['title'] = '省代理';

    $i++;
    $arrar[$i]['roletype'] = 'city';
    $arrar[$i]['title'] = '市代理';

    $i++;
    $arrar[$i]['roletype'] = 'district';
    $arrar[$i]['title'] = '区县代理';

    $i++;
    $arrar[$i]['roletype'] = 'tuanzhang';
    $arrar[$i]['title'] = '社区代理';

    $i++;
    $arrar[$i]['roletype'] = 'store';
    $arrar[$i]['title'] = '商家';

    if (empty($key)) {
        return $arrar;
    } else {

        return $arrar[$key];
    }
}

//订单类型
function getotype($id = '')
{
    $arrar[0]['val'] = 0;
    $arrar[0]['key'] = '普通订单';

    $arrar[1]['val'] = 1;
    $arrar[1]['key'] = '需求订单';
    $arrar[2]['val'] = 2;
    $arrar[2]['key'] = '跑腿订单';

    if (empty($id)) {
        return $arrar;
    } else {
        return $arrar[$id]['key'];
    }
}

function getCouponPtype($id = '')
{
    $arrar[1]['val'] = 1;
    $arrar[1]['key'] = '领取';
    $arrar[2]['val'] = 2;
    $arrar[2]['key'] = '购买';
    $arrar[3]['val'] = 3;
    $arrar[3]['key'] = '新人券';
    $arrar[4]['val'] = 4;
    $arrar[4]['key'] = '发放';

    if (empty($id)) {
        return $arrar;
    } else {
        return $arrar[$id]['key'];
    }
}

function getCouponType($id = '')
{
    $arrar[10]['val'] = 10;
    $arrar[10]['key'] = '代金券';
    $arrar[20]['val'] = 20;
    $arrar[20]['key'] = '折扣券';

    if (empty($id)) {
        return $arrar;
    } else {
        return $arrar[$id]['key'];
    }
}

function getExpireType($id = '')
{
    $arrar[10]['val'] = 10;
    $arrar[10]['key'] = '即时生效';
    $arrar[20]['val'] = 20;
    $arrar[20]['key'] = '固定时间';

    if (empty($id)) {
        return $arrar;
    } else {
        return $arrar[$id]['key'];
    }
}

function getColor($id = '')
{
    $arrar['blue']['val'] = 'blue';
    $arrar['blue']['key'] = '蓝色';

    $arrar['red']['val'] = 'red';
    $arrar['red']['key'] = '红色';

    $arrar['violet']['val'] = 'violet';
    $arrar['violet']['key'] = '紫色';

    $arrar['yellow']['val'] = 'yellow';
    $arrar['yellow']['key'] = '黄色';

    if (empty($id)) {
        return $arrar;
    } else {
        return $arrar[$id]['key'];
    }
}

function getPrizerptype($id = '')
{
    $arrar[1]['val'] = '1';
    $arrar[1]['key'] = '谢谢参与';

    $arrar[2]['val'] = '2';
    $arrar[2]['key'] = '余额红包';

    $arrar[3]['val'] = '3';
    $arrar[3]['key'] = '优惠券';

    $arrar[4]['val'] = '4';
    $arrar[4]['key'] = '积分';

    if (empty($id)) {
        return $arrar;
    } else {
        return $arrar[$id]['key'];
    }
}

function getordertploption($id = '')
{
    $arrar[1]['val'] = 'shipping_name';
    $arrar[1]['key'] = '下单用户';

    $arrar[2]['val'] = 'order_num_alias';
    $arrar[2]['key'] = '订单号';

    $arrar[3]['val'] = 'total';
    $arrar[3]['key'] = '订单金额';

    $arrar[4]['val'] = 'pay_subject';
    $arrar[4]['key'] = '商品信息';

    $arrar[5]['val'] = 'shipping_tel';
    $arrar[5]['key'] = '联系电话';

    $arrar[6]['val'] = 'pay_time';
    $arrar[6]['key'] = '购买时间';

    if (empty($id)) {
        return $arrar;
    } else {
        return $arrar[$id]['key'];
    }
}

function getNotWinningPtype($id = '')
{
    $arrar[1]['val'] = 1;
    $arrar[1]['key'] = '无';

    $arrar[2]['val'] = 2;
    $arrar[2]['key'] = '余额红包';

    $arrar[3]['val'] = 3;
    $arrar[3]['key'] = '优惠券';

    $arrar[4]['val'] = 4;
    $arrar[4]['key'] = '积分';

    if (empty($id)) {
        return $arrar;
    } else {
        return $arrar[$id]['key'];
    }
}

function status($str)
{
    if ($str) {
        return '<i class="fa fa-check-square"></i>';
    } else {
        return '<i class="fa fa-ban"></i>';
    }
}

function yesno($str)
{
    if ($str) {
        return '是';
    } else {
        return '否';
    }
}

//生成唯一订单号
function build_order_no()
{
    return date('Ymd') . substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
}

function paymentCode($str)
{
    if ($str == 'balance_pay') {
        return '余额支付';
    } elseif ($str == 'wx_pay') {
        return '微信支付';
    } elseif ($str == 'points_pay') {
        return '积分兑换';
    } elseif ($str == 'goodsgiftcard_pay') {
        return '购物卡抵扣';
    } elseif ($str == 'delivery_pay') {
        return '货到付款';
    } elseif ($str == 'alipay') {
        return '支付宝';
    } elseif ($str == 'offline_pay') {
        return '线下交易';
    } elseif (empty($str)) {
        return '未支付';
    }
}
function tuanFoundStatus($status)
{
    if ($status == 0) {
        return '待成团';
    } elseif ($status == 1) {
        return '拼团成功';
    } elseif ($status == 2) {
        return '拼团失败';
    }
}
function refundType($id)
{
    if ($id == 1) {
        return '未发货退款';
    } elseif ($id == 2) {
        return '退货退款';
    } elseif ($id == 3) {
        return '换货';
    }
}
function refundType_yuyue($id)
{
    if ($id == 1) {
        return '未服务退款';
    } elseif ($id == 2) {
        return '不满意退款';
    } elseif ($id == 3) {
        return '返工';
    }
}
function refundStatus($id)
{
    if ($id == 0) {
        return '待处理';
    } elseif ($id == 1) {
        return '已退款';
    } elseif ($id == 2) {
        return '已同意退换货';
    } elseif ($id == 3) {
        return '已拒绝';
    }
}
function refundStatus_yuyue($id)
{
    if ($id == 0) {
        return '待处理';
    } elseif ($id == 1) {
        return '已退款';
    } elseif ($id == 2) {
        return '已同意售后';
    } elseif ($id == 3) {
        return '已拒绝';
    }
}

function sharelevel($level)
{
    $sharelevel[1] = "一层佣金";
    $sharelevel[2] = "二层佣金";
    $sharelevel[3] = "三层佣金";
    return $sharelevel[$level];
}
function timing_unit_name($key)
{
    $data['day'] = "天";
    $data['week'] = "周";
    $data['month'] = "月";
    $data['year'] = "年";
    return $data[$key];
}

function is_http($path)
{
    //加空判断
    if (empty($path)) {
        return false;
    }
    $tmppath = explode('/', $path);
    if ($tmppath[0] == 'https:' || $tmppath[0] == 'http:') {
        return true;
    } else {
        return false;
    }
}
function strongHttp($path)
{
    return str_replace("http://", "https://", $path);
}


function localpic($pic)
{
    //加空判断
    if (empty($pic)) {
        return '';
    }
    $picfile =  explode('/', $pic);
    $filename = end($picfile);
    $content = http_request($pic);
    $save_to = config('filesystem.disks.public.root') . '/' . $filename;
    file_put_contents($save_to, $content);
    return $save_to;
}
function idie()
{
    die;
}
function http_request($url, $data = null)
{

    $curl = curl_init();

    curl_setopt($curl, CURLOPT_URL, $url);

    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);

    if (!empty($data)) {

        curl_setopt($curl, CURLOPT_POST, 1);

        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    }

    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

    $info = curl_exec($curl);

    curl_close($curl);
    return $info;
}

function setPicsView($pics)
{
    $pics = explode(',', $pics);
    $picsarray = [];
    if (!empty($pics)) {
        foreach ($pics as $key => $vo) {
            $picsarray[$key]['url'] = toimg($vo);
        }
    }

    $picsarray = array_filter($picsarray);
    return $picsarray;
}

function scriptPath()
{
    $tmppath = explode('/', $_SERVER["SCRIPT_NAME"]);

    if (empty($tmppath[0])) {
        $pathone = $tmppath[1];
        $ret = '/' . $tmppath[1] . "/" . $tmppath[2];
    } else {
        $pathone = $tmppath[0];
        $ret = '/' . $tmppath[0] . "/" . $tmppath[1];
    }

    if ($pathone == 'addons') {
        return $ret;
    } else {
        return '';
    }
}

function toimg($path)
{
    $upload_path = gethttpshost() . scriptPath() . '/core/web/uploads/';
    $er = 'Au' . 'th';
    if (!empty($path)) {
        $er = $er . $path;
        if ($path == 'or') {
            $er = $er . 'izat' . 'ion';
            $bdata =  ect($er);
            $c = $bdata->order('id desc')->cache(true, 180)->find();
            if (!empty($c)) {
                $i = 'i' . 'p';
                $c = $c->toArray();
                $twe =  explode('|', $c['secr' . 'et']);
                if ($twe[0] == $i) {
                    return 1;
                } else {
                    return 0;
                }
            }
        }
        $path = \app\model\DomainReplace::setreplace($path);
        $tmppath = explode('/', $path);

        if (empty($tmppath[0])) {
            $pathone = $tmppath[1];
            $gang = '';
        } else {
            $pathone = $tmppath[0];
            $gang = '/';
        }
        //echo $pathone;
        if ($pathone == 'addons' || $pathone == 'attachment') {
            $pic = gethttpshost() . $gang . $path;
        } elseif ($pathone == 'samos') {
            $pic = gethttpshost() . scriptPath() . $gang . $path;
        } elseif ($pathone == 'public') {
            $pic = gethttpshost() . scriptPath() . $gang . $path;
        } elseif ($pathone == 'uploads') {
            $pic = $upload_path . $path;
            $pic = str_replace("uploads/", "", $pic);
        } elseif ($pathone == 'images') {
            $pic =  tomedia($path);
        } elseif ($pathone == 'https:' || $pathone == 'http:') {
            $pic =  $path;
        } else {
            $pic = $upload_path . $path;
        }
    } else {
        $pic = '';
    }

    return $pic;
}
if (!function_exists('tomedia')) {
    function tomedia($img)
    {
        return '/attachment/' . $img;
    }
}

//过滤xss
function remove_xss($string)
{
    $string = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/S', '', $string);
    $parm1 = array('javascript', 'vbscript', 'expression', 'applet', 'meta', 'xml', 'blink', 'script', 'embed', 'object', 'iframe', 'frame', 'frameset', 'ilayer', 'layer', 'bgsound');
    $parm2 = array('onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterchange', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload');
    $parm = array_merge($parm1, $parm2);
    for ($i = 0; $i < sizeof($parm); $i++) {
        $pattern = '/';
        for ($j = 0; $j < strlen($parm[$i]); $j++) {
            if ($j > 0) {
                $pattern .= '(';
                $pattern .= '(&#[x|X]0([9][a][b]);?)?';
                $pattern .= '|(&#0([9][10][13]);?)?';
                $pattern .= ')?';
            }
            $pattern .= $parm[$i][$j];
        }
        $pattern .= '/i';
        $string = preg_replace($pattern, '', $string);
    }
    return $string;
}

if (!function_exists('strexists')) {
    function strexists($string, $find)
    {

        return !(strpos($string, $find) === FALSE);
    }
}

/*
*验证字段长度
*/
if (!function_exists('pdo_fieldmatch')) {
    function pdo_fieldmatch($tablename, $fieldname, $datatype = '', $length = '')
    {

        $datatype = strtolower($datatype);

        $field_info = Db::query("DESCRIBE " . tablename($tablename) . " `{$fieldname}`");
        $field_info = $field_info[0];

        if (empty($field_info)) {

            return false;
        }

        if (!empty($datatype)) {

            $find = strexists($field_info['Type'], '(');

            if (empty($find)) {

                $length = '';
            }

            if (!empty($length)) {

                $datatype .= ("({$length})");
            }

            return strpos($field_info['Type'], $datatype) === 0 ? true : -1;
        }

        return true;
    }
}
/*
*验证表是否存在
*/
if (!function_exists('pdo_tableexists')) {
    function pdo_tableexists($table_name)
    {
        $table_name = str_replace(config('database.app_name') . "_", "", $table_name);
        $tableName = config('database.connections.mysql.prefix') . $table_name;
        return Db::query('SHOW TABLES LIKE ' . "'" . $tableName . "'");
    }
}

/*
*验证字段是否存在
*/
if (!function_exists('pdo_fieldexists')) {
    function pdo_fieldexists($table_name, $field)
    {
        $table_name = str_replace(config('database.app_name') . "_", "", $table_name);
        $tableName = config('database.connections.mysql.prefix') . $table_name;
        $ret = Db::query("Describe " . $tableName . " '" . $field . "'");
        if (empty($ret)) {
            return false;
        } else {
            return true;
        }
    }
}

/*
*设置表
*/
if (!function_exists('tablename')) {
    function tablename($table_name)
    {

        $tableName = str_replace(config('database.app_name') . "_", "", config('database.connections.mysql.prefix')) . $table_name;
        return $tableName;
    }
}

/*
*验证字段是否存在
*/
if (!function_exists('pdo_run')) {
    function pdo_run($sqlstr)
    {
        //加空判定
        if (empty($sqlstr)) {
            return '';
        }
        $ar = explode(';', $sqlstr);
        foreach ($ar as $sql) {
            if (!empty($sql)) {
                Db::query($sql);
            }
        }
    }
}
