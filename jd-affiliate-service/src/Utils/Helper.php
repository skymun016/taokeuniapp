<?php

namespace JdAffiliate\Utils;

/**
 * 通用助手类
 */
class Helper
{
    /**
     * 获取请求参数
     */
    public static function getParam($key, $default = null, $type = 'string')
    {
        $value = null;

        // 优先从POST获取
        if (isset($_POST[$key])) {
            $value = $_POST[$key];
        }
        // 然后从GET获取
        elseif (isset($_GET[$key])) {
            $value = $_GET[$key];
        }
        // 最后从JSON body获取
        else {
            $input = self::getJsonInput();
            if (isset($input[$key])) {
                $value = $input[$key];
            }
        }

        // 如果没有获取到值，返回默认值
        if ($value === null) {
            return $default;
        }

        // 根据类型转换值
        return self::convertType($value, $type);
    }

    /**
     * 获取JSON输入数据
     */
    public static function getJsonInput()
    {
        static $input = null;

        if ($input === null) {
            $rawInput = file_get_contents('php://input');
            $input = json_decode($rawInput, true) ?: [];
        }

        return $input;
    }

    /**
     * 类型转换
     */
    public static function convertType($value, $type)
    {
        switch ($type) {
            case 'int':
            case 'integer':
                return (int)$value;

            case 'float':
            case 'double':
                return (float)$value;

            case 'bool':
            case 'boolean':
                return filter_var($value, FILTER_VALIDATE_BOOLEAN);

            case 'array':
                return is_array($value) ? $value : [$value];

            case 'string':
            default:
                return (string)$value;
        }
    }

    /**
     * 验证必需参数
     */
    public static function validateRequired($params, $required)
    {
        $missing = [];

        foreach ($required as $field) {
            if (!isset($params[$field]) || $params[$field] === '' || $params[$field] === null) {
                $missing[] = $field;
            }
        }

        if (!empty($missing)) {
            throw new \InvalidArgumentException('缺少必需参数: ' . implode(', ', $missing));
        }
    }

    /**
     * 计算执行时间
     */
    public static function calculateDuration($startTime)
    {
        return round((microtime(true) - $startTime) * 1000, 2);
    }

    /**
     * 生成唯一ID
     */
    public static function generateId($prefix = '')
    {
        return $prefix . uniqid() . mt_rand(1000, 9999);
    }

    /**
     * 格式化文件大小
     */
    public static function formatFileSize($bytes)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= pow(1024, $pow);

