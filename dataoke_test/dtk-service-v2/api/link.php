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
    
    if ($result) {
        $logger->info('转链成功', [
            'goods_id' => $goodsId,
            'platform' => $platform
        ]);
        
        successResponse($result, '转链成功');
    } else {
        $logger->warning('转链失败', [
            'goods_id' => $goodsId,
            'platform' => $platform
        ]);
        
        errorResponse('转链失败，请稍后重试', 500);
    }
    
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
