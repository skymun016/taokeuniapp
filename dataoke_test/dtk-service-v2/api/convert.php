<?php
/**
 * 转链API接口
 */

// 引入配置文件
require_once dirname(__DIR__) . '/vendor/autoload.php';

use Utils\Logger;
use Utils\Helper;
use Services\DataokeService;
use Services\LinkConvertService;

/**
 * API 响应函数
 */
function apiResponse($data = null, $message = 'success', $code = 200) {
    header('Content-Type: application/json; charset=utf-8');
    
    $response = [
        'code' => $code,
        'message' => $message,
        'data' => $data,
        'timestamp' => time()
    ];
    
    http_response_code($code);
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    exit;
}

$logger = new Logger();

try {
    // 获取请求路径和方法
    $path = $_SERVER['REQUEST_URI'];
    $method = $_SERVER['REQUEST_METHOD'];
    
    $logger->info('转链API请求', [
        'path' => $path,
        'method' => $method,
        'params' => $_REQUEST
    ]);
    
    // 路由分发
    $type = $_REQUEST['type'] ?? '';

    if ((strpos($path, '/convert.php') !== false && $type === 'privilege') ||
        strpos($path, '/api/convert/privilege') === 0 || strpos($path, '/convert/privilege') === 0) {
        handlePrivilegeLink();
    } elseif ((strpos($path, '/convert.php') !== false && $type === 'taokouling') ||
              strpos($path, '/api/convert/taokouling') === 0 || strpos($path, '/convert/taokouling') === 0) {
        handleTaokouling();
    } elseif ((strpos($path, '/convert.php') !== false && $type === 'smart') ||
              strpos($path, '/api/convert/smart') === 0 || strpos($path, '/convert/smart') === 0) {
        handleSmartConvert();
    } elseif ((strpos($path, '/convert.php') !== false && $type === 'batch') ||
              strpos($path, '/api/convert/batch') === 0 || strpos($path, '/convert/batch') === 0) {
        handleBatchConvert();
    } elseif ((strpos($path, '/convert.php') !== false && $type === 'materials') ||
              strpos($path, '/api/convert/materials') === 0 || strpos($path, '/convert/materials') === 0) {
        handleGoodsMaterials();
    } elseif ((strpos($path, '/convert.php') !== false && $type === 'enhanced') ||
              strpos($path, '/api/convert/enhanced') === 0 || strpos($path, '/convert/enhanced') === 0) {
        handleEnhancedConvert();
    } elseif (strpos($path, '/api/convert') === 0 || strpos($path, '/convert.php') !== false) {
        handleConvertStatus();
    } else {
        apiResponse(null, '接口不存在', 404);
    }
    
} catch (\Exception $e) {
    $logger->error('转链API异常', [
        'error' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ]);
    
    apiResponse(null, '系统内部错误: ' . $e->getMessage(), 500);
}

/**
 * 处理高效转链
 */
