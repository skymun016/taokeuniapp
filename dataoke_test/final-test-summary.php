<?php
/**
 * 大淘客API功能完整测试总结
 * 验证所有核心功能是否正常工作
 */

require_once 'DataokeAdapter.php';

echo "🎯 大淘客API功能完整测试总结\n";
echo "=====================================\n";
echo "测试时间: " . date('Y-m-d H:i:s') . "\n";
echo "PID: mm_52162983_39758207_72877900030 (渠道专属推广位)\n\n";

// 创建适配器实例
$adapter = new DataokeAdapter(
    '68768ef94834a',
    'f5a5707c8d7b69b8dbad1ec15506c3b1',
    'mm_52162983_39758207_72877900030'
);

$testResults = [];

// 1. 测试商品列表获取
echo "1️⃣ 测试商品列表获取 (v1.2.4)...\n";
$goodsResult = $adapter->getGoodsList(['pageId' => 1, 'pageSize' => 5]);
if ($goodsResult['success']) {
    echo "   ✅ 成功获取 " . count($goodsResult['data']['list']) . " 个商品\n";
    $testResults['商品列表'] = '✅ 正常';
    $testGoods = $goodsResult['data']['list'][0];
} else {
    echo "   ❌ 失败: {$goodsResult['message']}\n";
    $testResults['商品列表'] = '❌ 失败';
    exit;
}

// 2. 测试推广链接生成
echo "\n2️⃣ 测试推广链接生成 (v1.3.1)...\n";
$linkResult = $adapter->getPrivilegeLink([
    'goodsId' => $testGoods['goodsId'],
    'pid' => 'mm_52162983_39758207_72877900030'
]);

if ($linkResult['success']) {
    echo "   ✅ 推广链接生成成功\n";
    echo "   📊 返回数据:\n";
    echo "      - 推广链接: " . (!empty($linkResult['data']['itemUrl']) ? '✅' : '❌') . "\n";
    echo "      - 短链接: " . (!empty($linkResult['data']['shortUrl']) ? '✅' : '❌') . "\n";
    echo "      - 淘口令: " . (!empty($linkResult['data']['tpwd']) ? '✅' : '❌') . "\n";
    echo "      - 佣金比例: {$linkResult['data']['maxCommissionRate']}%\n";
    $testResults['推广链接'] = '✅ 正常';
} else {
    echo "   ❌ 失败: {$linkResult['message']}\n";
    $testResults['推广链接'] = '❌ 失败';
}

// 3. 测试搜索功能
echo "\n3️⃣ 测试商品搜索功能...\n";
$searchResult = $adapter->searchGoods('零食', ['pageSize' => 3]);
if ($searchResult['success']) {
    echo "   ✅ 搜索成功，找到 " . count($searchResult['data']['list']) . " 个商品\n";
    $testResults['商品搜索'] = '✅ 正常';
} else {
    echo "   ❌ 搜索失败: {$searchResult['message']}\n";
    $testResults['商品搜索'] = '❌ 失败';
}

// 4. 测试分类获取
echo "\n4️⃣ 测试超级分类获取...\n";
$categoryResult = $adapter->getSuperCategory();
if ($categoryResult['success']) {
    echo "   ✅ 分类获取成功，共 " . count($categoryResult['data']) . " 个分类\n";
    $testResults['超级分类'] = '✅ 正常';
} else {
    echo "   ❌ 分类获取失败: {$categoryResult['message']}\n";
    $testResults['超级分类'] = '❌ 失败';
}

// 5. 测试淘口令生成
echo "\n5️⃣ 测试淘口令生成...\n";
if (isset($linkResult) && $linkResult['success']) {
    $tklResult = $adapter->createTkl(
        mb_substr($testGoods['title'], 0, 20),
        $linkResult['data']['itemUrl']
    );
    
    if ($tklResult['success']) {
        echo "   ✅ 淘口令生成成功\n";
        echo "   淘口令: {$tklResult['data']['tkl']}\n";
        $testResults['淘口令生成'] = '✅ 正常';
    } else {
        echo "   ❌ 淘口令生成失败: {$tklResult['message']}\n";
        $testResults['淘口令生成'] = '❌ 失败';
    }
} else {
    echo "   ⚠️  跳过（推广链接生成失败）\n";
    $testResults['淘口令生成'] = '⚠️ 跳过';
}

// 测试结果汇总
echo "\n=====================================\n";
echo "🎯 测试结果汇总:\n";
echo "=====================================\n";

foreach ($testResults as $function => $status) {
    echo "   {$function}: {$status}\n";
}

$successCount = count(array_filter($testResults, function($status) {
    return strpos($status, '✅') !== false;
}));

$totalCount = count($testResults);

echo "\n📊 总体评估:\n";
echo "   成功率: {$successCount}/{$totalCount} (" . round($successCount/$totalCount*100, 1) . "%)\n";

if ($successCount >= 4) {
    echo "   🎉 大淘客API集成状态: 优秀！\n";
    echo "   ✅ 可以正式部署到生产环境\n";
} elseif ($successCount >= 3) {
    echo "   👍 大淘客API集成状态: 良好\n";
    echo "   ⚠️  建议修复失败项目后部署\n";
} else {
    echo "   ⚠️  大淘客API集成状态: 需要改进\n";
    echo "   ❌ 建议解决问题后再部署\n";
}

echo "\n=====================================\n";
echo "🔧 API版本信息:\n";
echo "   - 商品列表API: v1.2.4\n";
echo "   - 高效转链API: v1.3.1\n";
echo "   - 搜索API: v1.2.0\n";
echo "   - 分类API: v1.2.0\n";
echo "   - 淘口令API: v1.0.0\n";
echo "=====================================\n";

echo "测试完成！\n";
?>
