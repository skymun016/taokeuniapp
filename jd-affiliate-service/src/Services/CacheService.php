<?php

namespace JdAffiliateService\Services;

use JdAffiliate\Utils\Logger;
use Exception;

/**
 * 缓存服务类
 * 支持文件缓存和Redis缓存，提供缓存降级机制
 */
class CacheService
{
    private $driver;
    private $prefix;
    private $defaultTtl;
    private $cacheDir;
    private $redis;
    private $logger;
    
    // 缓存驱动类型
    const DRIVER_FILE = 'file';
    const DRIVER_REDIS = 'redis';
    
    // 默认缓存时间（秒）
    const DEFAULT_TTL = 3600; // 1小时
    
    public function __construct($config = [])
    {
        $this->driver = $config['driver'] ?? self::DRIVER_FILE;
        $this->prefix = $config['prefix'] ?? 'jd_affiliate_';
        $this->defaultTtl = $config['default_ttl'] ?? self::DEFAULT_TTL;
        $this->cacheDir = $config['cache_dir'] ?? __DIR__ . '/../../cache';
        $this->logger = new Logger();
        
        $this->initializeCache($config);
    }
    
    /**
     * 初始化缓存系统
     */
    private function initializeCache($config)
    {
        try {
            if ($this->driver === self::DRIVER_REDIS) {
                $this->initializeRedis($config);
            } else {
                $this->initializeFileCache();
            }
        } catch (Exception $e) {
            $this->logger->error('Cache initialization failed: ' . $e->getMessage());
            // 降级到文件缓存
            if ($this->driver !== self::DRIVER_FILE) {
                $this->driver = self::DRIVER_FILE;
                $this->initializeFileCache();
            }
        }
    }
    
    /**
     * 初始化Redis缓存
     */
    private function initializeRedis($config)
    {
        if (!extension_loaded('redis')) {
            throw new Exception('Redis extension not loaded');
        }
        
        $this->redis = new \Redis();
        $host = $config['redis_host'] ?? '127.0.0.1';
        $port = $config['redis_port'] ?? 6379;
        $password = $config['redis_password'] ?? null;
        $database = $config['redis_database'] ?? 0;
        
        if (!$this->redis->connect($host, $port)) {
            throw new Exception('Failed to connect to Redis server');
        }
        
        if ($password) {
            $this->redis->auth($password);
        }
        
        $this->redis->select($database);
        
        $this->logger->info('Redis cache initialized successfully');
    }
    
    /**
     * 初始化文件缓存
     */
    private function initializeFileCache()
    {
        if (!is_dir($this->cacheDir)) {
            if (!mkdir($this->cacheDir, 0755, true)) {
                throw new Exception('Failed to create cache directory: ' . $this->cacheDir);
            }
        }
        
        if (!is_writable($this->cacheDir)) {
            throw new Exception('Cache directory is not writable: ' . $this->cacheDir);
        }
        
        $this->logger->info('File cache initialized successfully');
    }
    
    /**
     * 获取缓存
     */
    public function get($key, $default = null)
    {
        try {
            $fullKey = $this->prefix . $key;
            
            if ($this->driver === self::DRIVER_REDIS && $this->redis) {
                return $this->getFromRedis($fullKey, $default);
            } else {
                return $this->getFromFile($fullKey, $default);
            }
        } catch (Exception $e) {
            $this->logger->error('Cache get failed for key ' . $key . ': ' . $e->getMessage());
            return $default;
        }
    }
    
