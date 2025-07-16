# 设计文档

## 概述

京东联盟服务是一个基于好单库API的独立后端服务，专门用于处理京东商品数据的获取、管理和联盟链接生成。该服务采用PHP开发，遵循RESTful API设计原则，提供高性能的商品数据服务。

## 架构

### 整体架构

```
jd-affiliate-service/
├── index.php              # 统一入口文件
├── config/                 # 配置文件目录
│   ├── config.php         # 主配置文件
│   ├── database.php       # 数据库配置
│   └── haodanku.php       # 好单库API配置
├── src/                   # 源代码目录
│   ├── Services/          # 业务服务层
│   │   ├── HaodankuService.php    # 好单库API服务
│   │   ├── DatabaseService.php    # 数据库服务
│   │   ├── CacheService.php       # 缓存服务
│   │   └── JdProductService.php   # 京东商品业务服务
│   ├── Models/            # 数据模型层
│   │   ├── JdProduct.php         # 京东商品模型
│   │   └── JdCategory.php        # 京东分类模型
│   └── Utils/             # 工具类
│       ├── Logger.php            # 日志工具
│       ├── Helper.php            # 通用助手
│       ├── EnvLoader.php         # 环境变量加载器
│       └── ApiResponse.php       # API响应格式化
├── api/                   # API接口目录
│   ├── products.php       # 商品相关API
│   ├── search.php         # 搜索API
│   ├── categories.php     # 分类API
│   ├── links.php          # 联盟链接API
│   ├── sync.php           # 数据同步API
│   └── test.php           # 系统测试API
├── admin/                 # 管理后台
│   └── index.php          # 管理界面
├── database/              # 数据库相关
│   ├── schema.sql         # 数据库结构
│   └── migrations/        # 数据库迁移文件
├── cache/                 # 缓存目录
├── logs/                  # 日志目录
├── cron/                  # 定时任务
│   ├── sync_products.php  # 商品同步任务
│   └── cleanup.php        # 数据清理任务
├── vendor/                # Composer依赖
├── .env                   # 环境变量文件
├── .env.example           # 环境变量示例
├── composer.json          # Composer配置
└── README.md              # 项目说明
```

### 服务层架构

1. **HaodankuService**: 负责与好单库API的所有交互
2. **JdProductService**: 处理京东商品相关的业务逻辑
3. **DatabaseService**: 提供数据库操作的统一接口
4. **CacheService**: 管理缓存策略和操作

## 组件和接口

### 1. 好单库API服务 (HaodankuService)

```php
class HaodankuService
{
    // 获取京东商品列表
    public function getJdProductList($params)
    
    // 获取京东商品详情
    public function getJdProductDetail($productId)
    
    // 搜索京东商品
    public function searchJdProducts($keyword, $params)
    
    // 生成京东联盟链接
    public function generateJdAffiliateLink($productId, $params)
    
    // 获取京东分类列表
    public function getJdCategories()
    
    // API认证和签名
    private function authenticate($params)
    
    // 发送HTTP请求
    private function makeRequest($endpoint, $params)
}
```

### 2. 京东商品服务 (JdProductService)

```php
class JdProductService
{
    // 获取商品列表（含缓存逻辑）
    public function getProductList($params)
    
    // 获取商品详情（含缓存逻辑）
    public function getProductDetail($productId)
    
    // 搜索商品
    public function searchProducts($keyword, $params)
    
    // 同步商品数据
    public function syncProducts($batchSize = 100)
    
    // 生成联盟链接
    public function generateAffiliateLink($productId, $params)
    
    // 数据格式转换
    private function formatProductData($rawData)
}
```

### 3. API接口设计

#### 商品列表接口
```
GET /api/products
参数:
- page: 页码 (默认: 1)
- pageSize: 每页数量 (默认: 20, 最大: 100)
- categoryId: 分类ID
- minPrice: 最低价格
- maxPrice: 最高价格
- minCommissionRate: 最低佣金率
- maxCommissionRate: 最高佣金率
- sortBy: 排序字段 (price|commissionRate|sales|createTime)
- sortOrder: 排序方向 (asc|desc)

响应格式:
{
    "code": 1,
    "msg": "success",
    "data": {
        "list": [...],
        "total": 1000,
        "page": 1,
        "pageSize": 20,
        "totalPages": 50
    },
    "time": 1640995200
}
```

#### 商品详情接口
```
GET /api/products/{productId}
响应格式:
{
    "code": 1,
    "msg": "success",
    "data": {
        "productId": "123456",
        "title": "商品标题",
        "price": 99.00,
        "originalPrice": 199.00,
        "commissionRate": 15.5,
        "commission": 15.34,
        "images": [...],
        "description": "商品描述",
        "shopName": "店铺名称",
        "categoryId": "1001",
        "sales": 1000,
        "stock": 500
    },
    "time": 1640995200
}
```

#### 搜索接口
```
GET /api/search
参数:
- keyword: 搜索关键词 (必需)
- page: 页码
- pageSize: 每页数量
- 其他过滤参数同商品列表

响应格式: 同商品列表
```

