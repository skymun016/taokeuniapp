<?php

// 手动加载类文件
spl_autoload_register(function ($class) {
    $base_dir = __DIR__ . '/src/';
    
    // 处理 JdAffiliateService 命名空间
    if (strpos($class, 'JdAffiliateService\\') === 0) {
        $relative_class = substr($class, strlen('JdAffiliateService\\'));
        $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
        if (file_exists($file)) {
            require $file;
            return;
        }
    }
    
    // 处理 JdAffiliate 命名空间
    if (strpos($class, 'JdAffiliate\\') === 0) {
        $relative_class = substr($class, strlen('JdAffiliate\\'));
        $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
        if (file_exists($file)) {
            require $file;
            return;
        }
    }
});

use JdAffiliateService\Services\CacheService;

echo "Testing CacheService...\n";

try {
    // 创建缓存服务实例
    $config = [
        'driver' => 'file',
        'prefix' => 'test_',
        'cache_dir' => __DIR__ . '/cache/test',
        'default_ttl' => 60
    ];
    
    $cache = new CacheService($config);
    
    // 测试基本功能
    echo "1. Testing set and get...\n";
    $testData = ['name' => 'John', 'age' => 30, 'active' => true];
    $result = $cache->set('user_123', $testData);
    echo "Set result: " . ($result ? 'SUCCESS' : 'FAILED') . "\n";
    
    $retrieved = $cache->get('user_123');
    echo "Retrieved data: " . json_encode($retrieved) . "\n";
    echo "Data matches: " . ($testData == $retrieved ? 'YES' : 'NO') . "\n";
    
    // 测试has方法
    echo "\n2. Testing has method...\n";
    echo "Key exists: " . ($cache->has('user_123') ? 'YES' : 'NO') . "\n";
    echo "Non-existent key: " . ($cache->has('non_existent') ? 'YES' : 'NO') . "\n";
    
    // 测试默认值
    echo "\n3. Testing default value...\n";
    $defaultValue = $cache->get('non_existent', 'default_value');
    echo "Default value: " . $defaultValue . "\n";
    
    // 测试删除
    echo "\n4. Testing delete...\n";
    $deleteResult = $cache->delete('user_123');
    echo "Delete result: " . ($deleteResult ? 'SUCCESS' : 'FAILED') . "\n";
    echo "Key exists after delete: " . ($cache->has('user_123') ? 'YES' : 'NO') . "\n";
    
    // 测试统计信息
    echo "\n5. Testing stats...\n";
    $cache->set('stat_test_1', 'value1');
    $cache->set('stat_test_2', 'value2');
    $stats = $cache->getStats();
    echo "Stats: " . json_encode($stats, JSON_PRETTY_PRINT) . "\n";
    
    // 清理测试
    echo "\n6. Testing cleanup...\n";
    $cache->clear();
    echo "Cache cleared\n";
    
    echo "\nAll tests completed successfully!\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}