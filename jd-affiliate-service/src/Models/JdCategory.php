<?php

namespace JdAffiliate\Models;

use JdAffiliate\Services\DatabaseService;
use JdAffiliate\Utils\Helper;
use JdAffiliate\Utils\Logger;

/**
 * 京东分类模型类
 */
class JdCategory
{
    private $db;
    private $logger;
    private $table = 'categories';

    // 分类属性
    private $data = [];

    // 必需字段
    private $required = ['category_id', 'category_name'];

    // 可填充字段
    private $fillable = [
        'category_id', 'category_name', 'parent_id', 'level',
        'sort_order', 'icon', 'description', 'is_active'
    ];

    // 数据类型转换
    private $casts = [
        'id' => 'int',
        'level' => 'int',
        'sort_order' => 'int',
        'is_active' => 'bool'
    ];

    public function __construct($data = [])
    {
        $this->db = DatabaseService::getInstance();
        $this->logger = new Logger();

        if (!empty($data)) {
            $this->fill($data);
        }
    }

    /**
     * 填充数据
     */
    public function fill(array $data)
    {
        foreach ($data as $key => $value) {
            if (in_array($key, $this->fillable)) {
                $this->data[$key] = $this->castAttribute($key, $value);
            }
        }

        return $this;
    }

    /**
     * 获取属性值
     */
    public function __get($key)
    {
        return $this->getAttribute($key);
    }

    /**
     * 设置属性值
     */
    public function __set($key, $value)
    {
        $this->setAttribute($key, $value);
    }

    /**
     * 检查属性是否存在
     */
    public function __isset($key)
    {
        return isset($this->data[$key]);
    }

    /**
     * 获取属性值
     */
    public function getAttribute($key)
    {
        if (array_key_exists($key, $this->data)) {
            return $this->data[$key];
        }

        return null;
    }

    /**
     * 设置属性值
     */
    public function setAttribute($key, $value)
    {
        if (in_array($key, $this->fillable)) {
            $this->data[$key] = $this->castAttribute($key, $value);
        }

        return $this;
    }

    /**
     * 类型转换
     */
    private function castAttribute($key, $value)
    {
        if (!isset($this->casts[$key]) || $value === null) {
            return $value;
        }

        switch ($this->casts[$key]) {
            case 'int':
                return (int)$value;
            case 'bool':
                return (bool)$value;
            default:
                return $value;
        }
    }

    /**
     * 验证数据
     */
    public function validate()
    {
        $errors = [];

        // 检查必需字段
        foreach ($this->required as $field) {
            if (!isset($this->data[$field]) || $this->data[$field] === '' || $this->data[$field] === null) {
                $errors[] = "字段 {$field} 是必需的";
            }
        }

        // 验证分类ID格式
        if (isset($this->data['category_id']) && !preg_match('/^[a-zA-Z0-9_-]+$/', $this->data['category_id'])) {
            $errors[] = '分类ID格式不正确';
        }

        // 验证分类名称长度
        if (isset($this->data['category_name']) && mb_strlen($this->data['category_name']) > 100) {
            $errors[] = '分类名称长度不能超过100个字符';
        }

        // 验证层级
        if (isset($this->data['level']) && ($this->data['level'] < 1 || $this->data['level'] > 5)) {
            $errors[] = '分类层级必须在1-5之间';
        }

        // 验证排序
        if (isset($this->data['sort_order']) && $this->data['sort_order'] < 0) {
            $errors[] = '排序值不能为负数';
        }

        return $errors;
    }

    /**
     * 保存分类
     */
    public function save()
    {
        // 验证数据
        $errors = $this->validate();
        if (!empty($errors)) {
            throw new \InvalidArgumentException('数据验证失败: ' . implode(', ', $errors));
        }

        // 准备保存数据
        $saveData = $this->prepareSaveData();

        try {
            if (isset($this->data['id']) && $this->data['id'] > 0) {
                // 更新现有记录
                $where = ['id' => $this->data['id']];
                $affectedRows = $this->db->update($this->table, $saveData, $where);
                
                $this->logger->info('分类更新成功', [
                    'category_id' => $this->data['category_id'],
                    'id' => $this->data['id']
                ]);

                return $affectedRows > 0;
            } else {
                // 插入新记录
                $id = $this->db->insert($this->table, $saveData);
                $this->data['id'] = $id;
                
                $this->logger->info('分类创建成功', [
                    'category_id' => $this->data['category_id'],
                    'id' => $id
                ]);

                return $id > 0;
            }
        } catch (\Exception $e) {
            $this->logger->error('分类保存失败', [
                'category_id' => $this->data['category_id'] ?? 'unknown',
                'error' => $e->getMessage()
            ]);

            throw new \RuntimeException('分类保存失败: ' . $e->getMessage());
        }
    }

