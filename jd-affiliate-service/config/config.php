<?php
/**
 * 京东联盟服务 - 主配置文件
 */

// 定义基础路径常量
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(__DIR__));
}
if (!defined('SRC_PATH')) {
    define('SRC_PATH', ROOT_PATH . '/src');
}
if (!defined('CONFIG_PATH')) {
    define('CONFIG_PATH', ROOT_PATH . '/config');
}
if (!defined('CACHE_PATH')) {
    define('CACHE_PATH', ROOT_PATH . '/cache');
}
if (!defined('LOGS_PATH')) {
    define('LOGS_PATH', ROOT_PATH . '/logs');
}

// 加载环境变量
require_once SRC_PATH . '/Utils/EnvLoader.php';
\JdAffiliate\Utils\EnvLoader::load();

// 基础配置
define('APP_CONFIG', [
    'app_name' => \JdAffiliate\Utils\EnvLoader::get('APP_NAME', '京东联盟服务'),
    'version' => \JdAffiliate\Utils\EnvLoader::get('APP_VERSION', '1.0.0'),
    'debug' => \JdAffiliate\Utils\EnvLoader::get('APP_DEBUG', true),
    'timezone' => \JdAffiliate\Utils\EnvLoader::get('APP_TIMEZONE', 'Asia/Shanghai'),
    
    // API配置
    'api' => [
        'default_page_size' => \JdAffiliate\Utils\EnvLoader::get('API_DEFAULT_PAGE_SIZE', 20),
        'max_page_size' => \JdAffiliate\Utils\EnvLoader::get('API_MAX_PAGE_SIZE', 100),
        'timeout' => \JdAffiliate\Utils\EnvLoader::get('API_TIMEOUT', 30),
        'rate_limit' => \JdAffiliate\Utils\EnvLoader::get('API_RATE_LIMIT', 1000), // 每小时请求限制
    ],
    
    // 缓存配置
    'cache' => [
        'driver' => \JdAffiliate\Utils\EnvLoader::get('CACHE_DRIVER', 'file'), // file, redis
        'path' => \JdAffiliate\Utils\EnvLoader::get('CACHE_PATH', CACHE_PATH),
        'prefix' => \JdAffiliate\Utils\EnvLoader::get('CACHE_PREFIX', 'jd_affiliate_'),
        'expire' => [
            'product_list' => \JdAffiliate\Utils\EnvLoader::get('CACHE_EXPIRE_PRODUCT_LIST', 1800),    // 商品列表缓存30分钟
            'product_detail' => \JdAffiliate\Utils\EnvLoader::get('CACHE_EXPIRE_PRODUCT_DETAIL', 3600),  // 商品详情缓存1小时
            'category' => \JdAffiliate\Utils\EnvLoader::get('CACHE_EXPIRE_CATEGORY', 7200),      // 分类缓存2小时
            'search' => \JdAffiliate\Utils\EnvLoader::get('CACHE_EXPIRE_SEARCH', 900),         // 搜索结果缓存15分钟
            'affiliate_link' => \JdAffiliate\Utils\EnvLoader::get('CACHE_EXPIRE_AFFILIATE_LINK', 300),  // 联盟链接缓存5分钟
        ]
    ],
    
    // 日志配置
    'log' => [
        'level' => \JdAffiliate\Utils\EnvLoader::get('LOG_LEVEL', 'info'), // debug, info, warning, error
        'path' => \JdAffiliate\Utils\EnvLoader::get('LOG_PATH', LOGS_PATH),
        'max_files' => \JdAffiliate\Utils\EnvLoader::get('LOG_MAX_FILES', 30), // 保留30天日志
        'max_size' => \JdAffiliate\Utils\EnvLoader::get('LOG_MAX_SIZE', 10 * 1024 * 1024), // 单文件最大10MB
    ],
    
    // CORS配置
    'cors' => [
        'allowed_origins' => explode(',', \JdAffiliate\Utils\EnvLoader::get('CORS_ALLOWED_ORIGINS', '*')),
        'allowed_methods' => explode(',', \JdAffiliate\Utils\EnvLoader::get('CORS_ALLOWED_METHODS', 'GET,POST,PUT,DELETE,OPTIONS')),
        'allowed_headers' => explode(',', \JdAffiliate\Utils\EnvLoader::get('CORS_ALLOWED_HEADERS', 'Content-Type,Authorization,X-Requested-With')),
        'allow_credentials' => \JdAffiliate\Utils\EnvLoader::get('CORS_ALLOW_CREDENTIALS', true),
    ],
]);

