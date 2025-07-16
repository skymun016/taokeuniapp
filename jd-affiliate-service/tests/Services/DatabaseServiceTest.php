<?php

namespace JdAffiliate\Tests\Services;

use PHPUnit\Framework\TestCase;
use JdAffiliate\Services\DatabaseService;
use JdAffiliate\Utils\EnvLoader;

class DatabaseServiceTest extends TestCase
{
    private $db;
    private $testTable = 'test_products';

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

        $this->db = DatabaseService::getInstance();
        
        // 创建测试表
        $this->createTestTable();
    }

    protected function tearDown(): void
    {
        // 清理测试表
        $this->dropTestTable();
    }

    private function createTestTable()
    {
        try {
            $sql = "CREATE TABLE IF NOT EXISTS jd_{$this->testTable} (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255) NOT NULL,
                price DECIMAL(10,2) DEFAULT 0,
                status TINYINT DEFAULT 1,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
            
            $this->db->execute($sql);
        } catch (\Exception $e) {
            $this->markTestSkipped('无法创建测试表: ' . $e->getMessage());
        }
    }

    private function dropTestTable()
    {
        try {
            $sql = "DROP TABLE IF EXISTS jd_{$this->testTable}";
            $this->db->execute($sql);
        } catch (\Exception $e) {
            // 忽略删除错误
        }
    }

    public function testSingleton()
    {
        $db1 = DatabaseService::getInstance();
        $db2 = DatabaseService::getInstance();
        
        $this->assertSame($db1, $db2);
    }

    public function testConnection()
    {
        $connection = $this->db->getConnection();
        $this->assertInstanceOf(\PDO::class, $connection);
    }

    public function testCheckConnection()
    {
        $result = $this->db->checkConnection();
        $this->assertTrue($result);
    }

    public function testInsert()
    {
        $data = [
            'name' => '测试商品',
            'price' => 99.99,
            'status' => 1
        ];
        
        $id = $this->db->insert($this->testTable, $data);
        $this->assertGreaterThan(0, $id);
        
        return $id;
    }

    /**
     * @depends testInsert
     */
    public function testFetchOne($id)
    {
        $sql = "SELECT * FROM jd_{$this->testTable} WHERE id = ?";
        $result = $this->db->fetchOne($sql, [$id]);
        
        $this->assertIsArray($result);
        $this->assertEquals('测试商品', $result['name']);
        $this->assertEquals('99.99', $result['price']);
        
        return $result;
    }

    public function testFetchAll()
    {
        // 插入测试数据
        $this->db->insert($this->testTable, ['name' => '商品1', 'price' => 10.00]);
        $this->db->insert($this->testTable, ['name' => '商品2', 'price' => 20.00]);
        
        $sql = "SELECT * FROM jd_{$this->testTable} ORDER BY id";
        $results = $this->db->fetchAll($sql);
        
        $this->assertIsArray($results);
        $this->assertGreaterThanOrEqual(2, count($results));
    }

    public function testFetchColumn()
    {
        $this->db->insert($this->testTable, ['name' => '测试商品', 'price' => 50.00]);
        
        $sql = "SELECT COUNT(*) FROM jd_{$this->testTable}";
        $count = $this->db->fetchColumn($sql);
        
        $this->assertGreaterThan(0, $count);
    }

    public function testUpdate()
    {
        $id = $this->db->insert($this->testTable, ['name' => '原始商品', 'price' => 100.00]);
        
        $updateData = ['name' => '更新商品', 'price' => 150.00];
        $where = ['id' => $id];
        
        $affectedRows = $this->db->update($this->testTable, $updateData, $where);
        $this->assertEquals(1, $affectedRows);
        
        // 验证更新结果
        $sql = "SELECT * FROM jd_{$this->testTable} WHERE id = ?";
        $result = $this->db->fetchOne($sql, [$id]);
        $this->assertEquals('更新商品', $result['name']);
        $this->assertEquals('150.00', $result['price']);
    }

    public function testDelete()
    {
        $id = $this->db->insert($this->testTable, ['name' => '待删除商品', 'price' => 200.00]);
        
        $where = ['id' => $id];
        $affectedRows = $this->db->delete($this->testTable, $where);
        $this->assertEquals(1, $affectedRows);
        
        // 验证删除结果
        $sql = "SELECT * FROM jd_{$this->testTable} WHERE id = ?";
        $result = $this->db->fetchOne($sql, [$id]);
        $this->assertEmpty($result);
    }

    public function testBatchInsert()
    {
        $dataList = [
            ['name' => '批量商品1', 'price' => 10.00],
            ['name' => '批量商品2', 'price' => 20.00],
            ['name' => '批量商品3', 'price' => 30.00],
        ];
        
        $successCount = $this->db->batchInsert($this->testTable, $dataList);
        $this->assertEquals(3, $successCount);
        
        // 验证插入结果
        $sql = "SELECT COUNT(*) FROM jd_{$this->testTable} WHERE name LIKE '批量商品%'";
        $count = $this->db->fetchColumn($sql);
        $this->assertEquals(3, $count);
    }

    public function testTransaction()
    {
        $result = $this->db->transaction(function($db) {
            $id1 = $db->insert($this->testTable, ['name' => '事务商品1', 'price' => 100.00]);
            $id2 = $db->insert($this->testTable, ['name' => '事务商品2', 'price' => 200.00]);
            
            return [$id1, $id2];
        });
        
        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        
        // 验证事务结果
        $sql = "SELECT COUNT(*) FROM jd_{$this->testTable} WHERE name LIKE '事务商品%'";
        $count = $this->db->fetchColumn($sql);
        $this->assertEquals(2, $count);
    }

    public function testTransactionRollback()
    {
        try {
            $this->db->transaction(function($db) {
                $db->insert($this->testTable, ['name' => '回滚商品1', 'price' => 100.00]);
                $db->insert($this->testTable, ['name' => '回滚商品2', 'price' => 200.00]);
                
                // 故意抛出异常触发回滚
                throw new \Exception('测试回滚');
            });
        } catch (\Exception $e) {
            $this->assertEquals('测试回滚', $e->getMessage());
        }
        
        // 验证回滚结果
        $sql = "SELECT COUNT(*) FROM jd_{$this->testTable} WHERE name LIKE '回滚商品%'";
        $count = $this->db->fetchColumn($sql);
        $this->assertEquals(0, $count);
    }

    public function testTableExists()
    {
        $exists = $this->db->tableExists($this->testTable);
        $this->assertTrue($exists);
        
        $notExists = $this->db->tableExists('non_existent_table');
        $this->assertFalse($notExists);
    }

    public function testGetTableSchema()
    {
        $schema = $this->db->getTableSchema($this->testTable);
        
        $this->assertIsArray($schema);
        $this->assertGreaterThan(0, count($schema));
        
        // 检查是否包含预期的字段
        $fields = array_column($schema, 'Field');
        $this->assertContains('id', $fields);
        $this->assertContains('name', $fields);
        $this->assertContains('price', $fields);
    }

    public function testPaginate()
    {
        // 插入测试数据
        for ($i = 1; $i <= 25; $i++) {
            $this->db->insert($this->testTable, [
                'name' => "分页商品{$i}",
                'price' => $i * 10
            ]);
        }
        
        $sql = "SELECT * FROM jd_{$this->testTable} WHERE name LIKE '分页商品%' ORDER BY id";
        $result = $this->db->paginate($sql, [], 1, 10);
        
        $this->assertIsArray($result);
        $this->assertArrayHasKey('list', $result);
        $this->assertArrayHasKey('total', $result);
        $this->assertArrayHasKey('page', $result);
        $this->assertArrayHasKey('pageSize', $result);
        $this->assertArrayHasKey('totalPages', $result);
        
        $this->assertEquals(25, $result['total']);
        $this->assertEquals(1, $result['page']);
        $this->assertEquals(10, $result['pageSize']);
        $this->assertEquals(3, $result['totalPages']);
        $this->assertCount(10, $result['list']);
    }

    public function testGetConnectionInfo()
    {
        $info = $this->db->getConnectionInfo();
        
        if ($info !== null) {
            $this->assertIsArray($info);
            $this->assertArrayHasKey('server_version', $info);
            $this->assertArrayHasKey('client_version', $info);
        }
    }

    public function testGetDatabaseSize()
    {
        $size = $this->db->getDatabaseSize();
        
        if ($size !== null) {
            $this->assertIsArray($size);
            $this->assertArrayHasKey('size_bytes', $size);
            $this->assertArrayHasKey('table_count', $size);
            $this->assertArrayHasKey('size_formatted', $size);
        }
    }
}