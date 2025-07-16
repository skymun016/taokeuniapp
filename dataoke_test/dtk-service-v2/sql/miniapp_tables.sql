-- 小程序端相关数据表

-- 搜索日志表
CREATE TABLE IF NOT EXISTS `dtk_search_logs` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `keyword` varchar(255) NOT NULL COMMENT '搜索关键词',
    `result_count` int(11) DEFAULT 0 COMMENT '搜索结果数量',
    `search_time` datetime NOT NULL COMMENT '搜索时间',
    `ip_address` varchar(45) DEFAULT NULL COMMENT 'IP地址',
    `user_agent` text COMMENT '用户代理',
    PRIMARY KEY (`id`),
    KEY `idx_keyword` (`keyword`),
    KEY `idx_search_time` (`search_time`),
    KEY `idx_ip_address` (`ip_address`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='搜索日志表';

-- 转链日志表
CREATE TABLE IF NOT EXISTS `dtk_convert_logs` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `goods_id` varchar(50) NOT NULL COMMENT '商品ID',
    `success` tinyint(1) DEFAULT 0 COMMENT '转链是否成功',
    `convert_time` datetime NOT NULL COMMENT '转链时间',
    `ip_address` varchar(45) DEFAULT NULL COMMENT 'IP地址',
    `user_agent` text COMMENT '用户代理',
    `error_message` text COMMENT '错误信息',
    PRIMARY KEY (`id`),
    KEY `idx_goods_id` (`goods_id`),
    KEY `idx_convert_time` (`convert_time`),
    KEY `idx_success` (`success`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='转链日志表';

-- 为商品表添加全文索引（忽略错误）
ALTER TABLE `dtk_goods` ADD FULLTEXT(`title`);

-- 为商品表添加缺失的字段（忽略错误）
ALTER TABLE `dtk_goods` ADD COLUMN `images` text COMMENT '商品图片数组JSON';
ALTER TABLE `dtk_goods` ADD COLUMN `detail_images` text COMMENT '详情图片数组JSON';
ALTER TABLE `dtk_goods` ADD COLUMN `specs` text COMMENT '规格信息JSON';
ALTER TABLE `dtk_goods` ADD COLUMN `service_tags` text COMMENT '服务标签JSON';
ALTER TABLE `dtk_goods` ADD COLUMN `brand_name` varchar(255) DEFAULT NULL COMMENT '品牌名称';
ALTER TABLE `dtk_goods` ADD COLUMN `description` text COMMENT '商品描述';
ALTER TABLE `dtk_goods` ADD COLUMN `shop_type` varchar(50) DEFAULT NULL COMMENT '店铺类型';
ALTER TABLE `dtk_goods` ADD COLUMN `link_expire_time` datetime DEFAULT NULL COMMENT '链接过期时间';

-- 添加索引优化查询性能（忽略错误）
ALTER TABLE `dtk_goods` ADD INDEX `idx_platform_tier` (`platform`, `tier_level`);
ALTER TABLE `dtk_goods` ADD INDEX `idx_platform_sales` (`platform`, `month_sales`);
ALTER TABLE `dtk_goods` ADD INDEX `idx_platform_price` (`platform`, `price`);
ALTER TABLE `dtk_goods` ADD INDEX `idx_platform_coupon` (`platform`, `coupon_amount`);
ALTER TABLE `dtk_goods` ADD INDEX `idx_platform_category` (`platform`, `category_id`);
ALTER TABLE `dtk_goods` ADD INDEX `idx_create_time` (`create_time`);
ALTER TABLE `dtk_goods` ADD INDEX `idx_update_time` (`update_time`);
ALTER TABLE `dtk_goods` ADD INDEX `idx_link_status` (`link_status`);
ALTER TABLE `dtk_goods` ADD INDEX `idx_shop_name` (`shop_name`);
ALTER TABLE `dtk_goods` ADD INDEX `idx_brand_name` (`brand_name`);
