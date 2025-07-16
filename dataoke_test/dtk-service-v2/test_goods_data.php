<?php
/**
 * 商品数据验证页面
 */

header('Content-Type: text/html; charset=utf-8');

require_once 'vendor/autoload.php';
require_once 'config/config.php';

try {
    $db = \Services\DatabaseService::getInstance();
    
    // 获取总体统计
    $totalStats = $db->fetchOne("
        SELECT 
            COUNT(*) as total_goods,
            COUNT(CASE WHEN DATE(create_time) = CURDATE() THEN 1 END) as today_created,
            COUNT(CASE WHEN DATE(update_time) = CURDATE() THEN 1 END) as today_updated,
            MAX(create_time) as last_created,
            MAX(update_time) as last_updated
        FROM dtk_goods 
        WHERE platform = 'taobao'
    ");
    
    // 获取最近更新的10个商品
    $recentGoods = $db->fetchAll("
        SELECT goods_id, title, month_sales, tier_level, link_status, create_time, update_time
        FROM dtk_goods 
        WHERE platform = 'taobao' 
        ORDER BY update_time DESC 
        LIMIT 10
    ");
    
    // 获取分层统计
    $tierStats = $db->fetchAll("
        SELECT 
            tier_level,
            COUNT(*) as count,
            COUNT(CASE WHEN link_status = 1 THEN 1 END) as converted,
            AVG(month_sales) as avg_sales
        FROM dtk_goods 
        WHERE platform = 'taobao'
        GROUP BY tier_level
        ORDER BY tier_level
    ");
    
} catch (Exception $e) {
    die('数据库错误: ' . $e->getMessage());
}

?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>商品数据验证</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; }
        .card { background: white; padding: 20px; margin-bottom: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 20px; }
        .stat-item { text-align: center; padding: 15px; background: #f8f9fa; border-radius: 6px; }
        .stat-number { font-size: 24px; font-weight: bold; color: #2c3e50; }
        .stat-label { font-size: 12px; color: #7f8c8d; margin-top: 5px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { padding: 8px; border: 1px solid #ddd; text-align: left; }
        th { background: #f8f9fa; font-weight: bold; }
        .tier1 { background: #ffe6e6; }
        .tier2 { background: #fff3cd; }
        .tier3 { background: #e3f2fd; }
        .today { color: #27ae60; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <h1>📊 商品数据验证报告</h1>
            <p>验证智能同步功能是否正常工作</p>
        </div>

        <div class="card">
            <h2>📈 总体统计</h2>
            <div class="stats-grid">
                <div class="stat-item">
                    <div class="stat-number"><?php echo number_format($totalStats['total_goods']); ?></div>
                    <div class="stat-label">总商品数</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number"><?php echo number_format($totalStats['today_created']); ?></div>
                    <div class="stat-label">今日新增</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number"><?php echo number_format($totalStats['today_updated']); ?></div>
                    <div class="stat-label">今日更新</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number"><?php echo date('H:i', strtotime($totalStats['last_updated'])); ?></div>
                    <div class="stat-label">最后更新时间</div>
                </div>
            </div>
        </div>

        <div class="card">
            <h2>🏷️ 分层统计</h2>
            <table>
                <thead>
                    <tr>
                        <th>层级</th>
                        <th>商品数量</th>
                        <th>已转链</th>
                        <th>转链率</th>
                        <th>平均销量</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tierStats as $tier): ?>
                    <tr class="tier<?php echo $tier['tier_level']; ?>">
                        <td>
                            Tier <?php echo $tier['tier_level']; ?>
                            <?php 
                            $tierNames = [1 => '热门', 2 => '普通', 3 => '冷门'];
                            echo '(' . ($tierNames[$tier['tier_level']] ?? '未知') . ')';
                            ?>
                        </td>
                        <td><?php echo number_format($tier['count']); ?></td>
                        <td><?php echo number_format($tier['converted']); ?></td>
                        <td>
                            <?php 
                            $rate = $tier['count'] > 0 ? round(($tier['converted'] / $tier['count']) * 100, 1) : 0;
                            echo $rate . '%';
                            ?>
                        </td>
                        <td><?php echo number_format($tier['avg_sales']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="card">
            <h2>🕒 最近更新的商品</h2>
            <table>
                <thead>
                    <tr>
                        <th>商品ID</th>
                        <th>商品标题</th>
                        <th>月销量</th>
                        <th>层级</th>
                        <th>转链状态</th>
                        <th>更新时间</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentGoods as $goods): ?>
                    <tr>
                        <td style="font-family: monospace; font-size: 11px;">
                            <?php echo substr($goods['goods_id'], 0, 20) . '...'; ?>
                        </td>
                        <td><?php echo htmlspecialchars(mb_substr($goods['title'], 0, 40)) . '...'; ?></td>
                        <td><?php echo number_format($goods['month_sales']); ?></td>
                        <td>Tier <?php echo $goods['tier_level']; ?></td>
                        <td>
                            <?php if ($goods['link_status'] == 1): ?>
                                <span style="color: #27ae60;">✅ 已转链</span>
                            <?php else: ?>
                                <span style="color: #e74c3c;">❌ 未转链</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php 
                            $updateTime = $goods['update_time'];
                            $isToday = date('Y-m-d', strtotime($updateTime)) == date('Y-m-d');
                            ?>
                            <span class="<?php echo $isToday ? 'today' : ''; ?>">
                                <?php echo date('m-d H:i', strtotime($updateTime)); ?>
                                <?php if ($isToday): ?>
                                    (今日)
                                <?php endif; ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="card">
            <h2>🔗 快速操作</h2>
            <div style="text-align: center;">
                <a href="/admin/index.php?page=goods" style="display: inline-block; padding: 10px 20px; background: #3498db; color: white; text-decoration: none; border-radius: 5px; margin: 5px;">
                    📦 商品管理
                </a>
                <a href="/admin/index.php?page=sync" style="display: inline-block; padding: 10px 20px; background: #27ae60; color: white; text-decoration: none; border-radius: 5px; margin: 5px;">
                    🔄 同步管理
                </a>
                <a href="/test_sync_page.html" style="display: inline-block; padding: 10px 20px; background: #e74c3c; color: white; text-decoration: none; border-radius: 5px; margin: 5px;">
                    🧪 同步测试
                </a>
            </div>
        </div>
    </div>
</body>
</html>
