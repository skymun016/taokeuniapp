-- 大淘客服务端 V2.0 数据库结构
-- 创建时间: 2025-07-16
-- 说明: 支持多平台商品数据管理和自动同步

-- 1. 商品信息表（支持多平台）
CREATE TABLE `dtk_goods` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `goods_id` varchar(100) NOT NULL COMMENT '商品ID（平台内唯一）',
  `item_id` varchar(100) DEFAULT NULL COMMENT '原始商品ID',
  `platform` varchar(20) NOT NULL DEFAULT 'taobao' COMMENT '平台标识',
  `title` varchar(500) NOT NULL COMMENT '商品标题',
  `dtitle` varchar(500) DEFAULT NULL COMMENT '推广标题',
  `original_price` decimal(10,2) DEFAULT NULL COMMENT '原价',
  `actual_price` decimal(10,2) DEFAULT NULL COMMENT '券后价',
  `coupon_price` decimal(10,2) DEFAULT 0.00 COMMENT '优惠券金额',
  `coupon_conditions` varchar(50) DEFAULT NULL COMMENT '优惠券使用条件',
  `coupon_start_time` datetime DEFAULT NULL COMMENT '优惠券开始时间',
  `coupon_end_time` datetime DEFAULT NULL COMMENT '优惠券结束时间',
  `coupon_total_num` int(11) DEFAULT 0 COMMENT '优惠券总数量',
  `coupon_remain_count` int(11) DEFAULT 0 COMMENT '优惠券剩余数量',
  `commission_rate` decimal(5,2) DEFAULT NULL COMMENT '佣金比例',
  `commission_type` tinyint(4) DEFAULT NULL COMMENT '佣金类型',
  `month_sales` int(11) DEFAULT 0 COMMENT '月销量',
  `two_hours_sales` int(11) DEFAULT 0 COMMENT '2小时销量',
  `daily_sales` int(11) DEFAULT 0 COMMENT '日销量',
  `main_pic` varchar(500) DEFAULT NULL COMMENT '主图链接',
  `marketing_main_pic` varchar(500) DEFAULT NULL COMMENT '营销主图',
  `detail_pics` text COMMENT '详情图片JSON',
  `item_link` varchar(1000) DEFAULT NULL COMMENT '商品链接',
  `coupon_link` varchar(1000) DEFAULT NULL COMMENT '优惠券链接',
  `shop_name` varchar(200) DEFAULT NULL COMMENT '店铺名称',
  `shop_type` tinyint(4) DEFAULT NULL COMMENT '店铺类型 1-天猫 0-淘宝',
  `shop_level` int(11) DEFAULT NULL COMMENT '店铺等级',
  `seller_id` varchar(100) DEFAULT NULL COMMENT '卖家ID',
  `brand_name` varchar(200) DEFAULT NULL COMMENT '品牌名称',
  `brand_id` int(11) DEFAULT NULL COMMENT '品牌ID',
  `cid` int(11) DEFAULT NULL COMMENT '一级分类ID',
  `subcid` varchar(200) DEFAULT NULL COMMENT '二级分类ID JSON',
  `tbcid` int(11) DEFAULT NULL COMMENT '淘宝分类ID',
  `desc_score` decimal(3,1) DEFAULT NULL COMMENT '描述评分',
  `service_score` decimal(3,1) DEFAULT NULL COMMENT '服务评分',
  `ship_score` decimal(3,1) DEFAULT NULL COMMENT '物流评分',
  `is_brand` tinyint(1) DEFAULT 0 COMMENT '是否品牌商品',
  `is_live` tinyint(1) DEFAULT 1 COMMENT '是否有效 1-有效 0-失效',
  `hot_push` int(11) DEFAULT 0 COMMENT '热推值',
  `activity_type` tinyint(4) DEFAULT NULL COMMENT '活动类型',
  `activity_start_time` datetime DEFAULT NULL COMMENT '活动开始时间',
  `activity_end_time` datetime DEFAULT NULL COMMENT '活动结束时间',
  `yunfeixian` tinyint(1) DEFAULT 0 COMMENT '是否运费险',
  `freeship_remote_district` tinyint(1) DEFAULT 0 COMMENT '偏远地区包邮',
  `data_version` int(11) DEFAULT 1 COMMENT '数据版本号',
  `sync_time` datetime DEFAULT NULL COMMENT '同步时间',
  -- 转链相关字段（智能混合策略）
  `privilege_link` varchar(1000) DEFAULT NULL COMMENT '推广链接',
  `coupon_click_url` varchar(1000) DEFAULT NULL COMMENT '优惠券推广链接',
  `tpwd` varchar(50) DEFAULT NULL COMMENT '淘口令',
  `link_expire_time` datetime DEFAULT NULL COMMENT '转链过期时间',
  `link_status` tinyint(1) DEFAULT 0 COMMENT '转链状态 0-未转链 1-已转链 2-已过期',
  `tier_level` tinyint(1) DEFAULT 3 COMMENT '商品层级 1-热门 2-普通 3-冷门',
  `last_convert_time` datetime DEFAULT NULL COMMENT '最后转链时间',
  `convert_count` int(11) DEFAULT 0 COMMENT '转链次数统计',
  `estimated_commission` decimal(10,2) DEFAULT NULL COMMENT '预估佣金金额',
  `create_time` datetime DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `update_time` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_platform_goods_id` (`platform`, `goods_id`),
  KEY `idx_platform` (`platform`),
  KEY `idx_cid` (`cid`),
  KEY `idx_actual_price` (`actual_price`),
  KEY `idx_commission_rate` (`commission_rate`),
  KEY `idx_month_sales` (`month_sales`),
  KEY `idx_is_live` (`is_live`),
  KEY `idx_sync_time` (`sync_time`),
  KEY `idx_title` (`title`(100)),
  KEY `idx_brand_name` (`brand_name`),
  KEY `idx_platform_cid` (`platform`, `cid`),
  KEY `idx_platform_live` (`platform`, `is_live`),
  KEY `idx_coupon_end_time` (`coupon_end_time`),
  KEY `idx_activity_end_time` (`activity_end_time`),
  -- 转链相关索引
  KEY `idx_tier_level` (`tier_level`),
  KEY `idx_link_status` (`link_status`),
  KEY `idx_link_expire_time` (`link_expire_time`),
  KEY `idx_tier_status` (`tier_level`, `link_status`),
  KEY `idx_platform_tier` (`platform`, `tier_level`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='商品信息表（多平台）';

-- 2. 商品分类表（支持多平台）
CREATE TABLE `dtk_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `platform` varchar(20) NOT NULL DEFAULT 'taobao' COMMENT '平台标识',
  `cid` int(11) NOT NULL COMMENT '分类ID',
  `cname` varchar(100) NOT NULL COMMENT '分类名称',
  `parent_id` int(11) DEFAULT 0 COMMENT '父分类ID',
  `level` tinyint(4) DEFAULT 1 COMMENT '分类层级',
  `sort_order` int(11) DEFAULT 0 COMMENT '排序',
  `is_active` tinyint(1) DEFAULT 1 COMMENT '是否启用',
  `icon_url` varchar(500) DEFAULT NULL COMMENT '分类图标',
  `description` varchar(500) DEFAULT NULL COMMENT '分类描述',
  `goods_count` int(11) DEFAULT 0 COMMENT '商品数量',
  `create_time` datetime DEFAULT CURRENT_TIMESTAMP,
  `update_time` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_platform_cid` (`platform`, `cid`),
  KEY `idx_platform` (`platform`),
  KEY `idx_parent_id` (`parent_id`),
  KEY `idx_level` (`level`),
  KEY `idx_is_active` (`is_active`),
  KEY `idx_sort_order` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='商品分类表（多平台）';

-- 3. 数据同步日志表
CREATE TABLE `dtk_sync_logs` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `platform` varchar(20) NOT NULL COMMENT '平台标识',
  `sync_type` varchar(50) NOT NULL COMMENT '同步类型（goods/category/promotion）',
  `sync_mode` varchar(20) DEFAULT 'auto' COMMENT '同步模式（auto/manual）',
  `status` tinyint(4) NOT NULL DEFAULT 0 COMMENT '状态 0-进行中 1-成功 2-失败',
  `start_time` datetime NOT NULL COMMENT '开始时间',
  `end_time` datetime DEFAULT NULL COMMENT '结束时间',
  `duration` int(11) DEFAULT 0 COMMENT '执行时长（秒）',
  `total_count` int(11) DEFAULT 0 COMMENT '总数量',
  `success_count` int(11) DEFAULT 0 COMMENT '成功数量',
  `error_count` int(11) DEFAULT 0 COMMENT '失败数量',
  `error_message` text COMMENT '错误信息',
  `sync_params` text COMMENT '同步参数JSON',
  `result_summary` text COMMENT '结果摘要JSON',
  `create_time` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_platform` (`platform`),
  KEY `idx_sync_type` (`sync_type`),
  KEY `idx_status` (`status`),
  KEY `idx_start_time` (`start_time`),
  KEY `idx_platform_type` (`platform`, `sync_type`),
  KEY `idx_platform_status` (`platform`, `status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='数据同步日志表';

-- 4. 推广链接缓存表
CREATE TABLE `dtk_promotion_cache` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `goods_id` varchar(100) NOT NULL COMMENT '商品ID',
  `platform` varchar(20) NOT NULL COMMENT '平台标识',
  `pid` varchar(100) NOT NULL COMMENT '推广位ID',
  `item_url` varchar(1000) DEFAULT NULL COMMENT '推广链接',
  `short_url` varchar(500) DEFAULT NULL COMMENT '短链接',
  `kuaizhan_url` varchar(500) DEFAULT NULL COMMENT '快站链接',
  `tpwd` varchar(50) DEFAULT NULL COMMENT '淘口令',
  `long_tpwd` varchar(200) DEFAULT NULL COMMENT '长淘口令',
  `commission_rate` decimal(5,2) DEFAULT NULL COMMENT '佣金比例',
  `coupon_click_url` varchar(1000) DEFAULT NULL COMMENT '优惠券链接',
  `expire_time` datetime DEFAULT NULL COMMENT '过期时间',
  `click_count` int(11) DEFAULT 0 COMMENT '点击次数',
  `last_click_time` datetime DEFAULT NULL COMMENT '最后点击时间',
  `create_time` datetime DEFAULT CURRENT_TIMESTAMP,
  `update_time` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_goods_platform_pid` (`goods_id`, `platform`, `pid`),
  KEY `idx_platform` (`platform`),
  KEY `idx_expire_time` (`expire_time`),
  KEY `idx_click_count` (`click_count`),
  KEY `idx_last_click_time` (`last_click_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='推广链接缓存表';

-- 5. 平台配置表
CREATE TABLE `dtk_platforms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `platform_code` varchar(20) NOT NULL COMMENT '平台代码',
  `platform_name` varchar(50) NOT NULL COMMENT '平台名称',
  `app_key` varchar(100) DEFAULT NULL COMMENT 'AppKey',
  `app_secret` varchar(100) DEFAULT NULL COMMENT 'AppSecret',
  `pid` varchar(100) DEFAULT NULL COMMENT '推广位ID',
  `version` varchar(20) DEFAULT 'v1.0.0' COMMENT 'API版本',
  `api_url` varchar(200) DEFAULT NULL COMMENT 'API接口地址',
  `timeout` int(11) DEFAULT 30 COMMENT '请求超时时间',
  `retry_times` int(11) DEFAULT 3 COMMENT '重试次数',
  `rate_limit` int(11) DEFAULT 100 COMMENT '频率限制（次/分钟）',
  `is_enabled` tinyint(1) DEFAULT 1 COMMENT '是否启用',
  `config_data` text COMMENT '扩展配置JSON',
  `last_sync_time` datetime DEFAULT NULL COMMENT '最后同步时间',
  `sync_status` tinyint(4) DEFAULT 0 COMMENT '同步状态 0-正常 1-异常',
  `error_message` varchar(500) DEFAULT NULL COMMENT '错误信息',
  `create_time` datetime DEFAULT CURRENT_TIMESTAMP,
  `update_time` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_platform_code` (`platform_code`),
  KEY `idx_is_enabled` (`is_enabled`),
  KEY `idx_sync_status` (`sync_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='平台配置表';

-- 6. 系统配置表
CREATE TABLE `dtk_system_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `config_key` varchar(100) NOT NULL COMMENT '配置键',
  `config_value` text COMMENT '配置值',
  `config_type` varchar(20) DEFAULT 'string' COMMENT '配置类型',
  `description` varchar(500) DEFAULT NULL COMMENT '配置描述',
  `is_public` tinyint(1) DEFAULT 0 COMMENT '是否公开',
  `create_time` datetime DEFAULT CURRENT_TIMESTAMP,
  `update_time` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_config_key` (`config_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='系统配置表';

-- 插入默认平台配置
INSERT INTO `dtk_platforms` (`platform_code`, `platform_name`, `app_key`, `app_secret`, `pid`, `version`, `api_url`, `is_enabled`) VALUES
('taobao', '淘宝/天猫', '68768ef94834a', 'f5a5707c8d7b69b8dbad1ec15506c3b1', 'mm_52162983_39758207_72877900030', 'v1.2.4', 'https://openapi.dataoke.com/api/', 1),
('jd', '京东', '', '', '', 'v1.0.0', 'https://openapi.dataoke.com/api/', 0),
('pdd', '拼多多', '', '', '', 'v1.0.0', 'https://openapi.dataoke.com/api/', 0);

-- 插入默认系统配置
INSERT INTO `dtk_system_config` (`config_key`, `config_value`, `config_type`, `description`, `is_public`) VALUES
('system.version', '2.0.0', 'string', '系统版本', 1),
('sync.enabled', '1', 'boolean', '是否启用自动同步', 0),
('sync.batch_size', '100', 'integer', '同步批次大小', 0),
('cache.expire.goods_list', '1800', 'integer', '商品列表缓存时间（秒）', 0),
('cache.expire.goods_detail', '3600', 'integer', '商品详情缓存时间（秒）', 0),
('cache.expire.category', '7200', 'integer', '分类缓存时间（秒）', 0);
