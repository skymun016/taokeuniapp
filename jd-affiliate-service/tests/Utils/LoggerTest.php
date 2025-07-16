<?php

namespace JdAffiliate\Tests\Utils;

use PHPUnit\Framework\TestCase;
use JdAffiliate\Utils\Logger;
use JdAffiliate\Utils\EnvLoader;

class LoggerTest extends TestCase
{
    private $testLogPath;
    private $logger;

    protected function setUp(): void
    {
        // 创建临时日志目录
        $this->testLogPath = sys_get_temp_dir() . '/jd_affiliate_test_logs_' . uniqid();
        mkdir($this->testLogPath, 0755, true);

        // 设置测试环境变量
        EnvLoader::set('LOG_PATH', $this->testLogPath);
        EnvLoader::set('LOG_LEVEL', 'debug');
        EnvLoader::set('LOG_MAX_FILES', 5);
        EnvLoader::set('LOG_MAX_SIZE', 1024); // 1KB for testing

        $this->logger = new Logger();
    }

    protected function tearDown(): void
    {
        // 清理测试日志目录
        if (is_dir($this->testLogPath)) {
            $files = glob($this->testLogPath . '/*');
            foreach ($files as $file) {
                unlink($file);
            }
            rmdir($this->testLogPath);
        }
    }

    public function testDebugLogging()
    {
        $this->logger->debug('Test debug message', ['key' => 'value']);
        
        $logFile = $this->testLogPath . '/app-' . date('Y-m-d') . '.log';
        $this->assertFileExists($logFile);
        
        $content = file_get_contents($logFile);
        $this->assertStringContainsString('DEBUG: Test debug message', $content);
        $this->assertStringContainsString('"key":"value"', $content);
    }

    public function testInfoLogging()
    {
        $this->logger->info('Test info message');
        
        $logFile = $this->testLogPath . '/app-' . date('Y-m-d') . '.log';
        $this->assertFileExists($logFile);
        
        $content = file_get_contents($logFile);
        $this->assertStringContainsString('INFO: Test info message', $content);
    }

    public function testWarningLogging()
    {
        $this->logger->warning('Test warning message');
        
        $logFile = $this->testLogPath . '/app-' . date('Y-m-d') . '.log';
        $content = file_get_contents($logFile);
        $this->assertStringContainsString('WARNING: Test warning message', $content);
    }

    public function testErrorLogging()
    {
        $this->logger->error('Test error message');
        
        $logFile = $this->testLogPath . '/app-' . date('Y-m-d') . '.log';
        $content = file_get_contents($logFile);
        $this->assertStringContainsString('ERROR: Test error message', $content);
    }

    public function testLogLevel()
    {
        // 设置日志级别为 warning
        EnvLoader::set('LOG_LEVEL', 'warning');
        $logger = new Logger();

        $logger->debug('Debug message');
        $logger->info('Info message');
        $logger->warning('Warning message');
        $logger->error('Error message');

        $logFile = $this->testLogPath . '/app-' . date('Y-m-d') . '.log';
        $content = file_get_contents($logFile);

        // 只有 warning 和 error 级别的日志应该被记录
        $this->assertStringNotContainsString('DEBUG: Debug message', $content);
        $this->assertStringNotContainsString('INFO: Info message', $content);
        $this->assertStringContainsString('WARNING: Warning message', $content);
        $this->assertStringContainsString('ERROR: Error message', $content);
    }

    public function testApiRequestLogging()
    {
        $this->logger->logApiRequest('GET', '/api/products', ['page' => 1], ['code' => 1], 150);
        
        $logFile = $this->testLogPath . '/app-' . date('Y-m-d') . '.log';
        $content = file_get_contents($logFile);
        
        $this->assertStringContainsString('INFO: API Request', $content);
        $this->assertStringContainsString('"method":"GET"', $content);
        $this->assertStringContainsString('"uri":"/api/products"', $content);
        $this->assertStringContainsString('"duration":"150ms"', $content);
    }

    public function testQueryLogging()
    {
        $this->logger->logQuery('SELECT * FROM products WHERE id = ?', [123], 25);
        
        $logFile = $this->testLogPath . '/app-' . date('Y-m-d') . '.log';
        $content = file_get_contents($logFile);
        
        $this->assertStringContainsString('DEBUG: Database Query', $content);
        $this->assertStringContainsString('SELECT * FROM products', $content);
        $this->assertStringContainsString('"duration":"25ms"', $content);
    }

    public function testCacheLogging()
    {
        $this->logger->logCache('get', 'product_123', true, 5);
        
        $logFile = $this->testLogPath . '/app-' . date('Y-m-d') . '.log';
        $content = file_get_contents($logFile);
        
        $this->assertStringContainsString('DEBUG: Cache Operation', $content);
        $this->assertStringContainsString('"operation":"get"', $content);
        $this->assertStringContainsString('"key":"product_123"', $content);
        $this->assertStringContainsString('"hit":true', $content);
    }

    public function testSyncLogging()
    {
        $this->logger->logSync('products', 'completed', ['total' => 100, 'success' => 95]);
        
        $logFile = $this->testLogPath . '/app-' . date('Y-m-d') . '.log';
        $content = file_get_contents($logFile);
        
        $this->assertStringContainsString('INFO: Sync Task', $content);
        $this->assertStringContainsString('"type":"products"', $content);
        $this->assertStringContainsString('"status":"completed"', $content);
        $this->assertStringContainsString('"total":100', $content);
    }

    public function testLogStats()
    {
        // 创建一些日志
        $this->logger->info('Test message 1');
        $this->logger->info('Test message 2');
        
        $stats = $this->logger->getLogStats();
        
        $this->assertIsArray($stats);
        $this->assertArrayHasKey('total_files', $stats);
        $this->assertArrayHasKey('total_size', $stats);
        $this->assertArrayHasKey('oldest_file', $stats);
        $this->assertArrayHasKey('newest_file', $stats);
        
        $this->assertGreaterThan(0, $stats['total_files']);
        $this->assertGreaterThan(0, $stats['total_size']);
    }

    public function testClearAllLogs()
    {
        // 创建一些日志
        $this->logger->info('Test message');
        
        $logFile = $this->testLogPath . '/app-' . date('Y-m-d') . '.log';
        $this->assertFileExists($logFile);
        
        // 清空日志
        $result = $this->logger->clearAllLogs();
        $this->assertTrue($result);
        $this->assertFileDoesNotExist($logFile);
    }

    public function testLogRotation()
    {
        // 创建大量日志以触发轮转
        $largeMessage = str_repeat('This is a test message for log rotation. ', 50);
        
        for ($i = 0; $i < 10; $i++) {
            $this->logger->info($largeMessage);
        }
        
        // 检查是否有轮转的日志文件
        $files = glob($this->testLogPath . '/app-*.log*');
        $this->assertGreaterThan(1, count($files));
    }
}