function handlePrivilegeLink()
{
    global $logger;
    
    if ($_SERVER['REQUEST_METHOD'] !== 'GET' && $_SERVER['REQUEST_METHOD'] !== 'POST') {
        apiResponse(null, '只支持GET和POST请求', 405);
        return;
    }
    
    try {
        // 获取参数
        $platform = $_REQUEST['platform'] ?? 'taobao';
        $goodsId = $_REQUEST['goodsId'] ?? $_REQUEST['goods_id'] ?? '';
        $pid = $_REQUEST['pid'] ?? '';
        $couponId = $_REQUEST['couponId'] ?? $_REQUEST['coupon_id'] ?? '';
        $channelId = $_REQUEST['channelId'] ?? $_REQUEST['channel_id'] ?? '';
        
        // 验证必需参数
        if (empty($goodsId)) {
            apiResponse(null, '商品ID不能为空', 400);
            return;
        }
        
        // 验证平台
        $enabledPlatforms = Helper::getEnabledPlatforms();
        if (!in_array($platform, $enabledPlatforms)) {
            apiResponse(null, "平台 {$platform} 未启用或不存在", 400);
            return;
        }
        
        $logger->info('开始高效转链', [
            'platform' => $platform,
            'goodsId' => $goodsId,
            'pid' => $pid,
            'couponId' => $couponId
        ]);
        
        // 执行转链
        $linkConvertService = new LinkConvertService($platform);

        $params = [
            'goodsId' => $goodsId
        ];

        // 添加可选参数
        if (!empty($pid)) {
            $params['pid'] = $pid;
        }
        if (!empty($couponId)) {
            $params['couponId'] = $couponId;
        }
        if (!empty($channelId)) {
            $params['channelId'] = $channelId;
        }

        $result = $linkConvertService->convertToPrivilegeLink($params);
        $logger->info('大淘客API返回', ['goodsId' => $goodsId, 'result' => $result]);
        
        // 优先使用 shortUrl 作为 privilege_link
        $finalPrivilegeLink = !empty($result['shortUrl']) ? $result['shortUrl'] : ($result['privilege_link'] ?? $result['itemUrl'] ?? '');
        
        // 格式化返回数据
        $responseData = [
            'goodsId' => $goodsId,
            'platform' => $platform,
            'couponInfo' => $result['couponInfo'] ?? '',
            'couponStartTime' => $result['couponStartTime'] ?? '',
            'couponEndTime' => $result['couponEndTime'] ?? '',
            'couponTotalCount' => $result['couponTotalCount'] ?? 0,
            'couponRemainCount' => $result['couponRemainCount'] ?? 0,
            'maxCommissionRate' => $result['maxCommissionRate'] ?? '',
            'minCommissionRate' => $result['minCommissionRate'] ?? '',
            'itemUrl' => $result['itemUrl'] ?? '',
            'couponClickUrl' => $result['couponClickUrl'] ?? '',
            'shortUrl' => $result['shortUrl'] ?? '',
            'privilege_link' => $finalPrivilegeLink,
            'tpwd' => $result['tpwd'] ?? '',
            'hasCoupon' => $result['hasCoupon'] ?? false,
            'estimatedCommission' => $result['estimatedCommission'] ?? null,
            'convertTime' => $result['convertTime'] ?? date('Y-m-d H:i:s')
        ];
        
        // 在每个 handleXXX 方法中，API调用后加如下逻辑：
        if (isset($result['goodsId']) && (!empty($result['shortUrl']) || !empty($result['privilege_link']) || !empty($result['privilegeLink']))) {
            $shortUrl = $result['shortUrl'] ?? '';
            $finalPrivilegeLink = $shortUrl ?: ($result['privilege_link'] ?? $result['privilegeLink'] ?? $result['itemUrl'] ?? '');
            $tpwd = $result['tpwd'] ?? '';
            try {
                $db = \Services\DatabaseService::getInstance()->getConnection();
                $updateSql = "UPDATE dtk_goods SET privilege_link = ?, tpwd = ?, link_status = 1, last_convert_time = NOW(), convert_count = convert_count + 1, link_expire_time = DATE_ADD(NOW(), INTERVAL 7 DAY) WHERE goods_id = ?";
                $updateStmt = $db->prepare($updateSql);
                $updateStmt->execute([$finalPrivilegeLink, $tpwd, $result['goodsId']]);
            } catch (Exception $e) {
                $logger->error('转链数据库更新失败', ['goods_id' => $result['goodsId'], 'error' => $e->getMessage()]);
            }
            // 保证响应结构
            $result['shortUrl'] = $shortUrl; // 只用大淘客API的最新值
            $result['privilege_link'] = $finalPrivilegeLink;
            $result['privilegeLink'] = $finalPrivilegeLink;
            $result['tpwd'] = $tpwd;
            $logger->info('接口最终响应', ['goods_id' => $result['goodsId'], 'result' => $result]);
        }

        apiResponse($responseData, '转链成功', 200);
        
    } catch (\Exception $e) {
        $logger->error('高效转链失败', [
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ]);
        
        apiResponse(null, '转链失败: ' . $e->getMessage(), 500);
    }
}

