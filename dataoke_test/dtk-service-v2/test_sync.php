<?php
/**
 * 同步功能测试脚本
 */

require_once 'vendor/autoload.php';

use Services\DataokeService;
use Services\DatabaseService;
use Utils\Logger;

$logger = new Logger();

try {
    echo "开始测试同步功能...\n";
    
    // 1. 测试大淘客API调用
    echo "1. 测试大淘客API调用...\n";
    $dataokeService = new DataokeService('taobao');
    $result = $dataokeService->getGoodsList(['pageId' => 1, 'pageSize' => 5]);
    
    if (empty($result['list'])) {
        throw new Exception('获取商品列表失败');
    }
    
    echo "   获取到 " . count($result['list']) . " 个商品\n";

    // 打印第一个商品的数据结构
    echo "   第一个商品数据结构:\n";
    $firstGoods = $result['list'][0];
    foreach ($firstGoods as $key => $value) {
        echo "     {$key}: " . (is_string($value) ? substr($value, 0, 50) : $value) . "\n";
    }
    
    // 2. 测试数据库连接
    echo "2. 测试数据库连接...\n";
    $db = DatabaseService::getInstance();
    
    // 测试查询
    $goodsCount = $db->fetchOne("SELECT COUNT(*) as count FROM dtk_goods WHERE platform = ?", ['taobao']);
    echo "   当前数据库中有 " . ($goodsCount['count'] ?? 0) . " 个商品\n";
    
    // 3. 测试保存单个商品
    echo "3. 测试保存单个商品...\n";
    $firstGoods = $result['list'][0];
    
    // 转换数据格式（使用实际的API字段名）
    $data = [
        'goods_id' => $firstGoods['goods_id'],
        'title' => $firstGoods['title'],
        'dtitle' => $firstGoods['dtitle'] ?? '',
        'original_price' => $firstGoods['original_price'],
        'actual_price' => $firstGoods['actual_price'],
        'coupon_price' => $firstGoods['coupon_price'] ?? 0,
        'commission_rate' => $firstGoods['commission_rate'],
        'month_sales' => $firstGoods['month_sales'] ?? 0,
        'main_pic' => $firstGoods['main_pic'],
        'item_link' => $firstGoods['item_link'],
        'coupon_link' => $firstGoods['coupon_link'] ?? '',
        'shop_name' => $firstGoods['shop_name'],
        'shop_type' => $firstGoods['shop_type'] ?? 1,
        'brand_name' => $firstGoods['brand_name'] ?? '',
        'cid' => $firstGoods['cid'],
        'desc_score' => $firstGoods['desc_score'] ?? 0,
        'service_score' => $firstGoods['service_score'] ?? 0,
        'ship_score' => $firstGoods['ship_score'] ?? 0,
        'platform' => 'taobao',
        'create_time' => date('Y-m-d H:i:s'),
        'update_time' => date('Y-m-d H:i:s')
    ];
    
    // 检查商品是否已存在
    $existing = $db->fetchOne(
        "SELECT id FROM dtk_goods WHERE goods_id = ? AND platform = ?",
        [$data['goods_id'], 'taobao']
    );
    
    if ($existing) {
        echo "   商品已存在，ID: " . $existing['id'] . "\n";
        
        // 更新商品
        unset($data['create_time']);
        $fields = array_keys($data);
        $setClause = implode(' = ?, ', $fields) . ' = ?';
        $sql = "UPDATE dtk_goods SET {$setClause} WHERE id = ?";
        $params = array_merge(array_values($data), [$existing['id']]);
        
        $db->execute($sql, $params);
        echo "   商品更新成功\n";
    } else {
        // 插入新商品
        $fields = array_keys($data);
        $placeholders = array_fill(0, count($fields), '?');
        $sql = "INSERT INTO dtk_goods (" . implode(', ', $fields) . ") VALUES (" . implode(', ', $placeholders) . ")";
        
        $db->execute($sql, array_values($data));
        echo "   商品插入成功\n";
    }
    
    // 4. 验证保存结果
    echo "4. 验证保存结果...\n";
    $savedGoods = $db->fetchOne(
        "SELECT * FROM dtk_goods WHERE goods_id = ? AND platform = ?",
        [$data['goods_id'], 'taobao']
    );
    
    if ($savedGoods) {
        echo "   商品保存成功，标题: " . $savedGoods['title'] . "\n";
        echo "   价格: " . $savedGoods['actual_price'] . " 元\n";
    } else {
        throw new Exception('商品保存失败');
    }
    
    // 5. 记录同步日志
    echo "5. 记录同步日志...\n";
    $logData = [
        'sync_type' => 'test_sync',
        'platform' => 'taobao',
        'success_count' => 1,
        'error_count' => 0,
        'duration' => 100,
        'start_time' => date('Y-m-d H:i:s'),
        'end_time' => date('Y-m-d H:i:s'),
        'create_time' => date('Y-m-d H:i:s')
    ];
    
    $fields = array_keys($logData);
    $placeholders = array_fill(0, count($fields), '?');
    $sql = "INSERT INTO dtk_sync_logs (" . implode(', ', $fields) . ") VALUES (" . implode(', ', $placeholders) . ")";
    
    $db->execute($sql, array_values($logData));
    echo "   同步日志记录成功\n";
    
    echo "\n✅ 所有测试通过！同步功能正常工作。\n";
    
} catch (Exception $e) {
    echo "\n❌ 测试失败: " . $e->getMessage() . "\n";
    echo "文件: " . $e->getFile() . "\n";
    echo "行号: " . $e->getLine() . "\n";
    
    $logger->error('同步测试失败', [
        'error' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ]);
}
?>
