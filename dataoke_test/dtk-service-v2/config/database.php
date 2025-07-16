<?php
/**
 * 数据库配置文件
 */

define('DATABASE_CONFIG', [
    'default' => 'mysql',
    
    'connections' => [
        'mysql' => [
            'driver' => 'mysql',
            'host' => \Utils\EnvLoader::get('DB_HOST'),
            'port' => \Utils\EnvLoader::get('DB_PORT'),
            'database' => \Utils\EnvLoader::get('DB_NAME'),
            'username' => \Utils\EnvLoader::get('DB_USER'),
            'password' => \Utils\EnvLoader::get('DB_PASS'),
            'charset' => \Utils\EnvLoader::get('DB_CHARSET'),
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => \Utils\EnvLoader::get('DB_PREFIX'),
            'strict' => true,
            'engine' => 'InnoDB',
            
            // 连接选项
            'options' => [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => true,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
            ],
            
            // 连接池配置
            'pool' => [
                'max_connections' => 20,
                'min_connections' => 5,
                'timeout' => 30,
                'idle_timeout' => 300,
            ]
        ],
        
        // 读写分离配置（可选）
        'mysql_read' => [
            'driver' => 'mysql',
            'host' => \Utils\EnvLoader::get('DB_READ_HOST'),
            'port' => \Utils\EnvLoader::get('DB_READ_PORT'),
            'database' => \Utils\EnvLoader::get('DB_READ_NAME'),
            'username' => \Utils\EnvLoader::get('DB_READ_USER'),
            'password' => \Utils\EnvLoader::get('DB_READ_PASS'),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => 'dtk_',
            'strict' => true,
            'engine' => 'InnoDB',
            'options' => [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
            ]
        ]
    ],
    
    // 数据库迁移配置
    'migrations' => [
        'table' => 'migrations',
        'path' => ROOT_PATH . '/database/migrations',
    ],
    
    // 查询日志配置
    'query_log' => [
        'enabled' => DTK_CONFIG['debug'] ?? false,
        'slow_query_time' => 1.0, // 慢查询阈值（秒）
        'log_file' => LOGS_PATH . '/query.log',
    ]
]);

// 表结构配置
define('TABLE_SCHEMAS', [
    'goods' => [
        'table' => 'goods',
        'primary_key' => 'id',
        'fillable' => [
            'goods_id', 'item_id', 'title', 'dtitle', 'original_price', 'actual_price',
            'coupon_price', 'coupon_conditions', 'coupon_start_time', 'coupon_end_time',
            'coupon_total_num', 'coupon_remain_count', 'commission_rate', 'commission_type',
            'month_sales', 'two_hours_sales', 'daily_sales', 'main_pic', 'marketing_main_pic',
            'detail_pics', 'item_link', 'coupon_link', 'shop_name', 'shop_type', 'shop_level',
            'seller_id', 'brand_name', 'brand_id', 'cid', 'subcid', 'tbcid', 'desc_score',
            'service_score', 'ship_score', 'is_brand', 'is_live', 'hot_push', 'activity_type',
            'activity_start_time', 'activity_end_time', 'yunfeixian', 'freeship_remote_district',
            'platform', 'sync_time'
        ],
        'timestamps' => true,
        'indexes' => [
            'idx_goods_id' => ['goods_id'],
            'idx_platform' => ['platform'],
            'idx_cid' => ['cid'],
            'idx_actual_price' => ['actual_price'],
            'idx_commission_rate' => ['commission_rate'],
            'idx_month_sales' => ['month_sales'],
            'idx_is_live' => ['is_live'],
            'idx_sync_time' => ['sync_time'],
            'idx_title' => ['title(100)'],
            'idx_brand_name' => ['brand_name'],
            'idx_platform_cid' => ['platform', 'cid'],
            'idx_platform_live' => ['platform', 'is_live']
        ]
    ],
    
    'categories' => [
        'table' => 'categories',
        'primary_key' => 'id',
        'fillable' => [
            'cid', 'cname', 'parent_id', 'level', 'platform', 'sort_order', 'is_active'
        ],
        'timestamps' => true,
        'indexes' => [
            'uk_platform_cid' => ['platform', 'cid'],
            'idx_parent_id' => ['parent_id'],
            'idx_level' => ['level'],
            'idx_platform' => ['platform'],
            'idx_is_active' => ['is_active']
        ]
    ],
    
    'sync_logs' => [
        'table' => 'sync_logs',
        'primary_key' => 'id',
        'fillable' => [
            'platform', 'sync_type', 'status', 'start_time', 'end_time', 'total_count',
            'success_count', 'error_count', 'error_message', 'sync_params'
        ],
        'timestamps' => true,
        'indexes' => [
            'idx_platform' => ['platform'],
            'idx_sync_type' => ['sync_type'],
            'idx_status' => ['status'],
            'idx_start_time' => ['start_time'],
            'idx_platform_type' => ['platform', 'sync_type']
        ]
    ],
    
    'promotion_cache' => [
        'table' => 'promotion_cache',
        'primary_key' => 'id',
        'fillable' => [
            'goods_id', 'platform', 'pid', 'item_url', 'short_url', 'kuaizhan_url',
            'tpwd', 'long_tpwd', 'commission_rate', 'coupon_click_url', 'expire_time'
        ],
        'timestamps' => true,
        'indexes' => [
            'uk_goods_platform_pid' => ['goods_id', 'platform', 'pid'],
            'idx_expire_time' => ['expire_time'],
            'idx_platform' => ['platform']
        ]
    ],
    
    'platforms' => [
        'table' => 'platforms',
        'primary_key' => 'id',
        'fillable' => [
            'platform_code', 'platform_name', 'app_key', 'app_secret', 'pid',
            'version', 'api_url', 'timeout', 'retry_times', 'rate_limit',
            'is_enabled', 'config_data'
        ],
        'timestamps' => true,
        'indexes' => [
            'uk_platform_code' => ['platform_code'],
            'idx_is_enabled' => ['is_enabled']
        ]
    ]
]);
?>
