<?php

namespace JdAffiliate\Utils;

/**
 * 环境变量加载器
 */
class EnvLoader
{
    private static $loaded = false;
    private static $variables = [];

    /**
     * 加载环境变量文件
     */
    public static function load($path = null)
    {
        if (self::$loaded) {
            return;
        }

        if ($path === null) {
            $path = dirname(dirname(__DIR__)) . '/.env';
        }

        if (!file_exists($path)) {
            self::$loaded = true;
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

                // 转换布尔值
                if (strtolower($value) === 'true') {
                    $value = true;
                } elseif (strtolower($value) === 'false') {
                    $value = false;
                } elseif (is_numeric($value)) {
                    $value = is_float($value + 0) ? (float)$value : (int)$value;
                }

                self::$variables[$key] = $value;
                
                // 设置到环境变量中
                if (!array_key_exists($key, $_ENV)) {
                    $_ENV[$key] = $value;
                }
                
                if (!getenv($key)) {
                    putenv("$key=$value");
                }
            }
        }

        self::$loaded = true;
    }

    /**
     * 获取环境变量值
     */
    public static function get($key, $default = null)
    {
        // 优先从已加载的变量中获取
        if (array_key_exists($key, self::$variables)) {
            return self::$variables[$key];
        }

        // 从环境变量中获取
        $value = getenv($key);
        if ($value !== false) {
            return $value;
        }

        // 从$_ENV中获取
        if (array_key_exists($key, $_ENV)) {
            return $_ENV[$key];
        }

        return $default;
    }

    /**
     * 设置环境变量
     */
    public static function set($key, $value)
    {
        self::$variables[$key] = $value;
        $_ENV[$key] = $value;
        putenv("$key=$value");
    }

    /**
     * 检查环境变量是否存在
     */
    public static function has($key)
    {
        return array_key_exists($key, self::$variables) || 
               getenv($key) !== false || 
               array_key_exists($key, $_ENV);
    }

    /**
     * 获取所有环境变量
     */
    public static function all()
    {
        return array_merge($_ENV, self::$variables);
    }
}