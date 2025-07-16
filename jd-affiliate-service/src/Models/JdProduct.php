<?php

namespace JdAffiliate\Models;

use JdAffiliate\Services\DatabaseService;
use JdAffiliate\Utils\Helper;
use JdAffiliate\Utils\Logger;

/**
 * 京东商品模型类
 */
class JdProduct
{
    private $db;
    private $logger;
    private $table = 'products';

    // 商品属性
    private $data = [];

    // 必需字段
    private $required = ['product_id', 'title', 'price'];

    // 可填充字段
    private $fillable = [
        'product_id', 'title', 'sub_title', 'price', 'original_price',
        'commission_rate', 'commission', 'category_id', 'category_name',
        'shop_id', 'shop_name', 'brand_name', 'main_image', 'images',
        'description', 'sales', 'stock', 'good_comments_rate',
        'coupon_info', 'promotion_info', 'sku_info', 'is_active', 'sync_time'
    ];

    // 数据类型转换
    private $casts = [
        'id' => 'int',
        'price' => 'float',
        'original_price' => 'float',
        'commission_rate' => 'float',
        'commission' => 'float',
        'sales' => 'int',
        'stock' => 'int',
        'good_comments_rate' => 'float',
        'is_active' => 'bool',
        'images' => 'json',
        'coupon_info' => 'json',
        'promotion_info' => 'json',
        'sku_info' => 'json'
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
            case 'float':
                return (float)$value;
            case 'bool':
                return (bool)$value;
            case 'json':
                return is_string($value) ? json_decode($value, true) : $value;
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

        // 验证商品ID格式
        if (isset($this->data['product_id']) && !preg_match('/^\d+$/', $this->data['product_id'])) {
            $errors[] = '商品ID格式不正确';
        }

        // 验证价格
        if (isset($this->data['price']) && $this->data['price'] < 0) {
            $errors[] = '商品价格不能为负数';
        }

        // 验证佣金率
        if (isset($this->data['commission_rate']) && ($this->data['commission_rate'] < 0 || $this->data['commission_rate'] > 100)) {
            $errors[] = '佣金率必须在0-100之间';
        }

        // 验证销量和库存
        if (isset($this->data['sales']) && $this->data['sales'] < 0) {
            $errors[] = '销量不能为负数';
        }

        if (isset($this->data['stock']) && $this->data['stock'] < 0) {
            $errors[] = '库存不能为负数';
        }

        // 验证好评率
        if (isset($this->data['good_comments_rate']) && ($this->data['good_comments_rate'] < 0 || $this->data['good_comments_rate'] > 100)) {
            $errors[] = '好评率必须在0-100之间';
        }

        return $errors;
    }

