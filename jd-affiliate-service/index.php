<?php
/**
 * 京东联盟服务 - 统一入口
 * 基于好单库API开发，专门处理京东商品数据管理
 */

// 设置错误报告
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 定义项目根目录
define('ROOT_PATH', __DIR__);
define('SRC_PATH', ROOT_PATH . '/src');
define('CONFIG_PATH', ROOT_PATH . '/config');
define('CACHE_PATH', ROOT_PATH . '/cache');
define('LOGS_PATH', ROOT_PATH . '/logs');

// 检查Composer自动加载
if (file_exists(ROOT_PATH . '/vendor/autoload.php')) {
    require_once ROOT_PATH . '/vendor/autoload.php';
}

// 加载配置文件
require_once CONFIG_PATH . '/config.php';
require_once CONFIG_PATH . '/database.php';

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
function apiResponse($data = null, $message = 'success', $code = 1) {
    $response = [
        'code' => $code,
        'msg' => $message,
        'data' => $data,
        'time' => time()
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

// 移除项目路径前缀（如果存在）
$basePath = dirname($_SERVER['SCRIPT_NAME']);
if ($basePath !== '/') {
    $path = str_replace($basePath, '', $path);
}

// 路由分发
try {
    // 管理后台路由
    if (strpos($path, '/admin') === 0) {
        if (file_exists(ROOT_PATH . '/admin/index.php')) {
            require_once ROOT_PATH . '/admin/index.php';
        } else {
            apiResponse(null, '管理后台暂未实现', 0);
        }
        exit;
    }
    
    // API路由
    switch (true) {
        case strpos($path, '/api/products') === 0:
            if (file_exists(ROOT_PATH . '/api/products.php')) {
                require_once ROOT_PATH . '/api/products.php';
            } else {
                apiResponse(null, '商品API暂未实现', 0);
            }
            break;
            
        case strpos($path, '/api/search') === 0:
            if (file_exists(ROOT_PATH . '/api/search.php')) {
                require_once ROOT_PATH . '/api/search.php';
            } else {
                apiResponse(null, '搜索API暂未实现', 0);
            }
            break;
            
        case strpos($path, '/api/categories') === 0:
            if (file_exists(ROOT_PATH . '/api/categories.php')) {
                require_once ROOT_PATH . '/api/categories.php';
            } else {
                apiResponse(null, '分类API暂未实现', 0);
            }
            break;
            
        case strpos($path, '/api/links') === 0:
            if (file_exists(ROOT_PATH . '/api/links.php')) {
                require_once ROOT_PATH . '/api/links.php';
            } else {
                apiResponse(null, '链接API暂未实现', 0);
            }
            break;
            
        case strpos($path, '/api/sync') === 0:
            if (file_exists(ROOT_PATH . '/api/sync.php')) {
                require_once ROOT_PATH . '/api/sync.php';
            } else {
                apiResponse(null, '同步API暂未实现', 0);
            }
            break;
            
        case strpos($path, '/api/test') === 0:
            if (file_exists(ROOT_PATH . '/api/test.php')) {
                require_once ROOT_PATH . '/api/test.php';
            } else {
                apiResponse(null, '测试API暂未实现', 0);
            }
            break;
            
        case $path === '/' || $path === '/index.php' || $path === '':
            // 显示API文档
            showApiDoc();
            break;
            
        default:
            apiResponse(null, 'API接口不存在', 0);
    }
    
} catch (Exception $e) {
    // 记录错误日志
    if (class_exists('\JdAffiliate\Utils\Logger')) {
        $logger = new \JdAffiliate\Utils\Logger();
        $logger->error('系统错误: ' . $e->getMessage(), [
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ]);
    }
    
    apiResponse(null, '系统内部错误', 0);
}

/**
 * 显示API文档
 */
function showApiDoc() {
    $config = APP_CONFIG;
    
    $html = <<<HTML
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>京东联盟服务 - API文档</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; margin: 0; padding: 20px; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; background: white; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .header { background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%); color: white; padding: 30px; border-radius: 8px 8px 0 0; }
        .content { padding: 30px; }
        .api-section { margin-bottom: 30px; }
        .api-item { background: #f8f9fa; padding: 15px; border-radius: 6px; margin-bottom: 10px; border-left: 4px solid #ff6b6b; }
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
            <h1>🛍️ 京东联盟服务</h1>
            <p>基于好单库API开发，专门处理京东商品数据管理和联盟链接生成</p>
        </div>
        
        <div class="content">
            <div class="api-section">
                <h2>📋 API接口列表</h2>
                
                <div class="api-item">
                    <span class="method get">GET</span>
                    <strong>/api/products</strong> - 获取京东商品列表
                    <p>参数: page, pageSize, categoryId, minPrice, maxPrice, sortBy</p>
                </div>
                
                <div class="api-item">
                    <span class="method get">GET</span>
                    <strong>/api/products/{id}</strong> - 获取京东商品详情
                    <p>参数: id (商品ID)</p>
                </div>
                
                <div class="api-item">
                    <span class="method get">GET</span>
                    <strong>/api/search</strong> - 搜索京东商品
                    <p>参数: keyword, page, pageSize</p>
                </div>
                
                <div class="api-item">
                    <span class="method get">GET</span>
                    <strong>/api/categories</strong> - 获取京东分类列表
                    <p>参数: parentId (可选)</p>
                </div>
                
                <div class="api-item">
                    <span class="method post">POST</span>
                    <strong>/api/links/generate</strong> - 生成京东联盟链接
                    <p>参数: productId, unionId, positionId</p>
                </div>
                
                <div class="api-item">
                    <span class="method post">POST</span>
                    <strong>/api/sync</strong> - 手动同步数据
                    <p>参数: type (products|categories)</p>
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
                        <strong>版本:</strong> <span class="status-ok">{$config['version']}</span>
                    </div>
                    <div class="status-item">
                        <strong>PHP版本:</strong> <span class="status-ok"><?php echo PHP_VERSION; ?></span>
                    </div>
                    <div class="status-item">
                        <strong>好单库API:</strong> <span class="status-ok">已配置</span>
                    </div>
                    <div class="status-item">
                        <strong>缓存:</strong> <span class="status-ok">{$config['cache']['driver']}</span>
                    </div>
                </div>
            </div>
            
            <div class="api-section">
                <h2>🔗 快速链接</h2>
                <p>
                    <a href="/admin" style="color: #ff6b6b; text-decoration: none;">📊 管理后台</a> |
                    <a href="/api/test" style="color: #ff6b6b; text-decoration: none;">🔍 系统检测</a> |
                    <a href="/api/products?page=1&pageSize=5" style="color: #ff6b6b; text-decoration: none;">📦 商品示例</a>
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