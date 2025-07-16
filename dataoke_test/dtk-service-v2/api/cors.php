<?php
/**
 * CORS跨域配置文件
 * 用于处理小程序和H5的跨域请求
 */

/**
 * 设置CORS头部
 */
function setCorsHeaders() {
    // 获取请求来源
    $origin = $_SERVER['HTTP_ORIGIN'] ?? '';
    
    // 允许的域名列表
    $allowedOrigins = [
        'https://dtkv2.zhishujiaoyu.com',
        'http://localhost:8080',
        'http://127.0.0.1:8080',
        'https://servicewechat.com', // 微信小程序域名
        'https://mp.weixin.qq.com',  // 微信公众平台
    ];
    
    // 检查来源是否在允许列表中，或者是微信小程序请求
    $isAllowed = false;
    
    // 允许所有微信相关域名
    if (strpos($origin, 'servicewechat.com') !== false || 
        strpos($origin, 'weixin.qq.com') !== false ||
        strpos($origin, 'mp.weixin.qq.com') !== false) {
        $isAllowed = true;
    }
    
    // 检查明确允许的域名
    if (in_array($origin, $allowedOrigins)) {
        $isAllowed = true;
    }
    
    // 开发环境允许localhost
    if (strpos($origin, 'localhost') !== false || 
        strpos($origin, '127.0.0.1') !== false ||
        strpos($origin, '192.168.') !== false) {
        $isAllowed = true;
    }
    
    // 设置CORS头部
    if ($isAllowed && !empty($origin)) {
        header("Access-Control-Allow-Origin: $origin");
    } else {
        // 如果没有Origin头部（如小程序请求），允许所有来源
        header("Access-Control-Allow-Origin: *");
    }
    
    // 设置其他CORS头部
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, Accept, Origin");
    header("Access-Control-Allow-Credentials: true");
    header("Access-Control-Max-Age: 86400"); // 24小时
    
    // 处理预检请求
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(200);
        exit;
    }
}

/**
 * 设置API响应头部
 */
function setApiHeaders() {
    // 设置CORS
    setCorsHeaders();
    
    // 设置内容类型
    header('Content-Type: application/json; charset=utf-8');
    
    // 禁用缓存
    header('Cache-Control: no-cache, no-store, must-revalidate');
    header('Pragma: no-cache');
    header('Expires: 0');
    
    // 安全头部
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: DENY');
    header('X-XSS-Protection: 1; mode=block');
}

/**
 * API 响应函数（带CORS支持）
 */
if (!function_exists('apiResponse')) {
    function apiResponse($data = null, $message = 'success', $code = 200) {
        // 设置响应头部
        setApiHeaders();

        $response = [
            'code' => $code,
            'message' => $message,
            'data' => $data,
            'timestamp' => time()
        ];

        http_response_code($code);
        echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }
}

/**
 * 错误响应函数
 */
function errorResponse($message = 'Internal Server Error', $code = 500, $details = null) {
    $data = null;
    if ($details && (defined('DEBUG') && DEBUG)) {
        $data = ['error_details' => $details];
    }
    
    apiResponse($data, $message, $code);
}

/**
 * 成功响应函数
 */
function successResponse($data = null, $message = 'success') {
    apiResponse($data, $message, 200);
}

/**
 * 验证请求方法
 */
function validateRequestMethod($allowedMethods = ['GET', 'POST']) {
    $method = $_SERVER['REQUEST_METHOD'];
    
    if (!in_array($method, $allowedMethods)) {
        errorResponse("Method $method not allowed", 405);
    }
}

/**
 * 获取请求数据
 */
function getRequestData() {
    $method = $_SERVER['REQUEST_METHOD'];
    
    switch ($method) {
        case 'GET':
            return $_GET;
            
        case 'POST':
            $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
            
            if (strpos($contentType, 'application/json') !== false) {
                $input = file_get_contents('php://input');
                $data = json_decode($input, true);
                return $data ?: [];
            } else {
                return $_POST;
            }
            
        case 'PUT':
        case 'DELETE':
            $input = file_get_contents('php://input');
            $data = json_decode($input, true);
            return $data ?: [];
            
        default:
            return [];
    }
}

/**
 * 记录API访问日志
 */
function logApiAccess($endpoint, $params = [], $response_code = 200) {
    $log_data = [
        'timestamp' => date('Y-m-d H:i:s'),
        'method' => $_SERVER['REQUEST_METHOD'],
        'endpoint' => $endpoint,
        'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
        'origin' => $_SERVER['HTTP_ORIGIN'] ?? 'unknown',
        'params' => $params,
        'response_code' => $response_code
    ];
    
    // 这里可以记录到日志文件或数据库
    error_log('API Access: ' . json_encode($log_data, JSON_UNESCAPED_UNICODE));
}

// 自动设置CORS头部
setCorsHeaders();
