<?php
/**
 * 智能同步管理页面 - 智能混合转链策略
 */

// 获取智能转链统计
try {
    $syncService = new \Services\GoodsSyncService('taobao');
    $linkStats = $syncService->getSmartLinkStats();
} catch (Exception $e) {
    $linkStats = [];
}

// 获取同步状态
try {
    $db = \Services\DatabaseService::getInstance();

    // 获取最近的智能同步记录
    $recentSyncs = $db->fetchAll(
        "SELECT * FROM dtk_sync_logs WHERE sync_type LIKE '%smart%' OR sync_type = 'hourly_smart_sync' ORDER BY create_time DESC LIMIT 5"
    );

    // 获取今日智能同步统计
    $todayStats = $db->fetchOne(
        "SELECT
            COUNT(*) as sync_count,
            SUM(success_count) as total_success,
            SUM(error_count) as total_errors
         FROM dtk_sync_logs
         WHERE DATE(create_time) = CURDATE() AND (sync_type LIKE '%smart%' OR sync_type = 'hourly_smart_sync')"
    );

    // 获取商品总数和转链统计
    $goodsStats = $db->fetchOne(
        "SELECT
            COUNT(*) as total_goods,
            COUNT(CASE WHEN link_status = 1 THEN 1 END) as converted_goods,
            COUNT(CASE WHEN tier_level = 1 THEN 1 END) as tier1_goods,
            MAX(update_time) as last_update
         FROM dtk_goods WHERE platform = 'taobao'"
    );

} catch (Exception $e) {
    $recentSyncs = [];
    $todayStats = ['sync_count' => 0, 'total_success' => 0, 'total_errors' => 0];
    $goodsStats = ['total_goods' => 0, 'converted_goods' => 0, 'tier1_goods' => 0, 'last_update' => null];
}

?>

<!-- 智能同步动画样式 -->
<style>
.sync-container {
    max-width: 800px;
    margin: 0 auto;
}

.sync-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 30px;
    border-radius: 15px;
    margin-bottom: 20px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.2);
}

.sync-button {
    background: linear-gradient(45deg, #ff6b6b, #ee5a24);
    color: white;
    border: none;
    padding: 15px 30px;
    border-radius: 25px;
    font-size: 16px;
    font-weight: bold;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}

.sync-button:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.3);
}

.sync-button:disabled {
    background: #95a5a6;
    cursor: not-allowed;
    transform: none;
}

.progress-container {
    display: none;
    margin-top: 20px;
    padding: 20px;
    background: rgba(255,255,255,0.1);
    border-radius: 10px;
}

.progress-bar {
    width: 100%;
    height: 8px;
    background: rgba(255,255,255,0.3);
    border-radius: 4px;
    overflow: hidden;
    margin-bottom: 15px;
}

.progress-fill {
    height: 100%;
    background: linear-gradient(90deg, #00d2ff, #3a7bd5);
    width: 0%;
    transition: width 0.5s ease;
    border-radius: 4px;
}

.sync-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 15px;
    margin-top: 15px;
}

.stat-item {
    text-align: center;
    padding: 10px;
    background: rgba(255,255,255,0.1);
    border-radius: 8px;
}

.stat-number {
    font-size: 24px;
    font-weight: bold;
    display: block;
}

.stat-label {
    font-size: 12px;
    opacity: 0.8;
}

.status-indicator {
    display: inline-block;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    margin-right: 8px;
}

