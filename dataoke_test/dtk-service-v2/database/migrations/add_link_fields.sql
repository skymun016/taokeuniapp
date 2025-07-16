-- 数据库迁移脚本：添加转链相关字段
-- 执行时间：2025-07-15
-- 说明：为智能混合转链策略添加必要的数据库字段

-- 添加转链相关字段
ALTER TABLE `dtk_goods` 
ADD COLUMN `privilege_link` varchar(1000) DEFAULT NULL COMMENT '推广链接' AFTER `sync_time`,
ADD COLUMN `coupon_click_url` varchar(1000) DEFAULT NULL COMMENT '优惠券推广链接' AFTER `privilege_link`,
ADD COLUMN `tpwd` varchar(50) DEFAULT NULL COMMENT '淘口令' AFTER `coupon_click_url`,
ADD COLUMN `link_expire_time` datetime DEFAULT NULL COMMENT '转链过期时间' AFTER `tpwd`,
ADD COLUMN `link_status` tinyint(1) DEFAULT 0 COMMENT '转链状态 0-未转链 1-已转链 2-已过期' AFTER `link_expire_time`,
ADD COLUMN `tier_level` tinyint(1) DEFAULT 3 COMMENT '商品层级 1-热门 2-普通 3-冷门' AFTER `link_status`,
ADD COLUMN `last_convert_time` datetime DEFAULT NULL COMMENT '最后转链时间' AFTER `tier_level`,
ADD COLUMN `convert_count` int(11) DEFAULT 0 COMMENT '转链次数统计' AFTER `last_convert_time`,
ADD COLUMN `estimated_commission` decimal(10,2) DEFAULT NULL COMMENT '预估佣金金额' AFTER `convert_count`;

-- 添加转链相关索引
ALTER TABLE `dtk_goods`
ADD INDEX `idx_tier_level` (`tier_level`),
ADD INDEX `idx_link_status` (`link_status`),
ADD INDEX `idx_link_expire_time` (`link_expire_time`),
ADD INDEX `idx_tier_status` (`tier_level`, `link_status`),
ADD INDEX `idx_platform_tier` (`platform`, `tier_level`);

-- 初始化商品层级（基于月销量）
UPDATE `dtk_goods` SET 
  `tier_level` = CASE 
    WHEN `month_sales` >= 1000 THEN 1  -- 热门商品
    WHEN `month_sales` >= 100 THEN 2   -- 普通商品
    ELSE 3                             -- 冷门商品
  END,
  `estimated_commission` = ROUND(
    COALESCE(`actual_price`, `original_price`, 0) * 
    COALESCE(`commission_rate`, 0) / 100, 
    2
  )
WHERE `tier_level` = 3;  -- 只更新默认值

-- 验证迁移结果
SELECT 
  tier_level,
  COUNT(*) as count,
  AVG(month_sales) as avg_sales,
  AVG(estimated_commission) as avg_commission
FROM dtk_goods 
GROUP BY tier_level 
ORDER BY tier_level;
