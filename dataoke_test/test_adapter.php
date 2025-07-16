<?php
/**
 * 测试大淘客适配器
 */

require_once 'DataokeAdapter.php';

// 配置信息
$appKey = '678fc81d72259';
$appSecret = '6fd2acba8bce6c039ab276256f003ced';
$pid = 'mm_52162983_2267550029_112173400498';

echo "=== 大淘客适配器测试 ===\n";

// 创建适配器实例
$adapter = new DataokeAdapter($appKey, $appSecret, $pid);

// 1. 测试连接
echo "1. 测试连接\n";
$result = $adapter->testConnection();
echo "连接测试结果: " . json_encode($result, JSON_UNESCAPED_UNICODE) . "\n\n";

// 2. 获取商品列表
echo "2. 获取商品列表\n";
$goodsList = $adapter->getGoodsList(['pageId' => 1, 'pageSize' => 5]);
if ($goodsList['success']) {
    echo "✅ 商品列表获取成功\n";
    echo "商品数量: " . count($goodsList['data']['list']) . "\n";
    echo "第一个商品标题: " . $goodsList['data']['list'][0]['title'] . "\n";
} else {
    echo "❌ 商品列表获取失败: " . $goodsList['message'] . "\n";
}
echo "\n";

// 3. 获取超级分类
echo "3. 获取超级分类\n";
$categories = $adapter->getSuperCategory();
if ($categories['success']) {
    echo "✅ 分类获取成功\n";
    echo "分类数量: " . count($categories['data']) . "\n";
    echo "第一个分类: " . $categories['data'][0]['cname'] . "\n";
} else {
    echo "❌ 分类获取失败: " . $categories['message'] . "\n";
}
echo "\n";

// 4. 搜索商品
echo "4. 搜索商品\n";
$searchResult = $adapter->searchGoods('手机', ['pageId' => 1, 'pageSize' => 3]);
if ($searchResult['success']) {
    echo "✅ 商品搜索成功\n";
    echo "搜索结果数量: " . count($searchResult['data']['list']) . "\n";
    if (!empty($searchResult['data']['list'])) {
        echo "第一个搜索结果: " . $searchResult['data']['list'][0]['title'] . "\n";
    }
} else {
    echo "❌ 商品搜索失败: " . $searchResult['message'] . "\n";
}
echo "\n";

// 5. 获取商品详情（使用商品列表中的第一个商品）
if ($goodsList['success'] && !empty($goodsList['data']['list'])) {
    echo "5. 获取商品详情\n";
    $goodsId = $goodsList['data']['list'][0]['goodsId'];
    echo "测试商品ID: {$goodsId}\n";
    
    $goodsDetail = $adapter->getGoodsDetails($goodsId);
    if ($goodsDetail['success']) {
        echo "✅ 商品详情获取成功\n";
        echo "商品标题: " . $goodsDetail['data']['title'] . "\n";
        echo "原价: " . $goodsDetail['data']['originalPrice'] . "\n";
        echo "券后价: " . $goodsDetail['data']['actualPrice'] . "\n";
    } else {
        echo "❌ 商品详情获取失败: " . $goodsDetail['message'] . "\n";
    }
    echo "\n";

    // 6. 生成推广链接
    echo "6. 生成推广链接\n";
    $couponId = $goodsList['data']['list'][0]['couponId'] ?? '';
    $privilegeLink = $adapter->getPrivilegeLink($goodsId, $couponId);
    if ($privilegeLink['success']) {
        echo "✅ 推广链接生成成功\n";
        echo "推广链接: " . $privilegeLink['data']['shortUrl'] . "\n";
        echo "优惠券链接: " . $privilegeLink['data']['couponShortUrl'] . "\n";
    } else {
        echo "❌ 推广链接生成失败: " . $privilegeLink['message'] . "\n";
    }
}

echo "\n=== 测试完成 ===\n";
