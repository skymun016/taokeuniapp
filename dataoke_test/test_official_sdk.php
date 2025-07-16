<?php
/**
 * 使用大淘客官方SDK测试
 */

// 引入官方SDK
require_once 'openapi-sdk-php/vendor/autoload.php';

// 配置信息
$appKey = '678fc81d72259';
$appSecret = '6fd2acba8bce6c039ab276256f003ced';
$version = 'v1.2.4';

echo "=== 大淘客官方SDK测试 ===\n";
echo "APP_KEY: {$appKey}\n";
echo "APP_SECRET: {$appSecret}\n";
echo "VERSION: {$version}\n\n";

// 测试1: 商品列表API
echo "1. 测试商品列表API\n";
try {
    $client = new GetGoodsList();
    $client->setAppKey($appKey);
    $client->setAppSecret($appSecret);
    $client->setVersion($version);
    
    $params = [
        'pageId' => 1,
        'pageSize' => 10
    ];
    
    $result = $client->setParams($params)->request();
    echo "商品列表结果: {$result}\n\n";
    
    $data = json_decode($result, true);
    if ($data && isset($data['code']) && $data['code'] == 0) {
        echo "✅ 商品列表API调用成功！\n";
        echo "商品数量: " . count($data['data']['list']) . "\n";
    } else {
        echo "❌ 商品列表API调用失败\n";
        echo "错误信息: " . ($data['msg'] ?? '未知错误') . "\n";
    }
} catch (Exception $e) {
    echo "❌ 商品列表API异常: " . $e->getMessage() . "\n";
}

echo "\n";

// 测试2: 超级分类API
echo "2. 测试超级分类API\n";
try {
    $client = new GetSuperCategory();
    $client->setAppKey($appKey);
    $client->setAppSecret($appSecret);
    $client->setVersion($version);
    
    $result = $client->setParams([])->request();
    echo "超级分类结果: {$result}\n\n";
    
    $data = json_decode($result, true);
    if ($data && isset($data['code']) && $data['code'] == 0) {
        echo "✅ 超级分类API调用成功！\n";
        echo "分类数量: " . count($data['data']) . "\n";
    } else {
        echo "❌ 超级分类API调用失败\n";
        echo "错误信息: " . ($data['msg'] ?? '未知错误') . "\n";
    }
} catch (Exception $e) {
    echo "❌ 超级分类API异常: " . $e->getMessage() . "\n";
}

echo "\n";

// 测试3: 商品详情API
echo "3. 测试商品详情API\n";
try {
    $client = new GetGoodsDetails();
    $client->setAppKey($appKey);
    $client->setAppSecret($appSecret);
    $client->setVersion($version);
    
    // 使用一个测试商品ID
    $params = [
        'goodsId' => '123456789'  // 这里需要一个真实的商品ID
    ];
    
    $result = $client->setParams($params)->request();
    echo "商品详情结果: {$result}\n\n";
    
    $data = json_decode($result, true);
    if ($data && isset($data['code']) && $data['code'] == 0) {
        echo "✅ 商品详情API调用成功！\n";
    } else {
        echo "❌ 商品详情API调用失败\n";
        echo "错误信息: " . ($data['msg'] ?? '未知错误') . "\n";
    }
} catch (Exception $e) {
    echo "❌ 商品详情API异常: " . $e->getMessage() . "\n";
}

echo "\n";

// 测试4: 尝试不同版本
echo "4. 测试不同API版本\n";
$versions = ['v1.2.4', 'v1.2.3', 'v1.2.2', 'v1.2.1', 'v1.2.0'];

foreach ($versions as $testVersion) {
    echo "测试版本: {$testVersion}\n";
    
    try {
        $client = new GetGoodsList();
        $client->setAppKey($appKey);
        $client->setAppSecret($appSecret);
        $client->setVersion($testVersion);
        
        $params = [
            'pageId' => 1,
            'pageSize' => 5
        ];
        
        $result = $client->setParams($params)->request();
        $data = json_decode($result, true);
        
        if ($data && isset($data['code']) && $data['code'] == 0) {
            echo "  ✅ 版本 {$testVersion} 可用！\n";
            echo "  商品数量: " . count($data['data']['list']) . "\n";
            break;
        } else {
            echo "  ❌ 版本 {$testVersion} 不可用\n";
            echo "  错误: " . ($data['msg'] ?? '未知错误') . "\n";
        }
    } catch (Exception $e) {
        echo "  ❌ 版本 {$testVersion} 异常: " . $e->getMessage() . "\n";
    }
    echo "\n";
}

echo "官方SDK测试完成\n";
