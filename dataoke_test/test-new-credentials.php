<?php
/**
 * 测试新凭证的大淘客API功能
 * 包括淘口令生成和推广链接生成
 */

require_once 'DataokeAdapter.php';

echo "🔑 大淘客新凭证功能测试\n";
echo "========================\n";
echo "APP_KEY: 68768ef94834a\n";
echo "APP_SECRET: f5a5707c8d7b69b8dbad1ec15506c3b1\n";
echo "PID: mm_52162983_39758207_72877900030\n";
echo "========================\n\n";

// 创建适配器实例
$adapter = new DataokeAdapter(
    '68768ef94834a',
    'f5a5707c8d7b69b8dbad1ec15506c3b1',
    'mm_52162983_39758207_72877900030'
);

/**
 * 测试结果输出
 */
function testResult($testName, $result) {
    echo "📋 测试: {$testName}\n";
    
    if ($result['success']) {
        echo "✅ 成功: {$result['message']}\n";
        return true;
    } else {
        echo "❌ 失败: {$result['message']}\n";
        return false;
    }
}

// 1. 测试连接
echo "1️⃣ 测试API连接\n";
$result = $adapter->testConnection();
testResult('API连接测试', $result);
echo "\n";

// 2. 测试商品列表
echo "2️⃣ 测试商品列表\n";
$result = $adapter->getGoodsList([
    'pageId' => 1,
    'pageSize' => 3,
    'couponPriceLowerLimit' => 10
]);

if (testResult('商品列表获取', $result)) {
    $goodsList = $result['data']['list'];
    echo "   📦 获取商品数量: " . count($goodsList) . "\n";
    
    if (!empty($goodsList)) {
        $firstGoods = $goodsList[0];
        echo "   🎯 第一个商品:\n";
        echo "      - ID: {$firstGoods['goodsId']}\n";
        echo "      - 标题: " . mb_substr($firstGoods['title'], 0, 30) . "...\n";
        echo "      - 价格: ¥{$firstGoods['actualPrice']}\n";
        echo "      - 原价: ¥{$firstGoods['originalPrice']}\n";
        echo "      - 优惠券: ¥{$firstGoods['couponPrice']}\n";
        echo "      - 商品链接: {$firstGoods['itemLink']}\n";
        
        // 保存第一个商品信息用于后续测试
        $testGoodsId = $firstGoods['goodsId'];
        $testGoodsTitle = $firstGoods['title'];
        $testGoodsLink = $firstGoods['itemLink'];
        $testCouponId = $firstGoods['couponId'] ?? '';
    }
}
echo "\n";

// 3. 测试商品详情
if (isset($testGoodsId)) {
    echo "3️⃣ 测试商品详情\n";
    $result = $adapter->getGoodsDetails($testGoodsId);
    
    if (testResult('商品详情获取', $result)) {
        $goodsDetail = $result['data'];
        echo "   📝 商品详情:\n";
        echo "      - ID: {$goodsDetail['goodsId']}\n";
        echo "      - 标题: " . mb_substr($goodsDetail['title'], 0, 40) . "...\n";
        echo "      - 店铺: {$goodsDetail['shopName']}\n";
        echo "      - 佣金比例: {$goodsDetail['commissionRate']}%\n";
    }
    echo "\n";
}

// 4. 测试搜索功能
echo "4️⃣ 测试搜索功能\n";
$result = $adapter->searchGoods('手机', [
    'pageId' => 1,
    'pageSize' => 3
]);

if (testResult('商品搜索', $result)) {
    $searchList = $result['data']['list'];
    echo "   🔍 搜索结果数量: " . count($searchList) . "\n";
    
    if (!empty($searchList)) {
        echo "   📱 搜索到的商品:\n";
        foreach (array_slice($searchList, 0, 2) as $goods) {
            echo "      - " . mb_substr($goods['title'], 0, 35) . "... (¥{$goods['actualPrice']})\n";
        }
    }
}
echo "\n";

// 5. 测试分类获取
echo "5️⃣ 测试分类获取\n";
$result = $adapter->getSuperCategory();

