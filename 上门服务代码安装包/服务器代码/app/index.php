<?php
//w7的路径
//define('TP_APIURL',   '/addons/xm_mallv3/public');
define('TP_APIURL',   '/public');
if (empty($_GET["do"])) {
    $_GET["do"] = $do;
}
$doDir = 'index';

if($_GET["do"]=='openid'){
    $_GET["do"] = 'member.openid';
}
if($_GET["do"]=='userinfo'){
    $_GET["do"] = 'member.userinfo';
}
if($_GET["do"]=='check'){
    $_GET["do"] = 'member.check';
}

$stmp = explode('.', $_GET["do"]);

if (empty($stmp[1])) {
    $_GET["do"] = $_GET["do"] . "/index";
} else {
    $_GET["do"] = $stmp[0] . "/" . $stmp[1];
}
if (!empty($stmp[2])) {
    $_GET["do"] = $_GET["do"] . "/" . $stmp[2];
}
if (strtolower($stmp[0]) == strtolower($doDir)) {
    $_GET["do"] = $_GET["do"];
} else {
    $_GET["do"] = $doDir . '/' . $_GET["do"];
}

$_GET["s"] = strtolower($_GET["do"]);
require '..'.TP_APIURL.'/index.php';