// 好单库API配置
define('HAODANKU_CONFIG', [
    'api_key' => \JdAffiliate\Utils\EnvLoader::get('HAODANKU_API_KEY', '7902F044597C'),
    'api_url' => \JdAffiliate\Utils\EnvLoader::get('HAODANKU_API_URL', 'https://api.haodanku.com'),
    'timeout' => \JdAffiliate\Utils\EnvLoader::get('HAODANKU_TIMEOUT', 30),
    'retry_times' => \JdAffiliate\Utils\EnvLoader::get('HAODANKU_RETRY_TIMES', 3),
    'rate_limit' => 100, // 每分钟请求限制
]);

// 京东联盟配置
define('JD_UNION_CONFIG', [
    'union_id' => \JdAffiliate\Utils\EnvLoader::get('JD_UNION_ID', ''),
    'position_id' => \JdAffiliate\Utils\EnvLoader::get('JD_POSITION_ID', ''),
    'site_id' => \JdAffiliate\Utils\EnvLoader::get('JD_SITE_ID', ''),
]);

// 数据同步配置
define('SYNC_CONFIG', [
    'enabled' => \JdAffiliate\Utils\EnvLoader::get('SYNC_ENABLED', true),
    'batch_size' => \JdAffiliate\Utils\EnvLoader::get('SYNC_BATCH_SIZE', 100),        // 每批处理数量
    'max_pages' => \JdAffiliate\Utils\EnvLoader::get('SYNC_MAX_PAGES', 50),          // 最大页数限制
    'sleep_time' => \JdAffiliate\Utils\EnvLoader::get('SYNC_SLEEP_TIME', 1),          // 批次间隔时间（秒）
    'max_execution_time' => \JdAffiliate\Utils\EnvLoader::get('SYNC_MAX_EXECUTION_TIME', 3600), // 最大执行时间（秒）
    
    // 同步策略
    'strategy' => [
        'products' => [
            'full_sync_hour' => 2,      // 每天凌晨2点全量同步
            'incremental_interval' => 30, // 增量同步间隔（分钟）
            'max_days' => 30,           // 保留最近30天数据
        ],
        'categories' => [
            'sync_hour' => 3,           // 每天凌晨3点同步分类
            'cache_time' => 86400,      // 分类缓存24小时
        ]
    ],
    
    // 数据清理
    'cleanup' => [
        'expired_products_days' => 7,   // 清理7天前失效商品
        'old_logs_days' => 30,          // 清理30天前日志
        'cache_cleanup_hour' => 4,      // 每天凌晨4点清理缓存
    ]
]);

// 错误码定义
define('ERROR_CODES', [
    'SUCCESS' => 1,
    'PARAM_ERROR' => 1001,
    'API_ERROR' => 1002,
    'DATABASE_ERROR' => 1003,
    'CACHE_ERROR' => 1004,
    'NETWORK_ERROR' => 1005,
    'AUTH_ERROR' => 1006,
    'RATE_LIMIT_ERROR' => 1007,
    'PRODUCT_NOT_FOUND' => 1008,
    'LINK_GENERATE_ERROR' => 1009,
    'SYSTEM_ERROR' => 9999,
]);

// 创建必要的目录
$dirs = [CACHE_PATH, LOGS_PATH];
foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}

// 设置时区
date_default_timezone_set(APP_CONFIG['timezone']);

// 设置错误处理
set_error_handler(function($severity, $message, $file, $line) {
    if (!(error_reporting() & $severity)) {
        return false;
    }
    
    if (class_exists('\JdAffiliate\Utils\Logger')) {
        $logger = new \JdAffiliate\Utils\Logger();
        $logger->error("PHP Error: {$message}", [
            'file' => $file,
            'line' => $line,
            'severity' => $severity
        ]);
    }
    
    return true;
});

// 设置异常处理
set_exception_handler(function($exception) {
    if (class_exists('\JdAffiliate\Utils\Logger')) {
        $logger = new \JdAffiliate\Utils\Logger();
        $logger->error("Uncaught Exception: " . $exception->getMessage(), [
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString()
        ]);
    }
    
    if (APP_CONFIG['debug']) {
        echo "Exception: " . $exception->getMessage() . "\n";
        echo "File: " . $exception->getFile() . ":" . $exception->getLine() . "\n";
    }
});
?>