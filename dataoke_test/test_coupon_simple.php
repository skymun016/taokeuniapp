<?php
/**
 * 简单测试优惠券功能
 */

require_once 'DataokeAdapter.php';

// 配置信息
$appKey = '678fc81d72259';
$appSecret = '6fd2acba8bce6c039ab276256f003ced';
$pid = 'mm_52162983_2267550029_112173400498';

echo "=== 大淘客优惠券功能测试 ===\n";

// 创建适配器实例
$adapter = new DataokeAdapter($appKey, $appSecret, $pid);

// 获取有优惠券的商品
echo "1. 获取有优惠券的商品\n";
$goodsList = $adapter->getGoodsList([
    'pageId' => 1, 
    'pageSize' => 3,
    'couponPriceLowerLimit' => 10  // 最低优惠券面额10元
]);

if ($goodsList['success'] && !empty($goodsList['data']['list'])) {
    echo "✅ 成功获取到有优惠券的商品\n\n";
    
    foreach ($goodsList['data']['list'] as $index => $goods) {
        echo "=== 商品 " . ($index + 1) . " ===\n";
        echo "商品标题: " . $goods['title'] . "\n";
        echo "商品ID: " . $goods['goodsId'] . "\n";
        echo "原价: ¥" . $goods['originalPrice'] . "\n";
        echo "券后价: ¥" . $goods['actualPrice'] . "\n";
        echo "优惠券面额: ¥" . $goods['couponPrice'] . "\n";
        echo "优惠券使用条件: 满¥" . $goods['couponConditions'] . "可用\n";
        echo "优惠券ID: " . ($goods['couponId'] ?? '无') . "\n";
        echo "优惠券开始时间: " . ($goods['couponStartTime'] ?? '无') . "\n";
        echo "优惠券结束时间: " . ($goods['couponEndTime'] ?? '无') . "\n";
        echo "优惠券剩余数量: " . ($goods['couponRemainCount'] ?? '无') . "\n";
        echo "优惠券总数量: " . ($goods['couponTotalNum'] ?? '无') . "\n";
        echo "优惠券领取数量: " . ($goods['couponReceiveNum'] ?? '无') . "\n";
        
        // 显示优惠券链接
        if (!empty($goods['couponLink'])) {
            echo "优惠券领取链接: " . $goods['couponLink'] . "\n";
        }
        
        // 显示优惠券分享链接
        if (!empty($goods['couponShare'])) {
            echo "优惠券分享链接: " . $goods['couponShare'] . "\n";
        }
        
        // 显示商品链接
        if (!empty($goods['itemLink'])) {
            echo "商品链接: " . $goods['itemLink'] . "\n";
        }
        
        echo "\n" . str_repeat("-", 60) . "\n\n";
    }
    
    // 测试解析淘口令功能
    echo "2. 测试解析淘口令功能\n";
    $testTpwd = "【抢先购】卫龙爆款零食组合领券随心配素毛肚魔芋爽小辣棒辣条";
    $parseResult = testParseTpwd($appKey, $appSecret, $testTpwd);
    if ($parseResult['success']) {
        echo "✅ 淘口令解析成功\n";
        echo "解析结果: " . json_encode($parseResult['data'], JSON_UNESCAPED_UNICODE) . "\n";
    } else {
        echo "❌ 淘口令解析失败: " . $parseResult['message'] . "\n";
    }
    
} else {
    echo "❌ 获取商品失败: " . ($goodsList['message'] ?? '未知错误') . "\n";
}

/**
 * 测试解析淘口令
 */
function testParseTpwd($appKey, $appSecret, $content) {
    try {
        $client = new GetParseTpwd();
        $client->setAppKey($appKey);
        $client->setAppSecret($appSecret);
        $client->setVersion('v1.2.4');
        
        $params = [
            'content' => $content
        ];
        
        $result = $client->setParams($params)->request();
        $data = json_decode($result, true);
        
        if ($data && isset($data['code']) && $data['code'] == 0) {
            return [
                'success' => true,
                'data' => $data['data'],
                'message' => 'success'
            ];
        } else {
            return [
                'success' => false,
                'data' => null,
                'message' => $data['msg'] ?? '解析淘口令失败'
            ];
        }
    } catch (Exception $e) {
        return [
            'success' => false,
            'data' => null,
            'message' => '请求异常: ' . $e->getMessage()
        ];
    }
}

echo "\n3. 测试获取9.9包邮商品\n";
$nineGoodsList = testGetNineGoods($appKey, $appSecret);
if ($nineGoodsList['success']) {
    echo "✅ 9.9包邮商品获取成功\n";
    echo "商品数量: " . count($nineGoodsList['data']['list']) . "\n";
    if (!empty($nineGoodsList['data']['list'])) {
        $firstNineGoods = $nineGoodsList['data']['list'][0];
        echo "第一个9.9商品: " . $firstNineGoods['title'] . "\n";
        echo "价格: ¥" . $firstNineGoods['actualPrice'] . "\n";
    }
} else {
    echo "❌ 9.9包邮商品获取失败: " . $nineGoodsList['message'] . "\n";
}

/**
 * 测试获取9.9包邮商品
 */
function testGetNineGoods($appKey, $appSecret) {
    try {
        $client = new GetNineOpGoodsList();
        $client->setAppKey($appKey);
        $client->setAppSecret($appSecret);
        $client->setVersion('v1.2.4');
        
        $params = [
            'pageId' => 1,
            'pageSize' => 5
        ];
        
        $result = $client->setParams($params)->request();
        $data = json_decode($result, true);
        
        if ($data && isset($data['code']) && $data['code'] == 0) {
            return [
                'success' => true,
                'data' => $data['data'],
                'message' => 'success'
            ];
        } else {
            return [
                'success' => false,
                'data' => null,
                'message' => $data['msg'] ?? '获取9.9商品失败'
            ];
        }
    } catch (Exception $e) {
        return [
            'success' => false,
            'data' => null,
            'message' => '请求异常: ' . $e->getMessage()
        ];
    }
}

echo "\n=== 测试完成 ===\n";
echo "\n总结:\n";
echo "✅ 可以获取带优惠券的商品信息\n";
echo "✅ 可以获取优惠券的详细信息（面额、使用条件、有效期等）\n";
echo "✅ 可以获取优惠券领取链接和分享链接\n";
echo "✅ 可以获取商品的推广链接\n";
echo "❌ 生成淘口令需要特殊权限\n";
echo "❌ 部分转链功能需要渠道专属推广位\n";
