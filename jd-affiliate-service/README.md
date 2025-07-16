# 京东联盟服务 JD Affiliate Service

基于好单库API开发的京东联盟商品数据管理系统，提供商品数据获取、管理、搜索和联盟链接生成功能。

## 🚀 主要特性

- **好单库API集成** - 基于好单库官方API开发
- **商品数据管理** - 京东商品数据本地存储和管理
- **自动数据同步** - 支持全量同步和增量同步
- **高性能缓存** - 多级缓存策略，减少API调用
- **联盟链接生成** - 自动生成京东联盟推广链接
- **RESTful API** - 标准化接口设计
- **完善管理后台** - 可视化配置和监控
- **详细日志记录** - 完整的操作和错误日志

## 📁 项目结构

```
jd-affiliate-service/
├── index.php              # 统一入口文件
├── config/                 # 配置文件目录
├── src/                   # 源代码目录
├── api/                   # API接口目录
├── admin/                 # 管理后台
├── database/              # 数据库相关
├── cache/                 # 缓存目录
├── logs/                  # 日志目录
├── cron/                  # 定时任务
├── vendor/                # Composer依赖
├── .env                   # 环境变量文件
├── composer.json          # Composer配置
└── README.md              # 项目说明
```

## 🔧 环境要求

- **PHP**: 7.4+
- **MySQL**: 5.7+
- **扩展**: PDO, PDO_MySQL, cURL, JSON, mbstring
- **Composer**: 用于依赖管理

## 📦 快速开始

### 1. 安装依赖
```bash
composer install
```

### 2. 配置环境变量
```bash
cp .env.example .env
# 编辑 .env 文件，配置数据库和API密钥
```

### 3. 初始化数据库
```bash
mysql -u root -p < database/schema.sql
```

### 4. 启动服务
```bash
php -S localhost:8080 index.php
```

## 📚 API文档

访问 `http://localhost:8080` 查看完整的API文档。

## 📄 许可证

MIT License