/**
 * 处理淘口令生成
 */
function handleTaokouling()
{
    global $logger;
    
    if ($_SERVER['REQUEST_METHOD'] !== 'GET' && $_SERVER['REQUEST_METHOD'] !== 'POST') {
        apiResponse(null, '只支持GET和POST请求', 405);
        return;
    }
    
    try {
        // 获取参数
        $platform = $_REQUEST['platform'] ?? 'taobao';
        $goodsId = $_REQUEST['goodsId'] ?? $_REQUEST['goods_id'] ?? '';
        $text = $_REQUEST['text'] ?? '';
        $url = $_REQUEST['url'] ?? '';
        $logo = $_REQUEST['logo'] ?? '';
        
        // 验证必需参数
        if (empty($goodsId) && empty($url)) {
            apiResponse(null, '商品ID或链接不能为空', 400);
            return;
        }
        
        // 验证平台
        $enabledPlatforms = Helper::getEnabledPlatforms();
        if (!in_array($platform, $enabledPlatforms)) {
            apiResponse(null, "平台 {$platform} 未启用或不存在", 400);
            return;
        }
        
        $logger->info('开始生成淘口令', [
            'platform' => $platform,
            'goodsId' => $goodsId,
            'text' => $text,
            'url' => $url
        ]);
        
        // 执行淘口令生成
        $linkConvertService = new LinkConvertService($platform);

        $params = [];

        // 优先使用商品ID生成淘口令
        if (!empty($goodsId)) {
            $params['goodsId'] = $goodsId;
        }

        if (!empty($text)) {
            $params['text'] = $text;
        }
        if (!empty($url)) {
            $params['url'] = $url;
        }
        if (!empty($logo)) {
            $params['logo'] = $logo;
        }

        $result = $linkConvertService->createTaokouling($params);
        
        if ($result && is_array($result)) {
            $responseData = [
                'goodsId' => $goodsId,
                'platform' => $platform,
                'tpwd' => $result['tpwd'] ?? '',
                'longTpwd' => $result['longTpwd'] ?? '',
                'text' => $text,
                'url' => $result['url'] ?? $url,
                'source' => $result['source'] ?? 'api',
                'note' => $result['note'] ?? '',
                'createTime' => $result['createTime'] ?? date('Y-m-d H:i:s')
            ];

            $message = !empty($result['tpwd']) ? '淘口令生成成功' : '淘口令生成完成（基于转链结果）';
            apiResponse($responseData, $message, 200);
        } else {
            throw new \Exception('淘口令生成失败：返回数据异常');
        }
        
    } catch (\Exception $e) {
        $logger->error('淘口令生成失败', [
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ]);
        
        apiResponse(null, '淘口令生成失败: ' . $e->getMessage(), 500);
    }
}

/**
 * 处理智能转链
 */