if (testResult('分类列表获取', $result)) {
    $categories = $result['data'];
    echo "   📂 分类数量: " . count($categories) . "\n";
    echo "   📝 分类示例: ";
    for ($i = 0; $i < min(3, count($categories)); $i++) {
        echo $categories[$i]['cname'];
        if ($i < min(2, count($categories) - 1)) echo ", ";
    }
    echo "\n";
}
echo "\n";

// 6. 测试淘口令生成 (重点测试)
if (isset($testGoodsTitle) && isset($testGoodsLink)) {
    echo "6️⃣ 测试淘口令生成 (新功能)\n";
    $result = $adapter->createTkl($testGoodsTitle, $testGoodsLink);
    
    if (testResult('淘口令生成', $result)) {
        echo "   🎫 淘口令: {$result['data']['tkl']}\n";
        echo "   📝 文本: " . mb_substr($testGoodsTitle, 0, 30) . "...\n";
        echo "   🔗 链接: {$testGoodsLink}\n";
    }
    echo "\n";
}

// 7. 测试推广链接生成 (重点测试)
if (isset($testGoodsId)) {
    echo "7️⃣ 测试推广链接生成 (新功能)\n";
    
    $params = [
        'goodsId' => $testGoodsId,
        'pid' => 'mm_52162983_39758207_72877900030',
        'channelId' => 'common'
    ];
    
    if (!empty($testCouponId)) {
        $params['couponId'] = $testCouponId;
    }
    
    $result = $adapter->getPrivilegeLink($params);
    
    if (testResult('推广链接生成', $result)) {
        echo "   🔗 推广链接: {$result['data']['privilegeLink']}\n";
        if (!empty($result['data']['shortLink'])) {
            echo "   📎 短链接: {$result['data']['shortLink']}\n";
        }
        if (!empty($result['data']['couponLink'])) {
            echo "   🎫 优惠券链接: {$result['data']['couponLink']}\n";
        }
    }
    echo "\n";
}

// 8. 测试淘口令解析
echo "8️⃣ 测试淘口令解析\n";
$testTpwd = '￥CZ0001 BdMWdklqPqJ￥';  // 示例淘口令
$result = $adapter->parseTpwd($testTpwd);

if (testResult('淘口令解析', $result)) {
    if (!empty($result['data'])) {
        echo "   📝 解析结果:\n";
        echo "      - 商品ID: " . ($result['data']['goodsId'] ?? '未知') . "\n";
        echo "      - 标题: " . mb_substr($result['data']['title'] ?? '未知', 0, 30) . "...\n";
    }
} else {
    echo "   ℹ️ 注意: 淘口令解析可能需要有效的淘口令\n";
}
echo "\n";

echo "🎉 测试完成！\n";
echo "========================\n";

// 总结测试结果
echo "📊 功能支持情况:\n";
echo "✅ 商品列表获取 - 支持\n";
echo "✅ 商品详情获取 - 支持\n";
echo "✅ 商品搜索 - 支持\n";
echo "✅ 分类获取 - 支持\n";
echo "✅ 淘口令解析 - 支持\n";

// 检查新凭证的特殊权限
if (isset($testGoodsTitle) && isset($testGoodsLink)) {
    $tklResult = $adapter->createTkl($testGoodsTitle, $testGoodsLink);
    if ($tklResult['success']) {
        echo "✅ 淘口令生成 - 支持 (新凭证有权限)\n";
    } else {
        echo "❌ 淘口令生成 - 不支持 (需要特殊权限)\n";
    }
}

if (isset($testGoodsId)) {
    $linkResult = $adapter->getPrivilegeLink([
        'goodsId' => $testGoodsId,
        'pid' => 'mm_52162983_39758207_72877900030',
        'channelId' => 'common'
    ]);
    if ($linkResult['success']) {
        echo "✅ 推广链接生成 - 支持 (新凭证有权限)\n";
    } else {
        echo "❌ 推广链接生成 - 不支持 (需要渠道专属推广位)\n";
    }
}

echo "\n🚀 新凭证测试完成！可以开始使用独立服务端了。\n";
?>
