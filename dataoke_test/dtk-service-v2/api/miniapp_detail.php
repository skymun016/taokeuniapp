<?php
/**
 * 小程序端商品详情API接口
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
    
    $logger->info('小程序商品详情API请求', [
        'method' => $method,
        'params' => $params
    ]);
    
    // 只支持GET请求
    if ($method !== 'GET') {
        Helper::jsonResponse(405, '只支持GET请求');
        exit;
    }
    
    // 获取商品ID
    $goodsId = $params['goods_id'] ?? $params['id'] ?? '';
    if (empty($goodsId)) {
        Helper::jsonResponse(400, '商品ID不能为空');
        exit;
    }
    
    // 处理商品详情请求
    handleGoodsDetail($goodsId);
    
} catch (Exception $e) {
    $logger->error('小程序商品详情API错误', [
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ]);
    
    Helper::jsonResponse(500, '服务器内部错误', null, [
        'error' => $e->getMessage()
    ]);
}

/**
 * 处理商品详情请求
 */
function handleGoodsDetail($goodsId) {
    global $logger;
    
    try {
        // 获取数据库连接
        $db = DatabaseService::getInstance()->getConnection();
        
        // 查询商品详情
        $sql = "
            SELECT 
                goods_id,
                title,
                image,
                images,
                price,
                original_price,
                coupon_amount,
                coupon_start_time,
                coupon_end_time,
                month_sales,
                shop_name,
                shop_type,
                tier_level,
                link_status,
                privilege_link,
                tpwd,
                item_url,
                category_id,
                brand_name,
                description,
                detail_images,
                specs,
                service_tags,
                create_time,
                update_time,
                last_convert_time,
                convert_count
            FROM dtk_goods 
            WHERE goods_id = ?
            LIMIT 1
        ";
        
        $stmt = $db->prepare($sql);
        $stmt->execute([$goodsId]);
        $goods = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$goods) {
            Helper::jsonResponse(404, '商品不存在');
            return;
        }
        
        // 格式化商品详情数据
        $formattedGoods = formatGoodsDetailForMiniapp($goods);
        
        // 获取相关商品推荐
        $relatedGoods = getRelatedGoods($goods['category_id'], $goodsId);
        
        // 返回结果
        Helper::jsonResponse(200, '获取成功', [
            'goods' => $formattedGoods,
            'related' => $relatedGoods
        ]);
        
    } catch (Exception $e) {
        $logger->error('获取商品详情失败', [
            'error' => $e->getMessage(),
            'goods_id' => $goodsId
        ]);
        
        Helper::jsonResponse(500, '获取商品详情失败', null, [
            'error' => $e->getMessage()
        ]);
    }
}

/**
 * 格式化商品详情数据为小程序端格式
 */
function formatGoodsDetailForMiniapp($goods) {
    // 解析图片数组
    $images = [];
    if (!empty($goods['images'])) {
        $images = json_decode($goods['images'], true) ?: [];
    }
    if (empty($images) && !empty($goods['image'])) {
        $images = [$goods['image']];
    }
    
    // 解析详情图片
    $detailImages = [];
    if (!empty($goods['detail_images'])) {
        $detailImages = json_decode($goods['detail_images'], true) ?: [];
    }
    
    // 解析规格信息
    $specs = [];
    if (!empty($goods['specs'])) {
        $specs = json_decode($goods['specs'], true) ?: [];
    }
    
    // 解析服务标签
    $serviceTags = [];
    if (!empty($goods['service_tags'])) {
        $serviceTags = json_decode($goods['service_tags'], true) ?: [];
    }
    
    return [
        'id' => $goods['goods_id'],
        'title' => $goods['title'],
        'image' => $goods['image'],
        'images' => $images,
        'price' => number_format(floatval($goods['price']), 2),
        'original_price' => number_format(floatval($goods['original_price']), 2),
        'coupon_amount' => intval($goods['coupon_amount']),
        'coupon_start_time' => $goods['coupon_start_time'],
        'coupon_end_time' => $goods['coupon_end_time'],
        'sales' => intval($goods['month_sales']),
        'shop_name' => $goods['shop_name'],
        'shop_type' => $goods['shop_type'],
        'tier_level' => intval($goods['tier_level']),
        'link_status' => intval($goods['link_status']),
        'has_coupon' => intval($goods['coupon_amount']) > 0,
        'discount_rate' => calculateDiscountRate($goods['price'], $goods['original_price']),
        'final_price' => calculateFinalPrice($goods['price'], $goods['coupon_amount']),
        'privilege_link' => $goods['privilege_link'] ?? '',
        'tpwd' => $goods['tpwd'] ?? '',
        'item_url' => $goods['item_url'] ?? '',
        'category_id' => intval($goods['category_id']),
        'brand_name' => $goods['brand_name'] ?? '',
        'description' => $goods['description'] ?? '',
        'detail_images' => $detailImages,
        'specs' => $specs,
        'service_tags' => $serviceTags,
        'create_time' => $goods['create_time'],
        'update_time' => $goods['update_time'],
        'last_convert_time' => $goods['last_convert_time'],
        'convert_count' => intval($goods['convert_count'])
    ];
}

/**
 * 获取相关商品推荐
 */
function getRelatedGoods($categoryId, $excludeGoodsId, $limit = 6) {
    try {
        $db = DatabaseService::getInstance()->getConnection();
        
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
                tier_level
            FROM dtk_goods 
            WHERE category_id = ? 
            AND goods_id != ?
            AND tier_level <= 2
            ORDER BY month_sales DESC, tier_level ASC
            LIMIT ?
        ";
        
        $stmt = $db->prepare($sql);
        $stmt->execute([$categoryId, $excludeGoodsId, $limit]);
        $goods = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return array_map(function($item) {
            return [
                'id' => $item['goods_id'],
                'title' => $item['title'],
                'image' => $item['image'],
                'price' => number_format(floatval($item['price']), 2),
                'original_price' => number_format(floatval($item['original_price']), 2),
                'coupon_amount' => intval($item['coupon_amount']),
                'sales' => intval($item['month_sales']),
                'shop_name' => $item['shop_name'],
                'tier_level' => intval($item['tier_level']),
                'final_price' => calculateFinalPrice($item['price'], $item['coupon_amount'])
            ];
        }, $goods);
        
    } catch (Exception $e) {
        return [];
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
