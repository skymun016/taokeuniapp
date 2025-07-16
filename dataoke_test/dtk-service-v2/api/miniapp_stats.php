<?php
/**
 * 小程序端统计信息API接口
 */

// 引入CORS配置
require_once __DIR__ . '/cors.php';

// 引入配置文件
require_once dirname(__DIR__) . '/vendor/autoload.php';

use Utils\Logger;
use Utils\Helper;
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
    
    // 处理统计信息请求
    handleStats($params);
    
} catch (Exception $e) {
    $logger->error('小程序统计API错误', [
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ]);
    
    errorResponse('服务器内部错误: ' . $e->getMessage(), 500);
}

/**
 * 处理统计信息请求
 */
function handleStats($params) {
    global $logger;
    
    try {
        // 获取数据库连接
        $db = DatabaseService::getInstance()->getConnection();
        
        $platform = $params['platform'] ?? 'taobao';
        
        // 获取商品统计
        $goodsStats = getGoodsStats($db, $platform);
        
        // 获取转链统计
        $convertStats = getConvertStats($db, $platform);
        
        // 获取搜索统计
        $searchStats = getSearchStats($db);
        
        // 获取热门分类
        $hotCategories = getHotCategories($db, $platform);
        
        // 获取今日数据
        $todayStats = getTodayStats($db, $platform);
        
        // 返回统计结果
        successResponse([
            'platform' => $platform,
            'goods' => $goodsStats,
            'convert' => $convertStats,
            'search' => $searchStats,
            'categories' => $hotCategories,
            'today' => $todayStats,
            'update_time' => date('Y-m-d H:i:s')
        ], '获取成功');

    } catch (Exception $e) {
        $logger->error('获取统计信息失败', [
            'error' => $e->getMessage(),
            'params' => $params
        ]);

        errorResponse('获取统计信息失败: ' . $e->getMessage(), 500);
    }
}

/**
 * 获取商品统计
 */
function getGoodsStats($db, $platform) {
    $sql = "
        SELECT 
            COUNT(*) as total,
            COUNT(CASE WHEN tier_level = 1 THEN 1 END) as tier1_count,
            COUNT(CASE WHEN tier_level = 2 THEN 1 END) as tier2_count,
            COUNT(CASE WHEN tier_level = 3 THEN 1 END) as tier3_count,
            COUNT(CASE WHEN link_status = 1 THEN 1 END) as converted_count,
            COUNT(CASE WHEN coupon_amount > 0 THEN 1 END) as coupon_count,
            AVG(price) as avg_price,
            AVG(month_sales) as avg_sales,
            MAX(update_time) as last_update
        FROM dtk_goods 
        WHERE platform = ?
    ";
    
    $stmt = $db->prepare($sql);
    $stmt->execute([$platform]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    return [
        'total' => intval($result['total']),
        'tier1' => intval($result['tier1_count']),
        'tier2' => intval($result['tier2_count']),
        'tier3' => intval($result['tier3_count']),
        'converted' => intval($result['converted_count']),
        'with_coupon' => intval($result['coupon_count']),
        'avg_price' => round(floatval($result['avg_price']), 2),
        'avg_sales' => round(floatval($result['avg_sales']), 0),
        'last_update' => $result['last_update']
    ];
}

/**
 * 获取转链统计
 */
function getConvertStats($db, $platform) {
    // 今日转链统计
    $todaySql = "
        SELECT 
            COUNT(*) as total,
            COUNT(CASE WHEN success = 1 THEN 1 END) as success_count
        FROM dtk_convert_logs 
        WHERE DATE(convert_time) = CURDATE()
    ";
    
    $stmt = $db->prepare($todaySql);
    $stmt->execute();
    $todayResult = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // 总转链统计
    $totalSql = "
        SELECT 
            COUNT(*) as total,
            COUNT(CASE WHEN success = 1 THEN 1 END) as success_count
        FROM dtk_convert_logs
    ";
    
    $stmt = $db->prepare($totalSql);
    $stmt->execute();
    $totalResult = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $todayTotal = intval($todayResult['total']);
    $todaySuccess = intval($todayResult['success_count']);
    $totalTotal = intval($totalResult['total']);
    $totalSuccess = intval($totalResult['success_count']);
    
    return [
        'today' => [
            'total' => $todayTotal,
            'success' => $todaySuccess,
            'success_rate' => $todayTotal > 0 ? round($todaySuccess / $todayTotal * 100, 1) : 0
        ],
        'total' => [
            'total' => $totalTotal,
            'success' => $totalSuccess,
            'success_rate' => $totalTotal > 0 ? round($totalSuccess / $totalTotal * 100, 1) : 0
        ]
    ];
}

/**
 * 获取搜索统计
 */
function getSearchStats($db) {
    // 今日搜索统计
    $todaySql = "
        SELECT 
            COUNT(*) as total,
            COUNT(DISTINCT keyword) as unique_keywords,
            AVG(result_count) as avg_results
        FROM dtk_search_logs 
        WHERE DATE(search_time) = CURDATE()
    ";
    
    $stmt = $db->prepare($todaySql);
    $stmt->execute();
    $todayResult = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // 热门搜索词
    $hotKeywordsSql = "
        SELECT 
            keyword,
            COUNT(*) as search_count
        FROM dtk_search_logs 
        WHERE DATE(search_time) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
        GROUP BY keyword
        ORDER BY search_count DESC
        LIMIT 10
    ";
    
    $stmt = $db->prepare($hotKeywordsSql);
    $stmt->execute();
    $hotKeywords = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    return [
        'today' => [
            'total' => intval($todayResult['total']),
            'unique_keywords' => intval($todayResult['unique_keywords']),
            'avg_results' => round(floatval($todayResult['avg_results']), 0)
        ],
        'hot_keywords' => array_map(function($item) {
            return [
                'keyword' => $item['keyword'],
                'count' => intval($item['search_count'])
            ];
        }, $hotKeywords)
    ];
}

/**
 * 获取热门分类
 */
function getHotCategories($db, $platform) {
    $sql = "
        SELECT 
            category_id,
            COUNT(*) as goods_count,
            AVG(month_sales) as avg_sales,
            COUNT(CASE WHEN tier_level = 1 THEN 1 END) as tier1_count
        FROM dtk_goods 
        WHERE platform = ? 
        AND category_id > 0
        GROUP BY category_id
        HAVING goods_count >= 10
        ORDER BY tier1_count DESC, avg_sales DESC
        LIMIT 10
    ";
    
    $stmt = $db->prepare($sql);
    $stmt->execute([$platform]);
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    return array_map(function($item) {
        return [
            'category_id' => intval($item['category_id']),
            'goods_count' => intval($item['goods_count']),
            'avg_sales' => round(floatval($item['avg_sales']), 0),
            'tier1_count' => intval($item['tier1_count'])
        ];
    }, $categories);
}

/**
 * 获取今日数据
 */
function getTodayStats($db, $platform) {
    $sql = "
        SELECT 
            COUNT(*) as new_goods,
            COUNT(CASE WHEN tier_level = 1 THEN 1 END) as new_tier1,
            COUNT(CASE WHEN link_status = 1 THEN 1 END) as new_converted,
            AVG(price) as avg_price
        FROM dtk_goods 
        WHERE platform = ? 
        AND DATE(create_time) = CURDATE()
    ";
    
    $stmt = $db->prepare($sql);
    $stmt->execute([$platform]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    return [
        'new_goods' => intval($result['new_goods']),
        'new_tier1' => intval($result['new_tier1']),
        'new_converted' => intval($result['new_converted']),
        'avg_price' => round(floatval($result['avg_price']), 2)
    ];
}
