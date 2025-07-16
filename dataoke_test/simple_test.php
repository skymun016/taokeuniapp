<?php
/**
 * 简单测试大淘客官方SDK
 */

echo "开始测试...\n";

// 引入官方SDK
require_once 'openapi-sdk-php/vendor/autoload.php';

echo "SDK加载完成\n";

// 配置信息
$appKey = '678fc81d72259';
$appSecret = '6fd2acba8bce6c039ab276256f003ced';
$version = 'v1.2.4';

echo "配置信息设置完成\n";

// 测试商品列表API
echo "创建GetGoodsList客户端...\n";
$client = new GetGoodsList();

echo "设置参数...\n";
$client->setAppKey($appKey);
$client->setAppSecret($appSecret);
$client->setVersion($version);

echo "准备请求参数...\n";
$params = [
    'pageId' => 1,
    'pageSize' => 5
];

echo "发送请求...\n";
$result = $client->setParams($params)->request();

echo "请求完成，结果: {$result}\n";

$data = json_decode($result, true);
if ($data && isset($data['code'])) {
    echo "响应代码: " . $data['code'] . "\n";
    if ($data['code'] == 0) {
        echo "✅ 成功！\n";
        if (isset($data['data']['list'])) {
            echo "商品数量: " . count($data['data']['list']) . "\n";
        }
    } else {
        echo "❌ 失败: " . ($data['msg'] ?? '未知错误') . "\n";
    }
} else {
    echo "❌ 无法解析响应\n";
}

echo "测试完成\n";
