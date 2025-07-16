<?php
/**
 * 商品API接口
 */

// 引入CORS配置
require_once __DIR__ . '/cors.php';

// 引入配置文件
require_once dirname(__DIR__) . '/vendor/autoload.php';

use Utils\Logger;
use Utils\Helper;
use Services\DataokeService;

$logger = new Logger();
$startTime = microtime(true);

try {
    $method = $_SERVER['REQUEST_METHOD'];
    $uri = $_SERVER['REQUEST_URI'];
    $path = parse_url($uri, PHP_URL_PATH);
    
    // 移除基础路径
    $basePath = dirname($_SERVER['SCRIPT_NAME']);
    if ($basePath !== '/') {
        $path = str_replace($basePath, '', $path);
    }
    
    // 路由分发
    if (strpos($path, '/api/goods/detail') === 0 || strpos($path, '/goods/detail') === 0) {
        handleGoodsDetail();
    } elseif (strpos($path, '/api/goods') === 0 || strpos($path, '/goods.php') === 0) {
        handleGoodsList();
    } else {
        apiResponse(null, '接口不存在', 404);
    }
    
} catch (Exception $e) {
    $logger->error('商品API错误', [
        'error' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ]);
    
    apiResponse(null, '系统内部错误: ' . $e->getMessage(), 500);
}

/**
 * 处理商品列表请求
 */
function handleGoodsList()
{
    global $logger, $startTime;
    
    // 只允许GET请求
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        apiResponse(null, '只支持GET请求', 405);
    }
    
    try {
        // 获取请求参数
        $params = [
            'pageId' => Helper::getParam('page', 1, 'int'),
            'pageSize' => Helper::getParam('pageSize', 20, 'int'),
            'cids' => Helper::getParam('cid', '', 'string'),
            'subcid' => Helper::getParam('subcid', '', 'string'),
            'sort' => Helper::getParam('sort', 'total_sales', 'string'),
            'hasCoupon' => Helper::getParam('hasCoupon', 1, 'int'),
            'freeShipment' => Helper::getParam('freeShipment', 0, 'int'),
            'brand' => Helper::getParam('brand', 0, 'int'),
            'brandIds' => Helper::getParam('brandIds', '', 'string'),
            'priceLowerLimit' => Helper::getParam('priceLowerLimit', '', 'string'),
            'priceUpperLimit' => Helper::getParam('priceUpperLimit', '', 'string'),
            'commissionRateLowerLimit' => Helper::getParam('commissionRateLowerLimit', '', 'string'),
            'commissionRateUpperLimit' => Helper::getParam('commissionRateUpperLimit', '', 'string'),
            'monthSalesLowerLimit' => Helper::getParam('monthSalesLowerLimit', '', 'string'),
            'monthSalesUpperLimit' => Helper::getParam('monthSalesUpperLimit', '', 'string'),
            'goodsIds' => Helper::getParam('goodsIds', '', 'string'),
        ];
        
        // 获取平台参数
        $platform = Helper::getParam('platform', 'taobao', 'string');
        
        // 验证平台是否启用
        if (!Helper::isPlatformEnabled($platform)) {
            apiResponse(null, "平台 {$platform} 未启用或不存在", 400);
        }
        
        // 验证分页参数
        if ($params['pageId'] < 1) {
            $params['pageId'] = 1;
        }
        
        if ($params['pageSize'] < 1 || $params['pageSize'] > DTK_CONFIG['api']['max_page_size']) {
            $params['pageSize'] = DTK_CONFIG['api']['default_page_size'];
        }
        
        // 创建大淘客服务实例
        $dataokeService = new DataokeService($platform);
        
        // 获取商品列表
        $result = $dataokeService->getGoodsList($params);
        
        // 记录API请求日志
        $duration = Helper::calculateDuration($startTime);
        $logger->logApiRequest('GET', '/api/goods', $params, ['code' => 0], $duration);
        
        // 返回结果
        apiResponse($result, '获取商品列表成功', 200);
        
    } catch (Exception $e) {
        $logger->error('获取商品列表失败', [
            'params' => $params ?? [],
            'platform' => $platform ?? 'unknown',
            'error' => $e->getMessage()
        ]);
        
        apiResponse(null, '获取商品列表失败: ' . $e->getMessage(), 500);
    }
}

/**
 * 处理商品详情请求
 */
function handleGoodsDetail()
{
    global $logger, $startTime;
    
    // 只允许GET请求
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        apiResponse(null, '只支持GET请求', 405);
    }
    
    try {
        // 获取商品ID
        $goodsId = Helper::getParam('id', '', 'string');
        if (empty($goodsId)) {
            $goodsId = Helper::getParam('goodsId', '', 'string');
        }
        
        if (empty($goodsId)) {
            apiResponse(null, '缺少商品ID参数', 400);
        }
        
        // 获取平台参数
        $platform = Helper::getParam('platform', 'taobao', 'string');
        
        // 验证平台是否启用
        if (!Helper::isPlatformEnabled($platform)) {
            apiResponse(null, "平台 {$platform} 未启用或不存在", 400);
        }
        
        // 创建大淘客服务实例
        $dataokeService = new DataokeService($platform);
        
        // 获取商品详情
        $result = $dataokeService->getGoodsDetail($goodsId);
        
        // 记录API请求日志
        $duration = Helper::calculateDuration($startTime);
        $logger->logApiRequest('GET', '/api/goods/detail', ['goodsId' => $goodsId, 'platform' => $platform], ['code' => 0], $duration);
        
        // 返回结果
        apiResponse($result, '获取商品详情成功', 200);
        
    } catch (Exception $e) {
        $logger->error('获取商品详情失败', [
            'goodsId' => $goodsId ?? 'unknown',
            'platform' => $platform ?? 'unknown',
            'error' => $e->getMessage()
        ]);
        
        apiResponse(null, '获取商品详情失败: ' . $e->getMessage(), 500);
    }
}
?>
