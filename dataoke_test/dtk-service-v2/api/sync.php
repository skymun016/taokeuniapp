<?php
/**
 * 商品同步API接口
 */

// 引入CORS配置
require_once __DIR__ . '/cors.php';

// 引入配置文件
require_once dirname(__DIR__) . '/vendor/autoload.php';

use Utils\Logger;
use Utils\Helper;
use Services\GoodsSyncService;

$logger = new Logger();

try {
    // 获取请求路径和方法
    $path = $_SERVER['REQUEST_URI'];
    $method = $_SERVER['REQUEST_METHOD'];
    
    $logger->info('同步API请求', [
        'path' => $path,
        'method' => $method,
        'params' => $_REQUEST
    ]);
    
    // 路由分发
    if ((strpos($path, '/sync.php') !== false && isset($_GET['type']) && $_GET['type'] === 'hourly') ||
        strpos($path, '/api/sync/hourly') === 0 || strpos($path, '/sync/hourly') === 0) {
        handleHourlySmartSync();
    } elseif ((strpos($path, '/sync.php') !== false && isset($_GET['type']) && $_GET['type'] === 'full') ||
        strpos($path, '/api/sync/full') === 0 || strpos($path, '/sync/full') === 0) {
        handleFullSync();
    } elseif ((strpos($path, '/sync.php') !== false && isset($_GET['type']) && $_GET['type'] === 'incremental') ||
              strpos($path, '/api/sync/incremental') === 0 || strpos($path, '/sync/incremental') === 0) {
        handleIncrementalSync();
    } elseif ((strpos($path, '/sync.php') !== false && isset($_GET['type']) && $_GET['type'] === 'preconvert') ||
              strpos($path, '/api/sync/preconvert') === 0 || strpos($path, '/sync/preconvert') === 0) {
        handlePreConvertHot();
    } elseif ((strpos($path, '/sync.php') !== false && isset($_GET['type']) && $_GET['type'] === 'convert_normal') ||
              strpos($path, '/api/sync/convert_normal') === 0 || strpos($path, '/sync/convert_normal') === 0) {
        handleConvertNormal();
    } elseif ((strpos($path, '/sync.php') !== false && isset($_GET['type']) && $_GET['type'] === 'update_tiers') ||
              strpos($path, '/api/sync/update_tiers') === 0 || strpos($path, '/sync/update_tiers') === 0) {
        handleUpdateTiers();
    } elseif ((strpos($path, '/sync.php') !== false && isset($_GET['type']) && $_GET['type'] === 'stats') ||
              strpos($path, '/api/sync/stats') === 0 || strpos($path, '/sync/stats') === 0) {
        handleSmartLinkStats();
    } elseif ((strpos($path, '/sync.php') !== false && isset($_GET['type']) && $_GET['type'] === 'clean') ||
              strpos($path, '/api/sync/clean') === 0 || strpos($path, '/sync/clean') === 0) {
        handleCleanExpired();
    } elseif (strpos($path, '/api/sync') === 0 || strpos($path, '/sync.php') !== false) {
        handleSyncStatus();
    } else {
        apiResponse(null, '接口不存在', 404);
    }
    
} catch (\Exception $e) {
    $logger->error('同步API异常', [
        'error' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ]);
    
    apiResponse(null, '系统内部错误: ' . $e->getMessage(), 500);
}

/**
 * 处理全量同步
 */
function handleFullSync()
{
    global $logger;
    
    if ($_SERVER['REQUEST_METHOD'] !== 'GET' && $_SERVER['REQUEST_METHOD'] !== 'POST') {
        apiResponse(null, '只支持GET和POST请求', 405);
        return;
    }
    
    try {
        // 获取参数
        $platform = $_REQUEST['platform'] ?? 'taobao';
        $maxPages = intval($_REQUEST['maxPages'] ?? 5); // 默认同步5页，避免首次同步时间过长
        
        // 验证平台
        $enabledPlatforms = Helper::getEnabledPlatforms();
        if (!in_array($platform, $enabledPlatforms)) {
            apiResponse(null, "平台 {$platform} 未启用或不存在", 400);
            return;
        }
        
        $logger->info('开始全量同步', ['platform' => $platform, 'maxPages' => $maxPages]);
        
        // 执行全量同步
        $syncService = new GoodsSyncService($platform);
        $result = $syncService->fullSync($maxPages);
        
        if ($result['success']) {
            apiResponse($result, '全量同步完成', 200);
        } else {
            apiResponse($result, '全量同步失败', 500);
        }
        
    } catch (\Exception $e) {
        $logger->error('全量同步失败', [
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ]);
        
        apiResponse(null, '全量同步失败: ' . $e->getMessage(), 500);
    }
}

