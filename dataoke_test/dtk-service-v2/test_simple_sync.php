<?php
/**
 * 简化版智能同步测试
 */

// 设置错误报告
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

// 设置执行时间限制
set_time_limit(120); // 2分钟

// 引入自动加载
require_once 'vendor/autoload.php';
require_once 'config/config.php';

use Services\GoodsSyncService;
use Utils\Logger;

header('Content-Type: application/json; charset=utf-8');

$logger = new Logger();

try {
    $logger->info('开始简化版智能同步测试');
    
    // 检查必要的类是否存在
    if (!class_exists('Services\GoodsSyncService')) {
        throw new Exception('GoodsSyncService 类不存在');
    }
    
    if (!class_exists('Services\SmartLinkService')) {
        throw new Exception('SmartLinkService 类不存在');
    }
    
    $logger->info('所有必要的类都存在');
    
    // 创建同步服务
    $syncService = new GoodsSyncService('taobao');
    $logger->info('GoodsSyncService 创建成功');
    
    // 测试获取统计信息
    $stats = $syncService->getSmartLinkStats();
    $logger->info('获取智能转链统计成功', ['stats_count' => count($stats)]);
    
    // 测试简化的同步（只同步1页，50条商品）
    $logger->info('开始执行简化同步（1页，50条商品）');
    
    $startTime = microtime(true);
    
    // 手动调用DataokeService获取商品
    $dataokeService = new \Services\DataokeService('taobao');
    $result = $dataokeService->getGoodsList([
        'pageId' => 1,
        'pageSize' => 50
    ]);
    
    $duration = round((microtime(true) - $startTime) * 1000, 2);
    $logger->info('获取商品数据完成', [
        'goods_count' => count($result['list'] ?? []),
        'duration' => $duration . 'ms'
    ]);
    
    if (empty($result['list'])) {
        throw new Exception('未获取到商品数据');
    }
    
    // 保存前5个商品进行测试
    $testGoods = array_slice($result['list'], 0, 5);
    $saved = 0;
    $errors = 0;
    
    foreach ($testGoods as $goods) {
        try {
            // 转换数据格式
            $data = $syncService->convertGoodsDataWithTier($goods);
            
            // 检查商品是否已存在
            $db = \Services\DatabaseService::getInstance();
            $existing = $db->fetchOne(
                "SELECT id FROM dtk_goods WHERE goods_id = ? AND platform = ?",
                [$data['goods_id'], 'taobao']
            );
            
            if ($existing) {
                $logger->info('商品已存在，跳过', ['goods_id' => $data['goods_id']]);
            } else {
                // 插入新商品（简化版，不执行转链）
                $insertData = [
                    'goods_id' => $data['goods_id'],
                    'title' => $data['title'],
                    'platform' => 'taobao',
                    'month_sales' => $data['month_sales'],
                    'tier_level' => $data['tier_level'],
                    'create_time' => date('Y-m-d H:i:s'),
                    'update_time' => date('Y-m-d H:i:s')
                ];
                
                $sql = "INSERT INTO dtk_goods (" . implode(', ', array_keys($insertData)) . ") VALUES (" . 
                       str_repeat('?,', count($insertData) - 1) . "?)";
                
                $db->execute($sql, array_values($insertData));
                $saved++;
                $logger->info('商品保存成功', ['goods_id' => $data['goods_id'], 'tier' => $data['tier_level']]);
            }
            
        } catch (Exception $e) {
            $errors++;
            $logger->error('保存商品失败', [
                'goods_id' => $goods['goodsId'] ?? 'unknown',
                'error' => $e->getMessage()
            ]);
        }
    }
    
    $totalDuration = round((microtime(true) - $startTime) * 1000, 2);
    
    // 返回成功结果
    $response = [
        'code' => 200,
        'message' => '简化版智能同步测试成功',
        'data' => [
            'success' => true,
            'totalSynced' => $saved,
            'totalErrors' => $errors,
            'tier1Converted' => 0, // 简化版不执行转链
            'duration' => $totalDuration . 'ms',
            'testMode' => true,
            'stats' => $stats
        ],
        'timestamp' => time()
    ];
    
    echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    $logger->error('简化版智能同步测试失败', [
        'error' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ]);
    
    $response = [
        'code' => 500,
        'message' => '简化版智能同步测试失败: ' . $e->getMessage(),
        'data' => null,
        'timestamp' => time()
    ];
    
    echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
}