    /**
     * 准备保存数据
     */
    private function prepareSaveData()
    {
        $saveData = [];

        foreach ($this->fillable as $field) {
            if (array_key_exists($field, $this->data)) {
                $saveData[$field] = $this->data[$field];
            }
        }

        return $saveData;
    }

    /**
     * 删除分类
     */
    public function delete()
    {
        if (!isset($this->data['id']) || $this->data['id'] <= 0) {
            throw new \RuntimeException('无法删除未保存的分类');
        }

        try {
            $where = ['id' => $this->data['id']];
            $affectedRows = $this->db->delete($this->table, $where);

            $this->logger->info('分类删除成功', [
                'category_id' => $this->data['category_id'],
                'id' => $this->data['id']
            ]);

            return $affectedRows > 0;
        } catch (\Exception $e) {
            $this->logger->error('分类删除失败', [
                'category_id' => $this->data['category_id'] ?? 'unknown',
                'id' => $this->data['id'],
                'error' => $e->getMessage()
            ]);

            throw new \RuntimeException('分类删除失败: ' . $e->getMessage());
        }
    }

    /**
     * 软删除（标记为无效）
     */
    public function softDelete()
    {
        $this->data['is_active'] = false;
        return $this->save();
    }

    /**
     * 恢复软删除
     */
    public function restore()
    {
        $this->data['is_active'] = true;
        return $this->save();
    }

    /**
     * 转换为数组
     */
    public function toArray()
    {
        return $this->data;
    }

    /**
     * 转换为JSON
     */
    public function toJson()
    {
        return Helper::jsonEncode($this->data);
    }

    /**
     * 根据ID查找分类
     */
    public static function find($id)
    {
        $db = DatabaseService::getInstance();
        $sql = "SELECT * FROM jd_categories WHERE id = ?";
        $data = $db->fetchOne($sql, [$id]);

        if ($data) {
            return new static($data);
        }

        return null;
    }

    /**
     * 根据分类ID查找分类
     */
    public static function findByCategoryId($categoryId)
    {
        $db = DatabaseService::getInstance();
        $sql = "SELECT * FROM jd_categories WHERE category_id = ?";
        $data = $db->fetchOne($sql, [$categoryId]);

        if ($data) {
            return new static($data);
        }

        return null;
    }

    /**
     * 获取所有分类
     */
    public static function getAll($activeOnly = true)
    {
        $db = DatabaseService::getInstance();
        
        $sql = "SELECT * FROM jd_categories";
        $bindings = [];

        if ($activeOnly) {
            $sql .= " WHERE is_active = 1";
        }

        $sql .= " ORDER BY level ASC, sort_order ASC, category_name ASC";

        $results = $db->fetchAll($sql, $bindings);

        return array_map(function($item) {
            return new static($item);
        }, $results);
    }

    /**
     * 获取顶级分类
     */
    public static function getTopLevel($activeOnly = true)
    {
        $db = DatabaseService::getInstance();
        
        $sql = "SELECT * FROM jd_categories WHERE parent_id IS NULL";
        $bindings = [];

        if ($activeOnly) {
            $sql .= " AND is_active = 1";
        }

        $sql .= " ORDER BY sort_order ASC, category_name ASC";

        $results = $db->fetchAll($sql, $bindings);

        return array_map(function($item) {
            return new static($item);
        }, $results);
    }

    /**
     * 获取子分类
     */
    public function getChildren($activeOnly = true)
    {
        if (!isset($this->data['category_id'])) {
            return [];
        }

        $db = $this->db;
        
        $sql = "SELECT * FROM jd_categories WHERE parent_id = ?";
        $bindings = [$this->data['category_id']];

        if ($activeOnly) {
            $sql .= " AND is_active = 1";
        }

        $sql .= " ORDER BY sort_order ASC, category_name ASC";

        $results = $db->fetchAll($sql, $bindings);

        return array_map(function($item) {
            return new static($item);
        }, $results);
    }