    /**
     * 保存商品
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
                
                $this->logger->info('商品更新成功', [
                    'product_id' => $this->data['product_id'],
                    'id' => $this->data['id']
                ]);

                return $affectedRows > 0;
            } else {
                // 插入新记录
                $id = $this->db->insert($this->table, $saveData);
                $this->data['id'] = $id;
                
                $this->logger->info('商品创建成功', [
                    'product_id' => $this->data['product_id'],
                    'id' => $id
                ]);

                return $id > 0;
            }
        } catch (\Exception $e) {
            $this->logger->error('商品保存失败', [
                'product_id' => $this->data['product_id'] ?? 'unknown',
                'error' => $e->getMessage()
            ]);

            throw new \RuntimeException('商品保存失败: ' . $e->getMessage());
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
                $value = $this->data[$field];

                // JSON字段处理
                if (isset($this->casts[$field]) && $this->casts[$field] === 'json' && is_array($value)) {
                    $value = json_encode($value, JSON_UNESCAPED_UNICODE);
                }

                $saveData[$field] = $value;
            }
        }

        return $saveData;
    }

    /**
     * 删除商品
     */
    public function delete()
    {
        if (!isset($this->data['id']) || $this->data['id'] <= 0) {
            throw new \RuntimeException('无法删除未保存的商品');
        }

        try {
            $where = ['id' => $this->data['id']];
            $affectedRows = $this->db->delete($this->table, $where);

            $this->logger->info('商品删除成功', [
                'product_id' => $this->data['product_id'],
                'id' => $this->data['id']
            ]);

            return $affectedRows > 0;
        } catch (\Exception $e) {
            $this->logger->error('商品删除失败', [
                'product_id' => $this->data['product_id'] ?? 'unknown',
                'id' => $this->data['id'],
                'error' => $e->getMessage()
            ]);

            throw new \RuntimeException('商品删除失败: ' . $e->getMessage());
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
     * 根据ID查找商品
     */
    public static function find($id)
    {
        $db = DatabaseService::getInstance();
        $sql = "SELECT * FROM jd_products WHERE id = ?";
        $data = $db->fetchOne($sql, [$id]);

        if ($data) {
            return new static($data);
        }

        return null;
    }

    /**
     * 根据商品ID查找商品
     */
    public static function findByProductId($productId)
    {
        $db = DatabaseService::getInstance();
        $sql = "SELECT * FROM jd_products WHERE product_id = ?";
        $data = $db->fetchOne($sql, [$productId]);

        if ($data) {
            return new static($data);
        }

        return null;
    }

    /**
     * 获取商品列表
     */
    public static function getList($params = [])
    {
        $db = DatabaseService::getInstance();
        
        // 构建查询条件
        $where = ['is_active = 1'];
        $bindings = [];

        if (!empty($params['category_id'])) {
            $where[] = 'category_id = ?';
            $bindings[] = $params['category_id'];
        }

        if (!empty($params['shop_id'])) {
            $where[] = 'shop_id = ?';
            $bindings[] = $params['shop_id'];
        }

        if (!empty($params['brand_name'])) {
            $where[] = 'brand_name = ?';
            $bindings[] = $params['brand_name'];
        }

        if (!empty($params['min_price'])) {
            $where[] = 'price >= ?';
            $bindings[] = $params['min_price'];
        }

        if (!empty($params['max_price'])) {
            $where[] = 'price <= ?';
            $bindings[] = $params['max_price'];
        }

        if (!empty($params['min_commission_rate'])) {
            $where[] = 'commission_rate >= ?';
            $bindings[] = $params['min_commission_rate'];
        }

        if (!empty($params['max_commission_rate'])) {
            $where[] = 'commission_rate <= ?';
            $bindings[] = $params['max_commission_rate'];
        }

        if (!empty($params['keyword'])) {
            $where[] = '(title LIKE ? OR description LIKE ?)';
            $keyword = '%' . $params['keyword'] . '%';
            $bindings[] = $keyword;
            $bindings[] = $keyword;
        }

        // 构建排序
        $orderBy = 'created_at DESC';
        if (!empty($params['sort_by'])) {
            $allowedSorts = ['price', 'commission_rate', 'sales', 'created_at'];
            if (in_array($params['sort_by'], $allowedSorts)) {
                $sortOrder = (!empty($params['sort_order']) && strtolower($params['sort_order']) === 'asc') ? 'ASC' : 'DESC';
                $orderBy = $params['sort_by'] . ' ' . $sortOrder;
            }
        }

        // 构建SQL
        $sql = "SELECT * FROM jd_products WHERE " . implode(' AND ', $where) . " ORDER BY " . $orderBy;

        // 分页
        $page = max(1, $params['page'] ?? 1);
        $pageSize = min(100, max(1, $params['page_size'] ?? 20));

        $result = $db->paginate($sql, $bindings, $page, $pageSize);

        // 转换为模型对象
        $result['list'] = array_map(function($item) {
            return new static($item);
        }, $result['list']);

        return $result;
    }

    /**
     * 搜索商品
     */
    public static function search($keyword, $params = [])
    {
        $params['keyword'] = $keyword;
        return static::getList($params);
    }

    /**
     * 批量插入商品
     */
    public static function batchInsert($products)
    {
        if (empty($products)) {
            return 0;
        }

        $db = DatabaseService::getInstance();
        $logger = new Logger();

        // 准备数据
        $dataList = [];
        foreach ($products as $product) {
            if ($product instanceof static) {
                $data = $product->prepareSaveData();
            } else {
                $productModel = new static($product);
                $errors = $productModel->validate();
                if (!empty($errors)) {
                    $logger->warning('批量插入商品验证失败', [
                        'product_id' => $product['product_id'] ?? 'unknown',
                        'errors' => $errors
                    ]);
                    continue;
                }
                $data = $productModel->prepareSaveData();
            }
            $dataList[] = $data;
        }

        if (empty($dataList)) {
            return 0;
        }

        try {
            $successCount = $db->batchInsert('products', $dataList, 'mysql', true);
            
            $logger->info('批量插入商品完成', [
                'total' => count($dataList),
                'success' => $successCount
            ]);

            return $successCount;
        } catch (\Exception $e) {
            $logger->error('批量插入商品失败', [
                'total' => count($dataList),
                'error' => $e->getMessage()
            ]);

            throw new \RuntimeException('批量插入商品失败: ' . $e->getMessage());
        }
    }

    /**
     * 更新同步时间
     */
    public function updateSyncTime()
    {
        $this->data['sync_time'] = date('Y-m-d H:i:s');
        return $this->save();
    }

    /**
     * 获取商品统计信息
     */
    public static function getStats()
    {
        $db = DatabaseService::getInstance();

        $stats = [
            'total' => 0,
            'active' => 0,
            'inactive' => 0,
            'avg_price' => 0,
            'avg_commission_rate' => 0,
            'total_sales' => 0
        ];

        try {
            // 总数统计
            $sql = "SELECT 
                        COUNT(*) as total,
                        SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active,
                        SUM(CASE WHEN is_active = 0 THEN 1 ELSE 0 END) as inactive,
                        AVG(price) as avg_price,
                        AVG(commission_rate) as avg_commission_rate,
                        SUM(sales) as total_sales
                    FROM jd_products";
            
            $result = $db->fetchOne($sql);
            if ($result) {
                $stats = array_merge($stats, [
                    'total' => (int)$result['total'],
                    'active' => (int)$result['active'],
                    'inactive' => (int)$result['inactive'],
                    'avg_price' => round((float)$result['avg_price'], 2),
                    'avg_commission_rate' => round((float)$result['avg_commission_rate'], 2),
                    'total_sales' => (int)$result['total_sales']
                ]);
            }
        } catch (\Exception $e) {
            $logger = new Logger();
            $logger->error('获取商品统计失败', ['error' => $e->getMessage()]);
        }

        return $stats;
    }
}