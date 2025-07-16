<?php
/**
 * 大淘客签名调试脚本
 * 用于调试签名生成过程
 */

// 引入SDK
require_once 'DataokeSDK.php';

// 配置信息 - 使用您提供的正确凭证
$config = [
    'app_key' => '678fc81d72259',
    'app_secret' => '6fd2acba8bce6c039ab276256f003ced',
    'pid' => 'mm_52162983_2267550029_112173400498',
    'api_url' => 'https://openapi.dataoke.com/api/'
];

echo "=== 大淘客签名调试 ===\n";
echo "APP_KEY: " . $config['app_key'] . "\n";
echo "APP_SECRET: " . $config['app_secret'] . "\n\n";

// 创建SDK实例
$sdk = new DataokeSDK(
    $config['app_key'],
    $config['app_secret'],
    $config['api_url']
);

// 测试分类列表接口的签名
echo "1. 测试分类列表接口签名\n";
$categoryParams = [
    'appKey' => $config['app_key'],
    'version' => '1.2.1'
];

$debugInfo = $sdk->debugSign($categoryParams);
echo "参数: " . json_encode($debugInfo['params'], JSON_UNESCAPED_UNICODE) . "\n";
echo "签名字符串: " . $debugInfo['sign_string'] . "\n";
echo "完整签名字符串: " . $debugInfo['full_sign_string'] . "\n";
echo "最终签名: " . $debugInfo['final_sign'] . "\n\n";

// 测试商品列表接口的签名
echo "2. 测试商品列表接口签名\n";
$goodsParams = [
    'appKey' => $config['app_key'],
    'version' => '1.2.1',
    'pageId' => 1,
    'pageSize' => 20,
    'sort' => 'total_sales_des'
];

$debugInfo2 = $sdk->debugSign($goodsParams);
echo "参数: " . json_encode($debugInfo2['params'], JSON_UNESCAPED_UNICODE) . "\n";
echo "签名字符串: " . $debugInfo2['sign_string'] . "\n";
echo "完整签名字符串: " . $debugInfo2['full_sign_string'] . "\n";
echo "最终签名: " . $debugInfo2['final_sign'] . "\n\n";

// 手动验证签名算法
echo "3. 手动验证签名算法\n";
$testParams = [
    'appKey' => $config['app_key'],
    'version' => '1.2.1'
];

// 按照官方文档手动生成签名
ksort($testParams);
$manualSignStr = '';
foreach ($testParams as $key => $value) {
    $manualSignStr .= $key . $value;
}
$manualFullStr = $config['app_secret'] . $manualSignStr . $config['app_secret'];
$manualSign = strtoupper(md5($manualFullStr));

echo "手动签名字符串: " . $manualSignStr . "\n";
echo "手动完整字符串: " . $manualFullStr . "\n";
echo "手动生成签名: " . $manualSign . "\n";
echo "SDK生成签名: " . $debugInfo['final_sign'] . "\n";
echo "签名是否一致: " . ($manualSign === $debugInfo['final_sign'] ? '✅ 是' : '❌ 否') . "\n\n";

// 测试实际API调用
echo "4. 测试实际API调用\n";
$result = $sdk->getCategoryList();
echo "HTTP状态码: " . $result['http_code'] . "\n";
echo "响应内容: " . $result['raw_response'] . "\n";
echo "请求参数: " . json_encode($result['request_params'], JSON_UNESCAPED_UNICODE) . "\n\n";

// 尝试不同的API版本
echo "5. 尝试不同的API版本\n";
$versions = ['1.2.1', '1.2.0', '1.1.0', '1.0.0'];

foreach ($versions as $version) {
    echo "测试版本: {$version}\n";
    
    $testParams = [
        'appKey' => $config['app_key'],
        'version' => $version
    ];
    
    // 生成签名
    ksort($testParams);
    $signStr = '';
    foreach ($testParams as $key => $value) {
        $signStr .= $key . $value;
    }
    $fullStr = $config['app_secret'] . $signStr . $config['app_secret'];
    $sign = strtoupper(md5($fullStr));
    $testParams['sign'] = $sign;
    
    // 发送请求
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $config['api_url'] . 'category/get-category-list',
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => http_build_query($testParams),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 10,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    echo "  HTTP状态码: {$httpCode}\n";
    echo "  响应: {$response}\n";
    
    $data = json_decode($response, true);
    if ($data && isset($data['code']) && $data['code'] == 0) {
        echo "  ✅ 版本 {$version} 可用！\n";
        break;
    } else {
        echo "  ❌ 版本 {$version} 不可用\n";
    }
    echo "\n";
}

echo "调试完成\n";
