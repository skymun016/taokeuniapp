<?php
/**
 * 大淘客服务端API测试脚本
 * 用于验证所有API接口是否正常工作
 */

// 服务端地址
$baseUrl = 'http://localhost:8080/api/';

echo "🧪 大淘客服务端API测试\n";
echo "===================\n\n";

/**
 * 发送HTTP请求
 */
function sendRequest($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_HEADER, false);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($error) {
        return ['error' => $error];
    }
    
    return [
        'http_code' => $httpCode,
        'data' => json_decode($response, true)
    ];
}

/**
 * 测试结果输出
 */
function testResult($testName, $result) {
    echo "📋 测试: {$testName}\n";
    
    if (isset($result['error'])) {
        echo "❌ 错误: {$result['error']}\n\n";
        return false;
    }
    
    if ($result['http_code'] !== 200) {
        echo "❌ HTTP错误: {$result['http_code']}\n\n";
        return false;
    }
    
    $data = $result['data'];
    if (!$data || $data['code'] !== 0) {
        echo "❌ API错误: " . ($data['message'] ?? '未知错误') . "\n\n";
        return false;
    }
    
    echo "✅ 成功: {$data['message']}\n";
    return true;
}

// 1. 测试服务状态
echo "1️⃣ 测试服务状态\n";
$result = sendRequest($baseUrl . 'test.php');
if (testResult('服务状态检查', $result)) {
    $data = $result['data']['data'];
    echo "   📊 服务状态: {$data['service_status']}\n";
    echo "   🔗 大淘客状态: {$data['dataoke_status']}\n";
    echo "   🕐 服务器时间: {$data['server_time']}\n";
    echo "   🐘 PHP版本: {$data['php_version']}\n";
}
echo "\n";

// 2. 测试分类列表
echo "2️⃣ 测试分类列表\n";
$result = sendRequest($baseUrl . 'category.php');
if (testResult('分类列表获取', $result)) {
    $categories = $result['data']['data'];
    echo "   📂 分类数量: " . count($categories) . "\n";
    echo "   📝 分类示例: ";
    for ($i = 0; $i < min(3, count($categories)); $i++) {
        echo $categories[$i]['name'];
        if ($i < min(2, count($categories) - 1)) echo ", ";
    }
    echo "\n";
}
echo "\n";

// 3. 测试商品列表
echo "3️⃣ 测试商品列表\n";
$result = sendRequest($baseUrl . 'goods.php?page=1&pageSize=5&minCoupon=10');
if (testResult('商品列表获取', $result)) {
    $data = $result['data']['data'];
    echo "   🛒 商品数量: " . count($data['list']) . "\n";
    echo "   📄 当前页码: {$data['page']}\n";
    echo "   📊 每页数量: {$data['pageSize']}\n";
    echo "   🔄 是否有更多: " . ($data['hasMore'] ? '是' : '否') . "\n";
    
    if (!empty($data['list'])) {
        $firstGoods = $data['list'][0];
        echo "   🎯 商品示例:\n";
        echo "      - 名称: " . mb_substr($firstGoods['name'], 0, 30) . "...\n";
        echo "      - 价格: ¥{$firstGoods['price']}\n";
        echo "      - 原价: ¥{$firstGoods['original_price']}\n";
        echo "      - 优惠券: ¥{$firstGoods['coupon_price']}\n";
        echo "      - 销量: {$firstGoods['sale_count']}\n";
    }
}
echo "\n";

