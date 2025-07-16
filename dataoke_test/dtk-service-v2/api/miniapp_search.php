<?php
/**
 * 小程序端商品搜索API接口
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
    
    $logger->info('小程序搜索API请求', [
        'method' => $method,
        'params' => $params
    ]);
    
    // 只支持GET请求
    if ($method !== 'GET') {
        Helper::jsonResponse(405, '只支持GET请求');
        exit;
    }
    
    // 获取搜索关键词
    $keyword = trim($params['keyword'] ?? '');
    if (empty($keyword)) {
        Helper::jsonResponse(400, '搜索关键词不能为空');
        exit;
    }
    
    // 处理搜索请求
    handleSearch($keyword, $params);
    
} catch (Exception $e) {
    $logger->error('小程序搜索API错误', [
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ]);
    
    Helper::jsonResponse(500, '服务器内部错误', null, [
        'error' => $e->getMessage()
    ]);
}

/**
 * 处理搜索请求
 */
function handleSearch($keyword, $params) {
    global $logger;
    
    try {
        // 获取数据库连接
        $db = DatabaseService::getInstance()->getConnection();
        
        // 解析参数
        $page = max(1, intval($params['page'] ?? 1));
        $limit = min(50, max(1, intval($params['limit'] ?? 20)));
        $offset = ($page - 1) * $limit;
        
        // 筛选参数
        $platform = $params['platform'] ?? 'taobao';
        $sort = $params['sort'] ?? 'relevance'; // relevance, sales, price_asc, price_desc
        $min_price = floatval($params['min_price'] ?? 0);
        $max_price = floatval($params['max_price'] ?? 0);
        $has_coupon = $params['has_coupon'] ?? '';
        $tier_level = $params['tier_level'] ?? '';
        
        // 构建搜索SQL
        $where = ['platform = ?'];
        $whereParams = [$platform];
        
        // 关键词搜索 - 使用全文搜索和LIKE结合
        $where[] = '(
            title LIKE ? OR 
            shop_name LIKE ? OR 
            brand_name LIKE ? OR
            MATCH(title) AGAINST(? IN NATURAL LANGUAGE MODE)
        )';
        $searchKeyword = "%{$keyword}%";
        $whereParams[] = $searchKeyword;
        $whereParams[] = $searchKeyword;
        $whereParams[] = $searchKeyword;
        $whereParams[] = $keyword;
        
        // 价格筛选
        if ($min_price > 0) {
            $where[] = 'price >= ?';
            $whereParams[] = $min_price;
        }
        if ($max_price > 0) {
            $where[] = 'price <= ?';
            $whereParams[] = $max_price;
        }
        
        // 优惠券筛选
        if ($has_coupon === '1') {
            $where[] = 'coupon_amount > 0';
        }
        
        // 等级筛选
        if (!empty($tier_level)) {
            $where[] = 'tier_level = ?';
            $whereParams[] = intval($tier_level);
        }
        
        $whereClause = implode(' AND ', $where);
        
        // 排序规则
        $orderBy = 'ORDER BY ';
        switch ($sort) {
            case 'sales':
                $orderBy .= 'month_sales DESC, tier_level ASC';
                break;
            case 'price_asc':
                $orderBy .= 'price ASC, tier_level ASC';
                break;
            case 'price_desc':
                $orderBy .= 'price DESC, tier_level ASC';
                break;
            case 'relevance':
            default:
                $orderBy .= '
                    CASE 
                        WHEN title LIKE ? THEN 1
                        WHEN shop_name LIKE ? THEN 2
                        WHEN brand_name LIKE ? THEN 3
                        ELSE 4
                    END,
                    tier_level ASC,
                    month_sales DESC
                ';
                // 为相关性排序添加参数
                $orderParams = [$searchKeyword, $searchKeyword, $searchKeyword];
                break;
        }
        
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
                image,
                price,
                original_price,
                coupon_amount,
                month_sales,
                shop_name,
                tier_level,
                link_status,
                privilege_link,
                tpwd,
                category_id,
                brand_name,
                create_time,
                update_time
            FROM dtk_goods 
            WHERE {$whereClause}
            {$orderBy}
            LIMIT {$limit} OFFSET {$offset}
        ";
        
        $stmt = $db->prepare($sql);
        
        // 执行查询，合并排序参数
        $executeParams = $whereParams;
        if ($sort === 'relevance') {
            $executeParams = array_merge($executeParams, $orderParams);
        }
        
        $stmt->execute($executeParams);
        $goods = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // 格式化商品数据
        $formattedGoods = array_map('formatGoodsForSearch', $goods);
        
        // 计算分页信息
        $totalPages = ceil($totalCount / $limit);
        $hasMore = $page < $totalPages;
        
        // 获取搜索建议
        $suggestions = getSearchSuggestions($keyword);
        
        // 记录搜索日志
        recordSearchLog($keyword, $totalCount);
        
        // 返回结果
        Helper::jsonResponse(200, '搜索成功', [
            'keyword' => $keyword,
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
                'sort' => $sort,
                'min_price' => $min_price,
                'max_price' => $max_price,
                'has_coupon' => $has_coupon,
                'tier_level' => $tier_level
            ],
            'suggestions' => $suggestions
        ]);
        
    } catch (Exception $e) {
        $logger->error('搜索失败', [
            'error' => $e->getMessage(),
            'keyword' => $keyword,
            'params' => $params
        ]);
        
        Helper::jsonResponse(500, '搜索失败', null, [
            'error' => $e->getMessage()
        ]);
    }
}

