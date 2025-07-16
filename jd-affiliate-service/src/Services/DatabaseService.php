<?php

namespace JdAffiliate\Services;

use JdAffiliate\Utils\Logger;
use JdAffiliate\Utils\Helper;

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
        if (!defined('DATABASE_CONFIG')) {
            throw new \RuntimeException('数据库配置未定义');
        }

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
            
            $this->logger->info('数据库连接成功', ['connection' => $name, 'database' => $config['database']]);
            
            return $pdo;
            
        } catch (\PDOException $e) {
            $this->logger->error('数据库连接失败', [
                'connection' => $name,
                'error' => $e->getMessage(),
                'host' => $config['host'],
                'database' => $config['database']
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
            
            $duration = Helper::calculateDuration($startTime);
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
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
    
    /**
     * 查询多条记录
     */
    public function fetchAll($sql, $params = [], $connection = 'mysql')
    {
        $stmt = $this->query($sql, $params, $connection);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    /**
     * 查询单个值
     */
    public function fetchColumn($sql, $params = [], $column = 0, $connection = 'mysql')
    {
        $stmt = $this->query($sql, $params, $connection);
        return $stmt->fetchColumn($column);
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
    public function batchInsert($table, $dataList, $connection = 'mysql', $ignore = false)
    {
        if (empty($dataList)) {
            return 0;
        }
        
        $fields = array_keys($dataList[0]);
        $placeholders = '(' . implode(', ', array_map(function($field) { return ":{$field}"; }, $fields)) . ')';
        
        $insertType = $ignore ? 'INSERT IGNORE' : 'INSERT';
        $sql = sprintf(
            "%s INTO %s%s (%s) VALUES %s",
            $insertType,
            $this->getTablePrefix($connection),
            $table,
            implode(', ', $fields),
            $placeholders
        );
        
        $pdo = $this->getConnection($connection);
        $stmt = $pdo->prepare($sql);
        
        $successCount = 0;
        $failedCount = 0;
        
        foreach ($dataList as $data) {
            try {
                $stmt->execute($data);
                $successCount++;
            } catch (\PDOException $e) {
                $failedCount++;
                $this->logger->error('批量插入失败', [
                    'table' => $table,
                    'data' => $data,
                    'error' => $e->getMessage()
                ]);
            }
        }
        
        $this->logger->info('批量插入完成', [
            'table' => $table,
            'total' => count($dataList),
            'success' => $successCount,
            'failed' => $failedCount
        ]);
        
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
        $pdo = $this->getConnection($connection);
        $result = $pdo->beginTransaction();
        
        $this->logger->debug('开始事务', ['connection' => $connection]);
        
        return $result;
    }
    
    /**
     * 提交事务
     */
    public function commit($connection = 'mysql')
    {
        $pdo = $this->getConnection($connection);
        $result = $pdo->commit();
        
        $this->logger->debug('提交事务', ['connection' => $connection]);
        
        return $result;
    }
    
    /**
     * 回滚事务
     */
    public function rollback($connection = 'mysql')
    {
        $pdo = $this->getConnection($connection);
        $result = $pdo->rollback();
        
        $this->logger->debug('回滚事务', ['connection' => $connection]);
        
        return $result;
    }
    
    /**
     * 执行事务
     */
    public function transaction(callable $callback, $connection = 'mysql')
    {
        $this->beginTransaction($connection);
        
        try {
            $result = $callback($this);
            $this->commit($connection);
            return $result;
        } catch (\Exception $e) {
            $this->rollback($connection);
            throw $e;
        }
    }
    
    /**
     * 获取表前缀
     */
    private function getTablePrefix($connection = 'mysql')
    {
        if (!defined('DATABASE_CONFIG')) {
            return '';
        }

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
        $fullTable = $this->getTablePrefix($connection) . $table;
        $sql = "DESCRIBE " . $fullTable;
        return $this->fetchAll($sql, [], $connection);
    }
    
    /**
     * 获取表的索引信息
     */
    public function getTableIndexes($table, $connection = 'mysql')
    {
        $fullTable = $this->getTablePrefix($connection) . $table;
        $sql = "SHOW INDEX FROM " . $fullTable;
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

            $duration = Helper::calculateDuration($startTime);
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
    
    /**
     * 获取数据库连接信息
     */
    public function getConnectionInfo($connection = 'mysql')
    {
        try {
            $pdo = $this->getConnection($connection);
            
            return [
                'server_version' => $pdo->getAttribute(\PDO::ATTR_SERVER_VERSION),
                'client_version' => $pdo->getAttribute(\PDO::ATTR_CLIENT_VERSION),
                'connection_status' => $pdo->getAttribute(\PDO::ATTR_CONNECTION_STATUS),
                'server_info' => $pdo->getAttribute(\PDO::ATTR_SERVER_INFO),
            ];
        } catch (\Exception $e) {
            $this->logger->error('获取数据库连接信息失败', ['error' => $e->getMessage()]);
            return null;
        }
    }
    
    /**
     * 检查数据库连接状态
     */
    public function checkConnection($connection = 'mysql')
    {
        try {
            $pdo = $this->getConnection($connection);
            $pdo->query('SELECT 1');
            return true;
        } catch (\Exception $e) {
            $this->logger->error('数据库连接检查失败', [
                'connection' => $connection,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
    
    /**
     * 获取数据库大小
     */
    public function getDatabaseSize($connection = 'mysql')
    {
        try {
            if (!defined('DATABASE_CONFIG')) {
                return null;
            }

            $config = DATABASE_CONFIG['connections'][$connection] ?? null;
            if (!$config) {
                return null;
            }

            $sql = "SELECT 
                        SUM(data_length + index_length) as size_bytes,
                        COUNT(*) as table_count
                    FROM information_schema.tables 
                    WHERE table_schema = ?";
            
            $result = $this->fetchOne($sql, [$config['database']], $connection);
            
            if ($result) {
                $result['size_formatted'] = Helper::formatFileSize($result['size_bytes']);
            }
            
            return $result;
        } catch (\Exception $e) {
            $this->logger->error('获取数据库大小失败', ['error' => $e->getMessage()]);
            return null;
        }
    }
    
    /**
     * 优化表
     */
    public function optimizeTable($table, $connection = 'mysql')
    {
        try {
            $fullTable = $this->getTablePrefix($connection) . $table;
            $sql = "OPTIMIZE TABLE " . $fullTable;
            $this->execute($sql, [], $connection);
            
            $this->logger->info('表优化完成', ['table' => $fullTable]);
            return true;
        } catch (\Exception $e) {
            $this->logger->error('表优化失败', [
                'table' => $table,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
    
    /**
     * 分页查询
     */
    public function paginate($sql, $params = [], $page = 1, $pageSize = 20, $connection = 'mysql')
    {
        // 计算总数
        $countSql = "SELECT COUNT(*) as total FROM ({$sql}) as count_table";
        $total = $this->fetchColumn($countSql, $params, 0, $connection);
        
        // 计算偏移量
        $offset = ($page - 1) * $pageSize;
        
        // 添加LIMIT子句
        $limitSql = $sql . " LIMIT {$offset}, {$pageSize}";
        $list = $this->fetchAll($limitSql, $params, $connection);
        
        return [
            'list' => $list,
            'total' => (int)$total,
            'page' => (int)$page,
            'pageSize' => (int)$pageSize,
            'totalPages' => (int)ceil($total / $pageSize)
        ];
    }
    
    /**
     * 关闭所有连接
     */
    public function closeAllConnections()
    {
        foreach ($this->connections as $name => $connection) {
            $this->connections[$name] = null;
            $this->logger->debug('关闭数据库连接', ['connection' => $name]);
        }
        
        $this->connections = [];
    }
    
    /**
     * 析构函数
     */
    public function __destruct()
    {
        $this->closeAllConnections();
    }
}