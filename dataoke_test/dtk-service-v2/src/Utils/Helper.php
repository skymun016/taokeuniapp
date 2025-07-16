<?php

namespace Utils;

/**
 * 辅助函数类
 */
class Helper
{
    /**
     * 获取请求参数
     */
    public static function getParam($key, $default = null, $type = 'string')
    {
        $value = $_GET[$key] ?? $_POST[$key] ?? $default;
        
        if ($value === null) {
            return $default;
        }
        
        switch ($type) {
            case 'int':
                return (int) $value;
            case 'float':
                return (float) $value;
            case 'bool':
                return filter_var($value, FILTER_VALIDATE_BOOLEAN);
            case 'array':
                return is_array($value) ? $value : explode(',', $value);
            case 'string':
            default:
                return trim((string) $value);
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
     * 数据转换
     */
    public static function convertData($data, $mapping)
    {
        if (empty($data) || empty($mapping)) {
            return $data;
        }
        
        $result = [];
        
        foreach ($mapping as $newKey => $oldKey) {
            if (isset($data[$oldKey])) {
                $result[$newKey] = $data[$oldKey];
            }
        }
        
        return $result;
    }
    
    /**
     * 批量数据转换
     */
    public static function convertDataList($dataList, $mapping)
    {
        if (empty($dataList) || !is_array($dataList)) {
            return [];
        }
        
        $result = [];
        foreach ($dataList as $data) {
            $result[] = self::convertData($data, $mapping);
        }
        
        return $result;
    }
    
    /**
     * 生成随机字符串
     */
    public static function generateNonce($length = 16)
    {
        if ($length == 6) {
            // 对于6位随机数，生成纯数字
            return str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        }

        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $nonce = '';
        for ($i = 0; $i < $length; $i++) {
            $nonce .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $nonce;
    }

    /**
     * 生成缓存键
     */
    public static function generateCacheKey($prefix, $params = [])
    {
        $cacheConfig = DTK_CONFIG['cache'];
        $key = $cacheConfig['prefix'] . $prefix;
        
        if (!empty($params)) {
            ksort($params);
            $key .= '_' . md5(serialize($params));
        }
        
        return $key;
    }
    
    /**
     * 格式化价格
     */
    public static function formatPrice($price)
    {
        return number_format((float) $price, 2, '.', '');
    }
    
    /**
     * 格式化佣金比例
     */
    public static function formatCommissionRate($rate)
    {
        return number_format((float) $rate, 2, '.', '');
    }
    
    /**
     * 清理HTML标签
     */
    public static function cleanHtml($html)
    {
        return strip_tags($html);
    }
    
    /**
     * 截取字符串
     */
    public static function truncate($string, $length = 100, $suffix = '...')
    {
        if (mb_strlen($string, 'UTF-8') <= $length) {
            return $string;
        }
        
        return mb_substr($string, 0, $length, 'UTF-8') . $suffix;
    }
    
    /**
     * 验证URL
     */
    public static function isValidUrl($url)
    {
        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }
    
    /**
     * 生成随机字符串
     */
    public static function generateRandomString($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        
        return $randomString;
    }
    
    /**
     * 数组分页
     */
    public static function arrayPaginate($array, $page = 1, $pageSize = 20)
    {
        $total = count($array);
        $offset = ($page - 1) * $pageSize;
        $items = array_slice($array, $offset, $pageSize);
        
        return [
            'items' => $items,
            'total' => $total,
            'page' => $page,
            'pageSize' => $pageSize,
            'totalPages' => ceil($total / $pageSize),
            'hasMore' => $offset + $pageSize < $total
        ];
    }
    
    /**
     * 检查平台是否启用
     */
    public static function isPlatformEnabled($platform)
    {
        $config = PLATFORM_CONFIG[$platform] ?? null;
        return $config && ($config['enabled'] ?? false);
    }
    
    /**
     * 获取平台配置
     */
    public static function getPlatformConfig($platform)
    {
        if (!self::isPlatformEnabled($platform)) {
            throw new \InvalidArgumentException("平台 {$platform} 未启用或不存在");
        }
        
        return PLATFORM_CONFIG[$platform];
    }
    
    /**
     * 获取所有启用的平台
     */
    public static function getEnabledPlatforms()
    {
        $enabled = [];
        
        foreach (PLATFORM_CONFIG as $platform => $config) {
            if ($config['enabled'] ?? false) {
                $enabled[] = $platform;
            }
        }
        
        return $enabled;
    }
    
    /**
     * 计算执行时间
     */
    public static function calculateDuration($startTime)
    {
        return round((microtime(true) - $startTime) * 1000, 2);
    }
    
    /**
     * 安全的JSON解码
     */
    public static function jsonDecode($json, $assoc = true)
    {
        if (empty($json)) {
            return $assoc ? [] : null;
        }
        
        $result = json_decode($json, $assoc);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \InvalidArgumentException('JSON解析错误: ' . json_last_error_msg());
        }
        
        return $result;
    }
    
    /**
     * 安全的JSON编码
     */
    public static function jsonEncode($data)
    {
        $result = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \InvalidArgumentException('JSON编码错误: ' . json_last_error_msg());
        }
        
        return $result;
    }
    
    /**
     * 获取客户端IP
     */
    public static function getClientIp()
    {
        $ipKeys = ['HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'HTTP_CLIENT_IP', 'REMOTE_ADDR'];
        
        foreach ($ipKeys as $key) {
            if (!empty($_SERVER[$key])) {
                $ip = $_SERVER[$key];
                if (strpos($ip, ',') !== false) {
                    $ip = trim(explode(',', $ip)[0]);
                }
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                    return $ip;
                }
            }
        }
        
        return $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    }
}
?>
