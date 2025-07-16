# 大淘客服务端完整部署指南

## 📋 项目概述

本项目包含两个主要部分：
1. **大淘客独立服务端** (`dataoke-service/`) - 提供API接口
2. **管理后台系统** (`dataoke-service/admin/`) - 可视化管理界面
3. **数据同步服务** (`database/`) - 商品数据本地化存储

## 📁 部署文件结构

需要上传到服务器的完整目录：
```
dataoke_test/
├── dataoke-service/           # 主服务目录（必需）
│   ├── index.php             # API入口文件
│   ├── .htaccess             # Apache重写规则
│   ├── config/               # 配置文件
│   │   ├── config.php        # 大淘客API配置
│   │   ├── admin_config.php  # 管理后台配置
│   │   └── database.php      # 数据库配置
│   ├── lib/                  # 核心类库
│   ├── api/                  # API接口文件
│   ├── admin/                # 管理后台（必需）
│   │   ├── index.php         # 后台入口
│   │   ├── install.php       # 安装脚本
│   │   ├── views/            # 页面模板
│   │   ├── lib/              # 后台类库
│   │   └── assets/           # 静态资源
│   ├── openapi-sdk-php/      # 大淘客官方SDK
│   ├── cache/                # 缓存目录（自动创建）
│   ├── logs/                 # 日志目录（自动创建）
│   └── data/                 # 数据存储目录（自动创建）
├── database/                 # 数据库相关（可选）
│   ├── dataoke_schema.sql    # 商品数据表结构
│   ├── DataokeQueryService.php   # 查询服务
│   ├── DataokeSyncService.php    # 同步服务
│   └── sync_script.php       # 同步脚本
└── DEPLOYMENT_GUIDE.md       # 本部署指南
```

## 🚀 部署步骤

### 第一步：服务器环境准备

#### 1.1 环境要求
- **PHP**: 7.4+ (推荐8.0+)
- **MySQL**: 5.7+ (推荐8.0+)
- **Web服务器**: Apache 2.4+ 或 Nginx 1.18+
- **PHP扩展**: PDO, PDO_MySQL, cURL, JSON, OpenSSL

#### 1.2 检查PHP扩展
```bash
php -m | grep -E "(pdo|curl|json|openssl)"
```

### 第二步：文件上传

#### 2.1 上传项目文件
将整个 `dataoke_test` 目录上传到服务器的Web根目录：
```bash
# 示例路径
/var/www/html/dataoke_test/
# 或
/home/www/your-domain.com/dataoke_test/
```

#### 2.2 设置目录权限
```bash
# 进入项目目录
cd /path/to/dataoke_test/dataoke-service/

# 设置基本权限
chmod 755 -R .

# 设置可写目录权限
chmod 777 cache/ logs/ data/
chown -R www-data:www-data cache/ logs/ data/

# 如果使用Nginx
chown -R nginx:nginx cache/ logs/ data/
```

### 第三步：数据库配置

#### 3.1 创建数据库
```sql
-- 登录MySQL
mysql -u root -p

-- 创建数据库
CREATE DATABASE dataoke_admin CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- 创建用户（可选，推荐）
CREATE USER 'dataoke_user'@'localhost' IDENTIFIED BY 'your_password';
GRANT ALL PRIVILEGES ON dataoke_admin.* TO 'dataoke_user'@'localhost';
FLUSH PRIVILEGES;
```

#### 3.2 配置数据库连接
编辑 `dataoke-service/config/database.php`：
```php
return [
    'host' => 'localhost',
    'port' => '3306',
    'database' => 'dataoke_admin',
    'username' => 'dataoke_user',  // 或 'root'
    'password' => 'your_password',
    'charset' => 'utf8mb4'
];
```

### 第四步：Web服务器配置

#### 4.1 Apache配置
项目已包含 `.htaccess` 文件，确保Apache启用了mod_rewrite：
```bash
# 启用mod_rewrite
a2enmod rewrite

# 重启Apache
systemctl restart apache2
```

虚拟主机配置示例：
```apache
<VirtualHost *:80>
    ServerName your-domain.com
    DocumentRoot /var/www/html/dataoke_test
    
    <Directory /var/www/html/dataoke_test>
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/dataoke_error.log
    CustomLog ${APACHE_LOG_DIR}/dataoke_access.log combined
</VirtualHost>
```

