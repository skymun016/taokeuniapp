<?php
/**
 * API错误处理页面
 */

// 引入CORS配置
require_once __DIR__ . '/cors.php';

// 设置CORS头部
setApiHeaders();

// 获取错误信息
$errorCode = http_response_code();
$errorMessage = '';

switch ($errorCode) {
    case 404:
        $errorMessage = 'API接口不存在';
        break;
    case 405:
        $errorMessage = '请求方法不允许';
        break;
    case 500:
        $errorMessage = '服务器内部错误';
        break;
    default:
        $errorMessage = '未知错误';
        break;
}

// 返回错误响应
echo json_encode([
    'code' => $errorCode,
    'message' => $errorMessage,
    'data' => null,
    'timestamp' => time()
], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
exit;
?>
