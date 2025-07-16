<?php
/**
 * 获取推广链接的详细信息
 */

require_once 'DataokeAdapter.php';

echo "🎉 推广链接生成成功测试\n";
echo "=====================================\n";

// 创建适配器实例
$adapter = new DataokeAdapter(
    '68768ef94834a',
    'f5a5707c8d7b69b8dbad1ec15506c3b1',
    'mm_52162983_39758207_72877900030'
);

// 获取商品
$goodsResult = $adapter->getGoodsList(['pageId' => 1, 'pageSize' => 1]);
$goods = $goodsResult['data']['list'][0];

echo "📦 测试商品信息:\n";
echo "   商品ID: {$goods['goodsId']}\n";
echo "   商品名称: " . mb_substr($goods['title'], 0, 40) . "...\n";
echo "   券后价: ¥{$goods['actualPrice']}\n";
echo "   优惠券: ¥{$goods['couponPrice']}\n\n";

// 生成推广链接
$params = [
    'goodsId' => $goods['goodsId'],
    'pid' => 'mm_52162983_39758207_72877900030'
];

echo "🔗 生成推广链接...\n";
$result = $adapter->getPrivilegeLink($params);

if ($result['success']) {
    echo "✅ 推广链接生成成功！\n\n";
    
    echo "📊 完整返回数据:\n";
    echo json_encode($result['data'], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    echo "\n\n";
    
    echo "🎯 关键链接信息:\n";
    foreach ($result['data'] as $key => $value) {
        if (strpos($key, 'Link') !== false || strpos($key, 'Url') !== false) {
            echo "   {$key}: {$value}\n";
        }
    }
    
} else {
    echo "❌ 推广链接生成失败: {$result['message']}\n";
}

echo "\n=====================================\n";
echo "🎊 恭喜！您的渠道专属推广位完全可用！\n";
echo "现在可以为小程序用户生成专属推广链接了！\n";
echo "=====================================\n";
?>
