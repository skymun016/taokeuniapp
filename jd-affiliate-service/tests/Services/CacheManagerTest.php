<?php

use PHPUnit\Framework\TestCase;
use JdAffiliateService\Services\CacheService;
use JdAffiliateService\Services\CacheManager;

class CacheManagerTest extends TestCase
{
    private $cacheService;
    private $cacheManager;
    private $testCacheDir;
    
    protected function setUp(): void
    {
        $this->testCacheDir = __DIR__ . '/../../cache/test';
        $config = [
            'driver' => 'file',
            'prefix' => 'test_',
            'cache_dir' => $this->testCacheDir,
            'default_ttl' => 60
        ];
        
        $this->cacheService = new CacheService($config);
        $this->cacheManager = new CacheManager($this->cacheService);
    }
    
    protected function tearDown(): void
    {
        // 清理测试缓存
        if ($this->cacheService) {
            $this->cacheService->clear();
        }
        
        // 删除测试缓存目录
        if (is_dir($this->testCacheDir)) {
            $this->removeDirectory($this->testCacheDir);
        }
    }
    
    public function testProductListCaching()
    {
        $params = [
            'page' => 1,
            'pageSize' => 20,
            'categoryId' => '1001'
        ];
        
        $testData = [
            'list' => [
                ['id' => 1, 'name' => 'Product 1'],
                ['id' => 2, 'name' => 'Product 2']
            ],
            'total' => 2
        ];
        
        // 测试设置缓存
        $result = $this->cacheManager->setProductList($params, $testData);
        $this->assertTrue($result);
        
        // 测试获取缓存
        $cachedData = $this->cacheManager->getProductList($params);
        $this->assertEquals($testData, $cachedData);
    }
    
    public function testProductDetailCaching()
    {
        $productId = '123456';
        $testData = [
            'id' => $productId,
            'name' => 'Test Product',
            'price' => 99.99
        ];
        
        // 测试设置缓存
        $result = $this->cacheManager->setProductDetail($productId, $testData);
        $this->assertTrue($result);
        
        // 测试获取缓存
        $cachedData = $this->cacheManager->getProductDetail($productId);
        $this->assertEquals($testData, $cachedData);
    }
    
    public function testSearchResultsCaching()
    {
        $keyword = 'test keyword';
        $params = ['page' => 1, 'pageSize' => 10];
        $testData = [
            'results' => [
                ['id' => 1, 'name' => 'Search Result 1']
            ],
            'total' => 1
        ];
        
        // 测试设置缓存
        $result = $this->cacheManager->setSearchResults($keyword, $params, $testData);
        $this->assertTrue($result);
        
        // 测试获取缓存
        $cachedData = $this->cacheManager->getSearchResults($keyword, $params);
        $this->assertEquals($testData, $cachedData);
    }
    
    public function testCategoriesCaching()
    {
        $testData = [
            ['id' => 1, 'name' => 'Category 1'],
            ['id' => 2, 'name' => 'Category 2']
        ];
        
        // 测试设置缓存
        $result = $this->cacheManager->setCategories($testData);
        $this->assertTrue($result);
        
        // 测试获取缓存
        $cachedData = $this->cacheManager->getCategories();
        $this->assertEquals($testData, $cachedData);
    }
    
    public function testAffiliateLinkCaching()
    {
        $productId = '123456';
        $params = ['unionId' => 'test_union', 'positionId' => 'test_position'];
        $testData = [
            'shortUrl' => 'https://u.jd.com/test',
            'longUrl' => 'https://item.jd.com/123456.html'
        ];
        
        // 测试设置缓存
        $result = $this->cacheManager->setAffiliateLink($productId, $params, $testData);
        $this->assertTrue($result);
        
        // 测试获取缓存
        $cachedData = $this->cacheManager->getAffiliateLink($productId, $params);
        $this->assertEquals($testData, $cachedData);
    }
    
    public function testApiResponseCaching()
    {
        $endpoint = '/api/test';
        $params = ['param1' => 'value1'];
        $testData = ['result' => 'success'];
        
        // 测试设置缓存
        $result = $this->cacheManager->setApiResponse($endpoint, $params, $testData);
        $this->assertTrue($result);
        
        // 测试获取缓存
        $cachedData = $this->cacheManager->getApiResponse($endpoint, $params);
        $this->assertEquals($testData, $cachedData);
    }
    
