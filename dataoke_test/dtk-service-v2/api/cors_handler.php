<?php
/**
 * CORS预检请求处理器
 */

// 引入CORS配置
require_once __DIR__ . '/cors.php';

// 设置CORS头部
setApiHeaders();

// 返回成功响应
http_response_code(200);
echo json_encode([
    'code' => 200,
    'message' => 'CORS preflight successful',
    'data' => null,
    'timestamp' => time()
], JSON_UNESCAPED_UNICODE);
exit;
?>
