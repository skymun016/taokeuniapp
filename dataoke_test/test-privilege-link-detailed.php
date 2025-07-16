<?php
/**
 * 详细测试推广链接生成，包括不同的参数组合
 */

require_once 'DataokeAdapter.php';

echo "🔗 详细测试推广链接生成\n";
echo "=====================================\n";

// 创建适配器实例
$adapter = new DataokeAdapter(
    '68768ef94834a',
    'f5a5707c8d7b69b8dbad1ec15506c3b1',
    'mm_52162983_39758207_72877900030'
);

// 首先获取一些商品来测试
echo "1️⃣ 先获取商品列表...\n";
$goodsResult = $adapter->getGoodsList(['pageId' => 1, 'pageSize' => 3]);

if (!$goodsResult['success']) {
    echo "❌ 获取商品列表失败: {$goodsResult['message']}\n";
    exit;
}

$goodsList = $goodsResult['data']['list'];
echo "✅ 获取到 " . count($goodsList) . " 个商品\n\n";

// 测试每个商品的推广链接生成
foreach ($goodsList as $index => $goods) {
    echo "2️⃣ 测试商品 " . ($index + 1) . ":\n";
    echo "   商品ID: {$goods['goodsId']}\n";
    echo "   商品名称: " . mb_substr($goods['title'], 0, 30) . "...\n";
    echo "   优惠券ID: " . ($goods['couponId'] ?? '无') . "\n";
    
    // 测试不同的参数组合
    $testCases = [
        [
            'name' => '基础参数',
            'params' => [
                'goodsId' => $goods['goodsId'],
                'pid' => 'mm_52162983_39758207_72877900030'
            ]
        ],
        [
            'name' => '带channelId',
            'params' => [
                'goodsId' => $goods['goodsId'],
                'pid' => 'mm_52162983_39758207_72877900030',
                'channelId' => 'common'
            ]
        ]
    ];
    
    // 如果有优惠券ID，添加带优惠券的测试
    if (!empty($goods['couponId'])) {
        $testCases[] = [
            'name' => '带优惠券ID',
            'params' => [
                'goodsId' => $goods['goodsId'],
                'pid' => 'mm_52162983_39758207_72877900030',
                'couponId' => $goods['couponId']
            ]
        ];
    }
    
    foreach ($testCases as $testCase) {
        echo "   🧪 测试: {$testCase['name']}\n";
        
        $result = $adapter->getPrivilegeLink($testCase['params']);
        
        if ($result['success']) {
            echo "   ✅ 成功！\n";
            if (!empty($result['data']['privilegeLink'])) {
                echo "      推广链接: " . substr($result['data']['privilegeLink'], 0, 60) . "...\n";
            }
            if (!empty($result['data']['shortLink'])) {
                echo "      短链接: " . substr($result['data']['shortLink'], 0, 60) . "...\n";
            }
            if (!empty($result['data']['couponLink'])) {
                echo "      优惠券链接: " . substr($result['data']['couponLink'], 0, 60) . "...\n";
            }
            break; // 成功了就不用测试其他参数了
        } else {
            echo "   ❌ 失败: {$result['message']}\n";
        }
    }
    
    echo "\n";
    
    // 只测试前2个商品，避免过多请求
    if ($index >= 1) {
        break;
    }
}

// 测试淘口令生成
echo "3️⃣ 测试淘口令生成:\n";
$firstGoods = $goodsList[0];
$tklResult = $adapter->createTkl(
    mb_substr($firstGoods['title'], 0, 20),
    $firstGoods['itemLink']
);

if ($tklResult['success']) {
    echo "✅ 淘口令生成成功！\n";
    echo "   淘口令: {$tklResult['data']['tkl']}\n";
} else {
    echo "❌ 淘口令生成失败: {$tklResult['message']}\n";
}

echo "\n=====================================\n";
echo "🎯 测试总结:\n";
echo "- 商品数据获取: ✅ 正常\n";
echo "- 推广链接生成: " . (isset($result) && $result['success'] ? "✅ 正常" : "❌ 需要检查") . "\n";
echo "- 淘口令生成: " . ($tklResult['success'] ? "✅ 正常" : "❌ 需要特殊权限") . "\n";
echo "=====================================\n";
?>
