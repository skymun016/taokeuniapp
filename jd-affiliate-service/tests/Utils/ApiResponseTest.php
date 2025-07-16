<?php

namespace JdAffiliate\Tests\Utils;

use PHPUnit\Framework\TestCase;
use JdAffiliate\Utils\ApiResponse;

class ApiResponseTest extends TestCase
{
    protected function setUp(): void
    {
        // 定义错误码常量（如果未定义）
        if (!defined('ERROR_CODES')) {
            define('ERROR_CODES', [
                'SUCCESS' => 1,
                'PARAM_ERROR' => 1001,
                'API_ERROR' => 1002,
                'DATABASE_ERROR' => 1003,
                'CACHE_ERROR' => 1004,
                'NETWORK_ERROR' => 1005,
                'AUTH_ERROR' => 1006,
                'RATE_LIMIT_ERROR' => 1007,
                'PRODUCT_NOT_FOUND' => 1008,
                'LINK_GENERATE_ERROR' => 1009,
                'SYSTEM_ERROR' => 9999,
            ]);
        }
    }

    public function testSuccess()
    {
        $response = ApiResponse::success(['test' => 'data'], 'success message');
        
        $this->assertEquals(1, $response['code']);
        $this->assertEquals('success message', $response['msg']);
        $this->assertEquals(['test' => 'data'], $response['data']);
        $this->assertIsInt($response['time']);
    }

    public function testError()
    {
        $response = ApiResponse::error('error message', 500);
        
        $this->assertEquals(500, $response['code']);
        $this->assertEquals('error message', $response['msg']);
        $this->assertIsInt($response['time']);
    }

    public function testParamError()
    {
        $response = ApiResponse::paramError('参数错误');
        
        $this->assertEquals(ERROR_CODES['PARAM_ERROR'], $response['code']);
        $this->assertEquals('参数错误', $response['msg']);
    }

    public function testApiError()
    {
        $response = ApiResponse::apiError('API调用失败');
        
        $this->assertEquals(ERROR_CODES['API_ERROR'], $response['code']);
        $this->assertEquals('API调用失败', $response['msg']);
    }

    public function testDatabaseError()
    {
        $response = ApiResponse::databaseError('数据库错误');
        
        $this->assertEquals(ERROR_CODES['DATABASE_ERROR'], $response['code']);
        $this->assertEquals('数据库错误', $response['msg']);
    }

    public function testCacheError()
    {
        $response = ApiResponse::cacheError('缓存错误');
        
        $this->assertEquals(ERROR_CODES['CACHE_ERROR'], $response['code']);
        $this->assertEquals('缓存错误', $response['msg']);
    }

    public function testNetworkError()
    {
        $response = ApiResponse::networkError('网络错误');
        
        $this->assertEquals(ERROR_CODES['NETWORK_ERROR'], $response['code']);
        $this->assertEquals('网络错误', $response['msg']);
    }

    public function testAuthError()
    {
        $response = ApiResponse::authError('认证失败');
        
        $this->assertEquals(ERROR_CODES['AUTH_ERROR'], $response['code']);
        $this->assertEquals('认证失败', $response['msg']);
    }

    public function testRateLimitError()
    {
        $response = ApiResponse::rateLimitError('请求过于频繁');
        
        $this->assertEquals(ERROR_CODES['RATE_LIMIT_ERROR'], $response['code']);
        $this->assertEquals('请求过于频繁', $response['msg']);
    }

    public function testProductNotFound()
    {
        $response = ApiResponse::productNotFound('商品不存在');
        
        $this->assertEquals(ERROR_CODES['PRODUCT_NOT_FOUND'], $response['code']);
        $this->assertEquals('商品不存在', $response['msg']);
    }

    public function testLinkGenerateError()
    {
        $response = ApiResponse::linkGenerateError('链接生成失败');
        
        $this->assertEquals(ERROR_CODES['LINK_GENERATE_ERROR'], $response['code']);
        $this->assertEquals('链接生成失败', $response['msg']);
    }

    public function testSystemError()
    {
        $response = ApiResponse::systemError('系统内部错误');
        
        $this->assertEquals(ERROR_CODES['SYSTEM_ERROR'], $response['code']);
        $this->assertEquals('系统内部错误', $response['msg']);
    }

    public function testPaginated()
    {
        $list = [['id' => 1], ['id' => 2]];
        $response = ApiResponse::paginated($list, 100, 1, 20);
        
        $this->assertEquals(1, $response['code']);
        $this->assertEquals($list, $response['data']['list']);
        $this->assertEquals(100, $response['data']['total']);
        $this->assertEquals(1, $response['data']['page']);
        $this->assertEquals(20, $response['data']['pageSize']);
        $this->assertEquals(5, $response['data']['totalPages']);
    }

    public function testList()
    {
        $list = [['id' => 1], ['id' => 2]];
        $response = ApiResponse::list($list);
        
        $this->assertEquals(1, $response['code']);
        $this->assertEquals($list, $response['data']['list']);
        $this->assertEquals(2, $response['data']['total']);
    }

    public function testDetail()
    {
        $item = ['id' => 1, 'name' => 'test'];
        $response = ApiResponse::detail($item);
        
        $this->assertEquals(1, $response['code']);
        $this->assertEquals($item, $response['data']);
    }

    public function testOperationSuccess()
    {
        $response = ApiResponse::operationSuccess('操作成功');
        
        $this->assertEquals(1, $response['code']);
        $this->assertEquals('操作成功', $response['msg']);
    }

    public function testOperationFailed()
    {
        $response = ApiResponse::operationFailed('操作失败');
        
        $this->assertEquals(0, $response['code']);
        $this->assertEquals('操作失败', $response['msg']);
    }

    public function testFromException()
    {
        $exception = new \InvalidArgumentException('参数无效');
        $response = ApiResponse::fromException($exception);
        
        $this->assertEquals(ERROR_CODES['PARAM_ERROR'], $response['code']);
        $this->assertEquals('参数无效', $response['msg']);
    }

    public function testFromExceptionWithDebug()
    {
        $exception = new \RuntimeException('运行时错误');
        $response = ApiResponse::fromException($exception, true);
        
        $this->assertEquals(ERROR_CODES['SYSTEM_ERROR'], $response['code']);
        $this->assertEquals('运行时错误', $response['msg']);
        $this->assertArrayHasKey('file', $response['data']);
        $this->assertArrayHasKey('line', $response['data']);
        $this->assertArrayHasKey('trace', $response['data']);
    }

    public function testValidate()
    {
        $validResponse = [
            'code' => 1,
            'msg' => 'success',
            'data' => null,
            'time' => time()
        ];
        
        $this->assertTrue(ApiResponse::validate($validResponse));
        
        $invalidResponse = [
            'code' => 1,
            'msg' => 'success'
            // 缺少 data 和 time
        ];
        
        $this->assertFalse(ApiResponse::validate($invalidResponse));
    }

    public function testFormat()
    {
        $response = [
            'code' => '1',
            'msg' => 'success',
            'data' => null,
            'time' => '1640995200'
        ];
        
        $formatted = ApiResponse::format($response);
        
        $this->assertIsInt($formatted['code']);
        $this->assertIsString($formatted['msg']);
        $this->assertIsInt($formatted['time']);
    }

    public function testFormatInvalid()
    {
        $this->expectException(\InvalidArgumentException::class);
        ApiResponse::format(['invalid' => 'response']);
    }
}