/**
 * 处理增量同步
 */
function handleIncrementalSync()
{
    global $logger;
    
    if ($_SERVER['REQUEST_METHOD'] !== 'GET' && $_SERVER['REQUEST_METHOD'] !== 'POST') {
        apiResponse(null, '只支持GET和POST请求', 405);
        return;
    }
    
    try {
        // 获取参数
        $platform = $_REQUEST['platform'] ?? 'taobao';
        $startTime = $_REQUEST['startTime'] ?? null;
        $endTime = $_REQUEST['endTime'] ?? null;
        
        // 验证平台
        $enabledPlatforms = Helper::getEnabledPlatforms();
        if (!in_array($platform, $enabledPlatforms)) {
            apiResponse(null, "平台 {$platform} 未启用或不存在", 400);
            return;
        }
        
        $logger->info('开始增量同步', [
            'platform' => $platform,
            'startTime' => $startTime,
            'endTime' => $endTime
        ]);
        
        // 执行增量同步
        $syncService = new GoodsSyncService($platform);
        $result = $syncService->incrementalSync($startTime, $endTime);
        
        if ($result['success']) {
            apiResponse($result, '增量同步完成', 200);
        } else {
            apiResponse($result, '增量同步失败', 500);
        }
        
    } catch (\Exception $e) {
        $logger->error('增量同步失败', [
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ]);
        
        apiResponse(null, '增量同步失败: ' . $e->getMessage(), 500);
    }
}

/**
 * 处理清理过期商品
 */
function handleCleanExpired()
{
    global $logger;
    
    if ($_SERVER['REQUEST_METHOD'] !== 'GET' && $_SERVER['REQUEST_METHOD'] !== 'POST') {
        apiResponse(null, '只支持GET和POST请求', 405);
        return;
    }
    
    try {
        // 获取参数
        $platform = $_REQUEST['platform'] ?? 'taobao';
        $days = intval($_REQUEST['days'] ?? 30);
        
        // 验证平台
        $enabledPlatforms = Helper::getEnabledPlatforms();
        if (!in_array($platform, $enabledPlatforms)) {
            apiResponse(null, "平台 {$platform} 未启用或不存在", 400);
            return;
        }
        
        $logger->info('开始清理过期商品', ['platform' => $platform, 'days' => $days]);
        
        // 执行清理
        $syncService = new GoodsSyncService($platform);
        $deletedCount = $syncService->cleanExpiredGoods($days);
        
        apiResponse([
            'deletedCount' => $deletedCount,
            'platform' => $platform,
            'days' => $days
        ], '清理过期商品完成', 200);
        
    } catch (\Exception $e) {
        $logger->error('清理过期商品失败', [
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ]);
        
        apiResponse(null, '清理过期商品失败: ' . $e->getMessage(), 500);
    }
}

/**
 * 处理同步状态查询
 */
function handleSyncStatus()
{
    global $logger;
    
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        apiResponse(null, '只支持GET请求', 405);
        return;
    }
    
    try {
        $platform = $_REQUEST['platform'] ?? 'taobao';
        
        // 获取同步统计信息
        $db = \Services\DatabaseService::getInstance();
        
        // 商品总数
        $goodsCount = $db->fetchOne("SELECT COUNT(*) as count FROM dtk_goods WHERE platform = ?", [$platform])['count'] ?? 0;
        
        // 最近同步记录
        $lastSync = $db->fetchOne(
            "SELECT * FROM dtk_sync_logs WHERE platform = ? ORDER BY create_time DESC LIMIT 1",
            [$platform]
        );
        
        // 今日同步统计
        $todayStats = $db->fetchOne(
            "SELECT 
                COUNT(*) as sync_count,
                SUM(success_count) as total_success,
                SUM(error_count) as total_errors
             FROM dtk_sync_logs 
             WHERE platform = ? AND DATE(create_time) = CURDATE()",
            [$platform]
        );
        
        apiResponse([
            'platform' => $platform,
            'goodsCount' => intval($goodsCount),
            'lastSync' => $lastSync,
            'todayStats' => [
                'syncCount' => intval($todayStats['sync_count'] ?? 0),
                'totalSuccess' => intval($todayStats['total_success'] ?? 0),
                'totalErrors' => intval($todayStats['total_errors'] ?? 0)
            ]
        ], '获取同步状态成功', 200);
        
    } catch (\Exception $e) {
        $logger->error('获取同步状态失败', [
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ]);
        
        apiResponse(null, '获取同步状态失败: ' . $e->getMessage(), 500);
    }
}

