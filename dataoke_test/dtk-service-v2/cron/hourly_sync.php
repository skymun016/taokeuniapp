<?php
/**
 * 每小时智能同步定时任务 - 智能混合转链策略
 *
 * 功能：
 * - 每小时同步200条商品数据
 * - Tier 1热门商品自动预转链
 * - 智能API调用频率控制
 *
 * 使用方法：
 * 1. 添加到系统 crontab：
 *    0 * * * * /usr/bin/php /path/to/dtk-service-v2/cron/hourly_sync.php
 *
 * 2. 或者通过 Web 请求触发：
 *    curl "http://your-domain.com/cron/hourly_sync.php"
 */

// 设置脚本执行时间限制
set_time_limit(0);

// 引入配置文件
require_once dirname(__DIR__) . '/vendor/autoload.php';

use Utils\Logger;
use Utils\EnvLoader;
use Services\GoodsSyncService;

// 加载环境变量
EnvLoader::load();

$logger = new Logger();

// 检查是否通过命令行运行
$isCli = php_sapi_name() === 'cli';

if (!$isCli) {
    // Web 访问时设置 JSON 响应头
    header('Content-Type: application/json; charset=utf-8');
}

$startTime = microtime(true);
$logger->info('=== 开始执行每小时智能同步任务 ===');

try {
    // 获取启用的平台列表
    $enabledPlatforms = ['taobao']; // 目前只支持淘宝平台
    
    $results = [];
    
    foreach ($enabledPlatforms as $platform) {
        $logger->info("开始同步平台: {$platform}");
        
        try {
            $syncService = new GoodsSyncService($platform);
            
            // 执行每小时智能同步（200条商品 + 分层转链）
            $result = $syncService->hourlySmartSync();
            
            $results[$platform] = $result;
            
            if ($result['success']) {
                $logger->info("平台 {$platform} 智能同步成功", [
                    'totalSynced' => $result['totalSynced'],
                    'totalErrors' => $result['totalErrors'],
                    'tier1Converted' => $result['tier1Converted'] ?? 0,
                    'duration' => $result['duration']
                ]);
            } else {
                $logger->error("平台 {$platform} 同步失败", [
                    'error' => $result['error'] ?? 'Unknown error'
                ]);
            }
            
        } catch (\Exception $e) {
            $error = "平台 {$platform} 同步异常: " . $e->getMessage();
            $logger->error($error, [
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            $results[$platform] = [
                'success' => false,
                'error' => $error
            ];
        }
        
        // 平台间间隔，避免请求过于频繁
        if (count($enabledPlatforms) > 1) {
            sleep(2);
        }
    }
    
    $totalDuration = round((microtime(true) - $startTime) * 1000, 2);
    
    // 统计总体结果
    $totalSynced = 0;
    $totalErrors = 0;
    $totalTier1Converted = 0;
    $successPlatforms = 0;
    $failedPlatforms = 0;

    foreach ($results as $platform => $result) {
        if ($result['success']) {
            $successPlatforms++;
            $totalSynced += $result['totalSynced'] ?? 0;
            $totalErrors += $result['totalErrors'] ?? 0;
            $totalTier1Converted += $result['tier1Converted'] ?? 0;
        } else {
            $failedPlatforms++;
        }
    }
    
    $summary = [
        'success' => $failedPlatforms === 0,
        'totalDuration' => $totalDuration . 'ms',
        'platforms' => [
            'total' => count($enabledPlatforms),
            'success' => $successPlatforms,
            'failed' => $failedPlatforms
        ],
        'goods' => [
            'synced' => $totalSynced,
            'errors' => $totalErrors,
            'tier1_converted' => $totalTier1Converted
        ],
        'results' => $results,
        'timestamp' => date('Y-m-d H:i:s')
    ];
    
    $logger->info('=== 每小时智能同步任务完成 ===', $summary);
    
    if ($isCli) {
        // 命令行输出
        echo "=== 每小时智能同步任务完成 ===\n";
        echo "总耗时: {$totalDuration}ms\n";
        echo "成功平台: {$successPlatforms}/" . count($enabledPlatforms) . "\n";
        echo "同步商品: {$totalSynced}\n";
        echo "错误数量: {$totalErrors}\n";
        echo "Tier1转链: {$totalTier1Converted}\n";
        
        if ($failedPlatforms > 0) {
            echo "失败平台:\n";
            foreach ($results as $platform => $result) {
                if (!$result['success']) {
                    echo "  - {$platform}: " . ($result['error'] ?? 'Unknown error') . "\n";
                }
            }
        }
    } else {
        // Web 响应
        echo json_encode([
            'code' => $failedPlatforms === 0 ? 200 : 500,
            'message' => $failedPlatforms === 0 ? '同步任务完成' : '部分平台同步失败',
            'data' => $summary
        ], JSON_UNESCAPED_UNICODE);
    }
    
} catch (\Exception $e) {
    $error = '同步任务执行异常: ' . $e->getMessage();
    $logger->error($error, [
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ]);
    
    if ($isCli) {
        echo "错误: {$error}\n";
        exit(1);
    } else {
        echo json_encode([
            'code' => 500,
            'message' => $error,
            'data' => null
        ], JSON_UNESCAPED_UNICODE);
    }
}

// 清理过期商品（每天执行一次，在每小时的第0分钟执行）
$currentHour = intval(date('H'));
$currentMinute = intval(date('i'));

if ($currentHour === 2 && $currentMinute < 10) { // 凌晨2点执行清理
    $logger->info('开始清理过期商品');
    
    try {
        foreach ($enabledPlatforms as $platform) {
            $syncService = new GoodsSyncService($platform);
            $deletedCount = $syncService->cleanExpiredGoods(30); // 清理30天前的商品
            
            $logger->info("平台 {$platform} 清理过期商品完成", [
                'deletedCount' => $deletedCount
            ]);
        }
    } catch (\Exception $e) {
        $logger->error('清理过期商品失败', [
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ]);
    }
}

if ($isCli) {
    exit(0);
}
?>
