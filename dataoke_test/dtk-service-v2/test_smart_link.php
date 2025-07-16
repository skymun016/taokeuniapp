<?php
/**
 * 智能转链功能测试脚本
 */

require_once 'vendor/autoload.php';
require_once 'config/config.php';

use Services\SmartLinkService;
use Services\GoodsSyncService;

echo "=== 智能混合转链策略测试 ===\n";

try {
    // 1. 测试数据库连接和基础查询
    echo "1. 测试数据库连接...\n";
    $db = \Services\DatabaseService::getInstance();
    $count = $db->fetchOne("SELECT COUNT(*) as count FROM dtk_goods WHERE platform = 'taobao'")['count'];
    echo "   数据库连接成功，共有 {$count} 个商品\n";
    
    // 2. 测试商品层级统计
    echo "\n2. 测试商品层级统计...\n";
    $smartLink = new SmartLinkService('taobao');
    $stats = $smartLink->getLinkStats();
    foreach ($stats as $stat) {
        echo "   Tier {$stat['tier_level']}: {$stat['total']} 个商品，平均销量 {$stat['avg_sales']}\n";
    }
    
    // 3. 测试获取热门商品列表
    echo "\n3. 测试获取热门商品列表...\n";
    $sql = "SELECT goods_id, title, month_sales, tier_level FROM dtk_goods 
            WHERE platform = 'taobao' AND tier_level = 1 
            ORDER BY month_sales DESC LIMIT 3";
    $hotGoods = $db->fetchAll($sql);
    foreach ($hotGoods as $goods) {
        echo "   商品ID: {$goods['goods_id']}, 销量: {$goods['month_sales']}, 层级: {$goods['tier_level']}\n";
    }
    
    // 4. 测试单个商品智能转链
    if (!empty($hotGoods)) {
        echo "\n4. 测试单个商品智能转链...\n";
        $testGoodsId = $hotGoods[0]['goods_id'];
        echo "   测试商品ID: {$testGoodsId}\n";
        
        try {
            $linkData = $smartLink->getSmartLinkData($testGoodsId);
            echo "   转链成功！\n";
            echo "   - 商品标题: {$linkData['title']}\n";
            echo "   - 原价: ¥{$linkData['originalPrice']}\n";
            echo "   - 实际价格: ¥{$linkData['actualPrice']}\n";
            echo "   - 佣金率: {$linkData['commissionRate']}%\n";
            echo "   - 预估佣金: ¥{$linkData['estimatedCommission']}\n";
            echo "   - 商品层级: Tier {$linkData['tierLevel']}\n";
            echo "   - 转链状态: {$linkData['linkStatus']}\n";
            
            if (!empty($linkData['privilegeLink'])) {
                echo "   - 推广链接: " . substr($linkData['privilegeLink'], 0, 50) . "...\n";
            }
            if (!empty($linkData['tpwd'])) {
                echo "   - 淘口令: {$linkData['tpwd']}\n";
            }
            
        } catch (\Exception $e) {
            echo "   转链失败: {$e->getMessage()}\n";
        }
    }
    
    // 5. 测试商品层级更新
    echo "\n5. 测试商品层级更新...\n";
    $syncService = new GoodsSyncService('taobao');
    $tierStats = $syncService->updateGoodsTiers();
    foreach ($tierStats as $stat) {
        echo "   Tier {$stat['tier_level']}: {$stat['count']} 个商品\n";
    }
    
    echo "\n=== 测试完成 ===\n";
    
} catch (\Exception $e) {
    echo "测试失败: {$e->getMessage()}\n";
    echo "文件: {$e->getFile()}\n";
    echo "行号: {$e->getLine()}\n";
}
