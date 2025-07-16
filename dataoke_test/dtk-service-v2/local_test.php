<?php
/**
 * 本地测试脚本
 */

// 引入配置文件
require_once __DIR__ . '/vendor/autoload.php';

use Utils\EnvLoader;
use Services\DataokeService;

// 加载环境变量
EnvLoader::load();

echo "=== 本地测试 ===\n";

// 测试数据库连接
echo "1. 测试数据库连接...\n";
try {
    $db = \Services\DatabaseService::getInstance();
    $result = $db->fetchOne("SELECT 1 as test");
    echo "✅ 数据库连接成功\n";
} catch (Exception $e) {
    echo "❌ 数据库连接失败: " . $e->getMessage() . "\n";
    exit(1);
}

// 测试大淘客 SDK
echo "\n2. 测试大淘客 SDK...\n";
try {
    $api = new GetGoodsList();
    $api->setAppKey(EnvLoader::get('TAOBAO_APP_KEY'));
    $api->setAppSecret(EnvLoader::get('TAOBAO_APP_SECRET'));
    $api->setVersion(EnvLoader::get('TAOBAO_VERSION'));
    $api->setParams(['pageId' => 1, 'pageSize' => 2]);
    
    $result = $api->request();
    echo "✅ SDK 调用成功\n";
    echo "返回数据类型: " . gettype($result) . "\n";
    
    if (is_string($result)) {
        $data = json_decode($result, true);
        if ($data) {
            echo "JSON 解析成功\n";
            echo "顶级键: " . implode(', ', array_keys($data)) . "\n";
            if (isset($data['data'])) {
                echo "data 键存在\n";
                if (is_array($data['data'])) {
                    echo "data 子键: " . implode(', ', array_keys($data['data'])) . "\n";
                }
            }
        }
    }
} catch (Exception $e) {
    echo "❌ SDK 调用失败: " . $e->getMessage() . "\n";
}

// 测试 DataokeService
echo "\n3. 测试 DataokeService...\n";
try {
    $service = new DataokeService('taobao');
    $result = $service->getGoodsList(['pageId' => 1, 'pageSize' => 2]);
    echo "✅ DataokeService 调用成功\n";
    echo "返回商品数量: " . count($result['list'] ?? []) . "\n";
    echo "总数: " . ($result['total'] ?? 0) . "\n";
} catch (Exception $e) {
    echo "❌ DataokeService 调用失败: " . $e->getMessage() . "\n";
}

echo "\n=== 测试完成 ===\n";
?>
