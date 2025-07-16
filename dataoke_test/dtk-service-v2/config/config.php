<?php
/**
 * 大淘客服务端 V2.0 - 主配置文件
 */

// 定义基础路径常量
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(__DIR__));
}
if (!defined('SRC_PATH')) {
    define('SRC_PATH', ROOT_PATH . '/src');
}
if (!defined('CACHE_PATH')) {
    define('CACHE_PATH', ROOT_PATH . '/cache');
}
if (!defined('LOGS_PATH')) {
    define('LOGS_PATH', ROOT_PATH . '/logs');
}

// 加载环境变量
require_once SRC_PATH . '/Utils/EnvLoader.php';
\Utils\EnvLoader::load();

// 基础配置
define('DTK_CONFIG', [
    'app_name' => \Utils\EnvLoader::get('APP_NAME', '大淘客服务端 V2.0'),
    'version' => \Utils\EnvLoader::get('APP_VERSION', '2.0.0'),
    'debug' => \Utils\EnvLoader::get('APP_DEBUG', true),
    'timezone' => \Utils\EnvLoader::get('APP_TIMEZONE', 'Asia/Shanghai'),
    
    // API配置
    'api' => [
        'default_page_size' => \Utils\EnvLoader::get('API_DEFAULT_PAGE_SIZE', 20),
        'max_page_size' => \Utils\EnvLoader::get('API_MAX_PAGE_SIZE', 200),
        'timeout' => \Utils\EnvLoader::get('API_TIMEOUT', 30),
        'rate_limit' => \Utils\EnvLoader::get('API_RATE_LIMIT', 1000), // 每小时请求限制
    ],
    
    // 缓存配置
    'cache' => [
        'driver' => \Utils\EnvLoader::get('CACHE_DRIVER', 'file'), // file, redis, memcache
        'path' => CACHE_PATH,
        'prefix' => \Utils\EnvLoader::get('CACHE_PREFIX', 'dtk_v2_'),
        'expire' => [
            'goods_list' => \Utils\EnvLoader::get('CACHE_EXPIRE_GOODS_LIST', 1800),    // 商品列表缓存30分钟
            'goods_detail' => \Utils\EnvLoader::get('CACHE_EXPIRE_GOODS_DETAIL', 3600),  // 商品详情缓存1小时
            'category' => \Utils\EnvLoader::get('CACHE_EXPIRE_CATEGORY', 7200),      // 分类缓存2小时
            'search' => \Utils\EnvLoader::get('CACHE_EXPIRE_SEARCH', 900),         // 搜索结果缓存15分钟
            'promotion' => \Utils\EnvLoader::get('CACHE_EXPIRE_PROMOTION', 86400),    // 推广链接缓存24小时
            'privilege_link' => \Utils\EnvLoader::get('CACHE_EXPIRE_PRIVILEGE_LINK', 300),  // 高效转链缓存5分钟
            'taokouling' => \Utils\EnvLoader::get('CACHE_EXPIRE_TAOKOULING', 600),    // 淘口令缓存10分钟
        ]
    ],
    
    // 日志配置
    'log' => [
        'level' => \Utils\EnvLoader::get('LOG_LEVEL', 'info'), // debug, info, warning, error
        'path' => LOGS_PATH,
        'max_files' => \Utils\EnvLoader::get('LOG_MAX_FILES', 30), // 保留30天日志
        'max_size' => \Utils\EnvLoader::get('LOG_MAX_SIZE', 10 * 1024 * 1024), // 单文件最大10MB
    ],
    
    // CORS配置
    'cors' => [
        'allowed_origins' => explode(',', \Utils\EnvLoader::get('CORS_ALLOWED_ORIGINS', '*')),
        'allowed_methods' => explode(',', \Utils\EnvLoader::get('CORS_ALLOWED_METHODS', 'GET,POST,PUT,DELETE,OPTIONS')),
        'allowed_headers' => explode(',', \Utils\EnvLoader::get('CORS_ALLOWED_HEADERS', 'Content-Type,Authorization,X-Requested-With')),
        'allow_credentials' => \Utils\EnvLoader::get('CORS_ALLOW_CREDENTIALS', true),
    ],
]);

