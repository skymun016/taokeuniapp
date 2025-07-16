<?php
/**
 * 系统测试接口
 */

// 引入CORS配置
require_once __DIR__ . '/cors.php';

use Utils\Logger;
use Utils\Helper;
use Services\DatabaseService;
use Services\CacheService;

// 只允许GET请求
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    apiResponse(405, '只支持GET请求');
}

$logger = new Logger();
$startTime = microtime(true);

try {
    // 系统基础信息
    $systemInfo = [
        'service_name' => DTK_CONFIG['app_name'],
        'version' => DTK_CONFIG['version'],
        'php_version' => PHP_VERSION,
        'server_time' => date('Y-m-d H:i:s'),
        'timezone' => date_default_timezone_get(),
        'memory_usage' => formatBytes(memory_get_usage(true)),
        'memory_peak' => formatBytes(memory_get_peak_usage(true)),
    ];
    
    // 检查必需的PHP扩展
    $requiredExtensions = ['pdo', 'pdo_mysql', 'curl', 'json', 'mbstring'];
    $extensionStatus = [];
    
    foreach ($requiredExtensions as $ext) {
        $extensionStatus[$ext] = extension_loaded($ext);
    }
    
    // 检查目录权限
    $directories = [
        'cache' => CACHE_PATH,
        'logs' => LOGS_PATH,
    ];
    
    $directoryStatus = [];
    foreach ($directories as $name => $path) {
        $directoryStatus[$name] = [
            'exists' => is_dir($path),
            'writable' => is_writable($path),
            'path' => $path
        ];
    }
    
    // 检查数据库连接
    $databaseStatus = [];
    try {
        $db = DatabaseService::getInstance();
        $connection = $db->getConnection();
        
        // 测试查询
        $result = $db->fetchOne("SELECT 1 as test");
        
        $databaseStatus = [
            'connected' => true,
            'test_query' => $result['test'] == 1,
            'driver' => $connection->getAttribute(PDO::ATTR_DRIVER_NAME),
            'version' => $connection->getAttribute(PDO::ATTR_SERVER_VERSION),
        ];
        
        // 检查表是否存在
        $tables = ['goods', 'categories', 'sync_logs', 'platforms'];
        $tableStatus = [];
        
        foreach ($tables as $table) {
            $tableStatus[$table] = $db->tableExists($table);
        }
        
        $databaseStatus['tables'] = $tableStatus;
        
    } catch (Exception $e) {
        $databaseStatus = [
            'connected' => false,
            'error' => $e->getMessage()
        ];
    }
    
    // 检查缓存系统
    $cacheStatus = [];
    try {
        $cache = CacheService::getInstance();
        
        // 测试缓存读写
        $testKey = 'test_' . time();
        $testValue = 'test_value_' . rand(1000, 9999);
        
        $cache->set($testKey, $testValue, 60);
        $retrievedValue = $cache->get($testKey);
        $cache->delete($testKey);
        
        $cacheStatus = [
            'working' => $retrievedValue === $testValue,
            'stats' => $cache->getStats()
        ];
        
    } catch (Exception $e) {
        $cacheStatus = [
            'working' => false,
            'error' => $e->getMessage()
        ];
    }
    
    // 检查平台配置
    $platformStatus = [];
    $enabledPlatforms = Helper::getEnabledPlatforms();
    
    foreach (PLATFORM_CONFIG as $platform => $config) {
        $platformStatus[$platform] = [
            'name' => $config['name'],
            'enabled' => $config['enabled'],
            'configured' => !empty($config['app_key']) && !empty($config['app_secret']),
            'api_url' => $config['api_url']
        ];
    }
    
    // 检查大淘客SDK
    $sdkStatus = [];
    try {
        // 检查SDK文件是否存在
        $sdkPath = ROOT_PATH . '/vendor/dtk-developer/openapi-sdk-php';
        $sdkExists = is_dir($sdkPath);
        
        if ($sdkExists) {
            // 尝试加载SDK类
            require_once $sdkPath . '/api/DtkClient.php';
            $sdkStatus = [
                'installed' => true,
                'path' => $sdkPath,
                'client_class' => class_exists('DtkClient')
            ];
        } else {
            $sdkStatus = [
                'installed' => false,
                'message' => 'SDK未安装，请运行 composer install'
            ];
        }
        
    } catch (Exception $e) {
        $sdkStatus = [
            'installed' => false,
            'error' => $e->getMessage()
        ];
    }
    
    // 计算总体状态
    $overallStatus = 'healthy';
    $issues = [];
    
    // 检查关键组件
    if (!$databaseStatus['connected']) {
        $overallStatus = 'error';
        $issues[] = '数据库连接失败';
    }
    
    if (!$cacheStatus['working']) {
        $overallStatus = 'warning';
        $issues[] = '缓存系统异常';
    }
    
    if (!$sdkStatus['installed']) {
        $overallStatus = 'warning';
        $issues[] = '大淘客SDK未安装';
    }
    
    if (empty($enabledPlatforms)) {
        $overallStatus = 'warning';
        $issues[] = '没有启用的平台';
    }
    
    foreach ($extensionStatus as $ext => $loaded) {
        if (!$loaded) {
            $overallStatus = 'error';
            $issues[] = "缺少PHP扩展: {$ext}";
        }
    }
    
    // 构建响应数据
    $responseData = [
        'status' => $overallStatus,
        'issues' => $issues,
        'system' => $systemInfo,
        'extensions' => $extensionStatus,
        'directories' => $directoryStatus,
        'database' => $databaseStatus,
        'cache' => $cacheStatus,
        'platforms' => $platformStatus,
        'sdk' => $sdkStatus,
        'performance' => [
            'response_time' => Helper::calculateDuration($startTime) . 'ms',
            'enabled_platforms' => count($enabledPlatforms),
            'total_platforms' => count(PLATFORM_CONFIG)
        ]
    ];
    
    // 记录测试日志
    $logger->info('系统状态检测', [
        'status' => $overallStatus,
        'issues_count' => count($issues),
        'response_time' => Helper::calculateDuration($startTime)
    ]);
    
    apiResponse(0, '系统状态检测完成', $responseData);
    
} catch (Exception $e) {
    $logger->error('系统测试失败', [
        'error' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ]);
    
    apiResponse(500, '系统测试失败: ' . $e->getMessage());
}

/**
 * 格式化字节数
 */
function formatBytes($bytes) {
    $units = ['B', 'KB', 'MB', 'GB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    
    $bytes /= pow(1024, $pow);
    
    return round($bytes, 2) . ' ' . $units[$pow];
}
?>
