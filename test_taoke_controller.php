<?php
/**
 * 直接测试TaokeController
 */

// 设置错误报告
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 定义必要的常量
define('IN_IA', true);

// 设置项目根目录
$rootPath = __DIR__ . '/上门服务代码安装包/服务器代码';
chdir($rootPath);

// 加载ThinkPHP
require_once $rootPath . '/vendor/autoload.php';

// 模拟全局变量
global $_W;
$_W = [
    'uniacid' => 1,
    'fans' => ['openid' => 'test_openid'],
    'i' => 1
];

// 模拟weid函数
if (!function_exists('weid')) {
    function weid() {
        return 1;
    }
}

// 模拟其他必要函数
if (!function_exists('getclient')) {
    function getclient() {
        return 'web';
    }
}

if (!function_exists('Author')) {
    function Author() {
        return new class {
            public static function getlan() {
                return 'zh-cn';
            }
        };
    }
}

if (!function_exists('iunserializer')) {
    function iunserializer($data) {
        return unserialize($data);
    }
}

if (!function_exists('getRealIP')) {
    function getRealIP() {
        return '127.0.0.1';
    }
}

if (!function_exists('UID')) {
    function UID() {
        return 1;
    }
}

if (!function_exists('input')) {
    function input($key, $default = '', $filter = '') {
        // 解析参数
        $keys = explode('.', $key);
        $method = array_shift($keys);
        $param = implode('.', $keys);
        
        switch ($method) {
            case 'param':
                return $_REQUEST[$param] ?? $default;
            case 'get':
                return $_GET[$param] ?? $default;
            case 'post':
                return $_POST[$param] ?? $default;
            default:
                return $_REQUEST[$key] ?? $default;
        }
    }
}

// 引入模型类
use app\model\TaokeGoods;

try {
    echo "开始测试TaokeController...\n";

    // 模拟请求参数
    $_GET['platform'] = 'taobao';
    $_REQUEST['platform'] = 'taobao';

    // 直接实例化和测试TaokeGoods模型
    echo "测试TaokeGoods模型...\n";
    
    $params = [
        'platform' => 'taobao',
        'page_size' => 10
    ];
    
    echo "调用TaokeGoods::getGoodsList()...\n";
    $result = TaokeGoods::getGoodsList($params);
    
    echo "查询结果:\n";
    echo "- 总数: " . $result->total() . "\n";
    echo "- 当前页: " . $result->currentPage() . "\n";
    echo "- 每页数量: " . $result->listRows() . "\n";
    
    $items = $result->items();
    echo "- 商品数量: " . count($items) . "\n";
    
    if (!empty($items)) {
        foreach ($items as $item) {
            echo "  * " . $item['title'] . " - ¥" . $item['actual_price'] . "\n";
        }
    }
    
    // 构造返回数据
    $responseData = [
        'code' => 1,
        'msg' => '获取成功',
        'data' => [
            'list' => $items,
            'total' => $result->total(),
            'page' => $result->currentPage(),
            'page_size' => $result->listRows(),
            'has_more' => $result->hasPages()
        ]
    ];
    
    echo "\n最终API响应:\n";
    echo json_encode($responseData, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . "\n";
    
} catch (\Exception $e) {
    echo "错误: " . $e->getMessage() . "\n";
    echo "文件: " . $e->getFile() . "\n";
    echo "行号: " . $e->getLine() . "\n";
    echo "堆栈跟踪:\n" . $e->getTraceAsString() . "\n";
}