// 多平台配置
define('PLATFORM_CONFIG', [
    'taobao' => [
        'name' => '淘宝/天猫',
        'enabled' => \Utils\EnvLoader::get('TAOBAO_ENABLED', true),
        'app_key' => \Utils\EnvLoader::get('TAOBAO_APP_KEY', '68768ef94834a'),
        'app_secret' => \Utils\EnvLoader::get('TAOBAO_APP_SECRET', 'f5a5707c8d7b69b8dbad1ec15506c3b1'),
        'pid' => \Utils\EnvLoader::get('TAOBAO_PID', 'mm_52162983_39758207_72877900030'),
        'version' => \Utils\EnvLoader::get('TAOBAO_VERSION', 'v1.2.4'),
        'api_url' => 'https://openapi.dataoke.com/api/',
        'timeout' => 30,
        'retry_times' => 3,
        'rate_limit' => 100, // 每分钟请求限制
    ],

    'jd' => [
        'name' => '京东',
        'enabled' => \Utils\EnvLoader::get('JD_ENABLED', false),
        'app_key' => \Utils\EnvLoader::get('JD_APP_KEY', ''),
        'app_secret' => \Utils\EnvLoader::get('JD_APP_SECRET', ''),
        'pid' => \Utils\EnvLoader::get('JD_PID', ''),
        'version' => \Utils\EnvLoader::get('JD_VERSION', 'v1.0.0'),
        'api_url' => 'https://openapi.dataoke.com/api/',
        'timeout' => 30,
        'retry_times' => 3,
        'rate_limit' => 100,
    ],

    'pdd' => [
        'name' => '拼多多',
        'enabled' => \Utils\EnvLoader::get('PDD_ENABLED', false),
        'app_key' => \Utils\EnvLoader::get('PDD_APP_KEY', ''),
        'app_secret' => \Utils\EnvLoader::get('PDD_APP_SECRET', ''),
        'pid' => \Utils\EnvLoader::get('PDD_PID', ''),
        'version' => \Utils\EnvLoader::get('PDD_VERSION', 'v1.0.0'),
        'api_url' => 'https://openapi.dataoke.com/api/',
        'timeout' => 30,
        'retry_times' => 3,
        'rate_limit' => 100,
    ],
]);

// 数据同步配置
define('SYNC_CONFIG', [
    'enabled' => \Utils\EnvLoader::get('SYNC_ENABLED', true),
    'batch_size' => \Utils\EnvLoader::get('SYNC_BATCH_SIZE', 100),        // 每批处理数量
    'max_pages' => \Utils\EnvLoader::get('SYNC_MAX_PAGES', 50),          // 最大页数限制
    'sleep_time' => \Utils\EnvLoader::get('SYNC_SLEEP_TIME', 1),          // 批次间隔时间（秒）
    'max_execution_time' => \Utils\EnvLoader::get('SYNC_MAX_EXECUTION_TIME', 3600), // 最大执行时间（秒）
    
    // 同步策略
    'strategy' => [
        'goods' => [
            'full_sync_hour' => 2,      // 每天凌晨2点全量同步
            'incremental_interval' => 30, // 增量同步间隔（分钟）
            'max_days' => 30,           // 保留最近30天数据
        ],
        'category' => [
            'sync_hour' => 3,           // 每天凌晨3点同步分类
            'cache_time' => 86400,      // 分类缓存24小时
        ]
    ],
    
    // 数据清理
    'cleanup' => [
        'expired_goods_days' => 7,      // 清理7天前失效商品
        'old_logs_days' => 30,          // 清理30天前日志
        'cache_cleanup_hour' => 4,      // 每天凌晨4点清理缓存
    ]
]);

// 数据映射配置
define('DATA_MAPPING', [
    'goods' => [
        'id' => 'id',
        'goods_id' => 'goodsId',
        'title' => 'title',
        'dtitle' => 'dtitle',
        'original_price' => 'originalPrice',
        'actual_price' => 'actualPrice',
        'coupon_price' => 'couponPrice',
        'commission_rate' => 'commissionRate',
        'month_sales' => 'monthSales',
        'main_pic' => 'mainPic',
        'item_link' => 'itemLink',
        'coupon_link' => 'couponLink',
        'shop_name' => 'shopName',
        'shop_type' => 'shopType',
        'brand_name' => 'brandName',
        'cid' => 'cid',
        'desc_score' => 'descScore',
        'service_score' => 'serviceScore',
        'ship_score' => 'shipScore',
        'platform' => 'platform',
        'price' => 'actualPrice',
        'commission' => 'commissionRate',
        'created_at' => 'createTime',
    ],
    
    'category' => [
        'cid' => 'cid',
        'cname' => 'cname',
        'parent_id' => 'parentId',
        'level' => 'level',
    ]
]);

// 错误码定义
define('ERROR_CODES', [
    'SUCCESS' => 0,
    'PARAM_ERROR' => 1001,
    'API_ERROR' => 1002,
    'DATABASE_ERROR' => 1003,
    'CACHE_ERROR' => 1004,
    'NETWORK_ERROR' => 1005,
    'AUTH_ERROR' => 1006,
    'RATE_LIMIT_ERROR' => 1007,
    'SYSTEM_ERROR' => 1008,
]);

// 创建必要的目录
$dirs = [CACHE_PATH, LOGS_PATH];
foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}

// 设置错误处理
set_error_handler(function($severity, $message, $file, $line) {
    if (!(error_reporting() & $severity)) {
        return false;
    }
    
    $logger = new \Utils\Logger();
    $logger->error("PHP Error: {$message}", [
        'file' => $file,
        'line' => $line,
        'severity' => $severity
    ]);
    
    return true;
});

// 设置异常处理
set_exception_handler(function($exception) {
    $logger = new \Utils\Logger();
    $logger->error("Uncaught Exception: " . $exception->getMessage(), [
        'file' => $exception->getFile(),
        'line' => $exception->getLine(),
        'trace' => $exception->getTraceAsString()
    ]);
    
    if (DTK_CONFIG['debug']) {
        echo "Exception: " . $exception->getMessage() . "\n";
        echo "File: " . $exception->getFile() . ":" . $exception->getLine() . "\n";
    }
});
?>