/**
 * 处理每小时智能同步
 */
function handleHourlySmartSync()
{
    global $logger;

    if ($_SERVER['REQUEST_METHOD'] !== 'GET' && $_SERVER['REQUEST_METHOD'] !== 'POST') {
        apiResponse(null, '只支持GET和POST请求', 405);
        return;
    }

    // 设置更长的执行时间限制
    set_time_limit(120); // 2分钟
    ini_set('memory_limit', '256M');

    try {
        $platform = $_REQUEST['platform'] ?? 'taobao';

        $logger->info('开始每小时智能同步', ['platform' => $platform]);

        $syncService = new GoodsSyncService($platform);
        $result = $syncService->hourlySmartSync();

        if ($result['success']) {
            apiResponse($result, '每小时智能同步成功', 200);
        } else {
            apiResponse($result, '每小时智能同步失败', 500);
        }

    } catch (\Exception $e) {
        $logger->error('每小时智能同步失败', [
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ]);

        apiResponse(null, '每小时智能同步失败: ' . $e->getMessage(), 500);
    }
}

/**
 * 处理热门商品预转链
 */
function handlePreConvertHot()
{
    global $logger;

    try {
        $platform = $_REQUEST['platform'] ?? 'taobao';
        $limit = intval($_REQUEST['limit'] ?? 100);

        $logger->info('开始热门商品预转链', ['platform' => $platform, 'limit' => $limit]);

        $syncService = new GoodsSyncService($platform);
        $result = $syncService->batchPreConvertHotGoods($limit);

        apiResponse($result, '热门商品预转链完成', 200);

    } catch (\Exception $e) {
        $logger->error('热门商品预转链失败', [
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ]);

        apiResponse(null, '热门商品预转链失败: ' . $e->getMessage(), 500);
    }
}

/**
 * 处理普通商品批量转链
 */
function handleConvertNormal()
{
    global $logger;

    try {
        $platform = $_REQUEST['platform'] ?? 'taobao';
        $limit = intval($_REQUEST['limit'] ?? 200);

        $logger->info('开始普通商品批量转链', ['platform' => $platform, 'limit' => $limit]);

        $syncService = new GoodsSyncService($platform);
        $result = $syncService->batchConvertNormalGoods($limit);

        apiResponse($result, '普通商品批量转链完成', 200);

    } catch (\Exception $e) {
        $logger->error('普通商品批量转链失败', [
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ]);

        apiResponse(null, '普通商品批量转链失败: ' . $e->getMessage(), 500);
    }
}

/**
 * 处理商品层级更新
 */
function handleUpdateTiers()
{
    global $logger;

    try {
        $platform = $_REQUEST['platform'] ?? 'taobao';

        $logger->info('开始更新商品层级', ['platform' => $platform]);

        $syncService = new GoodsSyncService($platform);
        $result = $syncService->updateGoodsTiers();

        apiResponse($result, '商品层级更新完成', 200);

    } catch (\Exception $e) {
        $logger->error('商品层级更新失败', [
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ]);

        apiResponse(null, '商品层级更新失败: ' . $e->getMessage(), 500);
    }
}

/**
 * 处理智能转链统计
 */
function handleSmartLinkStats()
{
    global $logger;

    try {
        $platform = $_REQUEST['platform'] ?? 'taobao';

        $logger->info('获取智能转链统计', ['platform' => $platform]);

        $syncService = new GoodsSyncService($platform);
        $result = $syncService->getSmartLinkStats();

        apiResponse($result, '获取智能转链统计成功', 200);

    } catch (\Exception $e) {
        $logger->error('获取智能转链统计失败', [
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ]);

        apiResponse(null, '获取智能转链统计失败: ' . $e->getMessage(), 500);
    }
}
?>
