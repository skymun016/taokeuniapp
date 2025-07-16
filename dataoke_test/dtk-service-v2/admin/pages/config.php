<?php
/**
 * 配置管理页面
 */

// 处理配置更新
if ($_POST['action'] === 'update_config') {
    // 这里可以添加配置更新逻辑
    $success = "配置更新功能待实现";
}

?>

<div class="card">
    <h3>大淘客配置</h3>
    <form method="POST">
        <input type="hidden" name="action" value="update_config">
        
        <div style="margin-bottom: 20px;">
            <label><strong>淘宝/天猫配置</strong></label>
            <table style="margin-top: 10px;">
                <tr>
                    <td style="width: 150px;">APP KEY:</td>
                    <td><input type="text" value="<?php echo EnvLoader::get('TAOBAO_APP_KEY'); ?>" readonly style="width: 300px; padding: 5px;"></td>
                </tr>
                <tr>
                    <td>APP SECRET:</td>
                    <td><input type="text" value="<?php echo substr(EnvLoader::get('TAOBAO_APP_SECRET'), 0, 10) . '...'; ?>" readonly style="width: 300px; padding: 5px;"></td>
                </tr>
                <tr>
                    <td>PID:</td>
                    <td><input type="text" value="<?php echo EnvLoader::get('TAOBAO_PID'); ?>" readonly style="width: 300px; padding: 5px;"></td>
                </tr>
                <tr>
                    <td>状态:</td>
                    <td><span style="color: green;">✓ 已启用</span></td>
                </tr>
            </table>
        </div>
        
        <div style="margin-bottom: 20px;">
            <label><strong>数据库配置</strong></label>
            <table style="margin-top: 10px;">
                <tr>
                    <td style="width: 150px;">主机:</td>
                    <td><input type="text" value="<?php echo EnvLoader::get('DB_HOST'); ?>" readonly style="width: 300px; padding: 5px;"></td>
                </tr>
                <tr>
                    <td>端口:</td>
                    <td><input type="text" value="<?php echo EnvLoader::get('DB_PORT'); ?>" readonly style="width: 300px; padding: 5px;"></td>
                </tr>
                <tr>
                    <td>数据库:</td>
                    <td><input type="text" value="<?php echo EnvLoader::get('DB_NAME'); ?>" readonly style="width: 300px; padding: 5px;"></td>
                </tr>
                <tr>
                    <td>用户名:</td>
                    <td><input type="text" value="<?php echo EnvLoader::get('DB_USER'); ?>" readonly style="width: 300px; padding: 5px;"></td>
                </tr>
            </table>
        </div>
        
        <?php if (isset($success)): ?>
            <div style="color: green; margin-bottom: 15px;"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <p style="color: #666; font-size: 14px;">
            配置信息从 .env 文件读取，如需修改请直接编辑 .env 文件。
        </p>
    </form>
</div>

<div class="card">
    <h3>系统配置</h3>
    <table>
        <tr>
            <th>配置项</th>
            <th>当前值</th>
        </tr>
        <tr>
            <td>调试模式</td>
            <td><?php echo EnvLoader::get('APP_DEBUG') ? '开启' : '关闭'; ?></td>
        </tr>
        <tr>
            <td>时区</td>
            <td><?php echo EnvLoader::get('APP_TIMEZONE'); ?></td>
        </tr>
        <tr>
            <td>缓存驱动</td>
            <td><?php echo EnvLoader::get('CACHE_DRIVER'); ?></td>
        </tr>
        <tr>
            <td>日志级别</td>
            <td><?php echo EnvLoader::get('LOG_LEVEL'); ?></td>
        </tr>
        <tr>
            <td>API 超时时间</td>
            <td><?php echo EnvLoader::get('API_TIMEOUT'); ?> 秒</td>
        </tr>
        <tr>
            <td>同步批次大小</td>
            <td><?php echo EnvLoader::get('SYNC_BATCH_SIZE'); ?></td>
        </tr>
    </table>
</div>
