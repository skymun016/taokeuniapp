<?php
/**
 * 大淘客API测试脚本
 * 用于测试大淘客配置是否正确
 */

// 大淘客配置信息
$config = [
    'app_key' => '678fc81d72259',
    'app_secret' => '6fd2acba8bce6c039ab276256f003ced',
    'pid' => 'mm_52162983_2267550029_112173400498',
    'api_url' => 'https://openapi.dataoke.com/api/'
];

/**
 * 生成签名
 */
function generateSign($params, $appSecret) {
    ksort($params);
    $signStr = '';
    foreach ($params as $key => $value) {
        if ($key != 'sign' && $value !== '') {
            $signStr .= $key . $value;
        }
    }
    return strtoupper(md5($appSecret . $signStr . $appSecret));
}

/**
 * 发送HTTP请求
 */
function sendRequest($url, $params) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($error) {
        return ['error' => $error];
    }
    
    return [
        'http_code' => $httpCode,
        'response' => $response
    ];
}

/**
 * 测试获取商品列表
 */
function testGetGoodsList($config) {
    echo "=== 测试获取淘宝商品列表 ===\n";
    
    $params = [
        'appKey' => $config['app_key'],
        'version' => '1.2.1',
        'pageId' => 1,
        'pageSize' => 10,
        'sort' => 'total_sales_des'
    ];
    
    // 生成签名
    $params['sign'] = generateSign($params, $config['app_secret']);
    
    $url = $config['api_url'] . 'goods/get-goods-list';
    
    echo "请求URL: $url\n";
    echo "请求参数: " . json_encode($params, JSON_UNESCAPED_UNICODE) . "\n";
    
    $result = sendRequest($url, $params);
    
    if (isset($result['error'])) {
        echo "请求错误: " . $result['error'] . "\n";
        return false;
    }
    
    echo "HTTP状态码: " . $result['http_code'] . "\n";
    echo "响应内容: " . $result['response'] . "\n";
    
    $data = json_decode($result['response'], true);
    if ($data) {
        echo "解析结果: \n";
        echo "- 状态码: " . ($data['code'] ?? 'N/A') . "\n";
        echo "- 消息: " . ($data['msg'] ?? 'N/A') . "\n";
        if (isset($data['data']['list'])) {
            echo "- 商品数量: " . count($data['data']['list']) . "\n";
            if (!empty($data['data']['list'])) {
                $firstGoods = $data['data']['list'][0];
                echo "- 第一个商品: " . ($firstGoods['title'] ?? 'N/A') . "\n";
                echo "- 商品价格: " . ($firstGoods['actualPrice'] ?? 'N/A') . "\n";
            }
        }
        return $data['code'] == 0;
    } else {
        echo "响应解析失败\n";
        return false;
    }
}

/**
 * 测试获取分类列表
 */
function testGetCategoryList($config) {
    echo "\n=== 测试获取分类列表 ===\n";
    
    $params = [
        'appKey' => $config['app_key'],
        'version' => '1.2.1'
    ];
    
    // 生成签名
    $params['sign'] = generateSign($params, $config['app_secret']);
    
    $url = $config['api_url'] . 'category/get-category-list';
    
    echo "请求URL: $url\n";
    
    $result = sendRequest($url, $params);
    
    if (isset($result['error'])) {
        echo "请求错误: " . $result['error'] . "\n";
        return false;
    }
    
    echo "HTTP状态码: " . $result['http_code'] . "\n";
    
    $data = json_decode($result['response'], true);
    if ($data) {
        echo "- 状态码: " . ($data['code'] ?? 'N/A') . "\n";
        echo "- 消息: " . ($data['msg'] ?? 'N/A') . "\n";
        if (isset($data['data']['list'])) {
            echo "- 分类数量: " . count($data['data']['list']) . "\n";
        }
        return $data['code'] == 0;
    } else {
        echo "响应解析失败\n";
        return false;
    }
}

// 开始测试
echo "大淘客API测试开始...\n";
echo "配置信息:\n";
echo "- APP_KEY: " . $config['app_key'] . "\n";
echo "- PID: " . $config['pid'] . "\n";
echo "- API_URL: " . $config['api_url'] . "\n\n";

// 测试分类列表
$categoryTest = testGetCategoryList($config);

// 测试商品列表
$goodsTest = testGetGoodsList($config);

echo "\n=== 测试结果汇总 ===\n";
echo "分类列表测试: " . ($categoryTest ? "✅ 成功" : "❌ 失败") . "\n";
echo "商品列表测试: " . ($goodsTest ? "✅ 成功" : "❌ 失败") . "\n";

if ($categoryTest && $goodsTest) {
    echo "\n🎉 大淘客API配置正确，可以正常获取数据！\n";
    echo "建议下一步：运行商品同步命令来导入商品数据\n";
} else {
    echo "\n⚠️  大淘客API配置可能有问题，请检查密钥信息\n";
}
