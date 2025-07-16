# 大淘客服务端 V2.0

基于大淘客官方SDK重新开发的多平台商品数据管理系统，支持商品数据本地存储和自动同步。

## 🚀 主要特性

- **多平台支持** - 淘宝/天猫、京东、拼多多
- **官方SDK集成** - 基于大淘客官方PHP SDK开发
- **数据本地化** - 商品数据本地存储，提升访问速度
- **自动同步** - 支持全量同步和增量同步
- **高性能缓存** - 多级缓存策略，减少API调用
- **完善管理后台** - 可视化配置和监控
- **RESTful API** - 标准化接口设计
- **详细日志** - 完整的操作和错误日志

## 📁 项目结构

```
dtk-service-v2/
├── index.php              # 统一入口
├── config/                 # 配置文件
├── src/                    # 源代码
│   ├── Services/          # 业务服务层
│   ├── Models/            # 数据模型层
│   └── Utils/             # 工具类
├── api/                   # API接口
├── admin/                 # 管理后台
├── database/              # 数据库相关
├── vendor/                # Composer依赖
├── cache/                 # 缓存目录
├── logs/                  # 日志目录
└── cron/                  # 定时任务
```

## 🔧 环境要求

- **PHP**: 7.4+
- **MySQL**: 5.7+
- **扩展**: PDO, PDO_MySQL, cURL, JSON, mbstring
- **Composer**: 用于依赖管理

## 📦 安装步骤

### 1. 下载项目
```bash
# 复制项目文件到服务器
cp -r dtk-service-v2 /var/www/html/
cd /var/www/html/dtk-service-v2
```

### 2. 安装依赖
```bash
# 安装Composer依赖
composer install

# 或者手动复制大淘客SDK
cp -r ../openapi-sdk-php vendor/dtk-developer/
```

### 3. 配置数据库
```bash
# 创建数据库
mysql -u root -p -e "CREATE DATABASE dtk_service_v2 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# 导入数据库结构
mysql -u root -p dtk_service_v2 < database/schema.sql
```

### 4. 配置权限
```bash
# 设置目录权限
chmod 755 cache/ logs/
chown -R www-data:www-data cache/ logs/
```

### 5. 配置Web服务器

#### Nginx配置
```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /var/www/html/dtk-service-v2;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php7.4-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

## ⚙️ 配置说明

### 1. 基础配置
编辑 `config/config.php` 修改基础配置：
- 调试模式
- 缓存配置
- 日志配置
- CORS配置

### 2. 数据库配置
编辑 `config/database.php` 修改数据库连接：
```php
'host' => 'localhost',
'database' => 'dtk_service_v2',
'username' => 'root',
'password' => 'your_password',
```

### 3. 平台配置
在 `config/config.php` 中配置各平台的API密钥：
```php
'taobao' => [
    'app_key' => 'your_app_key',
    'app_secret' => 'your_app_secret',
    'pid' => 'your_pid',
    'enabled' => true,
],
```

## 🔍 快速测试

### 1. 系统状态检测
```bash
curl http://your-domain.com/api/test
```

### 2. 查看API文档
访问：`http://your-domain.com/`

### 3. 管理后台
访问：`http://your-domain.com/admin/`

## 📚 API接口

### 商品接口
- `GET /api/goods` - 获取商品列表
- `GET /api/goods/detail` - 获取商品详情

### 分类接口
- `GET /api/category` - 获取分类列表

### 搜索接口
- `GET /api/search` - 搜索商品

### 同步接口
- `POST /api/sync` - 手动同步数据

## 🔄 数据同步

### 手动同步
```bash
# 同步商品数据
php cron/sync_goods.php

# 同步分类数据
php cron/sync_categories.php
```

### 自动同步（Crontab）
```bash
# 编辑定时任务
crontab -e

# 添加以下任务
# 每小时增量同步商品
0 * * * * /usr/bin/php /var/www/html/dtk-service-v2/cron/sync_goods.php incremental

# 每天凌晨2点全量同步
0 2 * * * /usr/bin/php /var/www/html/dtk-service-v2/cron/sync_goods.php full

# 每天凌晨3点同步分类
0 3 * * * /usr/bin/php /var/www/html/dtk-service-v2/cron/sync_categories.php
```

## 🛠️ 维护命令

### 清理缓存
```bash
composer clear-cache
```

### 查看日志
```bash
tail -f logs/app-$(date +%Y-%m-%d).log
```

### 数据库维护
```bash
# 清理过期数据
mysql -u root -p dtk_service_v2 -e "DELETE FROM dtk_goods WHERE is_live = 0 AND update_time < DATE_SUB(NOW(), INTERVAL 7 DAY);"
```

## 📞 技术支持

如遇问题，请检查：
1. PHP版本和扩展
2. 数据库连接
3. 目录权限
4. 大淘客API配置
5. 系统日志

## 📄 许可证

MIT License
