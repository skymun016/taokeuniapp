<?php
/**
 * 小程序端统计信息API接口 - 简化版本
 */

// 引入CORS配置
require_once __DIR__ . '/cors.php';

// 引入配置文件
require_once dirname(__DIR__) . '/vendor/autoload.php';

use Utils\Logger;
use Services\DatabaseService;

$logger = new Logger();

try {
    // 获取请求方法和参数
    $method = $_SERVER['REQUEST_METHOD'];
    $params = array_merge($_GET, $_POST);
    
    $logger->info('小程序统计API请求', [
        'method' => $method,
        'params' => $params
    ]);
    
    // 只支持GET请求
    if ($method !== 'GET') {
        errorResponse('只支持GET请求', 405);
    }
    
    // 获取平台参数
    $platform = $params['platform'] ?? 'taobao';
    
    // 获取数据库连接
    $db = DatabaseService::getInstance()->getConnection();
    
    // 获取商品统计
    $goodsSql = "
        SELECT 
            COUNT(*) as total,
            COUNT(CASE WHEN tier_level = 1 THEN 1 END) as tier1_count,
            COUNT(CASE WHEN tier_level = 2 THEN 1 END) as tier2_count,
            COUNT(CASE WHEN tier_level = 3 THEN 1 END) as tier3_count,
            COUNT(CASE WHEN link_status = 1 THEN 1 END) as converted_count,
            COUNT(CASE WHEN coupon_price > 0 THEN 1 END) as coupon_count,
            AVG(price) as avg_price,
            AVG(month_sales) as avg_sales,
            MAX(update_time) as last_update
        FROM dtk_goods 
        WHERE platform = ?
    ";
    
    $stmt = $db->prepare($goodsSql);
    $stmt->execute([$platform]);
    $goodsResult = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $goodsStats = [
        'total' => intval($goodsResult['total']),
        'tier1' => intval($goodsResult['tier1_count']),
        'tier2' => intval($goodsResult['tier2_count']),
        'tier3' => intval($goodsResult['tier3_count']),
        'converted' => intval($goodsResult['converted_count']),
        'with_coupon' => intval($goodsResult['coupon_count']),
        'avg_price' => round(floatval($goodsResult['avg_price']), 2),
        'avg_sales' => round(floatval($goodsResult['avg_sales']), 0),
        'last_update' => $goodsResult['last_update']
    ];
    
    // 获取今日数据
    $todaySql = "
        SELECT 
            COUNT(*) as new_goods,
            COUNT(CASE WHEN tier_level = 1 THEN 1 END) as new_tier1,
            COUNT(CASE WHEN link_status = 1 THEN 1 END) as new_converted,
            AVG(price) as avg_price
        FROM dtk_goods 
        WHERE platform = ? 
        AND DATE(create_time) = CURDATE()
    ";
    
    $stmt = $db->prepare($todaySql);
    $stmt->execute([$platform]);
    $todayResult = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $todayStats = [
        'new_goods' => intval($todayResult['new_goods']),
        'new_tier1' => intval($todayResult['new_tier1']),
        'new_converted' => intval($todayResult['new_converted']),
        'avg_price' => round(floatval($todayResult['avg_price']), 2)
    ];
    
    // 返回统计结果
    successResponse([
        'platform' => $platform,
        'goods' => $goodsStats,
        'today' => $todayStats,
        'update_time' => date('Y-m-d H:i:s')
    ], '获取成功');
    
} catch (Exception $e) {
    $logger->error('小程序统计API错误', [
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ]);
    
    errorResponse('服务器内部错误: ' . $e->getMessage(), 500);
}
