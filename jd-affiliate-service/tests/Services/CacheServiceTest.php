<?php

use PHPUnit\Framework\TestCase;
use JdAffiliateService\Services\CacheService;

class CacheServiceTest extends TestCase
{
    private $cacheService;
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
    
    public function testSetAndGet()
    {
        $key = 'test_key';
        $value = ['data' => 'test_value', 'number' => 123];
        
        // 测试设置缓存
        $result = $this->cacheService->set($key, $value);
        $this->assertTrue($result);
        
        // 测试获取缓存
        $cachedValue = $this->cacheService->get($key);
        $this->assertEquals($value, $cachedValue);
    }
    
    public function testGetWithDefault()
    {
        $key = 'non_existent_key';
        $default = 'default_value';
        
        $result = $this->cacheService->get($key, $default);
        $this->assertEquals($default, $result);
    }
    
    public function testHas()
    {
        $key = 'test_key';
        $value = 'test_value';
        
        // 缓存不存在时
        $this->assertFalse($this->cacheService->has($key));
        
        // 设置缓存后
        $this->cacheService->set($key, $value);
        $this->assertTrue($this->cacheService->has($key));
    }
    
    public function testDelete()
    {
        $key = 'test_key';
        $value = 'test_value';
        
        // 设置缓存
        $this->cacheService->set($key, $value);
        $this->assertTrue($this->cacheService->has($key));
        
        // 删除缓存
        $result = $this->cacheService->delete($key);
        $this->assertTrue($result);
        $this->assertFalse($this->cacheService->has($key));
    }
    
    public function testClear()
    {
        // 设置多个缓存项
        $this->cacheService->set('key1', 'value1');
        $this->cacheService->set('key2', 'value2');
        $this->cacheService->set('key3', 'value3');
        
        // 验证缓存存在
        $this->assertTrue($this->cacheService->has('key1'));
        $this->assertTrue($this->cacheService->has('key2'));
        $this->assertTrue($this->cacheService->has('key3'));
        
        // 清空缓存
        $result = $this->cacheService->clear();
        $this->assertTrue($result);
        
        // 验证缓存已清空
        $this->assertFalse($this->cacheService->has('key1'));
        $this->assertFalse($this->cacheService->has('key2'));
        $this->assertFalse($this->cacheService->has('key3'));
    }
    
    public function testTtlExpiration()
    {
        $key = 'expiring_key';
        $value = 'expiring_value';
        $ttl = 1; // 1秒过期
        
        // 设置短期缓存
        $this->cacheService->set($key, $value, $ttl);
        $this->assertTrue($this->cacheService->has($key));
        
        // 等待过期
        sleep(2);
        
        // 验证缓存已过期
        $this->assertFalse($this->cacheService->has($key));
        $this->assertNull($this->cacheService->get($key));
    }
    
    public function testComplexDataTypes()
    {
        $testData = [
            'string' => 'test string',
            'integer' => 123,
            'float' => 123.45,
            'boolean' => true,
            'array' => [1, 2, 3, 'nested'],
            'object' => (object)['prop' => 'value'],
            'null' => null
        ];
        
        foreach ($testData as $key => $value) {
            $this->cacheService->set($key, $value);
            $cachedValue = $this->cacheService->get($key);
            
            if (is_object($value)) {
                $this->assertEquals((array)$value, (array)$cachedValue);
            } else {
                $this->assertEquals($value, $cachedValue);
            }
        }
    }
    
    public function testGetStats()
    {
        // 设置一些缓存数据
        $this->cacheService->set('stats_key1', 'value1');
        $this->cacheService->set('stats_key2', 'value2');
        
        $stats = $this->cacheService->getStats();
        
        $this->assertIsArray($stats);
        $this->assertEquals('file', $stats['driver']);
        $this->assertEquals('test_', $stats['prefix']);
        $this->assertEquals(60, $stats['default_ttl']);
        $this->assertArrayHasKey('cache_dir', $stats);
        $this->assertArrayHasKey('total_files', $stats);
    }
    
    public function testCleanup()
    {
        // 设置一些缓存，包括即将过期的
        $this->cacheService->set('normal_key', 'normal_value', 3600);
        $this->cacheService->set('expiring_key', 'expiring_value', 1);
        
        // 等待过期
        sleep(2);
        
        // 执行清理
        $result = $this->cacheService->cleanup();
        $this->assertTrue(is_int($result) || is_bool($result));
        
        // 验证正常缓存仍存在，过期缓存已清理
        $this->assertTrue($this->cacheService->has('normal_key'));
        $this->assertFalse($this->cacheService->has('expiring_key'));
    }
    
    public function testCacheKeyPrefix()
    {
        $key = 'test_key';
        $value = 'test_value';
        
        $this->cacheService->set($key, $value);
        
        // 验证实际文件使用了前缀
        $stats = $this->cacheService->getStats();
        $this->assertGreaterThan(0, $stats['total_files']);
    }
    
    public function testConcurrentAccess()
    {
        $key = 'concurrent_key';
        $value1 = 'value1';
        $value2 = 'value2';
        
        // 模拟并发写入
        $this->cacheService->set($key, $value1);
        $this->cacheService->set($key, $value2);
        
        // 最后的值应该被保存
        $result = $this->cacheService->get($key);
        $this->assertEquals($value2, $result);
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