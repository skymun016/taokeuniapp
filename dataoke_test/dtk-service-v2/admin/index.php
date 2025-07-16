<?php
/**
 * 大淘客服务端管理后台
 */

// 引入配置文件
require_once dirname(__DIR__) . '/vendor/autoload.php';

use Utils\EnvLoader;
use Services\DatabaseService;

// 加载环境变量
EnvLoader::load();

// 简单的身份验证
session_start();

// 处理登录
if (isset($_POST['login'])) {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // 简单验证（实际项目中应该使用更安全的方式）
    if ($username === 'admin' && $password === 'admin123') {
        $_SESSION['admin_logged_in'] = true;
        header('Location: /admin');
        exit;
    } else {
        $error = '用户名或密码错误';
    }
}

// 处理退出
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: /admin');
    exit;
}

// 检查登录状态
$isLoggedIn = $_SESSION['admin_logged_in'] ?? false;

// 如果未登录，显示登录页面
if (!$isLoggedIn) {
    ?>
    <!DOCTYPE html>
    <html lang="zh-CN">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>大淘客服务端管理后台</title>
        <style>
            body { font-family: Arial, sans-serif; background: #f5f5f5; margin: 0; padding: 50px; }
            .login-container { max-width: 400px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
            .form-group { margin-bottom: 20px; }
            label { display: block; margin-bottom: 5px; font-weight: bold; }
            input[type="text"], input[type="password"] { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
            button { width: 100%; padding: 12px; background: #007cba; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; }
            button:hover { background: #005a87; }
            .error { color: red; margin-bottom: 15px; }
            h2 { text-align: center; color: #333; margin-bottom: 30px; }
        </style>
    </head>
    <body>
        <div class="login-container">
            <h2>管理后台登录</h2>
            <?php if (isset($error)): ?>
                <div class="error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <form method="POST">
                <div class="form-group">
                    <label for="username">用户名:</label>
                    <input type="text" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="password">密码:</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <button type="submit" name="login">登录</button>
            </form>
            <p style="text-align: center; margin-top: 20px; color: #666; font-size: 14px;">
                默认用户名: admin, 密码: admin123
            </p>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// 获取当前页面
$page = $_GET['page'] ?? 'dashboard';

// 获取系统状态
try {
    $db = \Services\DatabaseService::getInstance();
    $systemStatus = [
        'database' => true,
        'tables_count' => 0
    ];
    
    // 检查表数量
    $tables = $db->fetchAll("SHOW TABLES");
    $systemStatus['tables_count'] = count($tables);
    
} catch (Exception $e) {
    $systemStatus = [
        'database' => false,
        'error' => $e->getMessage()
    ];
}

?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>大淘客服务端管理后台</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f5f5f5; }
        .header { background: #2c3e50; color: white; padding: 15px 20px; display: flex; justify-content: space-between; align-items: center; }
        .header h1 { font-size: 20px; }
        .header .user-info { font-size: 14px; }
        .header .user-info a { color: #ecf0f1; text-decoration: none; margin-left: 10px; }
        .container { display: flex; min-height: calc(100vh - 60px); }
        .sidebar { width: 250px; background: #34495e; color: white; padding: 20px 0; }
        .sidebar ul { list-style: none; }
        .sidebar li { margin-bottom: 5px; }
        .sidebar a { color: white; text-decoration: none; padding: 12px 20px; display: block; transition: background 0.3s; }
        .sidebar a:hover, .sidebar a.active { background: #2c3e50; }
        .main-content { flex: 1; padding: 20px; }
        .card { background: white; border-radius: 8px; padding: 20px; margin-bottom: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .card h3 { margin-bottom: 15px; color: #2c3e50; }
        .status-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; }
        .status-item { background: #ecf0f1; padding: 15px; border-radius: 6px; }
        .status-item.success { background: #d5f4e6; }
        .status-item.error { background: #ffeaa7; }
        .btn { padding: 8px 16px; background: #3498db; color: white; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; display: inline-block; }
        .btn:hover { background: #2980b9; }
        .btn.danger { background: #e74c3c; }
        .btn.danger:hover { background: #c0392b; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #f8f9fa; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h1>大淘客服务端管理后台</h1>
        <div class="user-info">
            欢迎, admin
            <a href="?logout=1">退出</a>
        </div>
    </div>
    
    <div class="container">
        <div class="sidebar">
            <ul>
                <li><a href="?page=dashboard" class="<?php echo $page === 'dashboard' ? 'active' : ''; ?>">仪表盘</a></li>
                <li><a href="?page=config" class="<?php echo $page === 'config' ? 'active' : ''; ?>">配置管理</a></li>
                <li><a href="?page=goods" class="<?php echo $page === 'goods' ? 'active' : ''; ?>">商品管理</a></li>
                <li><a href="?page=convert" class="<?php echo $page === 'convert' ? 'active' : ''; ?>">转链测试</a></li>
                <li><a href="?page=sync" class="<?php echo $page === 'sync' ? 'active' : ''; ?>">同步管理</a></li>
                <li><a href="?page=logs" class="<?php echo $page === 'logs' ? 'active' : ''; ?>">日志查看</a></li>
            </ul>
        </div>
        
        <div class="main-content">
            <?php
            switch ($page) {
                case 'dashboard':
                    include 'pages/dashboard.php';
                    break;
                case 'config':
                    include 'pages/config.php';
                    break;
                case 'goods':
                    include 'pages/goods.php';
                    break;
                case 'convert':
                    include 'pages/convert.php';
                    break;
                case 'sync':
                    include 'pages/sync.php';
                    break;
                case 'logs':
                    include 'pages/logs.php';
                    break;
                default:
                    include 'pages/dashboard.php';
            }
            ?>
        </div>
    </div>
</body>
</html>
