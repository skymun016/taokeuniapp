<?php

namespace JdAffiliate\Utils;

/**
 * 日志工具类
 */
class Logger
{
    const LEVEL_DEBUG = 'debug';
    const LEVEL_INFO = 'info';
    const LEVEL_WARNING = 'warning';
    const LEVEL_ERROR = 'error';

    private $logPath;
    private $logLevel;
    private $maxFiles;
    private $maxSize;

    private static $levels = [
        self::LEVEL_DEBUG => 0,
        self::LEVEL_INFO => 1,
        self::LEVEL_WARNING => 2,
        self::LEVEL_ERROR => 3,
    ];

    public function __construct()
    {
        // 确保环境变量已加载
        EnvLoader::load();
        
        $this->logPath = EnvLoader::get('LOG_PATH', dirname(dirname(__DIR__)) . '/logs');
        $this->logLevel = EnvLoader::get('LOG_LEVEL', self::LEVEL_INFO);
        $this->maxFiles = EnvLoader::get('LOG_MAX_FILES', 30);
        $this->maxSize = EnvLoader::get('LOG_MAX_SIZE', 10 * 1024 * 1024);

        // 确保日志目录存在
        if (!is_dir($this->logPath)) {
            mkdir($this->logPath, 0755, true);
        }
    }

    /**
     * 记录调试日志
     */
    public function debug($message, array $context = [])
    {
        $this->log(self::LEVEL_DEBUG, $message, $context);
    }

    /**
     * 记录信息日志
     */
    public function info($message, array $context = [])
    {
        $this->log(self::LEVEL_INFO, $message, $context);
    }

    /**
     * 记录警告日志
     */
    public function warning($message, array $context = [])
    {
        $this->log(self::LEVEL_WARNING, $message, $context);
    }

    /**
     * 记录错误日志
     */
    public function error($message, array $context = [])
    {
        $this->log(self::LEVEL_ERROR, $message, $context);
    }

    /**
     * 记录API请求日志
     */
    public function logApiRequest($method, $uri, $params = [], $response = [], $duration = 0)
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
    public function logQuery($sql, $params = [], $duration = 0)
    {
        $this->debug('Database Query', [
            'sql' => $sql,
            'params' => $params,
            'duration' => $duration . 'ms'
        ]);
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
     * 记录同步任务日志
     */
    public function logSync($type, $status, $details = [])
    {
        $this->info('Sync Task', array_merge([
            'type' => $type,
            'status' => $status
        ], $details));
    }

    /**
     * 核心日志记录方法
     */
    private function log($level, $message, array $context = [])
    {
        // 检查日志级别
        if (!$this->shouldLog($level)) {
            return;
        }

        $timestamp = date('Y-m-d H:i:s');
        $logFile = $this->getLogFile();

        // 格式化日志消息
        $logEntry = $this->formatLogEntry($timestamp, $level, $message, $context);

        // 写入日志文件
        $this->writeToFile($logFile, $logEntry);

        // 检查并执行日志轮转
        $this->rotateLogIfNeeded($logFile);
    }

    /**
     * 检查是否应该记录该级别的日志
     */
    private function shouldLog($level)
    {
        $currentLevelValue = self::$levels[$this->logLevel] ?? 1;
        $logLevelValue = self::$levels[$level] ?? 0;

        return $logLevelValue >= $currentLevelValue;
    }

    /**
     * 获取当前日志文件路径
     */
    private function getLogFile()
    {
        $date = date('Y-m-d');
        return $this->logPath . '/app-' . $date . '.log';
    }

    /**
     * 格式化日志条目
     */
    private function formatLogEntry($timestamp, $level, $message, array $context = [])
    {
        $levelUpper = strtoupper($level);
        $contextStr = '';

        if (!empty($context)) {
            $contextStr = ' ' . json_encode($context, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        return "[{$timestamp}] {$levelUpper}: {$message}{$contextStr}" . PHP_EOL;
    }

    /**
     * 写入日志文件
     */
    private function writeToFile($logFile, $logEntry)
    {
        try {
            file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
        } catch (\Exception $e) {
            // 如果无法写入日志文件，尝试写入系统错误日志
            error_log("Failed to write to log file: " . $e->getMessage());
        }
    }

    /**
     * 检查并执行日志轮转
     */
    private function rotateLogIfNeeded($logFile)
    {
        if (!file_exists($logFile)) {
            return;
        }

        // 检查文件大小
        if (filesize($logFile) > $this->maxSize) {
            $this->rotateLog($logFile);
        }

        // 清理过期日志文件
        $this->cleanupOldLogs();
    }

    /**
     * 轮转日志文件
     */
    private function rotateLog($logFile)
    {
        $timestamp = date('Y-m-d_H-i-s');
        $rotatedFile = $logFile . '.' . $timestamp;

        try {
            rename($logFile, $rotatedFile);
        } catch (\Exception $e) {
            error_log("Failed to rotate log file: " . $e->getMessage());
        }
    }

    /**
     * 清理过期的日志文件
     */
    private function cleanupOldLogs()
    {
        try {
            $files = glob($this->logPath . '/app-*.log*');
            
            if (count($files) <= $this->maxFiles) {
                return;
            }

            // 按修改时间排序
            usort($files, function($a, $b) {
                return filemtime($a) - filemtime($b);
            });

            // 删除最旧的文件
            $filesToDelete = array_slice($files, 0, count($files) - $this->maxFiles);
            
            foreach ($filesToDelete as $file) {
                unlink($file);
            }
        } catch (\Exception $e) {
            error_log("Failed to cleanup old logs: " . $e->getMessage());
        }
    }

    /**
     * 获取日志统计信息
     */
    public function getLogStats()
    {
        $stats = [
            'total_files' => 0,
            'total_size' => 0,
            'oldest_file' => null,
            'newest_file' => null
        ];

        try {
            $files = glob($this->logPath . '/app-*.log*');
            $stats['total_files'] = count($files);

            if (!empty($files)) {
                $totalSize = 0;
                $oldestTime = PHP_INT_MAX;
                $newestTime = 0;

                foreach ($files as $file) {
                    $size = filesize($file);
                    $time = filemtime($file);

                    $totalSize += $size;

                    if ($time < $oldestTime) {
                        $oldestTime = $time;
                        $stats['oldest_file'] = basename($file);
                    }

                    if ($time > $newestTime) {
                        $newestTime = $time;
                        $stats['newest_file'] = basename($file);
                    }
                }

                $stats['total_size'] = $totalSize;
            }
        } catch (\Exception $e) {
            error_log("Failed to get log stats: " . $e->getMessage());
        }

        return $stats;
    }

    /**
     * 清空所有日志文件
     */
    public function clearAllLogs()
    {
        try {
            $files = glob($this->logPath . '/app-*.log*');
            
            foreach ($files as $file) {
                unlink($file);
            }

            return true;
        } catch (\Exception $e) {
            $this->error("Failed to clear logs: " . $e->getMessage());
            return false;
        }
    }
}