    /**
     * 设置缓存
     */
    public function set($key, $value, $ttl = null)
    {
        try {
            $fullKey = $this->prefix . $key;
            $ttl = $ttl ?? $this->defaultTtl;
            
            if ($this->driver === self::DRIVER_REDIS && $this->redis) {
                return $this->setToRedis($fullKey, $value, $ttl);
            } else {
                return $this->setToFile($fullKey, $value, $ttl);
            }
        } catch (Exception $e) {
            $this->logger->error('Cache set failed for key ' . $key . ': ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * 删除缓存
     */
    public function delete($key)
    {
        try {
            $fullKey = $this->prefix . $key;
            
            if ($this->driver === self::DRIVER_REDIS && $this->redis) {
                return $this->deleteFromRedis($fullKey);
            } else {
                return $this->deleteFromFile($fullKey);
            }
        } catch (Exception $e) {
            $this->logger->error('Cache delete failed for key ' . $key . ': ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * 清空所有缓存
     */
    public function clear()
    {
        try {
            if ($this->driver === self::DRIVER_REDIS && $this->redis) {
                return $this->clearRedis();
            } else {
                return $this->clearFileCache();
            }
        } catch (Exception $e) {
            $this->logger->error('Cache clear failed: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * 检查缓存是否存在
     */
    public function has($key)
    {
        try {
            $fullKey = $this->prefix . $key;
            
            if ($this->driver === self::DRIVER_REDIS && $this->redis) {
                return $this->redis->exists($fullKey) > 0;
            } else {
                $filePath = $this->getFilePath($fullKey);
                if (!file_exists($filePath)) {
                    return false;
                }
                
                $data = unserialize(file_get_contents($filePath));
                return $data['expires'] > time();
            }
        } catch (Exception $e) {
            $this->logger->error('Cache has check failed for key ' . $key . ': ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * 从Redis获取缓存
     */
    private function getFromRedis($key, $default)
    {
        $value = $this->redis->get($key);
        if ($value === false) {
            return $default;
        }
        
        return json_decode($value, true);
    }
    
    /**
     * 向Redis设置缓存
     */
    private function setToRedis($key, $value, $ttl)
    {
        $serializedValue = json_encode($value);
        return $this->redis->setex($key, $ttl, $serializedValue);
    }
    
    /**
     * 从Redis删除缓存
     */
    private function deleteFromRedis($key)
    {
        return $this->redis->del($key) > 0;
    }
    
    /**
     * 清空Redis缓存
     */
    private function clearRedis()
    {
        $keys = $this->redis->keys($this->prefix . '*');
        if (empty($keys)) {
            return true;
        }
        
        return $this->redis->del($keys) > 0;
    }
    
    /**
     * 从文件获取缓存
     */
    private function getFromFile($key, $default)
    {
        $filePath = $this->getFilePath($key);
        
        if (!file_exists($filePath)) {
            return $default;
        }
        
        $data = unserialize(file_get_contents($filePath));
        
        if ($data['expires'] <= time()) {
            unlink($filePath);
            return $default;
        }
        
        return $data['value'];
    }
    
    /**
     * 向文件设置缓存
     */
    private function setToFile($key, $value, $ttl)
    {
        $filePath = $this->getFilePath($key);
        $data = [
            'value' => $value,
            'expires' => time() + $ttl,
            'created' => time()
        ];
        
        $dir = dirname($filePath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        
        return file_put_contents($filePath, serialize($data)) !== false;
    }
    
    /**
     * 从文件删除缓存
     */
    private function deleteFromFile($key)
    {
        $filePath = $this->getFilePath($key);
        
        if (file_exists($filePath)) {
            return unlink($filePath);
        }
        
        return true;
    }
    
    /**
     * 清空文件缓存
     */
    private function clearFileCache()
    {
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($this->cacheDir, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );
        
        foreach ($iterator as $file) {
            if ($file->isFile()) {
                unlink($file->getRealPath());
            } elseif ($file->isDir()) {
                rmdir($file->getRealPath());
            }
        }
        
        return true;
    }
    
    /**
     * 获取缓存文件路径
     */
    private function getFilePath($key)
    {
        $hash = md5($key);
        $dir = substr($hash, 0, 2);
        return $this->cacheDir . '/' . $dir . '/' . $hash . '.cache';
    }
    
    /**
     * 获取缓存统计信息
     */
    public function getStats()
    {
        $stats = [
            'driver' => $this->driver,
            'prefix' => $this->prefix,
            'default_ttl' => $this->defaultTtl
        ];
        
        try {
            if ($this->driver === self::DRIVER_REDIS && $this->redis) {
                $info = $this->redis->info();
                $stats['redis_info'] = [
                    'used_memory' => $info['used_memory_human'] ?? 'N/A',
                    'connected_clients' => $info['connected_clients'] ?? 'N/A',
                    'total_commands_processed' => $info['total_commands_processed'] ?? 'N/A'
                ];
                
                $keys = $this->redis->keys($this->prefix . '*');
                $stats['total_keys'] = count($keys);
            } else {
                $stats['cache_dir'] = $this->cacheDir;
                $stats['cache_dir_size'] = $this->getCacheDirSize();
                $stats['total_files'] = $this->getCacheFileCount();
            }
        } catch (Exception $e) {
            $stats['error'] = $e->getMessage();
        }
        
        return $stats;
    }
    
    /**
     * 获取缓存目录大小
     */
    private function getCacheDirSize()
    {
        $size = 0;
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($this->cacheDir, \RecursiveDirectoryIterator::SKIP_DOTS)
        );
        
        foreach ($iterator as $file) {
            if ($file->isFile()) {
                $size += $file->getSize();
            }
        }
        
        return $this->formatBytes($size);
    }
    
    /**
     * 获取缓存文件数量
     */
    private function getCacheFileCount()
    {
        $count = 0;
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($this->cacheDir, \RecursiveDirectoryIterator::SKIP_DOTS)
        );
        
        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'cache') {
                $count++;
            }
        }
        
        return $count;
    }
    
    /**
     * 格式化字节数
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
    
    /**
     * 清理过期缓存
     */
    public function cleanup()
    {
        try {
            if ($this->driver === self::DRIVER_FILE) {
                return $this->cleanupFileCache();
            }
            // Redis会自动清理过期键
            return true;
        } catch (Exception $e) {
            $this->logger->error('Cache cleanup failed: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * 清理过期的文件缓存
     */
    private function cleanupFileCache()
    {
        $cleaned = 0;
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($this->cacheDir, \RecursiveDirectoryIterator::SKIP_DOTS)
        );
        
        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'cache') {
                $data = unserialize(file_get_contents($file->getRealPath()));
                if ($data['expires'] <= time()) {
                    unlink($file->getRealPath());
                    $cleaned++;
                }
            }
        }
        
        $this->logger->info("Cleaned up {$cleaned} expired cache files");
        return $cleaned;
    }
}