<?php

namespace Utils;

/**
 * 日志工具类
 */
class Logger
{
    private $logPath;
    private $maxFiles;
    private $maxSize;
    private $level;
    private $fileLoggingEnabled = true;
    
    const LEVELS = [
        'debug' => 0,
        'info' => 1,
        'warning' => 2,
        'error' => 3
    ];
    
    public function __construct()
    {
        $config = DTK_CONFIG['log'];
        $this->logPath = $config['path'];
        $this->maxFiles = $config['max_files'];
        $this->maxSize = $config['max_size'];
        $this->level = $config['level'];

        // 检查是否启用文件日志
        $this->fileLoggingEnabled = $_ENV['LOG_TO_FILE'] ?? true;
        if ($this->fileLoggingEnabled === 'false' || $this->fileLoggingEnabled === false) {
            $this->fileLoggingEnabled = false;
        }

        // 如果启用文件日志，确保日志目录存在
        if ($this->fileLoggingEnabled && !is_dir($this->logPath)) {
            if (!@mkdir($this->logPath, 0755, true)) {
                // 创建目录失败，禁用文件日志
                $this->fileLoggingEnabled = false;
            }
        }
    }
    
    /**
     * 记录调试日志
     */
    public function debug($message, $context = [])
    {
        $this->log('debug', $message, $context);
    }
    
    /**
     * 记录信息日志
     */
    public function info($message, $context = [])
    {
        $this->log('info', $message, $context);
    }
    
    /**
     * 记录警告日志
     */
    public function warning($message, $context = [])
    {
        $this->log('warning', $message, $context);
    }
    
    /**
     * 记录错误日志
     */
    public function error($message, $context = [])
    {
        $this->log('error', $message, $context);
    }
    
    /**
     * 记录日志
     */
    private function log($level, $message, $context = [])
    {
        // 检查日志级别
        if (self::LEVELS[$level] < self::LEVELS[$this->level]) {
            return;
        }
        
        $timestamp = date('Y-m-d H:i:s');
        $logFile = $this->getLogFile();
        
        // 构建日志内容
        $logData = [
            'timestamp' => $timestamp,
            'level' => strtoupper($level),
            'message' => $message,
            'context' => $context,
            'memory' => $this->formatBytes(memory_get_usage(true)),
            'pid' => getmypid(),
            'request_id' => $this->getRequestId()
        ];
        
        $logLine = json_encode($logData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "\n";

        // 安全写入日志文件
        $this->safeWriteLog($logFile, $logLine);

        // 检查文件大小并轮转
        $this->rotateLogFile($logFile);
    }
    
    /**
     * 获取日志文件路径
     */
    private function getLogFile()
    {
        $date = date('Y-m-d');
        return $this->logPath . "/app-{$date}.log";
    }
    
    /**
     * 轮转日志文件
     */
    private function rotateLogFile($logFile)
    {
        if (!file_exists($logFile)) {
            return;
        }
        
        // 检查文件大小
        if (filesize($logFile) > $this->maxSize) {
            $timestamp = date('His');
            $newFile = $logFile . ".{$timestamp}";
            rename($logFile, $newFile);
        }
        
        // 清理过期日志
        $this->cleanOldLogs();
    }
    
    /**
     * 清理过期日志
     */
    private function cleanOldLogs()
    {
        $files = glob($this->logPath . '/app-*.log*');
        if (count($files) <= $this->maxFiles) {
            return;
        }
        
        // 按修改时间排序
        usort($files, function($a, $b) {
            return filemtime($a) - filemtime($b);
        });
        
        // 删除最旧的文件
        $deleteCount = count($files) - $this->maxFiles;
        for ($i = 0; $i < $deleteCount; $i++) {
            unlink($files[$i]);
        }
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
     * 获取请求ID
     */
    private function getRequestId()
    {
        static $requestId = null;
        
        if ($requestId === null) {
            $requestId = substr(md5(uniqid(mt_rand(), true)), 0, 8);
        }
        
        return $requestId;
    }
    
    /**
     * 记录API请求日志
     */
    public function logApiRequest($method, $uri, $params = [], $response = null, $duration = 0)
    {
        $this->info('API Request', [
            'method' => $method,
            'uri' => $uri,
            'params' => $params,
            'response_code' => $response['code'] ?? null,
            'duration' => $duration . 'ms',
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
        ]);
    }
    
    /**
     * 记录数据库查询日志
     */
    public function logQuery($sql, $bindings = [], $duration = 0)
    {
        if (!DTK_CONFIG['debug']) {
            return;
        }
        
        $this->debug('Database Query', [
            'sql' => $sql,
            'bindings' => $bindings,
            'duration' => $duration . 'ms'
        ]);
    }
    
    /**
     * 记录同步日志
     */
    public function logSync($platform, $type, $status, $data = [])
    {
        $this->info('Data Sync', array_merge([
            'platform' => $platform,
            'type' => $type,
            'status' => $status
        ], $data));
    }
    
    /**
     * 记录缓存操作日志
     */
    public function logCache($operation, $key, $hit = null, $duration = 0)
    {
        $this->debug('Cache Operation', [
            'operation' => $operation,
            'key' => $key,
            'hit' => $hit,
            'duration' => $duration . 'ms'
        ]);
    }

    /**
     * 安全写入日志文件（带权限检查和容错）
     */
    private function safeWriteLog($logFile, $logLine)
    {
        // 如果禁用文件日志，直接使用系统日志
        if (!$this->fileLoggingEnabled) {
            $this->fallbackToSyslog($logLine);
            return;
        }

        try {
            // 检查日志目录是否存在，不存在则创建
            $logDir = dirname($logFile);
            if (!is_dir($logDir)) {
                if (!@mkdir($logDir, 0755, true)) {
                    // 创建目录失败，使用系统日志
                    $this->fallbackToSyslog($logLine);
                    return;
                }
            }

            // 检查目录是否可写
            if (!is_writable($logDir)) {
                // 尝试修改权限
                @chmod($logDir, 0755);
                if (!is_writable($logDir)) {
                    // 仍然不可写，使用系统日志
                    $this->fallbackToSyslog($logLine);
                    return;
                }
            }

            // 尝试写入日志文件
            $result = @file_put_contents($logFile, $logLine, FILE_APPEND | LOCK_EX);

            if ($result === false) {
                // 写入失败，使用系统日志
                $this->fallbackToSyslog($logLine);
                return;
            }

            // 设置文件权限
            @chmod($logFile, 0644);

        } catch (\Exception $e) {
            // 发生异常，使用系统日志
            $this->fallbackToSyslog($logLine);
        }
    }

    /**
     * 备用日志方案：使用系统日志
     */
    private function fallbackToSyslog($logLine)
    {
        try {
            // 解析日志数据
            $logData = json_decode(trim($logLine), true);
            if ($logData) {
                $message = sprintf(
                    '[%s] %s.%s: %s',
                    $logData['timestamp'] ?? date('Y-m-d H:i:s'),
                    $logData['level'] ?? 'INFO',
                    $logData['channel'] ?? 'app',
                    $logData['message'] ?? 'Unknown message'
                );

                // 写入系统日志
                error_log($message);
            }
        } catch (\Exception $e) {
            // 最后的备用方案：简单的error_log
            error_log('Logger: Failed to write log - ' . $logLine);
        }
    }
}
?>
