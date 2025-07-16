-- 创建小程序端日志表

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
