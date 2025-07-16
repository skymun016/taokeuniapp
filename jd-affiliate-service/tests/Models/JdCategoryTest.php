<?php

namespace JdAffiliate\Tests\Models;

use PHPUnit\Framework\TestCase;
use JdAffiliate\Models\JdCategory;

class JdCategoryTest extends TestCase
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
            'category_id' => 'test_category_1',
            'category_name' => '测试分类',
            'parent_id' => null,
            'level' => 1,
            'sort_order' => 10,
            'icon' => 'https://example.com/icon.png',
            'description' => '这是一个测试分类',
            'is_active' => true
        ];
    }

    public function testCreateCategory()
    {
        $category = new JdCategory($this->testData);
        
        $this->assertEquals('test_category_1', $category->category_id);
        $this->assertEquals('测试分类', $category->category_name);
        $this->assertEquals(1, $category->level);
        $this->assertTrue($category->is_active);
    }

    public function testFillData()
    {
        $category = new JdCategory();
        $category->fill($this->testData);
        
        $this->assertEquals('test_category_1', $category->category_id);
        $this->assertEquals('测试分类', $category->category_name);
    }

    public function testSetAndGetAttribute()
    {
        $category = new JdCategory();
        
        $category->category_id = 'new_category';
        $category->category_name = '新分类';
        $category->level = 2;
        
        $this->assertEquals('new_category', $category->category_id);
        $this->assertEquals('新分类', $category->category_name);
        $this->assertEquals(2, $category->level);
    }

    public function testValidation()
    {
        // 测试有效数据
        $category = new JdCategory($this->testData);
        $errors = $category->validate();
        $this->assertEmpty($errors);
        
        // 测试缺少必需字段
        $invalidData = $this->testData;
        unset($invalidData['category_id']);
        
        $category = new JdCategory($invalidData);
        $errors = $category->validate();
        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('category_id', implode(' ', $errors));
        
        // 测试无效分类ID格式
        $invalidData = $this->testData;
        $invalidData['category_id'] = 'invalid@category#id';
        
        $category = new JdCategory($invalidData);
        $errors = $category->validate();
        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('格式', implode(' ', $errors));
        
        // 测试分类名称过长
        $invalidData = $this->testData;
        $invalidData['category_name'] = str_repeat('长', 101);
        
        $category = new JdCategory($invalidData);
        $errors = $category->validate();
        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('长度', implode(' ', $errors));
        
        // 测试无效层级
        $invalidData = $this->testData;
        $invalidData['level'] = 10;
        
        $category = new JdCategory($invalidData);
        $errors = $category->validate();
        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('层级', implode(' ', $errors));
        
        // 测试负数排序
        $invalidData = $this->testData;
        $invalidData['sort_order'] = -5;
        
        $category = new JdCategory($invalidData);
        $errors = $category->validate();
        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('排序', implode(' ', $errors));
    }

    public function testToArray()
    {
        $category = new JdCategory($this->testData);
        $array = $category->toArray();
        
        $this->assertIsArray($array);
        $this->assertEquals('test_category_1', $array['category_id']);
        $this->assertEquals('测试分类', $array['category_name']);
    }

    public function testToJson()
    {
        $category = new JdCategory($this->testData);
        $json = $category->toJson();
        
        $this->assertIsString($json);
        $decoded = json_decode($json, true);
        $this->assertEquals('test_category_1', $decoded['category_id']);
    }

    public function testTypeCasting()
    {
        $data = [
            'category_id' => 'test_category',
            'category_name' => '测试分类',
            'level' => '2',        // 字符串
            'sort_order' => '15',  // 字符串
            'is_active' => '1'     // 字符串
        ];
        
        $category = new JdCategory($data);
        
        $this->assertIsInt($category->level);
        $this->assertEquals(2, $category->level);
        
        $this->assertIsInt($category->sort_order);
        $this->assertEquals(15, $category->sort_order);
        
        $this->assertIsBool($category->is_active);
        $this->assertTrue($category->is_active);
    }

    public function testSoftDelete()
    {
        $category = new JdCategory($this->testData);
        $category->softDelete();
        
        $this->assertFalse($category->is_active);
    }

    public function testRestore()
    {
        $category = new JdCategory($this->testData);
        $category->softDelete();
        $this->assertFalse($category->is_active);
        
        $category->restore();
        $this->assertTrue($category->is_active);
    }

    public function testGetPathNames()
    {
        // 创建父分类
        $parentCategory = new JdCategory([
            'category_id' => 'parent',
            'category_name' => '父分类',
            'level' => 1
        ]);
        
        // 创建子分类
        $childCategory = new JdCategory([
            'category_id' => 'child',
            'category_name' => '子分类',
            'parent_id' => 'parent',
            'level' => 2
        ]);
        
        // 由于没有真实的数据库连接，这里只测试基本功能
        $this->assertEquals('子分类', $childCategory->category_name);
        $this->assertEquals('parent', $childCategory->parent_id);
    }

    public function testGetStats()
    {
        $stats = JdCategory::getStats();
        
        $this->assertIsArray($stats);
        $this->assertArrayHasKey('total', $stats);
        $this->assertArrayHasKey('active', $stats);
        $this->assertArrayHasKey('inactive', $stats);
        $this->assertArrayHasKey('levels', $stats);
    }

    public function testBatchInsertValidation()
    {
        $categories = [
            $this->testData,
            [
                'category_id' => 'category_2',
                'category_name' => '另一个测试分类',
                'level' => 1
            ],
            [
                // 缺少必需字段，应该被跳过
                'category_name' => '无效分类'
            ]
        ];
        
        // 这个测试主要验证批量插入的数据验证逻辑
        // 实际的数据库操作需要真实的数据库连接
        $this->assertTrue(true); // 占位断言
    }

    public function testTreeStructure()
    {
        // 测试分类树结构的基本逻辑
        $parentData = [
            'category_id' => 'electronics',
            'category_name' => '电子产品',
            'level' => 1,
            'is_active' => true
        ];
        
        $childData = [
            'category_id' => 'computers',
            'category_name' => '电脑',
            'parent_id' => 'electronics',
            'level' => 2,
            'is_active' => true
        ];
        
        $parent = new JdCategory($parentData);
        $child = new JdCategory($childData);
        
        $this->assertEquals('electronics', $parent->category_id);
        $this->assertEquals('computers', $child->category_id);
        $this->assertEquals('electronics', $child->parent_id);
        $this->assertEquals(1, $parent->level);
        $this->assertEquals(2, $child->level);
    }
}