    public function testCacheWithCallback()
    {
        $params = ['page' => 1, 'pageSize' => 10];
        $expectedData = ['test' => 'callback data'];
        
        $callback = function() use ($expectedData) {
            return $expectedData;
        };
        
        // 第一次调用应该执行回调并缓存结果
        $result = $this->cacheManager->getProductList($params, $callback);
        $this->assertEquals($expectedData, $result);
        
        // 第二次调用应该从缓存获取
        $cachedResult = $this->cacheManager->getProductList($params);
        $this->assertEquals($expectedData, $cachedResult);
    }
    
    public function testBatchUpdate()
    {
        $updates = [
            'product_1' => ['id' => 1, 'name' => 'Product 1'],
            'product_2' => ['id' => 2, 'name' => 'Product 2'],
            'product_3' => ['id' => 3, 'name' => 'Product 3']
        ];
        
        $result = $this->cacheManager->batchUpdate('product_detail', $updates);
        $this->assertEquals(3, $result);
        
        // 验证批量更新的数据
        foreach ($updates as $key => $data) {
            $productId = str_replace('product_', '', $key);
            $cachedData = $this->cacheManager->getProductDetail($productId);
            $this->assertEquals($data, $cachedData);
        }
    }
    
    public function testWarmupCache()
    {
        $items = ['item1', 'item2', 'item3'];
        
        $callback = function($batch) {
            $result = [];
            foreach ($batch as $item) {
                $result[$item] = ['data' => $item . '_data'];
            }
            return $result;
        };
        
        // 测试预热缓存（categories支持预加载）
        $result = $this->cacheManager->warmupCache('categories', $items, $callback);
        $this->assertEquals(3, $result);
    }
    
    public function testGetCacheStats()
    {
        $stats = $this->cacheManager->getCacheStats();
        
        $this->assertIsArray($stats);
        $this->assertArrayHasKey('strategies', $stats);
        $this->assertArrayHasKey('cache_service_stats', $stats);
        $this->assertArrayHasKey('types', $stats);
        
        // 验证策略配置
        $this->assertArrayHasKey('product_list', $stats['strategies']);
        $this->assertArrayHasKey('product_detail', $stats['strategies']);
        $this->assertArrayHasKey('search_results', $stats['strategies']);
        $this->assertArrayHasKey('categories', $stats['strategies']);
        $this->assertArrayHasKey('affiliate_links', $stats['strategies']);
        $this->assertArrayHasKey('api_responses', $stats['strategies']);
    }
    
    public function testUpdateStrategy()
    {
        $newConfig = [
            'ttl' => 7200,
            'batch_size' => 100
        ];
        
        $result = $this->cacheManager->updateStrategy('product_list', $newConfig);
        $this->assertTrue($result);
        
        $stats = $this->cacheManager->getCacheStats();
        $this->assertEquals(7200, $stats['strategies']['product_list']['ttl']);
        $this->assertEquals(100, $stats['strategies']['product_list']['batch_size']);
    }
    
    public function testInvalidCacheType()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Unknown cache type: invalid_type');
        
        $this->cacheManager->updateStrategy('invalid_type', []);
    }
    
    public function testCacheKeyGeneration()
    {
        // 测试相同参数生成相同的缓存键
        $params1 = ['page' => 1, 'pageSize' => 20, 'categoryId' => '1001'];
        $params2 = ['page' => 1, 'pageSize' => 20, 'categoryId' => '1001'];
        
        $testData = ['test' => 'data'];
        
        $this->cacheManager->setProductList($params1, $testData);
        $cachedData = $this->cacheManager->getProductList($params2);
        
        $this->assertEquals($testData, $cachedData);
        
        // 测试不同参数生成不同的缓存键
        $params3 = ['page' => 2, 'pageSize' => 20, 'categoryId' => '1001'];
        $cachedData2 = $this->cacheManager->getProductList($params3);
        
        $this->assertNull($cachedData2);
    }
    
    public function testCallbackException()
    {
        $params = ['page' => 1];
        
        $callback = function() {
            throw new Exception('Callback error');
        };
        
        $result = $this->cacheManager->getProductList($params, $callback);
        $this->assertNull($result);
    }
    
    /**
     * 递归删除目录
     */
    private function removeDirectory($dir)
    {
        if (!is_dir($dir)) {
            return;
        }
        
        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            $path = $dir . '/' . $file;
            if (is_dir($path)) {
                $this->removeDirectory($path);
            } else {
                unlink($path);
            }
        }
        rmdir($dir);
    }
}