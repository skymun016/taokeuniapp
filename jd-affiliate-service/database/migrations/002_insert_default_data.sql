-- 迁移文件: 002_insert_default_data.sql
-- 描述: 插入默认数据
-- 版本: 1.0.0
-- 创建时间: 2025-01-16

USE `jd_affiliate`;

-- 插入默认系统配置
INSERT IGNORE INTO `jd_system_config` (`config_key`, `config_value`, `config_type`, `description`, `is_system`) VALUES
('sync_products_enabled', '1', 'bool', '是否启用商品同步', 1),
('sync_categories_enabled', '1', 'bool', '是否启用分类同步', 1),
('sync_batch_size', '100', 'int', '同步批次大小', 1),
('sync_max_pages', '50', 'int', '同步最大页数', 1),
('sync_sleep_time', '1', 'int', '同步间隔时间(秒)', 1),
('cache_expire_products', '3600', 'int', '商品缓存过期时间(秒)', 1),
('cache_expire_categories', '7200', 'int', '分类缓存过期时间(秒)', 1),
('cache_expire_search', '900', 'int', '搜索缓存过期时间(秒)', 1),
('cache_expire_affiliate_link', '300', 'int', '联盟链接缓存过期时间(秒)', 1),
('api_rate_limit', '1000', 'int', 'API速率限制(每小时)', 1),
('api_timeout', '30', 'int', 'API超时时间(秒)', 1),
('log_retention_days', '30', 'int', '日志保留天数', 1),
('log_max_size', '10485760', 'int', '单个日志文件最大大小(字节)', 1),
('affiliate_link_expire_hours', '24', 'int', '联盟链接过期时间(小时)', 1),
('haodanku_api_key', '7902F044597C', 'string', '好单库API密钥', 1),
('haodanku_api_url', 'https://api.haodanku.com', 'string', '好单库API地址', 1),
('jd_union_id', '', 'string', '京东联盟ID', 0),
('jd_position_id', '', 'string', '京东推广位ID', 0),
('jd_site_id', '', 'string', '京东站点ID', 0);

-- 插入默认京东分类数据
INSERT IGNORE INTO `jd_categories` (`category_id`, `category_name`, `parent_id`, `level`, `sort_order`, `description`, `is_active`) VALUES
('1', '电脑、办公', NULL, 1, 1, '电脑整机、电脑配件、外设产品、网络产品、办公设备等', 1),
('2', '手机、数码', NULL, 1, 2, '手机通讯、运营商、数码产品、影音娱乐等', 1),
('3', '家用电器', NULL, 1, 3, '大家电、生活电器、厨房电器、个护健康等', 1),
('4', '服饰内衣', NULL, 1, 4, '女装、男装、内衣、服饰配件等', 1),
('5', '家居家装', NULL, 1, 5, '家纺、灯具、家装建材、五金工具等', 1),
('6', '母婴', NULL, 1, 6, '奶粉、营养辅食、尿裤湿巾、洗护用品、童车童床等', 1),
('7', '食品饮料', NULL, 1, 7, '进口食品、地方特产、休闲食品、茶叶、酒类等', 1),
('8', '美容护肤', NULL, 1, 8, '面部护肤、身体护理、香水彩妆等', 1),
('9', '个护清洁', NULL, 1, 9, '口腔护理、女性护理、身体护理、家庭清洁等', 1),
('10', '钟表', NULL, 1, 10, '钟表、智能手表、传统手表等', 1),
('11', '鞋靴', NULL, 1, 11, '流行男鞋、时尚女鞋、功能鞋等', 1),
('12', '运动健康', NULL, 1, 12, '运动鞋服、健身训练、体育用品等', 1),
('13', '汽车用品', NULL, 1, 13, '维修保养、装饰用品、安全自驾、汽车配件等', 1),
('14', '图书', NULL, 1, 14, '教育、文学、经管、科技、生活、艺术等', 1),
('15', '文娱', NULL, 1, 15, '乐器、游戏、收藏、网络服务等', 1),
('16', '礼品箱包', NULL, 1, 16, '礼品、箱包、奢侈品等', 1),
('17', '玩具乐器', NULL, 1, 17, '益智玩具、遥控玩具、娃娃玩具、模型玩具等', 1),
('18', '医药保健', NULL, 1, 18, '中西药品、营养保健、医疗器械、成人用品等', 1),
('19', '宠物生活', NULL, 1, 19, '宠物主粮、宠物零食、宠物用品、宠物医疗等', 1),
('20', '农资绿植', NULL, 1, 20, '农资、园艺、鲜花绿植等', 1);

-- 插入二级分类示例（电脑、办公）
INSERT IGNORE INTO `jd_categories` (`category_id`, `category_name`, `parent_id`, `level`, `sort_order`, `description`, `is_active`) VALUES
('101', '电脑整机', '1', 2, 1, '台式机、笔记本、平板电脑、服务器等', 1),
('102', '电脑配件', '1', 2, 2, '内存、硬盘、显卡、主板、CPU等', 1),
('103', '外设产品', '1', 2, 3, '鼠标、键盘、音箱、摄像头、耳机等', 1),
('104', '网络产品', '1', 2, 4, '路由器、交换机、网卡、网线等', 1),
('105', '办公设备', '1', 2, 5, '打印机、投影仪、碎纸机、考勤机等', 1);

-- 插入二级分类示例（手机、数码）
INSERT IGNORE INTO `jd_categories` (`category_id`, `category_name`, `parent_id`, `level`, `sort_order`, `description`, `is_active`) VALUES
('201', '手机通讯', '2', 2, 1, '手机、对讲机、以旧换新等', 1),
('202', '运营商', '2', 2, 2, '合约机、选号中心、装宽带等', 1),
('203', '数码产品', '2', 2, 3, '数码相机、摄像机、单反相机等', 1),
('204', '影音娱乐', '2', 2, 4, '耳机、音箱、麦克风、功放等', 1);

-- 记录迁移执行日志
INSERT INTO `jd_sync_logs` (`sync_type`, `sync_mode`, `start_time`, `end_time`, `total_count`, `success_count`, `failed_count`, `status`, `extra_info`) VALUES
('migration', 'full', NOW(), NOW(), 2, 2, 0, 'completed', '{"migration": "002_insert_default_data", "description": "插入默认数据"}');