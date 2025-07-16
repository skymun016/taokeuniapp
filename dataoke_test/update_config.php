<?php
/**
 * 大淘客配置更新脚本
 * 用于更新数据库中的大淘客配置信息
 */

// 数据库配置
$dbConfig = [
    'host' => 'localhost',
    'port' => 3306,
    'database' => 'xinmai',
    'username' => 'root',
    'password' => '123456'
];

// 新的大淘客配置 - 使用用户提供的正确配置信息
$newConfig = [
    'taobao' => [
        'app_key' => '68768ef94834a',        // 用户提供的APP_KEY
        'app_secret' => 'f5a5707c8d7b69b8dbad1ec15506c3b1',     // 用户提供的APP_SECRET
        'pid' => 'mm_52162983_36356162_129870201',            // 用户提供的PID
        'relation_id' => '',    // 可选：渠道关系ID
        'status' => 1           // 1=启用, 0=禁用
    ],
    'jingdong' => [
        'app_key' => '',        // 京东APP_KEY（如果有）
        'app_secret' => '',     // 京东APP_SECRET（如果有）
        'pid' => '',            // 京东PID（如果有）
        'relation_id' => '',    // 可选：渠道关系ID
        'status' => 0           // 暂时禁用京东
    ]
];

echo "=== 大淘客配置更新脚本 ===\n";
echo "时间: " . date('Y-m-d H:i:s') . "\n\n";

// 检查配置是否填写
if (empty($newConfig['taobao']['app_key']) || empty($newConfig['taobao']['app_secret'])) {
    echo "❌ 错误: 请先在脚本中填入正确的大淘客配置信息\n";
    echo "\n请按以下步骤操作:\n";
    echo "1. 登录大淘客后台: https://www.dataoke.com/\n";
    echo "2. 进入开发者中心，查看应用信息\n";
    echo "3. 复制正确的APP_KEY和APP_SECRET\n";
    echo "4. 修改本脚本中的 \$newConfig 数组\n";
    echo "5. 重新运行此脚本\n\n";
    
    echo "当前配置状态:\n";
    echo "- APP_KEY: " . ($newConfig['taobao']['app_key'] ?: '未填写') . "\n";
    echo "- APP_SECRET: " . ($newConfig['taobao']['app_secret'] ?: '未填写') . "\n";
    echo "- PID: " . ($newConfig['taobao']['pid'] ?: '未填写') . "\n";
    exit(1);
}

try {
    // 连接数据库
    $dsn = "mysql:host={$dbConfig['host']};port={$dbConfig['port']};dbname={$dbConfig['database']};charset=utf8mb4";
    $pdo = new PDO($dsn, $dbConfig['username'], $dbConfig['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✅ 数据库连接成功\n\n";
    
    // 更新配置
    foreach ($newConfig as $platform => $config) {
        echo "更新 {$platform} 平台配置...\n";
        
        // 检查记录是否存在
        $checkSql = "SELECT id FROM ims_xm_mallv3_dataoke_config WHERE weid = 1 AND platform = :platform";
        $checkStmt = $pdo->prepare($checkSql);
        $checkStmt->execute(['platform' => $platform]);
        $exists = $checkStmt->fetch();
        
        if ($exists) {
            // 更新现有记录
            $updateSql = "UPDATE ims_xm_mallv3_dataoke_config SET 
                         app_key = :app_key,
                         app_secret = :app_secret,
                         pid = :pid,
                         relation_id = :relation_id,
                         status = :status,
                         update_time = NOW()
                         WHERE weid = 1 AND platform = :platform";
            
            $updateStmt = $pdo->prepare($updateSql);
            $result = $updateStmt->execute([
                'app_key' => $config['app_key'],
                'app_secret' => $config['app_secret'],
                'pid' => $config['pid'],
                'relation_id' => $config['relation_id'],
                'status' => $config['status'],
                'platform' => $platform
            ]);
            
            if ($result) {
                echo "  ✅ 更新成功\n";
            } else {
                echo "  ❌ 更新失败\n";
            }
        } else {
            // 插入新记录
            $insertSql = "INSERT INTO ims_xm_mallv3_dataoke_config 
                         (weid, platform, app_key, app_secret, pid, relation_id, api_url, timeout, status, remark, create_time, update_time)
                         VALUES (1, :platform, :app_key, :app_secret, :pid, :relation_id, 'https://openapi.dataoke.com/api/', 30, :status, :remark, NOW(), NOW())";
            
            $insertStmt = $pdo->prepare($insertSql);
            $result = $insertStmt->execute([
                'platform' => $platform,
                'app_key' => $config['app_key'],
                'app_secret' => $config['app_secret'],
                'pid' => $config['pid'],
                'relation_id' => $config['relation_id'],
                'status' => $config['status'],
                'remark' => $platform . '平台配置'
            ]);
            
            if ($result) {
                echo "  ✅ 插入成功\n";
            } else {
                echo "  ❌ 插入失败\n";
            }
        }
    }
    
    echo "\n=== 配置更新完成 ===\n";
    
    // 显示当前配置
    echo "\n当前数据库配置:\n";
    $querySql = "SELECT platform, app_key, LEFT(app_secret, 10) as secret_preview, LEFT(pid, 20) as pid_preview, status 
                 FROM ims_xm_mallv3_dataoke_config WHERE weid = 1 ORDER BY platform";
    $queryStmt = $pdo->prepare($querySql);
    $queryStmt->execute();
    $configs = $queryStmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($configs as $config) {
        $statusText = $config['status'] ? '启用' : '禁用';
        echo "- {$config['platform']}: {$config['app_key']} | {$config['secret_preview']}... | {$config['pid_preview']}... | {$statusText}\n";
    }
    
    echo "\n建议下一步:\n";
    echo "1. 运行测试脚本验证配置: php test_sdk.php\n";
    echo "2. 如果测试通过，可以开始同步商品数据\n";
    echo "3. 测试前端页面功能\n";
    
} catch (PDOException $e) {
    echo "❌ 数据库错误: " . $e->getMessage() . "\n";
} catch (Exception $e) {
    echo "❌ 其他错误: " . $e->getMessage() . "\n";
}

echo "\n脚本执行完成时间: " . date('Y-m-d H:i:s') . "\n";
