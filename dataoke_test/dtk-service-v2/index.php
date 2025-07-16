<?php
/**
 * 大淘客服务端 V2.0 - 统一入口
 * 基于官方SDK重新开发，支持多平台商品数据管理
 */

// 设置错误报告
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 设置时区
date_default_timezone_set('Asia/Shanghai');

// 定义项目根目录
define('ROOT_PATH', __DIR__);
define('SRC_PATH', ROOT_PATH . '/src');
define('CONFIG_PATH', ROOT_PATH . '/config');
define('CACHE_PATH', ROOT_PATH . '/cache');
define('LOGS_PATH', ROOT_PATH . '/logs');

// 自动加载器
require_once ROOT_PATH . '/vendor/autoload.php';

// 加载配置文件
require_once CONFIG_PATH . '/config.php';
require_once CONFIG_PATH . '/database.php';

// 加载核心类
require_once SRC_PATH . '/Utils/Logger.php';
require_once SRC_PATH . '/Utils/Helper.php';
require_once SRC_PATH . '/Services/DatabaseService.php';
require_once SRC_PATH . '/Services/CacheService.php';

// 处理CORS跨域请求
function handleCors() {
    $origin = $_SERVER['HTTP_ORIGIN'] ?? '*';
    
    header("Access-Control-Allow-Origin: {$origin}");
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
    header("Access-Control-Allow-Credentials: true");
    
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(200);
        exit;
    }
}

// 统一API响应格式
function apiResponse($code = 0, $message = 'success', $data = null) {
    $response = [
        'code' => $code,
        'message' => $message,
        'timestamp' => time(),
        'data' => $data
    ];
    
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

// 处理CORS
handleCors();

// 获取请求信息
$method = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];
$path = parse_url($uri, PHP_URL_PATH);
$query = parse_url($uri, PHP_URL_QUERY);

// 移除项目路径前缀（如果存在）
$basePath = dirname($_SERVER['SCRIPT_NAME']);
if ($basePath !== '/') {
    $path = str_replace($basePath, '', $path);
}

// 路由分发
try {
    // 管理后台路由
    if (strpos($path, '/admin') === 0) {
        require_once ROOT_PATH . '/admin/index.php';
        exit;
    }
    
    // API路由
    switch (true) {
        case strpos($path, '/api/goods') === 0:
            require_once ROOT_PATH . '/api/goods.php';
            break;
            
        case strpos($path, '/api/category') === 0:
            require_once ROOT_PATH . '/api/category.php';
            break;
            
        case strpos($path, '/api/search') === 0:
            require_once ROOT_PATH . '/api/search.php';
            break;
            
        case strpos($path, '/api/sync') === 0:
            require_once ROOT_PATH . '/api/sync.php';
            break;
            
        case strpos($path, '/api/test') === 0:
            require_once ROOT_PATH . '/api/test.php';
            break;
            
        case $path === '/' || $path === '/index.php' || $path === '':
            // 显示API文档
            showApiDoc();
            break;
            
        default:
            apiResponse(404, 'API接口不存在');
    }
    
} catch (Exception $e) {
    // 记录错误日志
    $logger = new \Utils\Logger();
    $logger->error('系统错误: ' . $e->getMessage(), [
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'trace' => $e->getTraceAsString()
    ]);
    
    apiResponse(500, '系统内部错误');
}

/**
 * 显示API文档
 */
function showApiDoc() {
    $config = DTK_CONFIG;
    
    $html = <<<HTML
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>大淘客服务端 V2.0 - API文档</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; margin: 0; padding: 20px; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; background: white; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; border-radius: 8px 8px 0 0; }
        .content { padding: 30px; }
        .api-section { margin-bottom: 30px; }
        .api-item { background: #f8f9fa; padding: 15px; border-radius: 6px; margin-bottom: 10px; border-left: 4px solid #007bff; }
        .method { display: inline-block; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: bold; }
        .get { background: #28a745; color: white; }
        .post { background: #007bff; color: white; }
        .status { margin-top: 20px; padding: 15px; background: #e9ecef; border-radius: 6px; }
        .status-item { display: inline-block; margin-right: 20px; }
        .status-ok { color: #28a745; }
        .status-error { color: #dc3545; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🚀 大淘客服务端 V2.0</h1>
            <p>基于官方SDK开发，支持多平台商品数据管理和自动同步</p>
        </div>
        
        <div class="content">
            <div class="api-section">
                <h2>📋 API接口列表</h2>
                
                <div class="api-item">
                    <span class="method get">GET</span>
                    <strong>/api/goods</strong> - 获取商品列表
                    <p>参数: page, pageSize, cid, keyword, sort, platform</p>
                </div>
                
                <div class="api-item">
                    <span class="method get">GET</span>
                    <strong>/api/goods/detail</strong> - 获取商品详情
                    <p>参数: id, goodsId, platform</p>
                </div>
                
                <div class="api-item">
                    <span class="method get">GET</span>
                    <strong>/api/category</strong> - 获取分类列表
                    <p>参数: platform, parentId</p>
                </div>
                
                <div class="api-item">
                    <span class="method get">GET</span>
                    <strong>/api/search</strong> - 搜索商品
                    <p>参数: keyword, page, pageSize, platform</p>
                </div>
                
                <div class="api-item">
                    <span class="method post">POST</span>
                    <strong>/api/sync</strong> - 手动同步数据
                    <p>参数: type (goods|category), platform</p>
                </div>
                
                <div class="api-item">
                    <span class="method get">GET</span>
                    <strong>/api/test</strong> - 系统状态检测
                </div>
            </div>
            
            <div class="api-section">
                <h2>⚙️ 系统配置</h2>
                <div class="status">
                    <div class="status-item">
                        <strong>版本:</strong> <span class="status-ok">V2.0.0</span>
                    </div>
                    <div class="status-item">
                        <strong>PHP版本:</strong> <span class="status-ok"><?php echo PHP_VERSION; ?></span>
                    </div>
                    <div class="status-item">
                        <strong>数据库:</strong> <span class="status-ok">MySQL</span>
                    </div>
                    <div class="status-item">
                        <strong>缓存:</strong> <span class="status-ok">文件缓存</span>
                    </div>
                </div>
            </div>
            
            <div class="api-section">
                <h2>🔗 快速链接</h2>
                <p>
                    <a href="/admin" style="color: #007bff; text-decoration: none;">📊 管理后台</a> |
                    <a href="/api/test" style="color: #007bff; text-decoration: none;">🔍 系统检测</a> |
                    <a href="/api/goods?page=1&pageSize=5" style="color: #007bff; text-decoration: none;">📦 商品示例</a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>
HTML;
    
    echo $html;
}
?>
