<?php
/**
 * 分类API接口
 */

use Utils\Logger;
use Utils\Helper;
use Services\DataokeService;

// 只允许GET请求
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    apiResponse(405, '只支持GET请求');
}

$logger = new Logger();
$startTime = microtime(true);

try {
    // 获取平台参数
    $platform = Helper::getParam('platform', 'taobao', 'string');
    
    // 验证平台是否启用
    if (!Helper::isPlatformEnabled($platform)) {
        apiResponse(400, "平台 {$platform} 未启用或不存在");
    }
    
    // 创建大淘客服务实例
    $dataokeService = new DataokeService($platform);
    
    // 获取分类列表
    $result = $dataokeService->getCategoryList();
    
    // 记录API请求日志
    $duration = Helper::calculateDuration($startTime);
    $logger->logApiRequest('GET', '/api/category', ['platform' => $platform], ['code' => 0], $duration);
    
    // 返回结果
    apiResponse(0, '获取分类列表成功', $result);
    
} catch (Exception $e) {
    $logger->error('获取分类列表失败', [
        'platform' => $platform ?? 'unknown',
        'error' => $e->getMessage()
    ]);
    
    apiResponse(500, '获取分类列表失败: ' . $e->getMessage());
}
?>
