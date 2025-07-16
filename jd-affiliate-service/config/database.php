<?php
/**
 * 数据库配置文件
 */

// 加载环境变量
if (!class_exists('\JdAffiliate\Utils\EnvLoader')) {
    require_once dirname(__DIR__) . '/src/Utils/EnvLoader.php';
    \JdAffiliate\Utils\EnvLoader::load();
}

// 数据库配置
define('DATABASE_CONFIG', [
    'default' => 'mysql',
    
    'connections' => [
        'mysql' => [
            'driver' => 'mysql',
            'host' => \JdAffiliate\Utils\EnvLoader::get('DB_HOST', 'localhost'),
            'port' => \JdAffiliate\Utils\EnvLoader::get('DB_PORT', 3306),
            'database' => \JdAffiliate\Utils\EnvLoader::get('DB_DATABASE', 'jd_affiliate'),
            'username' => \JdAffiliate\Utils\EnvLoader::get('DB_USERNAME', 'root'),
            'password' => \JdAffiliate\Utils\EnvLoader::get('DB_PASSWORD', ''),
            'charset' => \JdAffiliate\Utils\EnvLoader::get('DB_CHARSET', 'utf8mb4'),
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => \JdAffiliate\Utils\EnvLoader::get('DB_PREFIX', 'jd_'),
            'strict' => true,
            'engine' => 'InnoDB',
            'options' => [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
            ]
        ]
    ]
]);
?>