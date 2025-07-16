<?php
/**
 * 测试不同的签名算法
 */

$appKey = '678fc81d72259';
$appSecret = '6fd2acba8bce6c039ab276256f003ced';

$params = [
    'appKey' => $appKey,
    'version' => '1.2.4'  // 尝试最新版本
];

echo "=== 测试不同的签名算法 ===\n";
echo "APP_KEY: {$appKey}\n";
echo "APP_SECRET: {$appSecret}\n\n";

// 方法1: 原始方法 (secret + params + secret)
echo "方法1: secret + params + secret\n";
ksort($params);
$signStr1 = '';
foreach ($params as $key => $value) {
    $signStr1 .= $key . $value;
}
$fullStr1 = $appSecret . $signStr1 . $appSecret;
$sign1 = strtoupper(md5($fullStr1));
echo "签名字符串: {$signStr1}\n";
echo "完整字符串: {$fullStr1}\n";
echo "签名: {$sign1}\n\n";

// 方法2: 只有params，不加secret
echo "方法2: 只有params\n";
$sign2 = strtoupper(md5($signStr1));
echo "签名字符串: {$signStr1}\n";
echo "签名: {$sign2}\n\n";

// 方法3: params + secret
echo "方法3: params + secret\n";
$fullStr3 = $signStr1 . $appSecret;
$sign3 = strtoupper(md5($fullStr3));
echo "完整字符串: {$fullStr3}\n";
echo "签名: {$sign3}\n\n";

// 方法4: secret + params
echo "方法4: secret + params\n";
$fullStr4 = $appSecret . $signStr1;
$sign4 = strtoupper(md5($fullStr4));
echo "完整字符串: {$fullStr4}\n";
echo "签名: {$sign4}\n\n";

// 方法5: 使用&连接参数
echo "方法5: 使用&连接参数\n";
$signStr5 = '';
foreach ($params as $key => $value) {
    if ($signStr5) $signStr5 .= '&';
    $signStr5 .= $key . '=' . $value;
}
$fullStr5 = $appSecret . $signStr5 . $appSecret;
$sign5 = strtoupper(md5($fullStr5));
echo "签名字符串: {$signStr5}\n";
echo "完整字符串: {$fullStr5}\n";
echo "签名: {$sign5}\n\n";

// 测试所有方法
$signs = [
    'method1' => $sign1,
    'method2' => $sign2,
    'method3' => $sign3,
    'method4' => $sign4,
    'method5' => $sign5
];

foreach ($signs as $method => $sign) {
    echo "测试 {$method} (签名: {$sign})\n";
    
    $testParams = $params;
    $testParams['sign'] = $sign;
    
    $ch = curl_init();
    $queryString = http_build_query($testParams);
    $url = 'https://openapi.dataoke.com/api/category/get-category-list?' . $queryString;
    
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
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
        echo "  ✅ {$method} 成功！\n";
        break;
    } else {
        echo "  ❌ {$method} 失败\n";
    }
    echo "\n";
}

// 尝试不同的API版本
echo "=== 尝试不同的API版本 ===\n";
$versions = ['1.2.4', '1.2.3', '1.2.2', '1.2.1', '1.2.0'];

foreach ($versions as $version) {
    echo "测试版本: {$version}\n";
    
    $versionParams = [
        'appKey' => $appKey,
        'version' => $version
    ];
    
    ksort($versionParams);
    $versionSignStr = '';
    foreach ($versionParams as $key => $value) {
        $versionSignStr .= $key . $value;
    }
    $versionFullStr = $appSecret . $versionSignStr . $appSecret;
    $versionSign = strtoupper(md5($versionFullStr));
    $versionParams['sign'] = $versionSign;
    
    $ch = curl_init();
    $queryString = http_build_query($versionParams);
    $url = 'https://openapi.dataoke.com/api/category/get-category-list?' . $queryString;
    
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
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

echo "测试完成\n";
