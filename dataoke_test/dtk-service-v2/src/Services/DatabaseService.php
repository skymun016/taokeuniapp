<?php

namespace Services;

use Utils\Logger;

/**
 * 数据库服务类
 */
class DatabaseService
{
    private static $instance = null;
    private $connections = [];
    private $logger;
    
    private function __construct()
    {
        $this->connections = [];
        $this->logger = new Logger();
    }
    
    /**
     * 获取单例实例
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        
        return self::$instance;
    }
    
    /**
     * 获取数据库连接
     */
    public function getConnection($name = 'mysql')
    {
        if (!array_key_exists($name, $this->connections) || $this->connections[$name] === null) {
            $this->connections[$name] = $this->createConnection($name);
        }

        return $this->connections[$name];
    }
    
    /**
     * 创建数据库连接
     */
    private function createConnection($name)
    {
        $config = DATABASE_CONFIG['connections'][$name] ?? null;
        
        if (!$config) {
            throw new \InvalidArgumentException("数据库连接配置 {$name} 不存在");
        }
        
        try {
            $dsn = sprintf(
                '%s:host=%s;port=%s;dbname=%s;charset=%s',
                $config['driver'],
                $config['host'],
                $config['port'],
                $config['database'],
                $config['charset']
            );
            
            $pdo = new \PDO($dsn, $config['username'], $config['password'], $config['options']);
            
            $this->logger->info('数据库连接成功', ['connection' => $name]);
            
            return $pdo;
            
        } catch (\PDOException $e) {
            $this->logger->error('数据库连接失败', [
                'connection' => $name,
                'error' => $e->getMessage()
            ]);
            
            throw new \RuntimeException("数据库连接失败: " . $e->getMessage());
        }
    }
    
    /**
     * 执行查询
     */
    public function query($sql, $params = [], $connection = 'mysql')
    {
        $startTime = microtime(true);
        
        try {
            $pdo = $this->getConnection($connection);
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            
            $duration = \Utils\Helper::calculateDuration($startTime);
            $this->logger->logQuery($sql, $params, $duration);
            
            return $stmt;
            
        } catch (\PDOException $e) {
            $this->logger->error('数据库查询失败', [
                'sql' => $sql,
                'params' => $params,
                'error' => $e->getMessage()
            ]);
            
            throw new \RuntimeException("数据库查询失败: " . $e->getMessage());
        }
    }
    
    /**
     * 查询单条记录
     */
    public function fetchOne($sql, $params = [], $connection = 'mysql')
    {
        $stmt = $this->query($sql, $params, $connection);
        return $stmt->fetch();
    }
    
    /**
     * 查询多条记录
     */
    public function fetchAll($sql, $params = [], $connection = 'mysql')
    {
        $stmt = $this->query($sql, $params, $connection);
        return $stmt->fetchAll();
    }
    
    /**
     * 插入记录
     */
    public function insert($table, $data, $connection = 'mysql')
    {
        $fields = array_keys($data);
        $placeholders = array_map(function($field) { return ":{$field}"; }, $fields);
        
        $sql = sprintf(
            "INSERT INTO %s%s (%s) VALUES (%s)",
            $this->getTablePrefix($connection),
            $table,
            implode(', ', $fields),
            implode(', ', $placeholders)
        );
        
        $this->query($sql, $data, $connection);
        
        return $this->getConnection($connection)->lastInsertId();
    }
    
    /**
     * 批量插入记录
     */
    public function batchInsert($table, $dataList, $connection = 'mysql')
    {
        if (empty($dataList)) {
            return 0;
        }
        
        $fields = array_keys($dataList[0]);
        $placeholders = '(' . implode(', ', array_map(function($field) { return ":{$field}"; }, $fields)) . ')';
        
        $sql = sprintf(
            "INSERT INTO %s%s (%s) VALUES %s",
            $this->getTablePrefix($connection),
            $table,
            implode(', ', $fields),
            $placeholders
        );
        
        $pdo = $this->getConnection($connection);
        $stmt = $pdo->prepare($sql);
        
        $successCount = 0;
        
        foreach ($dataList as $data) {
            try {
                $stmt->execute($data);
                $successCount++;
            } catch (\PDOException $e) {
                $this->logger->error('批量插入失败', [
                    'table' => $table,
                    'data' => $data,
                    'error' => $e->getMessage()
                ]);
            }
        }
        
        return $successCount;
    }
    
