-- 数据库迁移脚本：添加短链接字段
-- 执行时间：2025-07-16
-- 说明：为淘口令功能添加短链接支持

-- 添加短链接字段
ALTER TABLE `dtk_goods` 
ADD COLUMN `short_url` varchar(500) DEFAULT NULL COMMENT '短链接' AFTER `privilege_link`;

-- 添加短链接索引
ALTER TABLE `dtk_goods`
ADD INDEX `idx_short_url` (`short_url`);

-- 验证迁移结果
SELECT COUNT(*) as total_goods FROM dtk_goods;
DESCRIBE dtk_goods;