#### 联盟链接生成接口
```
POST /api/links/generate
请求体:
{
    "productId": "123456",
    "unionId": "your_union_id",
    "positionId": "your_position_id"
}

响应格式:
{
    "code": 1,
    "msg": "success",
    "data": {
        "shortUrl": "https://u.jd.com/xxx",
        "longUrl": "https://item.jd.com/xxx.html?...",
        "qrCode": "data:image/png;base64,..."
    },
    "time": 1640995200
}
```

## 数据模型

### 京东商品表 (jd_products)

```sql
CREATE TABLE jd_products (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    product_id VARCHAR(50) UNIQUE NOT NULL COMMENT '京东商品ID',
    title VARCHAR(500) NOT NULL COMMENT '商品标题',
    sub_title VARCHAR(500) COMMENT '商品副标题',
    price DECIMAL(10,2) NOT NULL COMMENT '现价',
    original_price DECIMAL(10,2) COMMENT '原价',
    commission_rate DECIMAL(5,2) COMMENT '佣金率(%)',
    commission DECIMAL(10,2) COMMENT '佣金金额',
    category_id VARCHAR(50) COMMENT '分类ID',
    category_name VARCHAR(100) COMMENT '分类名称',
    shop_id VARCHAR(50) COMMENT '店铺ID',
    shop_name VARCHAR(200) COMMENT '店铺名称',
    brand_name VARCHAR(100) COMMENT '品牌名称',
    main_image VARCHAR(500) COMMENT '主图URL',
    images TEXT COMMENT '商品图片JSON',
    description TEXT COMMENT '商品描述',
    sales INT DEFAULT 0 COMMENT '销量',
    stock INT DEFAULT 0 COMMENT '库存',
    good_comments_rate DECIMAL(5,2) COMMENT '好评率',
    is_active TINYINT DEFAULT 1 COMMENT '是否有效',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_category_id (category_id),
    INDEX idx_price (price),
    INDEX idx_commission_rate (commission_rate),
    INDEX idx_sales (sales),
    INDEX idx_created_at (created_at),
    INDEX idx_is_active (is_active)
);
```

### 京东分类表 (jd_categories)

```sql
CREATE TABLE jd_categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    category_id VARCHAR(50) UNIQUE NOT NULL COMMENT '分类ID',
    category_name VARCHAR(100) NOT NULL COMMENT '分类名称',
    parent_id VARCHAR(50) COMMENT '父分类ID',
    level TINYINT DEFAULT 1 COMMENT '分类层级',
    sort_order INT DEFAULT 0 COMMENT '排序',
    is_active TINYINT DEFAULT 1 COMMENT '是否有效',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_parent_id (parent_id),
    INDEX idx_level (level),
    INDEX idx_sort_order (sort_order)
);
```

### 同步日志表 (sync_logs)

```sql
CREATE TABLE sync_logs (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    sync_type VARCHAR(50) NOT NULL COMMENT '同步类型',
    start_time TIMESTAMP NOT NULL COMMENT '开始时间',
    end_time TIMESTAMP COMMENT '结束时间',
    total_count INT DEFAULT 0 COMMENT '总数量',
    success_count INT DEFAULT 0 COMMENT '成功数量',
    failed_count INT DEFAULT 0 COMMENT '失败数量',
    status VARCHAR(20) DEFAULT 'running' COMMENT '状态',
    error_message TEXT COMMENT '错误信息',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

## 错误处理

### 错误码定义

```php
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
```

### 错误处理策略

1. **API调用失败**: 实现重试机制，最多重试3次
2. **数据库连接失败**: 记录错误日志，返回系统错误
3. **缓存失败**: 降级到直接API调用
4. **速率限制**: 实现指数退避算法

## 测试策略

### 单元测试

1. **HaodankuService测试**: 模拟API响应，测试数据解析
2. **JdProductService测试**: 测试业务逻辑和数据转换
3. **缓存服务测试**: 测试缓存读写和过期机制
4. **数据库服务测试**: 测试CRUD操作

### 集成测试

1. **API接口测试**: 测试完整的请求-响应流程
2. **数据同步测试**: 测试批量数据同步功能
3. **错误处理测试**: 测试各种异常情况的处理

### 性能测试

1. **并发测试**: 测试高并发下的系统稳定性
2. **缓存性能测试**: 测试缓存命中率和响应时间
3. **数据库性能测试**: 测试大数据量下的查询性能

## 部署和运维

### 环境要求

- PHP 7.4+
- MySQL 5.7+
- Redis (可选，用于缓存)
- Nginx/Apache
- Composer

### 配置管理

使用环境变量管理敏感配置：

```bash
# 好单库API配置
HAODANKU_API_KEY=your_api_key
HAODANKU_API_SECRET=your_api_secret
HAODANKU_API_URL=https://api.haodanku.com

# 数据库配置
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=jd_affiliate
DB_USERNAME=root
DB_PASSWORD=password

# 缓存配置
CACHE_DRIVER=file
CACHE_PREFIX=jd_affiliate_
REDIS_HOST=127.0.0.1
REDIS_PORT=6379

# 日志配置
LOG_LEVEL=info
LOG_PATH=/var/log/jd-affiliate
```

### 监控和告警

1. **API响应时间监控**: 监控各接口的响应时间
2. **错误率监控**: 监控API调用失败率
3. **数据同步监控**: 监控同步任务的执行状态
4. **系统资源监控**: 监控CPU、内存、磁盘使用情况