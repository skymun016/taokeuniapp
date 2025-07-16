<?php
/**
 * 转链API接口
 */

// 引入CORS配置
require_once __DIR__ . '/cors.php';

// 引入配置文件
require_once dirname(__DIR__) . '/vendor/autoload.php';

use Utils\Logger;
use Services\SmartLinkService;

$logger = new Logger();

try {
    // 验证请求方法
    validateRequestMethod(['POST']);
    
    // 获取请求数据
    $requestData = getRequestData();
    
    // 验证必需参数
    if (empty($requestData['goods_id'])) {
        errorResponse('商品ID不能为空', 400);
    }
    
    $goodsId = $requestData['goods_id'];
    $platform = $requestData['platform'] ?? 'taobao';
    
    $logger->info('转链请求', [
        'goods_id' => $goodsId,
        'platform' => $platform,
        'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
    ]);
    
    // 创建转链服务
    $linkService = new SmartLinkService($platform);
    
    // 执行转链
    $result = $linkService->convertSingleGoods($goodsId);
    $shortUrl = $result['shortUrl'] ?? '';
    $finalPrivilegeLink = $shortUrl ?: ($result['privilegeLink'] ?? $result['privilege_link'] ?? '');
    $tpwd = $result['tpwd'] ?? '';
    // 强制更新数据库
    try {
        $db = \Services\DatabaseService::getInstance()->getConnection();
        $updateSql = "UPDATE dtk_goods SET privilege_link = ?, tpwd = ?, link_status = 1, last_convert_time = NOW(), convert_count = convert_count + 1, link_expire_time = DATE_ADD(NOW(), INTERVAL 7 DAY) WHERE goods_id = ?";
        $updateStmt = $db->prepare($updateSql);
        $updateStmt->execute([$finalPrivilegeLink, $tpwd, $goodsId]);
    } catch (Exception $e) {
        $logger->error('转链数据库更新失败', ['goods_id' => $goodsId, 'error' => $e->getMessage()]);
    }
    // 保证 shortUrl 字段一定返回，并兼容 privilegeLink/privilege_link
    $response = $result;
    $response['shortUrl'] = $shortUrl; // 只用大淘客API的最新值
    $response['privilegeLink'] = $finalPrivilegeLink;
    $response['privilege_link'] = $finalPrivilegeLink;
    $response['tpwd'] = $tpwd;
    $logger->info('接口最终响应', ['goods_id' => $goodsId, 'response' => $response]);
    successResponse($response, '转链成功');
    
} catch (Exception $e) {
    $logger->error('转链API异常', [
        'error' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'goods_id' => $requestData['goods_id'] ?? 'unknown'
    ]);
    
    errorResponse('服务器内部错误', 500, $e->getMessage());
}
?>