.status-success { background: #27ae60; }
.status-warning { background: #f39c12; }
.status-error { background: #e74c3c; }

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}

.syncing {
    animation: pulse 2s infinite;
}

.tier-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
    margin-top: 20px;
}

.tier-card {
    background: rgba(255,255,255,0.1);
    padding: 15px;
    border-radius: 10px;
    text-align: center;
}

.tier-title {
    font-size: 14px;
    opacity: 0.8;
    margin-bottom: 10px;
}

.tier-number {
    font-size: 28px;
    font-weight: bold;
    margin-bottom: 5px;
}

.tier-progress {
    width: 100%;
    height: 6px;
    background: rgba(255,255,255,0.3);
    border-radius: 3px;
    overflow: hidden;
    margin-top: 10px;
}

.tier-progress-fill {
    height: 100%;
    border-radius: 3px;
    transition: width 0.5s ease;
}

.tier1-fill { background: linear-gradient(90deg, #ff6b6b, #ee5a24); }
.tier2-fill { background: linear-gradient(90deg, #feca57, #ff9ff3); }
.tier3-fill { background: linear-gradient(90deg, #48dbfb, #0abde3); }
</style>

<div class="sync-container">
    <div class="sync-card">
        <h3 style="margin-top: 0; text-align: center; font-size: 24px;">
            🚀 智能混合转链同步
        </h3>
        <p style="text-align: center; opacity: 0.9; margin-bottom: 25px;">
            每次同步200条商品，热门商品自动转链，提升转链效率
        </p>

        <div style="text-align: center;">
            <button id="smartSyncBtn" class="sync-button" onclick="startSmartSync()">
                🔄 开始智能同步
            </button>
        </div>

        <div id="progressContainer" class="progress-container">
            <div class="progress-bar">
                <div id="progressFill" class="progress-fill"></div>
            </div>

            <div id="syncStatus" style="text-align: center; margin-bottom: 15px;">
                准备开始同步...
            </div>

            <div class="sync-stats">
                <div class="stat-item">
                    <span id="syncedCount" class="stat-number">0</span>
                    <span class="stat-label">已同步商品</span>
                </div>
                <div class="stat-item">
                    <span id="convertedCount" class="stat-number">0</span>
                    <span class="stat-label">Tier1转链</span>
                </div>
                <div class="stat-item">
                    <span id="errorCount" class="stat-number">0</span>
                    <span class="stat-label">错误数量</span>
                </div>
                <div class="stat-item">
                    <span id="duration" class="stat-number">0s</span>
                    <span class="stat-label">执行时间</span>
                </div>
            </div>
        </div>
    </div>

    <div style="margin-top: 20px; text-align: center;">
        <button onclick="refreshStats()" class="btn" style="margin-right: 10px;">🔄 刷新统计</button>
        <a href="/api/sync.php?type=stats&platform=taobao" class="btn" target="_blank" style="margin-right: 10px;">📊 API统计</a>
        <a href="/api/sync.php?type=clean&platform=taobao&days=30" class="btn danger" target="_blank"
           onclick="return confirm('确定要清理30天前的过期商品吗？')">
            🗑️ 清理过期商品
        </a>
    </div>
</div>

<!-- 智能转链统计 -->
<div class="card">
    <h3>📊 智能转链统计</h3>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 15px; margin-bottom: 20px;">
        <div style="text-align: center; padding: 15px; border: 1px solid #ddd; border-radius: 6px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
            <div style="font-size: 28px; font-weight: bold;">
                <?php echo $goodsStats['total_goods'] ?? 0; ?>
            </div>
            <div style="opacity: 0.9; font-size: 14px;">总商品数</div>
        </div>

        <div style="text-align: center; padding: 15px; border: 1px solid #ddd; border-radius: 6px; background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%); color: white;">
            <div style="font-size: 28px; font-weight: bold;">
                <?php echo $goodsStats['tier1_goods'] ?? 0; ?>
            </div>
            <div style="opacity: 0.9; font-size: 14px;">热门商品 (Tier 1)</div>
        </div>

        <div style="text-align: center; padding: 15px; border: 1px solid #ddd; border-radius: 6px; background: linear-gradient(135deg, #00d2ff 0%, #3a7bd5 100%); color: white;">
            <div style="font-size: 28px; font-weight: bold;">
                <?php echo $goodsStats['converted_goods'] ?? 0; ?>
            </div>
            <div style="opacity: 0.9; font-size: 14px;">已转链商品</div>
        </div>

        <div style="text-align: center; padding: 15px; border: 1px solid #ddd; border-radius: 6px; background: linear-gradient(135deg, #feca57 0%, #ff9ff3 100%); color: white;">
            <div style="font-size: 28px; font-weight: bold;">
                <?php
                $convertRate = ($goodsStats['total_goods'] ?? 0) > 0 ?
                    round((($goodsStats['converted_goods'] ?? 0) / ($goodsStats['total_goods'] ?? 1)) * 100, 1) : 0;
                echo $convertRate;
                ?>%
            </div>
            <div style="opacity: 0.9; font-size: 14px;">转链成功率</div>
        </div>
    </div>

    <!-- 分层统计 -->
    <?php if (!empty($linkStats)): ?>
    <div class="tier-stats">
        <?php foreach ($linkStats as $stat): ?>
        <div class="tier-card">
            <div class="tier-title">
                Tier <?php echo $stat['tier_level']; ?>
                <?php
                $tierNames = [1 => '热门商品', 2 => '普通商品', 3 => '冷门商品'];
                echo $tierNames[$stat['tier_level']] ?? '未知';
                ?>
            </div>
            <div class="tier-number"><?php echo $stat['converted']; ?>/<?php echo $stat['total']; ?></div>
            <div style="font-size: 12px; opacity: 0.8;">
                转链率: <?php echo $stat['total'] > 0 ? round(($stat['converted'] / $stat['total']) * 100, 1) : 0; ?>%
            </div>
            <div class="tier-progress">
                <div class="tier-progress-fill tier<?php echo $stat['tier_level']; ?>-fill"
                     style="width: <?php echo $stat['total'] > 0 ? round(($stat['converted'] / $stat['total']) * 100, 1) : 0; ?>%"></div>
            </div>
            <div style="font-size: 11px; opacity: 0.7; margin-top: 5px;">
                平均销量: <?php echo number_format($stat['avg_sales'] ?? 0); ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <div style="margin-top: 20px; padding: 15px; background: #f8f9fa; border-radius: 6px;">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px;">
            <div style="text-align: center;">
                <div style="font-size: 18px; font-weight: bold; color: #27ae60;">
                    <?php echo $todayStats['sync_count'] ?? 0; ?>
                </div>
                <div style="color: #7f8c8d; font-size: 12px;">今日同步次数</div>
            </div>
            <div style="text-align: center;">
                <div style="font-size: 18px; font-weight: bold; color: #3498db;">
                    <?php echo $todayStats['total_success'] ?? 0; ?>
                </div>
                <div style="color: #7f8c8d; font-size: 12px;">今日同步商品</div>
            </div>
            <div style="text-align: center;">
                <div style="font-size: 18px; font-weight: bold; color: #e74c3c;">
                    <?php echo $todayStats['total_errors'] ?? 0; ?>
                </div>
                <div style="color: #7f8c8d; font-size: 12px;">同步错误</div>
            </div>
            <div style="text-align: center;">
                <div style="font-size: 18px; font-weight: bold; color: #9b59b6;">
                    <?php echo $goodsStats['last_update'] ? date('H:i', strtotime($goodsStats['last_update'])) : '--'; ?>
                </div>
                <div style="color: #7f8c8d; font-size: 12px;">最后更新</div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <h3>最近同步记录</h3>
    <?php if (empty($recentSyncs)): ?>
        <p>暂无同步记录</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>时间</th>
                    <th>类型</th>
                    <th>平台</th>
                    <th>成功</th>
                    <th>错误</th>
                    <th>耗时</th>
                    <th>时间范围</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recentSyncs as $sync): ?>
                <tr>
                    <td><?php echo $sync['create_time']; ?></td>
                    <td><?php echo $sync['sync_type'] === 'full_sync' ? '全量' : '增量'; ?></td>
                    <td><?php echo $sync['platform']; ?></td>
                    <td style="color: green;"><?php echo number_format($sync['success_count']); ?></td>
                    <td style="color: <?php echo $sync['error_count'] > 0 ? 'red' : 'green'; ?>;">
                        <?php echo number_format($sync['error_count']); ?>
                    </td>
                    <td><?php echo $sync['duration']; ?>ms</td>
                    <td style="font-size: 12px;">
                        <?php if ($sync['start_time']): ?>
                            <?php echo date('H:i', strtotime($sync['start_time'])); ?> -
                            <?php echo date('H:i', strtotime($sync['end_time'])); ?>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<div class="card">
    <h3>定时任务设置</h3>
    <p>要启用每小时自动同步，请在服务器上添加以下 crontab 任务：</p>
    <div style="background: #f8f9fa; padding: 15px; border-radius: 4px; font-family: monospace; font-size: 12px; margin: 10px 0;">
        # 每小时执行增量同步<br>
        0 * * * * /usr/bin/php <?php echo realpath(__DIR__ . '/../../cron/hourly_sync.php'); ?>
    </div>
    <p style="color: #666; font-size: 14px;">
        或者使用 Web 方式触发：每小时访问一次
        <a href="/cron/hourly_sync.php" target="_blank">/cron/hourly_sync.php</a>
    </p>
</div>

<script>
let syncInProgress = false;
let syncStartTime = 0;
let syncInterval = null;

/**
 * 开始智能同步
 */
async function startSmartSync() {
    if (syncInProgress) {
        alert('同步正在进行中，请稍候...');
        return;
    }

    const btn = document.getElementById('smartSyncBtn');
    const progressContainer = document.getElementById('progressContainer');
    const progressFill = document.getElementById('progressFill');
    const syncStatus = document.getElementById('syncStatus');

    // 初始化UI
    syncInProgress = true;
    syncStartTime = Date.now();
    btn.disabled = true;
    btn.textContent = '🔄 同步中...';
    btn.classList.add('syncing');
    progressContainer.style.display = 'block';

    // 重置统计
    updateSyncStats(0, 0, 0, 0);

    try {
        // 显示准备阶段
        updateSyncStatus('🚀 准备开始智能同步...', 10);
        await sleep(500);

        // 调用智能同步API
        updateSyncStatus('📡 调用大淘客API获取商品数据...', 20);

        const response = await fetch('/api/sync.php?type=hourly&platform=taobao', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
            }
        });

        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }

        const result = await response.json();

        if (result.code === 200 && result.data && result.data.success) {
            // 同步成功
            const data = result.data;

            // 模拟进度更新
            updateSyncStatus('💾 保存商品数据到数据库...', 60);
            await sleep(1000);

            updateSyncStatus('🔗 执行Tier 1商品智能转链...', 80);
            await sleep(1000);

            updateSyncStatus('✅ 智能同步完成！', 100);

            // 更新最终统计
            updateSyncStats(
                data.totalSynced || 0,
                data.tier1Converted || 0,
                data.totalErrors || 0,
                Math.round((Date.now() - syncStartTime) / 1000)
            );

            // 显示成功消息
            setTimeout(() => {
                alert(`🎉 智能同步成功完成！\n\n📊 同步统计：\n• 同步商品：${data.totalSynced || 0} 个\n• Tier1转链：${data.tier1Converted || 0} 个\n• 错误数量：${data.totalErrors || 0} 个\n• 执行时间：${data.duration || '未知'}`);

                // 刷新页面统计
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            }, 1000);

        } else {
            // 同步失败
            throw new Error(result.message || '同步失败');
        }

    } catch (error) {
        console.error('智能同步失败:', error);
        updateSyncStatus(`❌ 同步失败: ${error.message}`, 0);
        updateSyncStats(0, 0, 1, Math.round((Date.now() - syncStartTime) / 1000));

        setTimeout(() => {
            alert(`❌ 智能同步失败：\n\n${error.message}\n\n请检查网络连接和API配置。`);
        }, 500);
    } finally {
        // 重置UI状态
        syncInProgress = false;
        btn.disabled = false;
        btn.textContent = '🔄 开始智能同步';
        btn.classList.remove('syncing');

        if (syncInterval) {
            clearInterval(syncInterval);
            syncInterval = null;
        }
    }
}

/**
 * 更新同步状态
 */
function updateSyncStatus(message, progress) {
    const syncStatus = document.getElementById('syncStatus');
    const progressFill = document.getElementById('progressFill');

    syncStatus.textContent = message;
    progressFill.style.width = progress + '%';
}

/**
 * 更新同步统计
 */
function updateSyncStats(synced, converted, errors, duration) {
    document.getElementById('syncedCount').textContent = synced;
    document.getElementById('convertedCount').textContent = converted;
    document.getElementById('errorCount').textContent = errors;
    document.getElementById('duration').textContent = duration + 's';
}

/**
 * 刷新统计数据
 */
async function refreshStats() {
    try {
        const response = await fetch('/api/sync.php?type=stats&platform=taobao');
        const result = await response.json();

        if (result.code === 200) {
            // 刷新页面以显示最新统计
            window.location.reload();
        } else {
            alert('刷新统计失败: ' + result.message);
        }
    } catch (error) {
        console.error('刷新统计失败:', error);
        alert('刷新统计失败: ' + error.message);
    }
}

/**
 * 延迟函数
 */
function sleep(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}

// 页面加载完成后的初始化
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 智能混合转链同步页面已加载');

    // 检查是否有正在进行的同步
    const btn = document.getElementById('smartSyncBtn');
    if (btn) {
        btn.addEventListener('click', startSmartSync);
    }
});
</script>
