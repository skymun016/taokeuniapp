<?php
/**
 * 测试大淘客优惠券和淘口令功能
 */

require_once 'DataokeAdapter.php';

// 配置信息
$appKey = '678fc81d72259';
$appSecret = '6fd2acba8bce6c039ab276256f003ced';
$pid = 'mm_52162983_2267550029_112173400498';

echo "=== 大淘客优惠券和淘口令测试 ===\n";

// 创建适配器实例
$adapter = new DataokeAdapter($appKey, $appSecret, $pid);

// 1. 获取有优惠券的商品
echo "1. 获取有优惠券的商品列表\n";
$goodsList = $adapter->getGoodsList([
    'pageId' => 1, 
    'pageSize' => 5,
    'couponPriceLowerLimit' => 5  // 最低优惠券面额5元
]);

if ($goodsList['success'] && !empty($goodsList['data']['list'])) {
    echo "✅ 获取到有优惠券的商品\n";
    
    foreach ($goodsList['data']['list'] as $index => $goods) {
        echo "\n--- 商品 " . ($index + 1) . " ---\n";
        echo "商品标题: " . $goods['title'] . "\n";
        echo "商品ID: " . $goods['goodsId'] . "\n";
        echo "原价: ¥" . $goods['originalPrice'] . "\n";
        echo "券后价: ¥" . $goods['actualPrice'] . "\n";
        echo "优惠券面额: ¥" . $goods['couponPrice'] . "\n";
        echo "优惠券ID: " . ($goods['couponId'] ?? '无') . "\n";
        echo "优惠券链接: " . ($goods['couponLink'] ?? '无') . "\n";
        
        // 测试生成淘口令
        echo "\n尝试生成淘口令...\n";
        $tklResult = testCreateTkl($appKey, $appSecret, $goods);
        if ($tklResult['success']) {
            echo "✅ 淘口令生成成功: " . $tklResult['data'] . "\n";
        } else {
            echo "❌ 淘口令生成失败: " . $tklResult['message'] . "\n";
        }
        
        echo "\n" . str_repeat("-", 50) . "\n";
    }
} else {
    echo "❌ 获取商品失败: " . $goodsList['message'] . "\n";
}

/**
 * 测试生成淘口令
 */
function testCreateTkl($appKey, $appSecret, $goods) {
    try {
        $client = new GetCreateTkl();
        $client->setAppKey($appKey);
        $client->setAppSecret($appSecret);
        $client->setVersion('v1.2.4');
        
        $params = [
            'text' => $goods['title'],
            'url' => $goods['itemLink'] ?? $goods['couponLink']
        ];
        
        $result = $client->setParams($params)->request();
        $data = json_decode($result, true);
        
        if ($data && isset($data['code']) && $data['code'] == 0) {
            return [
                'success' => true,
                'data' => $data['data']['model'] ?? $data['data'],
                'message' => 'success'
            ];
        } else {
            return [
                'success' => false,
                'data' => null,
                'message' => $data['msg'] ?? '生成淘口令失败'
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

echo "\n=== 测试其他相关API ===\n";

// 2. 测试优惠券信息获取
echo "2. 测试获取优惠券信息\n";
if ($goodsList['success'] && !empty($goodsList['data']['list'])) {
    $firstGoods = $goodsList['data']['list'][0];
    $couponId = $firstGoods['couponId'] ?? '';
    
    if (!empty($couponId)) {
        echo "测试优惠券ID: {$couponId}\n";
        $couponResult = testGetCouponInfo($appKey, $appSecret, $couponId);
        if ($couponResult['success']) {
            echo "✅ 优惠券信息获取成功\n";
            echo "优惠券详情: " . json_encode($couponResult['data'], JSON_UNESCAPED_UNICODE) . "\n";
        } else {
            echo "❌ 优惠券信息获取失败: " . $couponResult['message'] . "\n";
        }
    } else {
        echo "❌ 没有找到优惠券ID\n";
    }
}

/**
 * 测试获取优惠券信息
 */
function testGetCouponInfo($appKey, $appSecret, $couponId) {
    try {
        $client = new GetCouponInfo();
        $client->setAppKey($appKey);
        $client->setAppSecret($appSecret);
        $client->setVersion('v1.2.4');

        $params = [
            'content' => $couponId  // 使用content参数而不是couponId
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
                'message' => $data['msg'] ?? '获取优惠券信息失败'
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

// 3. 测试转链功能（不需要特殊权限的版本）
echo "\n3. 测试商品转链\n";
if ($goodsList['success'] && !empty($goodsList['data']['list'])) {
    $firstGoods = $goodsList['data']['list'][0];
    $goodsId = $firstGoods['goodsId'];
    
    echo "测试商品ID: {$goodsId}\n";
    $convertResult = testConvertLink($appKey, $appSecret, $goodsId);
    if ($convertResult['success']) {
        echo "✅ 商品转链成功\n";
        echo "转链结果: " . json_encode($convertResult['data'], JSON_UNESCAPED_UNICODE) . "\n";
    } else {
        echo "❌ 商品转链失败: " . $convertResult['message'] . "\n";
    }
}

/**
 * 测试商品转链
 */
function testConvertLink($appKey, $appSecret, $goodsId) {
    try {
        $client = new GetTurnLink();
        $client->setAppKey($appKey);
        $client->setAppSecret($appSecret);
        $client->setVersion('v1.2.4');

        $params = [
            'title' => '测试商品转链',  // 添加必需的title参数
            'goodsId' => $goodsId
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
                'message' => $data['msg'] ?? '商品转链失败'
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
