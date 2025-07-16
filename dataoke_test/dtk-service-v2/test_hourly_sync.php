<?php
/**
 * 每小时智能同步测试脚本
 */

require_once 'vendor/autoload.php';
require_once 'config/config.php';

use Services\GoodsSyncService;
use Utils\Logger;

$logger = new Logger();

echo "=== 每小时智能同步测试 ===\n";

try {
    $platform = 'taobao';
    $syncService = new GoodsSyncService($platform);
    
    echo "开始执行每小时智能同步...\n";
    $startTime = microtime(true);
    
    // 执行每小时智能同步（限制为1页，避免超时）
    $result = $syncService->hourlySmartSync();
    
    $duration = round((microtime(true) - $startTime) * 1000, 2);
    
    if ($result['success']) {
        echo "✅ 同步成功！\n";
        echo "   - 同步商品: {$result['totalSynced']}\n";
        echo "   - 错误数量: {$result['totalErrors']}\n";
        echo "   - Tier1转链: {$result['tier1Converted']}\n";
        echo "   - 执行时间: {$duration}ms\n";
        echo "   - 总耗时: {$result['duration']}\n";
    } else {
        echo "❌ 同步失败: {$result['error']}\n";
    }
    
    // 获取最新统计
    echo "\n=== 转链统计 ===\n";
    $stats = $syncService->getSmartLinkStats();
    foreach ($stats as $stat) {
        $convertRate = $stat['total'] > 0 ? round(($stat['converted'] / $stat['total']) * 100, 1) : 0;
        echo "Tier {$stat['tier_level']}: {$stat['converted']}/{$stat['total']} ({$convertRate}%) 已转链\n";
    }
    
} catch (\Exception $e) {
    echo "❌ 测试失败: {$e->getMessage()}\n";
    echo "文件: {$e->getFile()}\n";
    echo "行号: {$e->getLine()}\n";
}

echo "\n=== 测试完成 ===\n";
