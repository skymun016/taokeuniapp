<?php
/**
 * 小程序端转链API接口
 */

// 引入CORS配置
require_once __DIR__ . '/cors.php';

// 引入配置文件
require_once dirname(__DIR__) . '/vendor/autoload.php';

use Utils\Logger;
use Utils\Helper;
use Services\DatabaseService;
use Services\SmartLinkService;

$logger = new Logger();

try {
    // 获取请求方法和参数
    $method = $_SERVER['REQUEST_METHOD'];
    $input = json_decode(file_get_contents('php://input'), true) ?: [];
    $params = array_merge($_GET, $_POST, $input);
    
    $logger->info('小程序转链API请求', [
        'method' => $method,
        'params' => $params
    ]);
    
    // 只支持POST请求
    if ($method !== 'POST') {
        Helper::jsonResponse(405, '只支持POST请求');
        exit;
    }
    
    // 获取商品ID
    $goodsId = $params['goods_id'] ?? $params['id'] ?? '';
    if (empty($goodsId)) {
        Helper::jsonResponse(400, '商品ID不能为空');
        exit;
    }
    
    // 处理转链请求
    handleConvert($goodsId, $params);
    
} catch (Exception $e) {
    $logger->error('小程序转链API错误', [
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ]);
    
    Helper::jsonResponse(500, '服务器内部错误', null, [
        'error' => $e->getMessage()
    ]);
}

/**
 * 处理转链请求
 */
function handleConvert($goodsId, $params) {
    global $logger;
    
    try {
        // 获取数据库连接
        $db = DatabaseService::getInstance()->getConnection();
        
        // 查询商品信息
        $sql = "
            SELECT 
                goods_id,
                title,
                image,
                price,
                original_price,
                coupon_amount,
                shop_name,
                tier_level,
                link_status,
                privilege_link,
                tpwd,
                item_url,
                last_convert_time,
                convert_count,
                link_expire_time
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
        
        // 检查是否已有有效的转链
        $hasValidLink = false;
        $privilegeLink = $goods['privilege_link'] ?? '';
        $tpwd = $goods['tpwd'] ?? '';
        
        if (!empty($privilegeLink) && !empty($tpwd)) {
            // 检查链接是否过期
            $expireTime = $goods['link_expire_time'];
            if (empty($expireTime) || strtotime($expireTime) > time()) {
                $hasValidLink = true;
            }
        }
        
        // 如果没有有效链接，进行转链
        // 修改为：每次都强制转链，拿到最新短链并写入数据库
        $logger->info('开始转链', ['goods_id' => $goodsId]);
        try {
            $smartLinkService = new SmartLinkService();
            $convertResult = $smartLinkService->convertSingleGoods($goodsId);
            $logger->info('大淘客API返回', ['goods_id' => $goodsId, 'convertResult' => $convertResult]);
            $shortUrl = $convertResult['shortUrl'] ?? '';
            $finalPrivilegeLink = $shortUrl ?: $convertResult['privilege_link'];
            if ($convertResult['success']) {
                // 强制更新商品转链信息
                $updateSql = "
                    UPDATE dtk_goods 
                    SET 
                        privilege_link = ?,
                        tpwd = ?,
                        link_status = 1,
                        last_convert_time = NOW(),
                        convert_count = convert_count + 1,
                        link_expire_time = DATE_ADD(NOW(), INTERVAL 7 DAY)
                    WHERE goods_id = ?
                ";
                $updateStmt = $db->prepare($updateSql);
                $updateStmt->execute([
                    $finalPrivilegeLink,
                    $convertResult['tpwd'],
                    $goodsId
                ]);
                $privilegeLink = $finalPrivilegeLink;
                $tpwd = $convertResult['tpwd'];
            } else {
                $logger->warning('转链失败', [
                    'goods_id' => $goodsId,
                    'error' => $convertResult['error'] ?? '未知错误'
                ]);
                $privilegeLink = $goods['item_url'] ?? '';
                $tpwd = '';
                $shortUrl = '';
            }
        } catch (Exception $e) {
            $logger->error('转链异常', [
                'goods_id' => $goodsId,
                'error' => $e->getMessage()
            ]);
            $privilegeLink = $goods['item_url'] ?? '';
            $tpwd = '';
            $shortUrl = '';
        }
        // 记录转链统计
        recordConvertStats($goodsId, !empty($privilegeLink));
        // 返回转链结果
        $responseData = [
            'goods_id' => $goodsId,
            'title' => $goods['title'],
            'image' => $goods['image'],
            'price' => number_format(floatval($goods['price']), 2),
            'original_price' => number_format(floatval($goods['original_price']), 2),
            'coupon_amount' => intval($goods['coupon_amount']),
            'final_price' => calculateFinalPrice($goods['price'], $goods['coupon_amount']),
            'shop_name' => $goods['shop_name'],
            'privilege_link' => $privilegeLink,
            'shortUrl' => $shortUrl ?? '', // 只用大淘客API的最新值
            'tpwd' => $tpwd,
            'item_url' => $goods['item_url'] ?? '',
            'has_privilege' => !empty($privilegeLink),
            'has_tpwd' => !empty($tpwd),
            'convert_time' => date('Y-m-d H:i:s'),
            'expire_time' => date('Y-m-d H:i:s', strtotime('+7 days'))
        ];
        $logger->info('接口最终响应', ['goods_id' => $goodsId, 'responseData' => $responseData]);
        Helper::jsonResponse(200, '转链成功', $responseData);
        
    } catch (Exception $e) {
        $logger->error('转链失败', [
            'error' => $e->getMessage(),
            'goods_id' => $goodsId
        ]);
        
        Helper::jsonResponse(500, '转链失败', null, [
            'error' => $e->getMessage()
        ]);
    }
}

/**
 * 记录转链统计
 */
function recordConvertStats($goodsId, $success) {
    try {
        $db = DatabaseService::getInstance()->getConnection();
        
        $sql = "
            INSERT INTO dtk_convert_logs (
                goods_id, 
                success, 
                convert_time, 
                ip_address,
                user_agent
            ) VALUES (?, ?, NOW(), ?, ?)
        ";
        
        $stmt = $db->prepare($sql);
        $stmt->execute([
            $goodsId,
            $success ? 1 : 0,
            $_SERVER['REMOTE_ADDR'] ?? '',
            $_SERVER['HTTP_USER_AGENT'] ?? ''
        ]);
        
    } catch (Exception $e) {
        // 统计记录失败不影响主流程
    }
}

/**
 * 计算最终价格（扣除优惠券后）
 */
function calculateFinalPrice($price, $couponAmount) {
    $finalPrice = floatval($price) - floatval($couponAmount);
    return number_format(max(0, $finalPrice), 2);
}
