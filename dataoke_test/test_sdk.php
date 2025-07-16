<?php
/**
 * 大淘客SDK测试脚本
 * 用于验证大淘客配置是否可用
 */

// 引入SDK
require_once 'DataokeSDK.php';

// 配置信息 - 使用用户提供的新配置
$config = [
    'app_key' => '68768ef94834a',
    'app_secret' => 'f5a5707c8d7b69b8dbad1ec15506c3b1',
    'pid' => 'mm_52162983_36356162_129870201',
    'api_url' => 'https://openapi.dataoke.com/api/'
];

echo "大淘客SDK测试开始...\n";
echo "时间: " . date('Y-m-d H:i:s') . "\n";
echo "配置信息:\n";
echo "- APP_KEY: " . $config['app_key'] . "\n";
echo "- APP_SECRET: " . substr($config['app_secret'], 0, 10) . "...\n";
echo "- PID: " . $config['pid'] . "\n";
echo "- API_URL: " . $config['api_url'] . "\n\n";

try {
    // 创建SDK实例
    $sdk = new DataokeSDK(
        $config['app_key'],
        $config['app_secret'],
        $config['api_url']
    );
    
    // 执行连接测试
    $results = $sdk->testConnection();
    
    // 分析测试结果
    echo "\n=== 测试结果分析 ===\n";
    
    $categorySuccess = false;
    $goodsSuccess = false;
    $searchSuccess = false;
    
    // 分析分类测试
    if (isset($results['category']['data']['code']) && $results['category']['data']['code'] == 0) {
        $categorySuccess = true;
        echo "✅ 分类接口: 正常\n";
    } else {
        echo "❌ 分类接口: 异常\n";
        if (isset($results['category']['data']['msg'])) {
            echo "   错误信息: " . $results['category']['data']['msg'] . "\n";
        }
    }
    
    // 分析商品列表测试
    if (isset($results['goods']['data']['code']) && $results['goods']['data']['code'] == 0) {
        $goodsSuccess = true;
        echo "✅ 商品列表接口: 正常\n";
    } else {
        echo "❌ 商品列表接口: 异常\n";
        if (isset($results['goods']['data']['msg'])) {
            echo "   错误信息: " . $results['goods']['data']['msg'] . "\n";
        }
    }
    
    // 分析搜索测试
    if (isset($results['search']['data']['code']) && $results['search']['data']['code'] == 0) {
        $searchSuccess = true;
        echo "✅ 商品搜索接口: 正常\n";
    } else {
        echo "❌ 商品搜索接口: 异常\n";
        if (isset($results['search']['data']['msg'])) {
            echo "   错误信息: " . $results['search']['data']['msg'] . "\n";
        }
    }
    
    // 总结
    echo "\n=== 总结 ===\n";
    $successCount = ($categorySuccess ? 1 : 0) + ($goodsSuccess ? 1 : 0) + ($searchSuccess ? 1 : 0);
    echo "成功接口数: {$successCount}/3\n";
    
    if ($successCount == 3) {
        echo "🎉 所有接口测试通过！大淘客配置正确，可以正常使用。\n";
        echo "\n建议下一步:\n";
        echo "1. 在项目中启用大淘客功能\n";
        echo "2. 运行商品同步命令导入数据\n";
        echo "3. 测试前端页面功能\n";
    } else if ($successCount > 0) {
        echo "⚠️  部分接口可用，建议检查配置或联系大淘客客服。\n";
    } else {
        echo "❌ 所有接口都不可用，请检查:\n";
        echo "1. APP_KEY和APP_SECRET是否正确\n";
        echo "2. 大淘客账户是否正常\n";
        echo "3. 网络连接是否正常\n";
        echo "4. API地址是否正确\n";
    }
    
    // 如果需要详细调试信息
    if ($successCount < 3) {
        echo "\n=== 详细调试信息 ===\n";
        
        foreach (['category', 'goods', 'search'] as $type) {
            if (isset($results[$type])) {
                echo "\n{$type} 接口详情:\n";
                echo "HTTP状态码: " . $results[$type]['http_code'] . "\n";
                echo "原始响应: " . $results[$type]['raw_response'] . "\n";
                
                if (isset($results[$type]['request_params'])) {
                    echo "请求参数: " . json_encode($results[$type]['request_params'], JSON_UNESCAPED_UNICODE) . "\n";
                }
            }
        }
    }
    
} catch (Exception $e) {
    echo "❌ 测试过程中发生异常:\n";
    echo "错误信息: " . $e->getMessage() . "\n";
    echo "文件: " . $e->getFile() . "\n";
    echo "行号: " . $e->getLine() . "\n";
}

echo "\n测试完成时间: " . date('Y-m-d H:i:s') . "\n";
