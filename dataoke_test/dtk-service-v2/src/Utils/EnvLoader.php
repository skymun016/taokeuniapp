<?php

namespace Utils;

/**
 * 环境变量加载器
 */
class EnvLoader
{
    private static $loaded = false;
    
    /**
     * 加载 .env 文件
     */
    public static function load($path = null)
    {
        if (self::$loaded) {
            return;
        }
        
        if ($path === null) {
            $path = ROOT_PATH . '/.env';
        }
        
        if (!file_exists($path)) {
            return;
        }
        
        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        
        foreach ($lines as $line) {
            // 跳过注释行
            if (strpos(trim($line), '#') === 0) {
                continue;
            }
            
            // 解析键值对
            if (strpos($line, '=') !== false) {
                list($key, $value) = explode('=', $line, 2);
                
                $key = trim($key);
                $value = trim($value);
                
                // 移除引号
                if (preg_match('/^"(.*)"$/', $value, $matches)) {
                    $value = $matches[1];
                } elseif (preg_match("/^'(.*)'$/", $value, $matches)) {
                    $value = $matches[1];
                }
                
                // 处理布尔值
                if (strtolower($value) === 'true') {
                    $value = true;
                } elseif (strtolower($value) === 'false') {
                    $value = false;
                } elseif (strtolower($value) === 'null') {
                    $value = null;
                } elseif (is_numeric($value)) {
                    $value = strpos($value, '.') !== false ? (float) $value : (int) $value;
                }
                
                // 设置环境变量
                $_ENV[$key] = $value;
            }
        }
        
        self::$loaded = true;
    }
    
    /**
     * 获取环境变量
     */
    public static function get($key, $default = null)
    {
        return $_ENV[$key] ?? $default;
    }
    
    /**
     * 设置环境变量
     */
    public static function set($key, $value)
    {
        $_ENV[$key] = $value;
    }
    
    /**
     * 检查环境变量是否存在
     */
    public static function has($key)
    {
        return isset($_ENV[$key]);
    }
    
    /**
     * 获取所有环境变量
     */
    public static function all()
    {
        return $_ENV;
    }
}
?>
