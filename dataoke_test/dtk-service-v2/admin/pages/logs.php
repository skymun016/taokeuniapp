<?php
/**
 * 日志查看页面
 */

?>

<div class="card">
    <h3>系统日志</h3>
    <p>查看系统运行日志</p>
    
    <div style="margin: 20px 0;">
        <select style="padding: 8px; margin-right: 10px;">
            <option>应用日志</option>
            <option>错误日志</option>
            <option>API日志</option>
            <option>同步日志</option>
        </select>
        <button class="btn">查看日志</button>
        <button class="btn danger">清空日志</button>
    </div>
    
    <div style="background: #f8f9fa; padding: 15px; border-radius: 4px; font-family: monospace; font-size: 12px; max-height: 400px; overflow-y: auto;">
        <p>日志查看功能开发中...</p>
        <p>可以在这里显示实时日志内容</p>
    </div>
</div>
