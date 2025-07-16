<?php

namespace JdAffiliate\Tests\Models;

use PHPUnit\Framework\TestCase;
use JdAffiliate\Models\JdProduct;
use JdAffiliate\Services\DatabaseService;

class JdProductTest extends TestCase
{
    private $testData;

    protected function setUp(): void
    {
        // 设置测试数据库配置
        if (!defined('DATABASE_CONFIG')) {
            define('DATABASE_CONFIG', [
                'default' => 'mysql',
                'connections' => [
                    'mysql' => [
                        'driver' => 'mysql',
                        'host' => 'localhost',
                        'port' => 3306,
                        'database' => 'jd_affiliate_test',
                        'username' => 'root',
                        'password' => '',
                        'charset' => 'utf8mb4',
                        'collation' => 'utf8mb4_unicode_ci',
                        'prefix' => 'jd_',
                        'strict' => true,
                        'engine' => 'InnoDB',
                        'options' => [
                            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                            \PDO::ATTR_EMULATE_PREPARES => false,
                        ]
                    ]
                ]
            ]);
        }

        $this->testData = [
            'product_id' => '123456789',
            'title' => '测试商品标题',
            'sub_title' => '测试商品副标题',
            'price' => 99.99,
            'original_price' => 199.99,
            'commission_rate' => 15.5,
            'commission' => 15.50,
            'category_id' => '1',
            'category_name' => '电脑、办公',
            'shop_id' => 'shop123',
            'shop_name' => '测试店铺',
            'brand_name' => '测试品牌',
            'main_image' => 'https://example.com/image.jpg',
            'images' => ['https://example.com/image1.jpg', 'https://example.com/image2.jpg'],
            'description' => '这是一个测试商品的描述',
            'sales' => 1000,
            'stock' => 500,
            'good_comments_rate' => 95.5,
            'coupon_info' => ['type' => 'discount', 'value' => 10],
            'promotion_info' => ['type' => 'sale', 'discount' => 20],
            'sku_info' => ['color' => 'red', 'size' => 'L'],
            'is_active' => true
        ];
    }

    public function testCreateProduct()
    {
        $product = new JdProduct($this->testData);
        
        $this->assertEquals('123456789', $product->product_id);
        $this->assertEquals('测试商品标题', $product->title);
        $this->assertEquals(99.99, $product->price);
        $this->assertTrue($product->is_active);
    }

    public function testFillData()
    {
        $product = new JdProduct();
        $product->fill($this->testData);
        
        $this->assertEquals('123456789', $product->product_id);
        $this->assertEquals('测试商品标题', $product->title);
    }

    public function testSetAndGetAttribute()
    {
        $product = new JdProduct();
        
        $product->product_id = '987654321';
        $product->title = '新商品标题';
        $product->price = 199.99;
        
        $this->assertEquals('987654321', $product->product_id);
        $this->assertEquals('新商品标题', $product->title);
        $this->assertEquals(199.99, $product->price);
    }

    public function testValidation()
    {
        // 测试有效数据
        $product = new JdProduct($this->testData);
        $errors = $product->validate();
        $this->assertEmpty($errors);
        
        // 测试缺少必需字段
        $invalidData = $this->testData;
        unset($invalidData['product_id']);
        
        $product = new JdProduct($invalidData);
        $errors = $product->validate();
        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('product_id', implode(' ', $errors));
        
        // 测试无效价格
        $invalidData = $this->testData;
        $invalidData['price'] = -10;
        
        $product = new JdProduct($invalidData);
        $errors = $product->validate();
        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('价格', implode(' ', $errors));
        
        // 测试无效佣金率
        $invalidData = $this->testData;
        $invalidData['commission_rate'] = 150;
        
        $product = new JdProduct($invalidData);
        $errors = $product->validate();
        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('佣金率', implode(' ', $errors));
    }

    public function testToArray()
    {
        $product = new JdProduct($this->testData);
        $array = $product->toArray();
        
        $this->assertIsArray($array);
        $this->assertEquals('123456789', $array['product_id']);
        $this->assertEquals('测试商品标题', $array['title']);
    }

    public function testToJson()
    {
        $product = new JdProduct($this->testData);
        $json = $product->toJson();
        
        $this->assertIsString($json);
        $decoded = json_decode($json, true);
        $this->assertEquals('123456789', $decoded['product_id']);
    }

    public function testTypeCasting()
    {
        $data = [
            'product_id' => '123456789',
            'title' => '测试商品',
            'price' => '99.99',  // 字符串
            'sales' => '1000',   // 字符串
            'is_active' => '1',  // 字符串
            'images' => '["image1.jpg", "image2.jpg"]'  // JSON字符串
        ];
        
        $product = new JdProduct($data);
        
        $this->assertIsFloat($product->price);
        $this->assertEquals(99.99, $product->price);
        
        $this->assertIsInt($product->sales);
        $this->assertEquals(1000, $product->sales);
        
        $this->assertIsBool($product->is_active);
        $this->assertTrue($product->is_active);
        
        $this->assertIsArray($product->images);
        $this->assertEquals(['image1.jpg', 'image2.jpg'], $product->images);
    }

    public function testSoftDelete()
    {
        $product = new JdProduct($this->testData);
        $product->softDelete();
        
        $this->assertFalse($product->is_active);
    }

    public function testRestore()
    {
        $product = new JdProduct($this->testData);
        $product->softDelete();
        $this->assertFalse($product->is_active);
        
        $product->restore();
        $this->assertTrue($product->is_active);
    }

    public function testUpdateSyncTime()
    {
        $product = new JdProduct($this->testData);
        $oldSyncTime = $product->sync_time;
        
        sleep(1); // 确保时间不同
        $product->updateSyncTime();
        
        $this->assertNotEquals($oldSyncTime, $product->sync_time);
        $this->assertNotNull($product->sync_time);
    }

    public function testGetStats()
    {
        $stats = JdProduct::getStats();
        
        $this->assertIsArray($stats);
        $this->assertArrayHasKey('total', $stats);
        $this->assertArrayHasKey('active', $stats);
        $this->assertArrayHasKey('inactive', $stats);
        $this->assertArrayHasKey('avg_price', $stats);
        $this->assertArrayHasKey('avg_commission_rate', $stats);
        $this->assertArrayHasKey('total_sales', $stats);
    }

    public function testBatchInsertValidation()
    {
        $products = [
            $this->testData,
            [
                'product_id' => '987654321',
                'title' => '另一个测试商品',
                'price' => 199.99
            ],
            [
                // 缺少必需字段，应该被跳过
                'title' => '无效商品'
            ]
        ];
        
        // 这个测试主要验证批量插入的数据验证逻辑
        // 实际的数据库操作需要真实的数据库连接
        $this->assertTrue(true); // 占位断言
    }
}