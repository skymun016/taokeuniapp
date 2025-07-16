<?php
/**
 * 调试大淘客 API 调用
 */

// 引入配置文件
require_once __DIR__ . '/vendor/autoload.php';

use Utils\EnvLoader;

// 加载环境变量
EnvLoader::load();

echo "<h2>大淘客 API 调试</h2>";

// 显示配置信息
echo "<h3>配置信息</h3>";
echo "APP_KEY: " . EnvLoader::get('TAOBAO_APP_KEY') . "<br>";
echo "APP_SECRET: " . substr(EnvLoader::get('TAOBAO_APP_SECRET'), 0, 10) . "...<br>";
echo "PID: " . EnvLoader::get('TAOBAO_PID') . "<br>";
echo "VERSION: " . EnvLoader::get('TAOBAO_VERSION') . "<br>";

// 检查 SDK 是否可用
echo "<h3>SDK 检查</h3>";
if (class_exists('DtkClient')) {
    echo "✅ DtkClient 类已加载<br>";
} else {
    echo "❌ DtkClient 类未找到<br>";
}

if (class_exists('GetGoodsList')) {
    echo "✅ GetGoodsList 类已加载<br>";
} else {
    echo "❌ GetGoodsList 类未找到<br>";
}

// 尝试直接调用 API
echo "<h3>API 调用测试</h3>";
try {
    if (!class_exists('DtkClient') || !class_exists('GetGoodsList')) {
        throw new Exception('SDK 类未正确加载');
    }
    
    $appKey = EnvLoader::get('TAOBAO_APP_KEY');
    $appSecret = EnvLoader::get('TAOBAO_APP_SECRET');
    
    echo "创建 API 实例...<br>";
    $api = new GetGoodsList();

    echo "设置 appKey 和 appSecret...<br>";
    $api->setAppKey($appKey);
    $api->setAppSecret($appSecret);

    echo "设置版本号...<br>";
    $api->setVersion(EnvLoader::get('TAOBAO_VERSION'));

    echo "设置参数...<br>";
    $params = [
        'pageId' => 1,
        'pageSize' => 5,  // 只获取5个商品用于测试
    ];
    $api->setParams($params);

    echo "调用 API...<br>";
    $result = $api->request();
    
    echo "<h4>API 返回结果：</h4>";
    echo "<pre>";
    print_r($result);
    echo "</pre>";
    
    // 检查返回数据结构
    if (is_array($result)) {
        echo "<h4>数据结构分析：</h4>";
        echo "返回类型: " . gettype($result) . "<br>";
        echo "顶级键: " . implode(', ', array_keys($result)) . "<br>";
        
        if (isset($result['data'])) {
            echo "data 字段存在<br>";
            if (isset($result['data']['list'])) {
                echo "data.list 字段存在，商品数量: " . count($result['data']['list']) . "<br>";
            } else {
                echo "data.list 字段不存在<br>";
                echo "data 字段内容: <pre>" . print_r($result['data'], true) . "</pre>";
            }
        } else {
            echo "data 字段不存在<br>";
        }
    } else {
        echo "返回结果不是数组类型: " . gettype($result) . "<br>";
    }
    
} catch (Exception $e) {
    echo "❌ API 调用失败: " . $e->getMessage() . "<br>";
    echo "错误文件: " . $e->getFile() . ":" . $e->getLine() . "<br>";
    echo "错误堆栈: <pre>" . $e->getTraceAsString() . "</pre>";
}

// 测试我们的 DataokeService
echo "<h3>DataokeService 测试</h3>";
try {
    $dataokeService = new \Services\DataokeService('taobao');

    $params = [
        'pageId' => 1,
        'pageSize' => 3,
    ];

    echo "调用 DataokeService::getGoodsList()...<br>";
    $result = $dataokeService->getGoodsList($params);

    echo "<h4>DataokeService 返回结果：</h4>";
    echo "<pre>";
    print_r($result);
    echo "</pre>";

} catch (Exception $e) {
    echo "❌ DataokeService 调用失败: " . $e->getMessage() . "<br>";
    echo "错误文件: " . $e->getFile() . ":" . $e->getLine() . "<br>";
}

// 检查 SDK 文件
echo "<h3>SDK 文件检查</h3>";
$sdkPath = __DIR__ . '/vendor/dtk-developer/openapi-sdk-php';
echo "SDK 路径: " . $sdkPath . "<br>";
echo "SDK 目录存在: " . (is_dir($sdkPath) ? '是' : '否') . "<br>";

if (is_dir($sdkPath)) {
    $apiDir = $sdkPath . '/api';
    echo "API 目录存在: " . (is_dir($apiDir) ? '是' : '否') . "<br>";
    
    if (is_dir($apiDir)) {
        $files = scandir($apiDir);
        echo "API 目录文件: " . implode(', ', array_filter($files, function($f) { return $f !== '.' && $f !== '..'; })) . "<br>";
    }
}

?>