    /**
     * 获取父分类
     */
    public function getParent()
    {
        if (!isset($this->data['parent_id']) || $this->data['parent_id'] === null) {
            return null;
        }

        return static::findByCategoryId($this->data['parent_id']);
    }

    /**
     * 获取分类路径
     */
    public function getPath()
    {
        $path = [$this];
        $current = $this;

        while ($parent = $current->getParent()) {
            array_unshift($path, $parent);
            $current = $parent;
        }

        return $path;
    }

    /**
     * 获取分类路径名称
     */
    public function getPathNames($separator = ' > ')
    {
        $path = $this->getPath();
        $names = array_map(function($category) {
            return $category->category_name;
        }, $path);

        return implode($separator, $names);
    }

    /**
     * 检查是否有子分类
     */
    public function hasChildren()
    {
        if (!isset($this->data['category_id'])) {
            return false;
        }

        $sql = "SELECT COUNT(*) FROM jd_categories WHERE parent_id = ? AND is_active = 1";
        $count = $this->db->fetchColumn($sql, [$this->data['category_id']]);

        return $count > 0;
    }

    /**
     * 获取分类树
     */
    public static function getTree($parentId = null, $activeOnly = true)
    {
        $db = DatabaseService::getInstance();
        
        $sql = "SELECT * FROM jd_categories WHERE ";
        $bindings = [];

        if ($parentId === null) {
            $sql .= "parent_id IS NULL";
        } else {
            $sql .= "parent_id = ?";
            $bindings[] = $parentId;
        }

        if ($activeOnly) {
            $sql .= " AND is_active = 1";
        }

        $sql .= " ORDER BY sort_order ASC, category_name ASC";

        $results = $db->fetchAll($sql, $bindings);

        $tree = [];
        foreach ($results as $item) {
            $category = new static($item);
            $categoryData = $category->toArray();
            $categoryData['children'] = static::getTree($category->category_id, $activeOnly);
            $tree[] = $categoryData;
        }

        return $tree;
    }

    /**
     * 批量插入分类
     */
    public static function batchInsert($categories)
    {
        if (empty($categories)) {
            return 0;
        }

        $db = DatabaseService::getInstance();
        $logger = new Logger();

        // 准备数据
        $dataList = [];
        foreach ($categories as $category) {
            if ($category instanceof static) {
                $data = $category->prepareSaveData();
            } else {
                $categoryModel = new static($category);
                $errors = $categoryModel->validate();
                if (!empty($errors)) {
                    $logger->warning('批量插入分类验证失败', [
                        'category_id' => $category['category_id'] ?? 'unknown',
                        'errors' => $errors
                    ]);
                    continue;
                }
                $data = $categoryModel->prepareSaveData();
            }
            $dataList[] = $data;
        }

        if (empty($dataList)) {
            return 0;
        }

        try {
            $successCount = $db->batchInsert('categories', $dataList, 'mysql', true);
            
            $logger->info('批量插入分类完成', [
                'total' => count($dataList),
                'success' => $successCount
            ]);

            return $successCount;
        } catch (\Exception $e) {
            $logger->error('批量插入分类失败', [
                'total' => count($dataList),
                'error' => $e->getMessage()
            ]);

            throw new \RuntimeException('批量插入分类失败: ' . $e->getMessage());
        }
    }

    /**
     * 获取分类统计信息
     */
    public static function getStats()
    {
        $db = DatabaseService::getInstance();

        $stats = [
            'total' => 0,
            'active' => 0,
            'inactive' => 0,
            'levels' => []
        ];

        try {
            // 总数统计
            $sql = "SELECT 
                        COUNT(*) as total,
                        SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active,
                        SUM(CASE WHEN is_active = 0 THEN 1 ELSE 0 END) as inactive
                    FROM jd_categories";
            
            $result = $db->fetchOne($sql);
            if ($result) {
                $stats['total'] = (int)$result['total'];
                $stats['active'] = (int)$result['active'];
                $stats['inactive'] = (int)$result['inactive'];
            }

            // 层级统计
            $sql = "SELECT level, COUNT(*) as count FROM jd_categories WHERE is_active = 1 GROUP BY level ORDER BY level";
            $levels = $db->fetchAll($sql);
            foreach ($levels as $level) {
                $stats['levels'][$level['level']] = (int)$level['count'];
            }

        } catch (\Exception $e) {
            $logger = new Logger();
            $logger->error('获取分类统计失败', ['error' => $e->getMessage()]);
        }

        return $stats;
    }
}