function handleSmartConvert()
{
    global $logger;

    if ($_SERVER['REQUEST_METHOD'] !== 'GET' && $_SERVER['REQUEST_METHOD'] !== 'POST') {
        apiResponse(null, '只支持GET和POST请求', 405);
        return;
    }

    try {
        // 获取参数
        $platform = $_REQUEST['platform'] ?? 'taobao';
        $goodsId = $_REQUEST['goodsId'] ?? $_REQUEST['goods_id'] ?? '';
        $url = $_REQUEST['url'] ?? '';
        $needTaokouling = $_REQUEST['needTaokouling'] ?? $_REQUEST['need_taokouling'] ?? false;
        $taokoulingText = $_REQUEST['taokoulingText'] ?? $_REQUEST['taokouling_text'] ?? '';

        // 验证参数
        if (empty($goodsId) && empty($url)) {
            apiResponse(null, '商品ID或链接不能为空', 400);
            return;
        }

        // 验证平台
        $enabledPlatforms = Helper::getEnabledPlatforms();
        if (!in_array($platform, $enabledPlatforms)) {
            apiResponse(null, "平台 {$platform} 未启用或不存在", 400);
            return;
        }

        $logger->info('开始智能转链', [
            'platform' => $platform,
            'goodsId' => $goodsId,
            'url' => $url,
            'needTaokouling' => $needTaokouling
        ]);

        // 执行智能转链
        $linkConvertService = new LinkConvertService($platform);

        $params = [
            'goodsId' => $goodsId,
            'url' => $url,
            'needTaokouling' => $needTaokouling,
            'taokoulingText' => $taokoulingText
        ];

        $result = $linkConvertService->smartConvert($params);

        apiResponse($result, '智能转链成功', 200);

    } catch (\Exception $e) {
        $logger->error('智能转链失败', [
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ]);

        apiResponse(null, '智能转链失败: ' . $e->getMessage(), 500);
    }
}

/**
 * 处理批量转链
 */
function handleBatchConvert()
{
    global $logger;

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        apiResponse(null, '只支持POST请求', 405);
        return;
    }

    try {
        // 获取参数
        $platform = $_REQUEST['platform'] ?? 'taobao';
        $goodsIds = $_REQUEST['goodsIds'] ?? $_REQUEST['goods_ids'] ?? [];
        $needTaokouling = $_REQUEST['needTaokouling'] ?? $_REQUEST['need_taokouling'] ?? false;

        // 如果是字符串，尝试解析为数组
        if (is_string($goodsIds)) {
            $goodsIds = explode(',', $goodsIds);
            $goodsIds = array_map('trim', $goodsIds);
            $goodsIds = array_filter($goodsIds);
        }

        // 验证参数
        if (empty($goodsIds) || !is_array($goodsIds)) {
            apiResponse(null, '商品ID列表不能为空', 400);
            return;
        }

        if (count($goodsIds) > 50) {
            apiResponse(null, '单次批量转链最多支持50个商品', 400);
            return;
        }

        // 验证平台
        $enabledPlatforms = Helper::getEnabledPlatforms();
        if (!in_array($platform, $enabledPlatforms)) {
            apiResponse(null, "平台 {$platform} 未启用或不存在", 400);
            return;
        }

        $logger->info('开始批量转链', [
            'platform' => $platform,
            'count' => count($goodsIds),
            'needTaokouling' => $needTaokouling
        ]);

        // 执行批量转链
        $linkConvertService = new LinkConvertService($platform);

        $options = [
            'needTaokouling' => $needTaokouling
        ];

        $result = $linkConvertService->batchConvert($goodsIds, $options);

        apiResponse($result, '批量转链完成', 200);

    } catch (\Exception $e) {
        $logger->error('批量转链失败', [
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ]);

        apiResponse(null, '批量转链失败: ' . $e->getMessage(), 500);
    }
}

/**
 * 处理商品素材获取
 */
function handleGoodsMaterials()
{
    global $logger;

    if ($_SERVER['REQUEST_METHOD'] !== 'GET' && $_SERVER['REQUEST_METHOD'] !== 'POST') {
        apiResponse(null, '只支持GET和POST请求', 405);
        return;
    }

    try {
        // 获取参数
        $platform = $_REQUEST['platform'] ?? 'taobao';
        $goodsId = $_REQUEST['goodsId'] ?? $_REQUEST['goods_id'] ?? '';

        // 验证参数
        if (empty($goodsId)) {
            apiResponse(null, '商品ID不能为空', 400);
            return;
        }

        // 验证平台
        $enabledPlatforms = Helper::getEnabledPlatforms();
        if (!in_array($platform, $enabledPlatforms)) {
            apiResponse(null, "平台 {$platform} 未启用或不存在", 400);
            return;
        }

        $logger->info('开始获取商品素材', [
            'platform' => $platform,
            'goodsId' => $goodsId
        ]);

        // 获取商品素材
        $linkConvertService = new LinkConvertService($platform);

        $params = [
            'goodsId' => $goodsId
        ];

        $result = $linkConvertService->getGoodsMaterials($params);

        apiResponse($result, '获取商品素材成功', 200);

    } catch (\Exception $e) {
        $logger->error('获取商品素材失败', [
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ]);

        apiResponse(null, '获取商品素材失败: ' . $e->getMessage(), 500);
    }
}

