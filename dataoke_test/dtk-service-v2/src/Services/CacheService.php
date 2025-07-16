<?php

namespace Services;

use Utils\Logger;
use Utils\Helper;

/**
 * 缓存服务类
 */
class CacheService
{
    private static $instance = null;
    private $logger;
    private $config;
    
    private function __construct()
    {
        $this->logger = new Logger();
        $this->config = DTK_CONFIG['cache'];
        
        // 确保缓存目录存在
        if (!is_dir($this->config['path'])) {
            mkdir($this->config['path'], 0755, true);
        }
    }
    
    /**
     * 获取单例实例
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        
        return self::$instance;
    }
    
    /**
     * 获取缓存
     */
    public function get($key, $default = null)
    {
        $startTime = microtime(true);
        
        try {
            $cacheFile = $this->getCacheFile($key);
            
            if (!file_exists($cacheFile)) {
                $this->logger->logCache('get', $key, false, Helper::calculateDuration($startTime));
                return $default;
            }
            
            $content = file_get_contents($cacheFile);
            $data = Helper::jsonDecode($content);
            
            // 检查是否过期
            if ($data['expire'] > 0 && $data['expire'] < time()) {
                unlink($cacheFile);
                $this->logger->logCache('get', $key, false, Helper::calculateDuration($startTime));
                return $default;
            }
            
            $this->logger->logCache('get', $key, true, Helper::calculateDuration($startTime));
            return $data['value'];
            
        } catch (\Exception $e) {
            $this->logger->error('缓存读取失败', [
                'key' => $key,
                'error' => $e->getMessage()
            ]);
            
            return $default;
        }
    }
    
    /**
     * 设置缓存
     */
    public function set($key, $value, $expire = 3600)
    {
        $startTime = microtime(true);
        
        try {
            $cacheFile = $this->getCacheFile($key);
            $cacheDir = dirname($cacheFile);
            
            // 确保缓存目录存在
            if (!is_dir($cacheDir)) {
                mkdir($cacheDir, 0755, true);
            }
            
            $data = [
                'key' => $key,
                'value' => $value,
                'expire' => $expire > 0 ? time() + $expire : 0,
                'created' => time()
            ];
            
            $content = Helper::jsonEncode($data);
            $result = file_put_contents($cacheFile, $content, LOCK_EX);
            
            $this->logger->logCache('set', $key, null, Helper::calculateDuration($startTime));
            
            return $result !== false;
            
        } catch (\Exception $e) {
            $this->logger->error('缓存写入失败', [
                'key' => $key,
                'error' => $e->getMessage()
            ]);
            
            return false;
        }
    }
    
    /**
     * 删除缓存
     */
    public function delete($key)
    {
        $startTime = microtime(true);
        
        try {
            $cacheFile = $this->getCacheFile($key);
            
            if (file_exists($cacheFile)) {
                $result = unlink($cacheFile);
                $this->logger->logCache('delete', $key, null, Helper::calculateDuration($startTime));
                return $result;
            }
            
            return true;
            
        } catch (\Exception $e) {
            $this->logger->error('缓存删除失败', [
                'key' => $key,
                'error' => $e->getMessage()
            ]);
            
            return false;
        }
    }
    
    /**
     * 检查缓存是否存在
     */
    public function exists($key)
    {
        $cacheFile = $this->getCacheFile($key);
        
        if (!file_exists($cacheFile)) {
            return false;
        }
        
        try {
            $content = file_get_contents($cacheFile);
            $data = Helper::jsonDecode($content);
            
            // 检查是否过期
            if ($data['expire'] > 0 && $data['expire'] < time()) {
                unlink($cacheFile);
                return false;
            }
            
            return true;
            
        } catch (\Exception $e) {
            return false;
        }
    }
    
    /**
     * 清空所有缓存
     */
    public function clear()
    {
        try {
            $files = glob($this->config['path'] . '/*');
            $count = 0;
            
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                    $count++;
                }
            }
            
            $this->logger->info('缓存清理完成', ['count' => $count]);
            
            return $count;
            
        } catch (\Exception $e) {
            $this->logger->error('缓存清理失败', [
                'error' => $e->getMessage()
            ]);
            
            return 0;
        }
    }
    
    /**
     * 清理过期缓存
     */
    public function cleanExpired()
    {
        try {
            $files = glob($this->config['path'] . '/*');
            $count = 0;
            
            foreach ($files as $file) {
                if (!is_file($file)) {
                    continue;
                }
                
                try {
                    $content = file_get_contents($file);
                    $data = Helper::jsonDecode($content);
                    
                    // 检查是否过期
                    if ($data['expire'] > 0 && $data['expire'] < time()) {
                        unlink($file);
                        $count++;
                    }
                    
                } catch (\Exception $e) {
                    // 无效的缓存文件，直接删除
                    unlink($file);
                    $count++;
                }
            }
            
            $this->logger->info('过期缓存清理完成', ['count' => $count]);
            
            return $count;
            
        } catch (\Exception $e) {
            $this->logger->error('过期缓存清理失败', [
                'error' => $e->getMessage()
            ]);
            
            return 0;
        }
    }
    
    /**
     * 获取缓存统计信息
     */
    public function getStats()
    {
        try {
            $files = glob($this->config['path'] . '/*');
            $totalFiles = 0;
            $totalSize = 0;
            $expiredFiles = 0;
            
            foreach ($files as $file) {
                if (!is_file($file)) {
                    continue;
                }
                
                $totalFiles++;
                $totalSize += filesize($file);
                
                try {
                    $content = file_get_contents($file);
                    $data = Helper::jsonDecode($content);
                    
                    if ($data['expire'] > 0 && $data['expire'] < time()) {
                        $expiredFiles++;
                    }
                    
                } catch (\Exception $e) {
                    $expiredFiles++;
                }
            }
            
            return [
                'total_files' => $totalFiles,
                'total_size' => $totalSize,
                'total_size_formatted' => $this->formatBytes($totalSize),
                'expired_files' => $expiredFiles,
                'valid_files' => $totalFiles - $expiredFiles,
                'cache_path' => $this->config['path']
            ];
            
        } catch (\Exception $e) {
            $this->logger->error('获取缓存统计失败', [
                'error' => $e->getMessage()
            ]);
            
            return [];
        }
    }
    
    /**
     * 获取缓存文件路径
     */
    private function getCacheFile($key)
    {
        $hash = md5($key);
        $dir = substr($hash, 0, 2);
        
        return $this->config['path'] . '/' . $dir . '/' . $hash . '.cache';
    }
    
    /**
     * 格式化字节数
     */
    private function formatBytes($bytes)
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= pow(1024, $pow);
        
        return round($bytes, 2) . ' ' . $units[$pow];
    }
    
    /**
     * 记住缓存（如果不存在则设置）
     */
    public function remember($key, $callback, $expire = 3600)
    {
        $value = $this->get($key);
        
        if ($value !== null) {
            return $value;
        }
        
        $value = $callback();
        $this->set($key, $value, $expire);
        
        return $value;
    }
}
?>
