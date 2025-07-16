<?php
/**
 * 自动加载器
 */

// 定义基础路径
$vendorDir = __DIR__;
$baseDir = dirname($vendorDir);

// PSR-4 自动加载映射
$psr4Map = [
    'Services\\' => $baseDir . '/src/Services/',
    'Models\\' => $baseDir . '/src/Models/',
    'Utils\\' => $baseDir . '/src/Utils/',
];

// 注册自动加载器
spl_autoload_register(function ($class) use ($psr4Map) {
    foreach ($psr4Map as $prefix => $baseDir) {
        $len = strlen($prefix);
        if (strncmp($prefix, $class, $len) !== 0) {
            continue;
        }
        
        $relativeClass = substr($class, $len);
        $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
        
        if (file_exists($file)) {
            require $file;
            return true;
        }
    }
    
    return false;
});

// 加载大淘客SDK
$sdkPath = $vendorDir . '/dtk-developer/openapi-sdk-php';
if (is_dir($sdkPath)) {
    // 自动加载SDK中的类
    spl_autoload_register(function ($class) use ($sdkPath) {
        // 大淘客SDK的类通常在api目录下
        $file = $sdkPath . '/api/' . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return true;
        }
        
        return false;
    });
}

// 加载配置文件
$configFiles = [
    $baseDir . '/config/config.php',
    $baseDir . '/config/database.php',
];

foreach ($configFiles as $configFile) {
    if (file_exists($configFile)) {
        require_once $configFile;
    }
}
?>
