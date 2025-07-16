<?php
/**
 * 仪表盘页面
 */

// 获取系统统计信息
try {
    $db = \Services\DatabaseService::getInstance();
    
    // 商品统计
    $goodsCount = $db->fetchOne("SELECT COUNT(*) as count FROM dtk_goods")['count'] ?? 0;
    
    // 分类统计
    $categoryCount = $db->fetchOne("SELECT COUNT(*) as count FROM dtk_categories")['count'] ?? 0;
    
    // 同步日志统计
    $syncLogCount = $db->fetchOne("SELECT COUNT(*) as count FROM dtk_sync_logs")['count'] ?? 0;
    
    // 最近同步时间
    $lastSync = $db->fetchOne("SELECT create_time FROM dtk_sync_logs ORDER BY create_time DESC LIMIT 1");
    $lastSyncTime = $lastSync ? $lastSync['create_time'] : '从未同步';
    
    // 平台配置统计
    $platformCount = $db->fetchOne("SELECT COUNT(*) as count FROM dtk_platforms WHERE enabled = 1")['count'] ?? 0;
    
} catch (Exception $e) {
    $goodsCount = 0;
    $categoryCount = 0;
    $syncLogCount = 0;
    $lastSyncTime = '数据库连接失败';
    $platformCount = 0;
}

// 获取环境信息
$envInfo = [
    'php_version' => PHP_VERSION,
    'memory_usage' => round(memory_get_usage() / 1024 / 1024, 2) . ' MB',
    'memory_peak' => round(memory_get_peak_usage() / 1024 / 1024, 2) . ' MB',
    'server_time' => date('Y-m-d H:i:s'),
    'timezone' => date_default_timezone_get(),
];

?>

<div class="card">
    <h3>系统概览</h3>
    <div class="status-grid">
        <div class="status-item <?php echo $systemStatus['database'] ? 'success' : 'error'; ?>">
            <h4>数据库状态</h4>
            <p><?php echo $systemStatus['database'] ? '连接正常' : '连接失败'; ?></p>
            <?php if (!$systemStatus['database']): ?>
                <small><?php echo htmlspecialchars($systemStatus['error']); ?></small>
            <?php endif; ?>
        </div>
        
        <div class="status-item success">
            <h4>商品数量</h4>
            <p><?php echo number_format($goodsCount); ?> 个</p>
        </div>
        
        <div class="status-item success">
            <h4>分类数量</h4>
            <p><?php echo number_format($categoryCount); ?> 个</p>
        </div>
        
        <div class="status-item success">
            <h4>启用平台</h4>
            <p><?php echo $platformCount; ?> 个</p>
        </div>
    </div>
</div>

<div class="card">
    <h3>同步状态</h3>
    <div class="status-grid">
        <div class="status-item">
            <h4>同步记录</h4>
            <p><?php echo number_format($syncLogCount); ?> 条</p>
        </div>
        
        <div class="status-item">
            <h4>最近同步</h4>
            <p><?php echo htmlspecialchars($lastSyncTime); ?></p>
        </div>
    </div>
    
    <div style="margin-top: 20px;">
        <a href="?page=sync" class="btn">管理同步</a>
        <a href="/api/test" class="btn" target="_blank">系统检测</a>
    </div>
</div>

<div class="card">
    <h3>环境信息</h3>
    <table>
        <tr>
            <th>项目</th>
            <th>值</th>
        </tr>
        <tr>
            <td>PHP 版本</td>
            <td><?php echo $envInfo['php_version']; ?></td>
        </tr>
        <tr>
            <td>内存使用</td>
            <td><?php echo $envInfo['memory_usage']; ?></td>
        </tr>
        <tr>
            <td>内存峰值</td>
            <td><?php echo $envInfo['memory_peak']; ?></td>
        </tr>
        <tr>
            <td>服务器时间</td>
            <td><?php echo $envInfo['server_time']; ?></td>
        </tr>
        <tr>
            <td>时区</td>
            <td><?php echo $envInfo['timezone']; ?></td>
        </tr>
        <tr>
            <td>数据库表数量</td>
            <td><?php echo $systemStatus['tables_count']; ?> 个</td>
        </tr>
    </table>
</div>

<div class="card">
    <h3>快速操作</h3>
    <div style="display: flex; gap: 10px; flex-wrap: wrap;">
        <a href="?page=config" class="btn">配置管理</a>
        <a href="?page=goods" class="btn">商品管理</a>
        <a href="/api/goods/list" class="btn" target="_blank">商品API</a>
        <a href="/api/category/list" class="btn" target="_blank">分类API</a>
    </div>
</div>