/**
 * 处理增强转链
 */
function handleEnhancedConvert()
{
    global $logger;

    if ($_SERVER['REQUEST_METHOD'] !== 'GET' && $_SERVER['REQUEST_METHOD'] !== 'POST') {
        apiResponse(null, '只支持GET和POST请求', 405);
        return;
    }

    try {
        // 获取参数
        $platform = $_REQUEST['platform'] ?? 'taobao';
        $goodsId = $_REQUEST['goodsId'] ?? $_REQUEST['goods_id'] ?? '';
        $includeMaterials = $_REQUEST['includeMaterials'] ?? $_REQUEST['include_materials'] ?? false;
        $needTaokouling = $_REQUEST['needTaokouling'] ?? $_REQUEST['need_taokouling'] ?? false;
        $taokoulingText = $_REQUEST['taokoulingText'] ?? $_REQUEST['taokouling_text'] ?? '';

        // 验证参数
        if (empty($goodsId)) {
            apiResponse(null, '商品ID不能为空', 400);
            return;
        }

        // 验证平台
        $enabledPlatforms = Helper::getEnabledPlatforms();
        if (!in_array($platform, $enabledPlatforms)) {
            apiResponse(null, "平台 {$platform} 未启用或不存在", 400);
            return;
        }

        $logger->info('开始增强转链', [
            'platform' => $platform,
            'goodsId' => $goodsId,
            'includeMaterials' => $includeMaterials,
            'needTaokouling' => $needTaokouling
        ]);

        // 执行增强转链
        $linkConvertService = new LinkConvertService($platform);

        $params = [
            'goodsId' => $goodsId,
            'includeMaterials' => $includeMaterials,
            'needTaokouling' => $needTaokouling,
            'taokoulingText' => $taokoulingText
        ];

        $result = $linkConvertService->enhancedConvert($params);

        apiResponse($result, '增强转链成功', 200);

    } catch (\Exception $e) {
        $logger->error('增强转链失败', [
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ]);

        apiResponse(null, '增强转链失败: ' . $e->getMessage(), 500);
    }
}

/**
 * 处理转链状态查询
 */
function handleConvertStatus()
{
    global $logger;

    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        apiResponse(null, '只支持GET请求', 405);
        return;
    }

    try {
        $platform = $_REQUEST['platform'] ?? 'taobao';

        // 获取转链统计信息
        $linkConvertService = new LinkConvertService($platform);
        $stats = $linkConvertService->getConvertStats();

        $responseData = [
            'platform' => $platform,
            'supportedFeatures' => $stats['supportedFeatures'],
            'apiEndpoints' => [
                'privilegeLink' => '/api/convert.php?type=privilege',
                'taokouling' => '/api/convert.php?type=taokouling',
                'smartConvert' => '/api/convert.php?type=smart',
                'batchConvert' => '/api/convert.php?type=batch',
                'goodsMaterials' => '/api/convert.php?type=materials',
                'enhancedConvert' => '/api/convert.php?type=enhanced'
            ],
            'timestamp' => date('Y-m-d H:i:s')
        ];

        apiResponse($responseData, '获取转链状态成功', 200);

    } catch (\Exception $e) {
        $logger->error('获取转链状态失败', [
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ]);

        apiResponse(null, '获取转链状态失败: ' . $e->getMessage(), 500);
    }
}
?>