        return round($bytes, 2) . ' ' . $units[$pow];
    }

    /**
     * 格式化时间差
     */
    public static function formatTimeDiff($timestamp)
    {
        $diff = time() - $timestamp;

        if ($diff < 60) {
            return $diff . '秒前';
        } elseif ($diff < 3600) {
            return floor($diff / 60) . '分钟前';
        } elseif ($diff < 86400) {
            return floor($diff / 3600) . '小时前';
        } elseif ($diff < 2592000) {
            return floor($diff / 86400) . '天前';
        } else {
            return date('Y-m-d H:i:s', $timestamp);
        }
    }

    /**
     * 安全的JSON编码
     */
    public static function jsonEncode($data, $options = 0)
    {
        $defaultOptions = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES;
        return json_encode($data, $defaultOptions | $options);
    }

    /**
     * 安全的JSON解码
     */
    public static function jsonDecode($json, $assoc = true)
    {
        $data = json_decode($json, $assoc);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \InvalidArgumentException('JSON解析错误: ' . json_last_error_msg());
        }

        return $data;
    }

    /**
     * 数组安全获取值
     */
    public static function arrayGet($array, $key, $default = null)
    {
        if (!is_array($array)) {
            return $default;
        }

        // 支持点号分隔的键名
        if (strpos($key, '.') !== false) {
            $keys = explode('.', $key);
            $value = $array;

            foreach ($keys as $k) {
                if (!is_array($value) || !array_key_exists($k, $value)) {
                    return $default;
                }
                $value = $value[$k];
            }

            return $value;
        }

        return array_key_exists($key, $array) ? $array[$key] : $default;
    }

    /**
     * 数组安全设置值
     */
    public static function arraySet(&$array, $key, $value)
    {
        if (!is_array($array)) {
            $array = [];
        }

        // 支持点号分隔的键名
        if (strpos($key, '.') !== false) {
            $keys = explode('.', $key);
            $current = &$array;

            foreach ($keys as $k) {
                if (!isset($current[$k]) || !is_array($current[$k])) {
                    $current[$k] = [];
                }
                $current = &$current[$k];
            }

            $current = $value;
        } else {
            $array[$key] = $value;
        }
    }

    /**
     * 生成随机字符串
     */
    public static function randomString($length = 10, $characters = null)
    {
        if ($characters === null) {
            $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        }

        $charactersLength = strlen($characters);
        $randomString = '';

        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        return $randomString;
    }

    /**
     * URL安全的Base64编码
     */
    public static function base64UrlEncode($data)
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    /**
     * URL安全的Base64解码
     */
    public static function base64UrlDecode($data)
    {
        return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
    }

    /**
     * 验证邮箱格式
     */
    public static function isValidEmail($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * 验证URL格式
     */
    public static function isValidUrl($url)
    {
        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }

    /**
     * 验证手机号格式（中国）
     */
    public static function isValidMobile($mobile)
    {
        return preg_match('/^1[3-9]\d{9}$/', $mobile);
    }

    /**
     * 清理HTML标签
     */
    public static function stripHtml($string, $allowedTags = '')
    {
        return strip_tags($string, $allowedTags);
    }

    /**
     * 截取字符串
     */
    public static function truncate($string, $length, $suffix = '...')
    {
        if (mb_strlen($string, 'UTF-8') <= $length) {
            return $string;
        }

        return mb_substr($string, 0, $length, 'UTF-8') . $suffix;
    }

    /**
     * 获取客户端IP地址
     */
    public static function getClientIp()
    {
        $ipKeys = ['HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'HTTP_CLIENT_IP', 'REMOTE_ADDR'];

        foreach ($ipKeys as $key) {
            if (!empty($_SERVER[$key])) {
                $ips = explode(',', $_SERVER[$key]);
                $ip = trim($ips[0]);
                
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                    return $ip;
                }
            }
        }

        return $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    }

    /**
     * 获取用户代理
     */
    public static function getUserAgent()
    {
        return $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
    }

    /**
     * 检查是否为AJAX请求
     */
    public static function isAjax()
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    /**
     * 检查是否为POST请求
     */
    public static function isPost()
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    /**
     * 检查是否为GET请求
     */
    public static function isGet()
    {
        return $_SERVER['REQUEST_METHOD'] === 'GET';
    }

    /**
     * 获取当前URL
     */
    public static function getCurrentUrl()
    {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $uri = $_SERVER['REQUEST_URI'] ?? '/';

        return $protocol . '://' . $host . $uri;
    }

    /**
     * 构建查询字符串
     */
    public static function buildQuery($params)
    {
        return http_build_query($params, '', '&', PHP_QUERY_RFC3986);
    }

    /**
     * 解析查询字符串
     */
    public static function parseQuery($query)
    {
        parse_str($query, $params);
        return $params;
    }

    /**
     * 数组转XML
     */
    public static function arrayToXml($array, $rootElement = 'root', $xml = null)
    {
        if ($xml === null) {
            $xml = new \SimpleXMLElement("<?xml version='1.0' encoding='UTF-8'?><{$rootElement}></{$rootElement}>");
        }

        foreach ($array as $key => $value) {
            if (is_array($value)) {
                self::arrayToXml($value, $key, $xml->addChild($key));
            } else {
                $xml->addChild($key, htmlspecialchars($value));
            }
        }

        return $xml->asXML();
    }

    /**
     * 检查配置是否启用
     */
    public static function isConfigEnabled($configKey, $default = false)
    {
        $value = EnvLoader::get($configKey, $default);
        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * 获取内存使用情况
     */
    public static function getMemoryUsage($format = true)
    {
        $usage = memory_get_usage(true);
        return $format ? self::formatFileSize($usage) : $usage;
    }

    /**
     * 获取峰值内存使用情况
     */
    public static function getPeakMemoryUsage($format = true)
    {
        $usage = memory_get_peak_usage(true);
        return $format ? self::formatFileSize($usage) : $usage;
    }
}