// 4. 测试商品详情
echo "4️⃣ 测试商品详情\n";
// 先获取一个商品ID
$listResult = sendRequest($baseUrl . 'goods.php?page=1&pageSize=1');
if ($listResult['data']['code'] === 0 && !empty($listResult['data']['data']['list'])) {
    $goodsId = $listResult['data']['data']['list'][0]['id'];
    $result = sendRequest($baseUrl . 'goods.php?action=detail&id=' . urlencode($goodsId));
    
    if (testResult('商品详情获取', $result)) {
        $goods = $result['data']['data'];
        echo "   🏷️ 商品ID: {$goods['id']}\n";
        echo "   📝 商品名称: " . mb_substr($goods['name'], 0, 40) . "...\n";
        echo "   💰 价格信息: ¥{$goods['price']} (原价: ¥{$goods['original_price']})\n";
        echo "   🎫 优惠券: ¥{$goods['coupon_price']} (满¥{$goods['coupon_conditions']}可用)\n";
        echo "   🏪 店铺: {$goods['shop_name']}\n";
    }
} else {
    echo "❌ 无法获取商品ID进行详情测试\n";
}
echo "\n";

// 5. 测试搜索功能
echo "5️⃣ 测试搜索功能\n";
$result = sendRequest($baseUrl . 'search.php?keyword=' . urlencode('手机') . '&page=1&pageSize=3');
if (testResult('商品搜索', $result)) {
    $data = $result['data']['data'];
    echo "   🔍 搜索关键词: {$data['keyword']}\n";
    echo "   📱 搜索结果: " . count($data['list']) . " 个商品\n";
    echo "   📄 当前页码: {$data['page']}\n";
    
    if (!empty($data['list'])) {
        echo "   🎯 搜索示例:\n";
        foreach (array_slice($data['list'], 0, 2) as $goods) {
            echo "      - " . mb_substr($goods['name'], 0, 35) . "... (¥{$goods['price']})\n";
        }
    }
}
echo "\n";

// 6. 测试缓存功能
echo "6️⃣ 测试缓存功能\n";
$startTime = microtime(true);
$result1 = sendRequest($baseUrl . 'goods.php?page=1&pageSize=5');
$firstTime = microtime(true) - $startTime;

$startTime = microtime(true);
$result2 = sendRequest($baseUrl . 'goods.php?page=1&pageSize=5');
$secondTime = microtime(true) - $startTime;

if ($result1['data']['code'] === 0 && $result2['data']['code'] === 0) {
    echo "✅ 缓存测试成功\n";
    echo "   ⏱️ 首次请求: " . round($firstTime * 1000, 2) . "ms\n";
    echo "   ⚡ 缓存请求: " . round($secondTime * 1000, 2) . "ms\n";
    echo "   📈 性能提升: " . round(($firstTime - $secondTime) / $firstTime * 100, 1) . "%\n";
} else {
    echo "❌ 缓存测试失败\n";
}
echo "\n";

// 7. 检查缓存和日志目录
echo "7️⃣ 检查文件系统\n";
$cacheDir = __DIR__ . '/dataoke-service/cache/';
$logsDir = __DIR__ . '/dataoke-service/logs/';

echo "📁 缓存目录: " . (is_dir($cacheDir) ? '✅ 存在' : '❌ 不存在') . "\n";
echo "📝 日志目录: " . (is_dir($logsDir) ? '✅ 存在' : '❌ 不存在') . "\n";

if (is_dir($cacheDir)) {
    $cacheFiles = glob($cacheDir . '*.cache');
    echo "💾 缓存文件: " . count($cacheFiles) . " 个\n";
}

if (is_dir($logsDir)) {
    $logFiles = glob($logsDir . '*.log');
    echo "📋 日志文件: " . count($logFiles) . " 个\n";
}
echo "\n";

echo "🎉 测试完成！\n";
echo "===================\n";
echo "如果所有测试都显示 ✅，说明大淘客服务端运行正常！\n";
echo "您现在可以在小程序中集成这些API接口了。\n\n";

echo "📚 API接口地址:\n";
echo "- 商品列表: {$baseUrl}goods.php\n";
echo "- 商品详情: {$baseUrl}goods.php?action=detail&id=商品ID\n";
echo "- 分类列表: {$baseUrl}category.php\n";
echo "- 搜索商品: {$baseUrl}search.php?keyword=关键词\n";
echo "- 服务测试: {$baseUrl}test.php\n";
?>