#### 4.2 Nginx配置
```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /var/www/html/dataoke_test;
    index index.php;

    # 大淘客服务端
    location /dataoke-service/ {
        try_files $uri $uri/ /dataoke-service/index.php?$query_string;
        
        location ~ \.php$ {
            fastcgi_pass unix:/var/run/php/php8.0-fpm.sock;
            fastcgi_index index.php;
            fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
            include fastcgi_params;
        }
    }

    # 静态文件
    location ~* \.(css|js|png|jpg|jpeg|gif|ico|svg)$ {
        expires 1M;
        add_header Cache-Control "public, immutable";
    }

    # 安全设置
    location ~ /\. {
        deny all;
    }
    
    location ~* \.(log|cache|json)$ {
        deny all;
    }
}
```

### 第五步：运行安装脚本

#### 5.1 Web界面安装（推荐）
1. 访问安装页面：
   ```
   http://your-domain.com/dataoke-service/admin/install.php
   ```

2. 按照向导完成安装：
   - 环境检查
   - 数据库连接测试
   - 创建数据表
   - 插入初始数据

#### 5.2 命令行安装
```bash
cd /path/to/dataoke_test/dataoke-service/admin/
php install.php
```

### 第六步：配置大淘客API

#### 6.1 编辑API配置
编辑 `dataoke-service/config/config.php`：
```php
// 大淘客API配置
define('DATAOKE_CONFIG', [
    'app_key' => 'your_app_key',
    'app_secret' => 'your_app_secret',
    'pid' => 'your_pid',
    'version' => 'v1.2.4'
]);
```

#### 6.2 测试API连接
访问测试接口：
```
http://your-domain.com/dataoke-service/api/test
```

### 第七步：访问管理后台

#### 7.1 登录管理后台
访问地址：
```
http://your-domain.com/dataoke-service/admin/
```

默认账户：
- **用户名**: admin
- **密码**: admin123

#### 7.2 首次登录设置
1. 立即修改默认密码
2. 配置大淘客API参数
3. 测试API连接
4. 设置缓存和CORS参数

## 🔧 可选功能部署

### 商品数据本地化（可选）

如果需要将大淘客商品数据存储到本地数据库：

#### 1. 数据表已在安装时自动创建

#### 2. 配置数据同步
编辑 `database/sync_script.php` 中的数据库配置

#### 3. 设置定时同步
```bash
# 添加到crontab
crontab -e

# 每小时同步一次商品数据
0 * * * * /usr/bin/php /path/to/dataoke_test/database/sync_script.php sync_goods

# 每天凌晨2点同步分类数据
0 2 * * * /usr/bin/php /path/to/dataoke_test/database/sync_script.php sync_categories
```

## 🔒 安全配置

### 1. 文件权限安全
```bash
# 配置文件只读
chmod 644 dataoke-service/config/*.php

# 敏感目录禁止Web访问
echo "deny from all" > dataoke-service/data/.htaccess
echo "deny from all" > dataoke-service/logs/.htaccess
```

### 2. 数据库安全
- 使用专用数据库用户
- 定期备份数据库
- 启用MySQL慢查询日志

### 3. Web服务器安全
- 隐藏服务器版本信息
- 启用HTTPS（推荐）
- 配置防火墙规则

## 📊 监控和维护

### 1. 日志监控
```bash
# 查看API访问日志
tail -f dataoke-service/logs/$(date +%Y-%m-%d).log

# 查看错误日志
grep "ERROR" dataoke-service/logs/*.log
```

### 2. 性能监控
- 监控缓存命中率
- 检查数据库连接数
- 观察API响应时间

### 3. 定期维护
- 清理过期日志和缓存
- 更新大淘客API配置
- 备份重要数据

## 🆘 故障排除

### 常见问题

1. **500错误**
   - 检查PHP错误日志
   - 验证文件权限
   - 确认数据库连接

2. **API无响应**
   - 检查大淘客配置
   - 验证网络连接
   - 查看API日志

3. **管理后台无法访问**
   - 检查.htaccess规则
   - 验证数据库表
   - 确认session配置

### 联系支持
如遇到部署问题，请提供：
- 服务器环境信息
- 错误日志内容
- 配置文件内容（隐藏敏感信息）

---

**部署完成后，您将拥有一个功能完整的大淘客服务端和管理后台系统！**
