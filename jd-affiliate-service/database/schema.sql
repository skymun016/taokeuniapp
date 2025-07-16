-- 京东联盟服务数据库结构
-- 创建时间: 2025-01-16
-- 版本: 1.0.0

-- 设置字符集
SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- 京东商品表
-- ----------------------------
DROP TABLE IF EXISTS `jd_products`;
CREATE TABLE `jd_products` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `product_id` varchar(50) NOT NULL COMMENT '京东商品ID',
  `title` varchar(500) NOT NULL COMMENT '商品标题',
  `sub_title` varchar(500) DEFAULT NULL COMMENT '商品副标题',
  `price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '现价',
  `original_price` decimal(10,2) DEFAULT NULL COMMENT '原价',
  `commission_rate` decimal(5,2) DEFAULT NULL COMMENT '佣金率(%)',
  `commission` decimal(10,2) DEFAULT NULL COMMENT '佣金金额',
  `category_id` varchar(50) DEFAULT NULL COMMENT '分类ID',
  `category_name` varchar(100) DEFAULT NULL COMMENT '分类名称',
  `shop_id` varchar(50) DEFAULT NULL COMMENT '店铺ID',
  `shop_name` varchar(200) DEFAULT NULL COMMENT '店铺名称',
  `brand_name` varchar(100) DEFAULT NULL COMMENT '品牌名称',
  `main_image` varchar(500) DEFAULT NULL COMMENT '主图URL',
  `images` text COMMENT '商品图片JSON',
  `description` text COMMENT '商品描述',
  `sales` int(11) DEFAULT '0' COMMENT '销量',
  `stock` int(11) DEFAULT '0' COMMENT '库存',
  `good_comments_rate` decimal(5,2) DEFAULT NULL COMMENT '好评率',
  `coupon_info` text COMMENT '优惠券信息JSON',
  `promotion_info` text COMMENT '促销信息JSON',
  `sku_info` text COMMENT 'SKU信息JSON',
  `is_active` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否有效(1:有效 0:无效)',
  `sync_time` timestamp NULL DEFAULT NULL COMMENT '同步时间',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_product_id` (`product_id`),
  KEY `idx_category_id` (`category_id`),
  KEY `idx_price` (`price`),
  KEY `idx_commission_rate` (`commission_rate`),
  KEY `idx_sales` (`sales`),
  KEY `idx_shop_id` (`shop_id`),
  KEY `idx_brand_name` (`brand_name`),
  KEY `idx_is_active` (`is_active`),
  KEY `idx_created_at` (`created_at`),
  KEY `idx_sync_time` (`sync_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='京东商品表';

-- ----------------------------
-- 京东分类表
-- ----------------------------
DROP TABLE IF EXISTS `jd_categories`;
CREATE TABLE `jd_categories` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `category_id` varchar(50) NOT NULL COMMENT '分类ID',
  `category_name` varchar(100) NOT NULL COMMENT '分类名称',
  `parent_id` varchar(50) DEFAULT NULL COMMENT '父分类ID',
  `level` tinyint(4) NOT NULL DEFAULT '1' COMMENT '分类层级',
  `sort_order` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
  `icon` varchar(500) DEFAULT NULL COMMENT '分类图标',
  `description` text COMMENT '分类描述',
  `is_active` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否有效(1:有效 0:无效)',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_category_id` (`category_id`),
  KEY `idx_parent_id` (`parent_id`),
  KEY `idx_level` (`level`),
  KEY `idx_sort_order` (`sort_order`),
  KEY `idx_is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='京东分类表';

-- ----------------------------
-- 同步日志表
-- ----------------------------
DROP TABLE IF EXISTS `jd_sync_logs`;
CREATE TABLE `jd_sync_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `sync_type` varchar(50) NOT NULL COMMENT '同步类型(products:商品 categories:分类)',
  `sync_mode` varchar(20) NOT NULL DEFAULT 'full' COMMENT '同步模式(full:全量 incremental:增量)',
  `start_time` timestamp NOT NULL COMMENT '开始时间',
  `end_time` timestamp NULL DEFAULT NULL COMMENT '结束时间',
  `total_count` int(11) NOT NULL DEFAULT '0' COMMENT '总数量',
  `success_count` int(11) NOT NULL DEFAULT '0' COMMENT '成功数量',
  `failed_count` int(11) NOT NULL DEFAULT '0' COMMENT '失败数量',
  `status` varchar(20) NOT NULL DEFAULT 'running' COMMENT '状态(running:运行中 completed:已完成 failed:失败)',
  `error_message` text COMMENT '错误信息',
  `extra_info` text COMMENT '额外信息JSON',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `idx_sync_type` (`sync_type`),
  KEY `idx_sync_mode` (`sync_mode`),
  KEY `idx_status` (`status`),
  KEY `idx_start_time` (`start_time`),
  KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='同步日志表';

-- ----------------------------
-- 联盟链接缓存表
-- ----------------------------
DROP TABLE IF EXISTS `jd_affiliate_links`;
CREATE TABLE `jd_affiliate_links` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `product_id` varchar(50) NOT NULL COMMENT '商品ID',
  `union_id` varchar(50) NOT NULL COMMENT '联盟ID',
  `position_id` varchar(50) DEFAULT NULL COMMENT '推广位ID',
  `site_id` varchar(50) DEFAULT NULL COMMENT '站点ID',
  `short_url` varchar(500) NOT NULL COMMENT '短链接',
  `long_url` varchar(1000) NOT NULL COMMENT '长链接',
  `qr_code` text COMMENT '二维码数据',
  `click_count` int(11) NOT NULL DEFAULT '0' COMMENT '点击次数',
  `last_click_time` timestamp NULL DEFAULT NULL COMMENT '最后点击时间',
  `expire_time` timestamp NOT NULL COMMENT '过期时间',
  `is_active` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否有效',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_product_union_position` (`product_id`,`union_id`,`position_id`),
  KEY `idx_union_id` (`union_id`),
  KEY `idx_expire_time` (`expire_time`),
  KEY `idx_is_active` (`is_active`),
  KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='联盟链接缓存表';

-- ----------------------------
-- API调用统计表
-- ----------------------------
DROP TABLE IF EXISTS `jd_api_stats`;
CREATE TABLE `jd_api_stats` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `api_name` varchar(100) NOT NULL COMMENT 'API名称',
  `method` varchar(10) NOT NULL COMMENT '请求方法',
  `endpoint` varchar(200) NOT NULL COMMENT '接口端点',
  `request_params` text COMMENT '请求参数JSON',
  `response_code` int(11) DEFAULT NULL COMMENT '响应状态码',
  `response_time` int(11) DEFAULT NULL COMMENT '响应时间(毫秒)',
  `client_ip` varchar(45) DEFAULT NULL COMMENT '客户端IP',
  `user_agent` varchar(500) DEFAULT NULL COMMENT '用户代理',
  `error_message` text COMMENT '错误信息',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `idx_api_name` (`api_name`),
  KEY `idx_method` (`method`),
  KEY `idx_response_code` (`response_code`),
  KEY `idx_client_ip` (`client_ip`),
  KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='API调用统计表';

-- ----------------------------
-- 系统配置表
-- ----------------------------
DROP TABLE IF EXISTS `jd_system_config`;
CREATE TABLE `jd_system_config` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `config_key` varchar(100) NOT NULL COMMENT '配置键',
  `config_value` text COMMENT '配置值',
  `config_type` varchar(20) NOT NULL DEFAULT 'string' COMMENT '配置类型(string:字符串 int:整数 float:浮点数 bool:布尔值 json:JSON)',
  `description` varchar(500) DEFAULT NULL COMMENT '配置描述',
  `is_system` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否系统配置(1:是 0:否)',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_config_key` (`config_key`),
  KEY `idx_config_type` (`config_type`),
  KEY `idx_is_system` (`is_system`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='系统配置表';

-- ----------------------------
-- 缓存表
-- ----------------------------
DROP TABLE IF EXISTS `jd_cache`;
CREATE TABLE `jd_cache` (
  `cache_key` varchar(255) NOT NULL COMMENT '缓存键',
  `cache_value` longtext NOT NULL COMMENT '缓存值',
  `expire_time` timestamp NOT NULL COMMENT '过期时间',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`cache_key`),
  KEY `idx_expire_time` (`expire_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='缓存表';

-- ----------------------------
-- 插入默认配置数据
-- ----------------------------
INSERT INTO `jd_system_config` (`config_key`, `config_value`, `config_type`, `description`, `is_system`) VALUES
('sync_products_enabled', '1', 'bool', '是否启用商品同步', 1),
('sync_categories_enabled', '1', 'bool', '是否启用分类同步', 1),
('sync_batch_size', '100', 'int', '同步批次大小', 1),
('cache_expire_products', '3600', 'int', '商品缓存过期时间(秒)', 1),
('cache_expire_categories', '7200', 'int', '分类缓存过期时间(秒)', 1),
('api_rate_limit', '1000', 'int', 'API速率限制(每小时)', 1),
('log_retention_days', '30', 'int', '日志保留天数', 1),
('affiliate_link_expire_hours', '24', 'int', '联盟链接过期时间(小时)', 1);

-- ----------------------------
-- 插入默认分类数据
-- ----------------------------
INSERT INTO `jd_categories` (`category_id`, `category_name`, `parent_id`, `level`, `sort_order`, `is_active`) VALUES
('1', '电脑、办公', NULL, 1, 1, 1),
('2', '手机、数码', NULL, 1, 2, 1),
('3', '家用电器', NULL, 1, 3, 1),
('4', '服饰内衣', NULL, 1, 4, 1),
('5', '家居家装', NULL, 1, 5, 1),
('6', '母婴', NULL, 1, 6, 1),
('7', '食品饮料', NULL, 1, 7, 1),
('8', '美容护肤', NULL, 1, 8, 1),
('9', '个护清洁', NULL, 1, 9, 1),
('10', '钟表', NULL, 1, 10, 1),
('11', '鞋靴', NULL, 1, 11, 1),
('12', '运动健康', NULL, 1, 12, 1),
('13', '汽车用品', NULL, 1, 13, 1),
('14', '图书', NULL, 1, 14, 1),
('15', '文娱', NULL, 1, 15, 1);

SET FOREIGN_KEY_CHECKS = 1;