    /**
     * 更新记录
     */
    public function update($table, $data, $where, $connection = 'mysql')
    {
        $setClause = [];
        foreach (array_keys($data) as $field) {
            $setClause[] = "{$field} = :{$field}";
        }
        
        $whereClause = [];
        $whereParams = [];
        foreach ($where as $field => $value) {
            $whereClause[] = "{$field} = :where_{$field}";
            $whereParams["where_{$field}"] = $value;
        }
        
        $sql = sprintf(
            "UPDATE %s%s SET %s WHERE %s",
            $this->getTablePrefix($connection),
            $table,
            implode(', ', $setClause),
            implode(' AND ', $whereClause)
        );
        
        $params = array_merge($data, $whereParams);
        $stmt = $this->query($sql, $params, $connection);
        
        return $stmt->rowCount();
    }
    
    /**
     * 删除记录
     */
    public function delete($table, $where, $connection = 'mysql')
    {
        $whereClause = [];
        foreach (array_keys($where) as $field) {
            $whereClause[] = "{$field} = :{$field}";
        }
        
        $sql = sprintf(
            "DELETE FROM %s%s WHERE %s",
            $this->getTablePrefix($connection),
            $table,
            implode(' AND ', $whereClause)
        );
        
        $stmt = $this->query($sql, $where, $connection);
        
        return $stmt->rowCount();
    }
    
    /**
     * 开始事务
     */
    public function beginTransaction($connection = 'mysql')
    {
        return $this->getConnection($connection)->beginTransaction();
    }
    
    /**
     * 提交事务
     */
    public function commit($connection = 'mysql')
    {
        return $this->getConnection($connection)->commit();
    }
    
    /**
     * 回滚事务
     */
    public function rollback($connection = 'mysql')
    {
        return $this->getConnection($connection)->rollback();
    }
    
    /**
     * 获取表前缀
     */
    private function getTablePrefix($connection = 'mysql')
    {
        $config = DATABASE_CONFIG['connections'][$connection] ?? [];
        return $config['prefix'] ?? '';
    }
    
    /**
     * 检查表是否存在
     */
    public function tableExists($table, $connection = 'mysql')
    {
        $fullTable = $this->getTablePrefix($connection) . $table;
        $sql = "SHOW TABLES LIKE ?";

        $result = $this->fetchOne($sql, [$fullTable], $connection);

        return !empty($result);
    }
    
    /**
     * 获取表结构
     */
    public function getTableSchema($table, $connection = 'mysql')
    {
        $sql = "DESCRIBE " . $this->getTablePrefix($connection) . $table;
        return $this->fetchAll($sql, [], $connection);
    }
    
    /**
     * 执行SQL语句（支持参数绑定）
     */
    public function execute($sql, $params = [], $connection = 'mysql')
    {
        $startTime = microtime(true);

        try {
            $pdo = $this->getConnection($connection);
            $stmt = $pdo->prepare($sql);
            $result = $stmt->execute($params);

            $duration = \Utils\Helper::calculateDuration($startTime);
            $this->logger->logQuery($sql, $params, $duration);

            return $stmt->rowCount();

        } catch (\PDOException $e) {
            $this->logger->error('SQL执行失败', [
                'sql' => $sql,
                'params' => $params,
                'error' => $e->getMessage()
            ]);

            throw new \RuntimeException("SQL执行失败: " . $e->getMessage());
        }
    }
}
?>
