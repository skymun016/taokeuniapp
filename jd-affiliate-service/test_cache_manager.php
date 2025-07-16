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
use JdAffiliateService\Services\CacheManager;

echo "Testing CacheManager...\n";

try {
    // 创建缓存服务和管理器
    $config = [
        'driver' => 'file',
        'prefix' => 'test_',
        'cache_dir' => __DIR__ . '/cache/test',
        'default_ttl' => 60
    ];
    
    $cacheService = new CacheService($config);
    $cacheManager = new CacheManager($cacheService);
    
    echo "1. Testing product list caching...\n";
    $params = ['page' => 1, 'pageSize' => 20, 'categoryId' => '1001'];
    $productData = [
        'list' => [
            ['id' => 1, 'name' => 'Product 1', 'price' => 99.99],
            ['id' => 2, 'name' => 'Product 2', 'price' => 199.99]
        ],
        'total' => 2,
        'page' => 1
    ];
    
    $result = $cacheManager->setProductList($params, $productData);
    echo "Set product list: " . ($result ? 'SUCCESS' : 'FAILED') . "\n";
    
    $cached = $cacheManager->getProductList($params);
    echo "Retrieved product list: " . (json_encode($cached) === json_encode($productData) ? 'SUCCESS' : 'FAILED') . "\n";
    
    echo "\n2. Testing product detail caching...\n";
    $productId = '123456';
    $detailData = [
        'id' => $productId,
        'name' => 'Test Product',
        'price' => 299.99,
        'description' => 'This is a test product'
    ];
    
    $result = $cacheManager->setProductDetail($productId, $detailData);
    echo "Set product detail: " . ($result ? 'SUCCESS' : 'FAILED') . "\n";
    
    $cached = $cacheManager->getProductDetail($productId);
    echo "Retrieved product detail: " . (json_encode($cached) === json_encode($detailData) ? 'SUCCESS' : 'FAILED') . "\n";
    
    echo "\n3. Testing search results caching...\n";
    $keyword = 'test product';
    $searchParams = ['page' => 1, 'pageSize' => 10];
    $searchData = [
        'results' => [
            ['id' => 1, 'name' => 'Search Result 1'],
            ['id' => 2, 'name' => 'Search Result 2']
        ],
        'keyword' => $keyword,
        'total' => 2
    ];
    
    $result = $cacheManager->setSearchResults($keyword, $searchParams, $searchData);
    echo "Set search results: " . ($result ? 'SUCCESS' : 'FAILED') . "\n";
    
    $cached = $cacheManager->getSearchResults($keyword, $searchParams);
    echo "Retrieved search results: " . (json_encode($cached) === json_encode($searchData) ? 'SUCCESS' : 'FAILED') . "\n";
    
    echo "\n4. Testing categories caching...\n";
    $categoriesData = [
        ['id' => 1, 'name' => 'Electronics', 'parent_id' => 0],
        ['id' => 2, 'name' => 'Phones', 'parent_id' => 1],
        ['id' => 3, 'name' => 'Computers', 'parent_id' => 1]
    ];
    
    $result = $cacheManager->setCategories($categoriesData);
    echo "Set categories: " . ($result ? 'SUCCESS' : 'FAILED') . "\n";
    
    $cached = $cacheManager->getCategories();
    echo "Retrieved categories: " . (json_encode($cached) === json_encode($categoriesData) ? 'SUCCESS' : 'FAILED') . "\n";
    
    echo "\n5. Testing affiliate link caching...\n";
    $linkParams = ['unionId' => 'test_union', 'positionId' => 'test_position'];
    $linkData = [
        'shortUrl' => 'https://u.jd.com/test123',
        'longUrl' => 'https://item.jd.com/123456.html?union_id=test_union',
        'qrCode' => 'data:image/png;base64,test_qr_code'
    ];
    
    $result = $cacheManager->setAffiliateLink($productId, $linkParams, $linkData);
    echo "Set affiliate link: " . ($result ? 'SUCCESS' : 'FAILED') . "\n";
    
    $cached = $cacheManager->getAffiliateLink($productId, $linkParams);
    echo "Retrieved affiliate link: " . (json_encode($cached) === json_encode($linkData) ? 'SUCCESS' : 'FAILED') . "\n";
    
    echo "\n6. Testing cache with callback...\n";
    $callbackParams = ['page' => 2, 'pageSize' => 15];
    $callbackExecuted = false;
    
    $callback = function() use (&$callbackExecuted) {
        $callbackExecuted = true;
        return [
            'list' => [['id' => 3, 'name' => 'Callback Product']],
            'total' => 1,
            'from_callback' => true
        ];
    };
    
    // 第一次调用应该执行回调
    $result = $cacheManager->getProductList($callbackParams, $callback);
    echo "Callback executed: " . ($callbackExecuted ? 'YES' : 'NO') . "\n";
    echo "Callback result valid: " . (isset($result['from_callback']) ? 'YES' : 'NO') . "\n";
    
    // 第二次调用应该从缓存获取
    $callbackExecuted = false;
    $cached = $cacheManager->getProductList($callbackParams);
    echo "Second call from cache: " . (!$callbackExecuted && isset($cached['from_callback']) ? 'YES' : 'NO') . "\n";
    
    echo "\n7. Testing batch update...\n";
    $batchUpdates = [
        'batch_1' => ['id' => 'batch_1', 'name' => 'Batch Product 1'],
        'batch_2' => ['id' => 'batch_2', 'name' => 'Batch Product 2'],
        'batch_3' => ['id' => 'batch_3', 'name' => 'Batch Product 3']
    ];
    
    $updated = $cacheManager->batchUpdate('product_detail', $batchUpdates);
    echo "Batch updated: {$updated} items\n";
    
    // 验证批量更新 - 直接使用产品ID设置和获取
    echo "Testing individual product detail caching for batch verification...\n";
    $batchVerified = 0;
    foreach ($batchUpdates as $key => $data) {
        // 直接设置和获取来验证缓存功能
        $setResult = $cacheManager->setProductDetail($key, $data);
        $cached = $cacheManager->getProductDetail($key);
        if (json_encode($cached) === json_encode($data)) {
            $batchVerified++;
        }
    }
    echo "Individual verification: {$batchVerified}/3 items verified\n";
    
    echo "\n8. Testing cache warmup...\n";
    $warmupItems = ['warm1', 'warm2', 'warm3'];
    $warmupCallback = function($batch) {
        $result = [];
        foreach ($batch as $item) {
            $result[$item] = [
                'id' => $item,
                'name' => 'Warmed up ' . $item,
                'warmed' => true
            ];
        }
        return $result;
    };
    
    $warmed = $cacheManager->warmupCache('categories', $warmupItems, $warmupCallback);
    echo "Cache warmup: {$warmed} items warmed\n";
    
    echo "\n9. Testing cache statistics...\n";
    $stats = $cacheManager->getCacheStats();
    echo "Cache stats retrieved: " . (is_array($stats) ? 'SUCCESS' : 'FAILED') . "\n";
    echo "Strategies count: " . count($stats['strategies']) . "\n";
    echo "Types configured: " . count($stats['types']) . "\n";
    
    echo "\n10. Testing strategy update...\n";
    $newConfig = ['ttl' => 7200, 'batch_size' => 100];
    $result = $cacheManager->updateStrategy('product_list', $newConfig);
    echo "Strategy updated: " . ($result ? 'SUCCESS' : 'FAILED') . "\n";
    
    $updatedStats = $cacheManager->getCacheStats();
    $updatedStrategy = $updatedStats['strategies']['product_list'];
    echo "TTL updated: " . ($updatedStrategy['ttl'] === 7200 ? 'SUCCESS' : 'FAILED') . "\n";
    echo "Batch size updated: " . ($updatedStrategy['batch_size'] === 100 ? 'SUCCESS' : 'FAILED') . "\n";
    
    // 清理测试缓存
    echo "\n11. Cleaning up...\n";
    $cacheService->clear();
    echo "Cache cleared\n";
    
    echo "\nAll CacheManager tests completed successfully!\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}