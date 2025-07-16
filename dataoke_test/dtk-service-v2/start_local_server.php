<?php
/**
 * 本地测试服务器启动脚本
 */

echo "=== 大淘客服务端本地测试 ===\n";
echo "启动本地 PHP 开发服务器...\n";
echo "访问地址: http://localhost:8080\n";
echo "管理后台: http://localhost:8080/admin\n";
echo "API 调试: http://localhost:8080/debug_api.php\n";
echo "系统检测: http://localhost:8080/api/test\n";
echo "\n按 Ctrl+C 停止服务器\n";
echo "========================\n\n";

// 启动 PHP 内置服务器
$command = 'php -S localhost:8080 -t ' . __DIR__;
passthru($command);
?>
