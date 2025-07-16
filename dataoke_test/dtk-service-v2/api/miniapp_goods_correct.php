<?php
/**
 * 小程序端商品列表API接口 - 修复版本
 * 使用正确的数据库字段名
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
    
    $logger->info('小程序商品列表API请求', [
        'method' => $method,
        'params' => $params
    ]);
    
    // 只支持GET请求
    if ($method !== 'GET') {
        errorResponse('只支持GET请求', 405);
    }
    
    // 处理商品列表请求
    handleGoodsList($params);
    
} catch (Exception $e) {
    $logger->error('小程序商品列表API错误', [
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ]);
    
    errorResponse('服务器内部错误: ' . $e->getMessage(), 500);
}

/**
 * 处理商品列表请求
 */
function handleGoodsList($params) {
    global $logger;
    
    try {
        // 获取数据库连接
        $db = DatabaseService::getInstance()->getConnection();
        
        // 解析参数
        $page = max(1, intval($params['page'] ?? 1));
        $limit = min(50, max(1, intval($params['limit'] ?? 20))); // 限制每页最多50条
        $offset = ($page - 1) * $limit;
        
        // 筛选参数
        $platform = $params['platform'] ?? 'taobao';
        $keyword = trim($params['keyword'] ?? '');
        $filter = $params['filter'] ?? ''; // coupon, hot, new
        $tier_level = $params['tier_level'] ?? '';
        $link_status = $params['link_status'] ?? '';
        $category_id = $params['category_id'] ?? '';
        
        // 构建SQL查询
        $where = ['platform = ?'];
        $whereParams = [$platform];

        // 支持通过goods_id精确查找
        if (!empty($params['goods_id'])) {
            $where[] = 'goods_id = ?';
            $whereParams[] = $params['goods_id'];
            $limit = 1;
        }
        
        // 关键词搜索
        if (!empty($keyword)) {
            $where[] = '(title LIKE ? OR shop_name LIKE ?)';
            $whereParams[] = "%{$keyword}%";
            $whereParams[] = "%{$keyword}%";
        }
        
        // 筛选条件
        if ($filter === 'coupon') {
            $where[] = 'coupon_price > 0';
        } elseif ($filter === 'hot') {
            $where[] = 'tier_level = 1';
            $where[] = 'month_sales > 100';
        } elseif ($filter === 'new') {
            $where[] = 'DATE(create_time) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)';
        }
        
        // 等级筛选
        if (!empty($tier_level)) {
            $where[] = 'tier_level = ?';
            $whereParams[] = intval($tier_level);
        }
        
        // 转链状态筛选
        if (!empty($link_status)) {
            $where[] = 'link_status = ?';
            $whereParams[] = intval($link_status);
        }
        
        // 分类筛选
        if (!empty($category_id)) {
            $where[] = 'cid = ?';
            $whereParams[] = intval($category_id);
        }
        
        $whereClause = implode(' AND ', $where);
        
        // 查询总数
        $countSql = "SELECT COUNT(*) as total FROM dtk_goods WHERE {$whereClause}";
        $countStmt = $db->prepare($countSql);
        $countStmt->execute($whereParams);
        $totalCount = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // 查询商品列表
        $sql = "
            SELECT 
                goods_id,
                title,
                main_pic as image,
                actual_price as price,
                original_price,
                coupon_price as coupon_amount,
                month_sales,
                shop_name,
                tier_level,
                link_status,
                privilege_link,
                tpwd,
                cid as category_id,
                create_time,
                update_time
            FROM dtk_goods 
            WHERE {$whereClause}
            ORDER BY 
                CASE 
                    WHEN tier_level = 1 THEN 1
                    WHEN tier_level = 2 THEN 2
                    ELSE 3
                END,
                month_sales DESC,
                update_time DESC
            LIMIT {$limit} OFFSET {$offset}
        ";
        
        $stmt = $db->prepare($sql);
        $stmt->execute($whereParams);
        $goods = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // 格式化商品数据
        $formattedGoods = array_map('formatGoodsForMiniapp', $goods);
        
        // 计算分页信息
        $totalPages = ceil($totalCount / $limit);
        $hasMore = $page < $totalPages;
        
        // 返回结果
        successResponse([
            'list' => $formattedGoods,
            'pagination' => [
                'page' => $page,
                'limit' => $limit,
                'total' => intval($totalCount),
                'totalPages' => $totalPages,
                'hasMore' => $hasMore
            ],
            'filters' => [
                'platform' => $platform,
                'keyword' => $keyword,
                'filter' => $filter,
                'tier_level' => $tier_level,
                'link_status' => $link_status,
                'category_id' => $category_id
            ]
        ], '获取成功');
        
    } catch (Exception $e) {
        $logger->error('获取商品列表失败', [
            'error' => $e->getMessage(),
            'params' => $params
        ]);
        
        errorResponse('获取商品列表失败: ' . $e->getMessage(), 500);
    }
}

/**
 * 格式化商品数据为小程序端格式
 */
function formatGoodsForMiniapp($goods) {
    return [
        'id' => $goods['goods_id'],
        'title' => $goods['title'],
        'pic' => $goods['image'],  // 小程序期望的字段名
        'image' => $goods['image'], // 保持兼容性
        'actual_price' => floatval($goods['price']), // 小程序期望的字段名
        'price' => number_format(floatval($goods['price']), 2), // 保持兼容性
        'original_price' => number_format(floatval($goods['original_price']), 2),
        'coupon_price' => intval($goods['coupon_amount']), // 小程序期望的字段名
        'coupon_amount' => intval($goods['coupon_amount']), // 保持兼容性
        'month_sales' => intval($goods['month_sales']), // 小程序期望的字段名
        'sales' => intval($goods['month_sales']), // 保持兼容性
        'shop_name' => $goods['shop_name'],
        'tier_level' => intval($goods['tier_level']),
        'link_status' => intval($goods['link_status']),
        'has_coupon' => intval($goods['coupon_amount']) > 0,
        'discount_rate' => calculateDiscountRate($goods['price'], $goods['original_price']),
        'final_price' => calculateFinalPrice($goods['price'], $goods['coupon_amount']),
        'privilege_link' => $goods['privilege_link'] ?? '',
        'tpwd' => $goods['tpwd'] ?? '',
        'category_id' => intval($goods['category_id']),
        'create_time' => $goods['create_time'],
        'update_time' => $goods['update_time']
    ];
}

/**
 * 计算折扣率
 */
function calculateDiscountRate($price, $originalPrice) {
    if (empty($originalPrice) || $originalPrice <= 0) {
        return 0;
    }
    
    $discount = (1 - floatval($price) / floatval($originalPrice)) * 100;
    return max(0, round($discount, 1));
}

/**
 * 计算最终价格（扣除优惠券后）
 */
function calculateFinalPrice($price, $couponAmount) {
    $finalPrice = floatval($price) - floatval($couponAmount);
    return number_format(max(0, $finalPrice), 2);
}