/**
 * 格式化搜索结果商品数据
 */
function formatGoodsForSearch($goods) {
    return [
        'id' => $goods['goods_id'],
        'title' => $goods['title'],
        'image' => $goods['image'],
        'price' => number_format(floatval($goods['price']), 2),
        'original_price' => number_format(floatval($goods['original_price']), 2),
        'coupon_amount' => intval($goods['coupon_amount']),
        'sales' => intval($goods['month_sales']),
        'shop_name' => $goods['shop_name'],
        'tier_level' => intval($goods['tier_level']),
        'link_status' => intval($goods['link_status']),
        'has_coupon' => intval($goods['coupon_amount']) > 0,
        'discount_rate' => calculateDiscountRate($goods['price'], $goods['original_price']),
        'final_price' => calculateFinalPrice($goods['price'], $goods['coupon_amount']),
        'brand_name' => $goods['brand_name'] ?? '',
        'category_id' => intval($goods['category_id']),
        'create_time' => $goods['create_time'],
        'update_time' => $goods['update_time']
    ];
}

/**
 * 获取搜索建议
 */
function getSearchSuggestions($keyword, $limit = 5) {
    try {
        $db = DatabaseService::getInstance()->getConnection();
        
        $sql = "
            SELECT DISTINCT title
            FROM dtk_goods 
            WHERE title LIKE ? 
            AND platform = 'taobao'
            ORDER BY month_sales DESC
            LIMIT ?
        ";
        
        $stmt = $db->prepare($sql);
        $stmt->execute(["%{$keyword}%", $limit]);
        $results = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        return array_slice($results, 0, $limit);
        
    } catch (Exception $e) {
        return [];
    }
}

/**
 * 记录搜索日志
 */
function recordSearchLog($keyword, $resultCount) {
    try {
        $db = DatabaseService::getInstance()->getConnection();
        
        $sql = "
            INSERT INTO dtk_search_logs (keyword, result_count, search_time, ip_address)
            VALUES (?, ?, NOW(), ?)
        ";
        
        $stmt = $db->prepare($sql);
        $stmt->execute([
            $keyword,
            $resultCount,
            $_SERVER['REMOTE_ADDR'] ?? ''
        ]);
        
    } catch (Exception $e) {
        // 搜索日志记录失败不影响主流程
    }
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
