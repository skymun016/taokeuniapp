<?php
/**
 * 测试渠道专属推广位的推广链接生成
 */

require_once 'DataokeAdapter.php';

echo "🔗 测试渠道专属推广位推广链接生成\n";
echo "=====================================\n";
echo "PID: mm_52162983_39758207_72877900030 (渠道专属推广位)\n";
echo "商品ID: 8XBz8KXUnt9pvgb3NJSg4wsbtj-36ON23KC0VvwWqbdCRn\n\n";

// 创建适配器实例
$adapter = new DataokeAdapter(
    '68768ef94834a',
    'f5a5707c8d7b69b8dbad1ec15506c3b1',
    'mm_52162983_39758207_72877900030'
);

// 测试推广链接生成
$params = [
    'goodsId' => '8XBz8KXUnt9pvgb3NJSg4wsbtj-36ON23KC0VvwWqbdCRn',
    'pid' => 'mm_52162983_39758207_72877900030',
    'channelId' => 'common'
];

echo "📋 测试参数:\n";
foreach ($params as $key => $value) {
    echo "   {$key}: {$value}\n";
}
echo "\n";

echo "🚀 开始测试...\n";
$result = $adapter->getPrivilegeLink($params);

if ($result['success']) {
    echo "✅ 推广链接生成成功！\n\n";
    echo "📊 返回数据:\n";
    
    if (!empty($result['data']['privilegeLink'])) {
        echo "🔗 推广链接: {$result['data']['privilegeLink']}\n";
    }
    
    if (!empty($result['data']['shortLink'])) {
        echo "📎 短链接: {$result['data']['shortLink']}\n";
    }
    
    if (!empty($result['data']['couponLink'])) {
        echo "🎫 优惠券链接: {$result['data']['couponLink']}\n";
    }
    
    if (!empty($result['data']['itemLink'])) {
        echo "🛒 商品链接: {$result['data']['itemLink']}\n";
    }
    
    echo "\n🎉 渠道专属推广位功能正常！\n";
    
} else {
    echo "❌ 推广链接生成失败\n";
    echo "错误信息: {$result['message']}\n\n";
    
    // 尝试不同的channelId
    echo "🔄 尝试使用不同的channelId...\n";
    
    $channelIds = ['common', 'default', 'taobao', ''];
    
    foreach ($channelIds as $channelId) {
        echo "   测试 channelId: '{$channelId}'\n";
        
        $testParams = [
            'goodsId' => '8XBz8KXUnt9pvgb3NJSg4wsbtj-36ON23KC0VvwWqbdCRn',
            'pid' => 'mm_52162983_39758207_72877900030'
        ];
        
        if (!empty($channelId)) {
            $testParams['channelId'] = $channelId;
        }
        
        $testResult = $adapter->getPrivilegeLink($testParams);
        
        if ($testResult['success']) {
            echo "   ✅ 成功！推广链接: " . substr($testResult['data']['privilegeLink'], 0, 50) . "...\n";
            break;
        } else {
            echo "   ❌ 失败: {$testResult['message']}\n";
        }
    }
}

echo "\n";
echo "=====================================\n";
echo "测试完成\n";
?>
