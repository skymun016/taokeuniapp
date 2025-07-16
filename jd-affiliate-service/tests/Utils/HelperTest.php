<?php

namespace JdAffiliate\Tests\Utils;

use PHPUnit\Framework\TestCase;
use JdAffiliate\Utils\Helper;

class HelperTest extends TestCase
{
    protected function setUp(): void
    {
        // 清理全局变量
        $_GET = [];
        $_POST = [];
        $_SERVER = [];
    }

    public function testGetParamFromGet()
    {
        $_GET['test'] = 'value';
        $result = Helper::getParam('test');
        $this->assertEquals('value', $result);
    }

    public function testGetParamFromPost()
    {
        $_POST['test'] = 'value';
        $result = Helper::getParam('test');
        $this->assertEquals('value', $result);
    }

    public function testGetParamWithDefault()
    {
        $result = Helper::getParam('nonexistent', 'default');
        $this->assertEquals('default', $result);
    }

    public function testGetParamWithTypeConversion()
    {
        $_GET['int_value'] = '123';
        $_GET['float_value'] = '123.45';
        $_GET['bool_value'] = 'true';

        $this->assertEquals(123, Helper::getParam('int_value', null, 'int'));
        $this->assertEquals(123.45, Helper::getParam('float_value', null, 'float'));
        $this->assertTrue(Helper::getParam('bool_value', null, 'bool'));
    }

    public function testValidateRequired()
    {
        $params = ['name' => 'test', 'age' => 25];
        
        // 应该不抛出异常
        Helper::validateRequired($params, ['name', 'age']);
        
        // 应该抛出异常
        $this->expectException(\InvalidArgumentException::class);
        Helper::validateRequired($params, ['name', 'age', 'email']);
    }

    public function testCalculateDuration()
    {
        $startTime = microtime(true);
        usleep(1000); // 1ms
        $duration = Helper::calculateDuration($startTime);
        
        $this->assertGreaterThan(0, $duration);
        $this->assertLessThan(100, $duration); // 应该小于100ms
    }

    public function testGenerateId()
    {
        $id1 = Helper::generateId();
        $id2 = Helper::generateId('test_');
        
        $this->assertNotEmpty($id1);
        $this->assertStringStartsWith('test_', $id2);
        $this->assertNotEquals($id1, $id2);
    }

    public function testFormatFileSize()
    {
        $this->assertEquals('0 B', Helper::formatFileSize(0));
        $this->assertEquals('1 KB', Helper::formatFileSize(1024));
        $this->assertEquals('1 MB', Helper::formatFileSize(1024 * 1024));
        $this->assertEquals('1 GB', Helper::formatFileSize(1024 * 1024 * 1024));
    }

    public function testJsonEncode()
    {
        $data = ['name' => '测试', 'value' => 123];
        $json = Helper::jsonEncode($data);
        
        $this->assertStringContainsString('测试', $json);
        $this->assertStringContainsString('123', $json);
    }

    public function testJsonDecode()
    {
        $json = '{"name":"test","value":123}';
        $data = Helper::jsonDecode($json);
        
        $this->assertEquals('test', $data['name']);
        $this->assertEquals(123, $data['value']);
    }

    public function testJsonDecodeInvalid()
    {
        $this->expectException(\InvalidArgumentException::class);
        Helper::jsonDecode('invalid json');
    }

    public function testArrayGet()
    {
        $array = [
            'level1' => [
                'level2' => [
                    'value' => 'test'
                ]
            ]
        ];

        $this->assertEquals('test', Helper::arrayGet($array, 'level1.level2.value'));
        $this->assertEquals('default', Helper::arrayGet($array, 'nonexistent', 'default'));
    }

    public function testArraySet()
    {
        $array = [];
        Helper::arraySet($array, 'level1.level2.value', 'test');
        
        $this->assertEquals('test', $array['level1']['level2']['value']);
    }

    public function testRandomString()
    {
        $str1 = Helper::randomString(10);
        $str2 = Helper::randomString(10);
        
        $this->assertEquals(10, strlen($str1));
        $this->assertEquals(10, strlen($str2));
        $this->assertNotEquals($str1, $str2);
    }

    public function testBase64UrlEncode()
    {
        $data = 'test data with special chars +/=';
        $encoded = Helper::base64UrlEncode($data);
        $decoded = Helper::base64UrlDecode($encoded);
        
        $this->assertEquals($data, $decoded);
        $this->assertStringNotContainsString('+', $encoded);
        $this->assertStringNotContainsString('/', $encoded);
        $this->assertStringNotContainsString('=', $encoded);
    }

    public function testIsValidEmail()
    {
        $this->assertTrue(Helper::isValidEmail('test@example.com'));
        $this->assertFalse(Helper::isValidEmail('invalid-email'));
    }

    public function testIsValidUrl()
    {
        $this->assertTrue(Helper::isValidUrl('https://example.com'));
        $this->assertFalse(Helper::isValidUrl('invalid-url'));
    }

    public function testIsValidMobile()
    {
        $this->assertTrue(Helper::isValidMobile('13812345678'));
        $this->assertFalse(Helper::isValidMobile('12345678901'));
    }

    public function testTruncate()
    {
        $text = '这是一个很长的测试文本';
        $truncated = Helper::truncate($text, 5);
        
        $this->assertEquals('这是一个很...', $truncated);
    }

    public function testGetClientIp()
    {
        $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
        $ip = Helper::getClientIp();
        
        $this->assertEquals('127.0.0.1', $ip);
    }

    public function testIsPost()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $this->assertTrue(Helper::isPost());
        
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $this->assertFalse(Helper::isPost());
    }

    public function testIsGet()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $this->assertTrue(Helper::isGet());
        
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $this->assertFalse(Helper::isGet());
    }

    public function testBuildQuery()
    {
        $params = ['name' => 'test', 'value' => 123];
        $query = Helper::buildQuery($params);
        
        $this->assertStringContainsString('name=test', $query);
        $this->assertStringContainsString('value=123', $query);
    }

    public function testParseQuery()
    {
        $query = 'name=test&value=123';
        $params = Helper::parseQuery($query);
        
        $this->assertEquals('test', $params['name']);
        $this->assertEquals('123', $params['value']);
    }

    public function testGetMemoryUsage()
    {
        $usage = Helper::getMemoryUsage();
        $this->assertStringContainsString('B', $usage);
        
        $usageRaw = Helper::getMemoryUsage(false);
        $this->assertIsInt($usageRaw);
        $this->assertGreaterThan(0, $usageRaw);
    }
}