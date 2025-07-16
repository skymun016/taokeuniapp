<?php

namespace Services;

use Utils\Logger;
use Utils\Helper;

/**
 * 大淘客服务类
 */
class DataokeService
{
    private $logger;
    private $cache;
    private $config;
    private $platform;
    
    public function __construct($platform = 'taobao')
    {
        $this->platform = $platform;
        $this->logger = new Logger();
        $this->cache = CacheService::getInstance();
        $this->config = Helper::getPlatformConfig($platform);
    }
    
    /**
     * 获取商品列表
     */
    public function getGoodsList($params = [])
    {
        try {
            // 设置默认参数
            $defaultParams = [
                'pageId' => 1,
                'pageSize' => 20,
                'cids' => '',
                'subcid' => '',
                'sort' => 'total_sales',
                'hasCoupon' => 1,
                'freeShipment' => 0,
                'brand' => 0,
                'brandIds' => '',
                'priceLowerLimit' => '',
                'priceUpperLimit' => '',
                'commissionRateLowerLimit' => '',
                'commissionRateUpperLimit' => '',
                'monthSalesLowerLimit' => '',
                'monthSalesUpperLimit' => '',
                'goodsIds' => ''
            ];
            
            $params = array_merge($defaultParams, $params);
            
            // 生成缓存键
            $cacheKey = Helper::generateCacheKey('goods_list', $params);
            $cacheExpire = DTK_CONFIG['cache']['expire']['goods_list'];
            
            // 尝试从缓存获取
            $cachedData = $this->cache->get($cacheKey);
            if ($cachedData !== null) {
                $this->logger->info('商品列表缓存命中', ['cache_key' => $cacheKey]);
                return $cachedData;
            }
            
            // 调用大淘客API
            $result = $this->callApi('GetGoodsList', $params);
            
            // 调试：记录实际返回的数据结构
            $this->logger->info('API返回数据结构', [
                'result_type' => gettype($result),
                'result_keys' => is_array($result) ? array_keys($result) : 'not_array',
                'data_keys' => isset($result['data']) && is_array($result['data']) ? array_keys($result['data']) : 'no_data_key'
            ]);

            if ($result && isset($result['data'])) {
                // 数据转换
                $convertedData = $this->convertGoodsData($result['data']);

                // 缓存结果
                $this->cache->set($cacheKey, $convertedData, $cacheExpire);

                $this->logger->info('商品列表获取成功', [
                    'count' => count($convertedData['list'] ?? []),
                    'total' => $convertedData['total'] ?? 0
                ]);

                return $convertedData;
            }

            throw new \Exception('API返回数据格式错误: ' . json_encode($result));
            
        } catch (\Exception $e) {
            $this->logger->error('获取商品列表失败', [
                'params' => $params,
                'error' => $e->getMessage()
            ]);
            
            throw $e;
        }
    }
    
    /**
     * 获取商品详情
     */
    public function getGoodsDetail($goodsId)
    {
        try {
            // 生成缓存键
            $cacheKey = Helper::generateCacheKey('goods_detail', ['goods_id' => $goodsId]);
            $cacheExpire = DTK_CONFIG['cache']['expire']['goods_detail'];
            
            // 尝试从缓存获取
            $cachedData = $this->cache->get($cacheKey);
            if ($cachedData !== null) {
                $this->logger->info('商品详情缓存命中', ['goods_id' => $goodsId]);
                return $cachedData;
            }
            
            // 调用大淘客API
            $params = ['goodsId' => $goodsId];
            $result = $this->callApi('GetGoodsDetails', $params);
            
            if ($result && isset($result['data'])) {
                // 数据转换
                $convertedData = $this->convertGoodsDetailData($result['data']);

                // 缓存结果
                $this->cache->set($cacheKey, $convertedData, $cacheExpire);

                $this->logger->info('商品详情获取成功', ['goods_id' => $goodsId]);

                return $convertedData;
            }
            
            throw new \Exception('API返回数据格式错误');
            
        } catch (\Exception $e) {
            $this->logger->error('获取商品详情失败', [
                'goods_id' => $goodsId,
                'error' => $e->getMessage()
            ]);
            
            throw $e;
        }
    }
    
    /**
     * 获取分类列表
     */
    public function getCategoryList()
    {
        try {
            // 生成缓存键
            $cacheKey = Helper::generateCacheKey('category_list');
            $cacheExpire = DTK_CONFIG['cache']['expire']['category'];
            
            // 尝试从缓存获取
            $cachedData = $this->cache->get($cacheKey);
            if ($cachedData !== null) {
                $this->logger->info('分类列表缓存命中');
                return $cachedData;
            }
            
            // 调用大淘客API
            $result = $this->callApi('GetSuperCategory');
            
            if ($result && isset($result['data'])) {
                // 数据转换
                $convertedData = $this->convertCategoryData($result['data']);

                // 缓存结果
                $this->cache->set($cacheKey, $convertedData, $cacheExpire);

                $this->logger->info('分类列表获取成功', ['count' => count($convertedData)]);

                return $convertedData;
            }
            
            throw new \Exception('API返回数据格式错误');
            
        } catch (\Exception $e) {
            $this->logger->error('获取分类列表失败', [
                'error' => $e->getMessage()
            ]);
            
            throw $e;
        }
    }
    
    /**
     * 调用大淘客API（公共方法）
     */
    public function callApi($apiName, $params = [])
    {
        try {
            // 检查SDK是否可用
            if (!class_exists('DtkClient')) {
                throw new \Exception('大淘客SDK未正确加载');
            }
            
            // 根据API名称调用对应的方法
            switch ($apiName) {
                case 'GetGoodsList':
                    if (!class_exists('GetGoodsList')) {
                        throw new \Exception('GetGoodsList类未找到');
                    }
                    $api = new \GetGoodsList();
                    break;
                    
                case 'GetGoodsDetails':
                    if (!class_exists('GetGoodsDetails')) {
                        throw new \Exception('GetGoodsDetails类未找到');
                    }
                    $api = new \GetGoodsDetails();
                    break;

                case 'GetSuperCategory':
                    if (!class_exists('GetSuperCategory')) {
                        throw new \Exception('GetSuperCategory类未找到');
                    }
                    $api = new \GetSuperCategory();
                    break;

                case 'GetPullGoodsByTime':
                    if (!class_exists('GetPullGoodsByTime')) {
                        throw new \Exception('GetPullGoodsByTime类未找到');
                    }
                    $api = new \GetPullGoodsByTime();
                    break;

                case 'GetPrivilegeLink':
                    if (!class_exists('GetPrivilegeLink')) {
                        throw new \Exception('GetPrivilegeLink类未找到');
                    }
                    $api = new \GetPrivilegeLink();
                    break;

                case 'GetCreateTkl':
                    if (!class_exists('GetCreateTkl')) {
                        throw new \Exception('GetCreateTkl类未找到');
                    }
                    $api = new \GetCreateTkl();
                    break;

                case 'GetGoodsDetailV2':
                    if (!class_exists('GetGoodsDetailV2')) {
                        throw new \Exception('GetGoodsDetailV2类未找到');
                    }
                    $api = new \GetGoodsDetailV2();
                    break;

                case 'GetDtkSearchGoods':
                    if (!class_exists('GetDtkSearchGoods')) {
                        throw new \Exception('GetDtkSearchGoods类未找到');
                    }
                    $api = new \GetDtkSearchGoods();
                    break;
                    
                default:
                    throw new \Exception("不支持的API: {$apiName}");
            }
            
            // 设置 appKey 和 appSecret
            $api->setAppKey($this->config['app_key']);
            $api->setAppSecret($this->config['app_secret']);

            // 设置版本号 - 不同API使用不同版本
            $version = $this->getApiVersion($apiName);
            $api->setVersion($version);

            // 设置API参数
            $api->setParams($params);

            // 执行API调用
            $startTime = microtime(true);
            $result = $api->request();
            $duration = Helper::calculateDuration($startTime);

            // 解析 JSON 返回结果
            if (is_string($result)) {
                // 记录原始响应用于调试
                $this->logger->info('API原始响应', [
                    'api' => $apiName,
                    'raw_response' => $result
                ]);

                $result = Helper::jsonDecode($result);
            }

            $this->logger->info('大淘客API调用', [
                'api' => $apiName,
                'params' => $params,
                'duration' => $duration . 'ms',
                'success' => !empty($result)
            ]);

            return $result;
            
        } catch (\Exception $e) {
            $this->logger->error('大淘客API调用失败', [
                'api' => $apiName,
                'params' => $params,
                'error' => $e->getMessage()
            ]);
            
            throw $e;
        }
    }
    
    /**
     * 转换商品数据格式
     */
    private function convertGoodsData($data)
    {
        if (!isset($data['list'])) {
            return ['list' => [], 'total' => 0];
        }

        $mapping = DATA_MAPPING['goods'];
        $convertedList = [];

        foreach ($data['list'] as $item) {
            $converted = Helper::convertData($item, $mapping);
            // 添加平台信息
            $converted['platform'] = $this->platform;
            $convertedList[] = $converted;
        }

        return [
            'list' => $convertedList,
            'total' => $data['totalNum'] ?? count($convertedList),
            'page' => $data['pageId'] ?? 1,
            'pageSize' => count($convertedList)
        ];
    }
    
    /**
     * 转换商品详情数据格式
     */
    private function convertGoodsDetailData($data)
    {
        $mapping = DATA_MAPPING['goods'];
        return Helper::convertData($data, $mapping);
    }
    
    /**
     * 转换分类数据格式
     */
    private function convertCategoryData($data)
    {
        if (!is_array($data)) {
            return [];
        }

        $mapping = DATA_MAPPING['category'];
        return Helper::convertDataList($data, $mapping);
    }

    /**
     * 高效转链 - 将商品链接转换为推广链接
     */
    public function getPrivilegeLink($params = [])
    {
        // 必需参数验证
        if (empty($params['goodsId'])) {
            throw new \Exception('商品ID不能为空');
        }

        // 验证商品ID格式（支持数字ID和新格式字符串ID）
        if (empty(trim($params['goodsId']))) {
            throw new \Exception('商品ID格式错误，不能为空');
        }

        // 设置默认参数
        $defaultParams = [
            'pid' => $this->config['pid'] ?? '',
            'couponId' => '',
            'channelId' => '',
            'rebateType' => 0,
            'specialId' => '',
            'externalId' => '',
            'xid' => '',
            'leftSymbol' => '￥',
            'rightSymbol' => '￥'
        ];

        $params = array_merge($defaultParams, $params);

        // 移除空值参数，但保留数字0
        $params = array_filter($params, function($value) {
            return $value !== '' && $value !== null;
        });

        // 生成缓存键
        $cacheKey = Helper::generateCacheKey('privilege_link', $params);
        $cacheExpire = DTK_CONFIG['cache']['expire']['privilege_link'] ?? 300; // 5分钟缓存

        // 尝试从缓存获取
        $cachedData = $this->cache->get($cacheKey);
        if ($cachedData !== null) {
            $this->logger->info('转链缓存命中', ['goods_id' => $params['goodsId']]);
            return $cachedData;
        }

        try {
            $startTime = microtime(true);
            $result = $this->callApi('GetPrivilegeLink', $params);
            $duration = Helper::calculateDuration($startTime);

            if ($result && isset($result['data'])) {
                // 验证返回数据的完整性
                $data = $result['data'];
                if (empty($data['itemUrl']) && empty($data['couponClickUrl'])) {
                    throw new \Exception('转链返回的链接为空，可能商品不存在或已下架');
                }

                // 缓存成功的转链结果
                $this->cache->set($cacheKey, $data, $cacheExpire);

                $this->logger->info('高效转链成功', [
                    'goods_id' => $params['goodsId'],
                    'duration' => $duration,
                    'has_coupon' => !empty($data['couponInfo']),
                    'commission_rate' => $data['maxCommissionRate'] ?? 'unknown'
                ]);

                return $data;
            }

            // 处理API返回的错误信息
            $errorMsg = $result['msg'] ?? '未知错误';
            $errorCode = $result['code'] ?? -1;

            throw new \Exception("转链失败 (错误码: {$errorCode}): {$errorMsg}");

        } catch (\Exception $e) {
            $this->logger->error('高效转链失败', [
                'params' => $params,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            throw $e;
        }
    }

    /**
     * 生成淘口令 - 基于转链结果
     */
    public function createTaokouling($params = [])
    {
        // 参数验证
        if (empty($params['text']) && empty($params['url']) && empty($params['goodsId'])) {
            throw new \Exception('淘口令文案、链接或商品ID至少需要提供一项');
        }

        try {
            $startTime = microtime(true);

            // 如果有商品ID但没有URL，先通过高效转链获取推广链接和淘口令
            if (!empty($params['goodsId']) && empty($params['url'])) {
                $privilegeResult = $this->getPrivilegeLink(['goodsId' => $params['goodsId']]);

                $duration = Helper::calculateDuration($startTime);

                // 记录调试信息
                $this->logger->info('转链结果详情', [
                    'goodsId' => $params['goodsId'],
                    'has_tpwd' => !empty($privilegeResult['tpwd']),
                    'tpwd' => $privilegeResult['tpwd'] ?? 'empty',
                    'has_longTpwd' => !empty($privilegeResult['longTpwd']),
                    'longTpwd' => $privilegeResult['longTpwd'] ?? 'empty'
                ]);

                // 直接使用高效转链返回的淘口令（即使为空也返回）
                return [
                    'tpwd' => $privilegeResult['tpwd'] ?? '',
                    'longTpwd' => $privilegeResult['longTpwd'] ?? '',
                    'text' => $params['text'] ?? '商品推荐',
                    'url' => $privilegeResult['itemUrl'] ?? $privilegeResult['couponClickUrl'] ?? '',
                    'source' => 'privilege_link',
                    'createTime' => date('Y-m-d H:i:s')
                ];
            }

            // 如果只有URL和文案，尝试调用原始淘口令API（虽然可能失败）
            if (!empty($params['url']) && !empty($params['text'])) {
                // 设置默认参数
                $defaultParams = [
                    'text' => $params['text'],
                    'url' => $params['url'],
                    'logo' => '',
                    'userId' => '',
                    'leftSymbol' => '￥',
                    'rightSymbol' => '￥'
                ];

                // 移除空值参数
                $apiParams = array_filter($defaultParams, function($value) {
                    return $value !== '' && $value !== null;
                });

                // URL需要进行编码
                if (!empty($apiParams['url'])) {
                    $apiParams['url'] = urlencode($apiParams['url']);
                }

                try {
                    $result = $this->callApi('GetCreateTkl', $apiParams);

                    if ($result && isset($result['data']) && !empty($result['data']['tpwd'])) {
                        $duration = Helper::calculateDuration($startTime);

                        $this->logger->info('原始API淘口令生成成功', [
                            'text' => $params['text'],
                            'duration' => $duration,
                            'tpwd_length' => strlen($result['data']['tpwd'])
                        ]);

                        return $result['data'];
                    }
                } catch (\Exception $e) {
                    // 原始API失败，记录日志但不抛出异常
                    $this->logger->warning('原始淘口令API失败，将返回基础信息', [
                        'error' => $e->getMessage()
                    ]);
                }

                // 如果原始API失败，返回基础信息
                $duration = Helper::calculateDuration($startTime);

                return [
                    'tpwd' => '', // 空淘口令
                    'text' => $params['text'],
                    'url' => $params['url'],
                    'source' => 'fallback',
                    'note' => '淘口令API暂不可用，请使用推广链接'
                ];
            }

            throw new \Exception('参数不足，无法生成淘口令');

        } catch (\Exception $e) {
            $this->logger->error('淘口令生成失败', [
                'params' => $params,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            throw $e;
        }
    }

    /**
     * 获取API对应的版本号
     */
    private function getApiVersion($apiName)
    {
        // 不同API使用不同的版本号
        $apiVersions = [
            'GetGoodsList' => 'v1.2.4',
            'GetGoodsDetails' => 'v1.2.4',
            'GetSuperCategory' => 'v1.2.4',
            'GetPullGoodsByTime' => 'v1.2.4',
            'GetPrivilegeLink' => 'v1.0.0',  // 高效转链使用v1.0.0
            'GetCreateTkl' => 'v1.0.0',      // 淘口令生成使用v1.0.0
            'GetGoodsDetailV2' => 'v1.0.0',  // 平台商品素材使用v1.0.0
            'GetDtkSearchGoods' => 'v1.2.4',
        ];

        return $apiVersions[$apiName] ?? $this->config['version'];
    }

    /**
     * 直接调用平台商品素材API
     */
    private function callGoodsDetailV2Api($params)
    {
        // 构建请求参数 - 使用新验签方式
        $nonce = Helper::generateNonce(6); // 6位随机数
        $timer = round(microtime(true) * 1000); // 毫秒级时间戳

        $requestParams = [
            'appKey' => $this->config['app_key'],
            'version' => 'v1.0.0',
            'goodsId' => $params['goodsId'],
            'nonce' => $nonce,
            'timer' => $timer
        ];

        // 生成新验签方式的签名
        $requestParams['signRan'] = $this->generateNewSign($requestParams);

        // 构建请求URL
        $url = 'https://openapi.dataoke.com/open-api/goods/get_goods_detail_v2?' . http_build_query($requestParams);

        // 记录请求信息用于调试
        $this->logger->info('平台商品素材API请求', [
            'url' => $url,
            'params' => array_merge($requestParams, ['sign' => '***'])
        ]);

        // 发送请求
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_USERAGENT, 'DTK-Service/2.0');
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/x-www-form-urlencoded',
            'Accept: application/json'
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        // 记录响应信息用于调试
        $this->logger->info('平台商品素材API响应', [
            'http_code' => $httpCode,
            'response_length' => strlen($response),
            'response_preview' => substr($response, 0, 200)
        ]);

        if ($error) {
            throw new \Exception('CURL错误: ' . $error);
        }

        if ($httpCode !== 200) {
            throw new \Exception('HTTP错误: ' . $httpCode . ', 响应: ' . $response);
        }

        // 解析响应
        $result = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('JSON解析错误: ' . json_last_error_msg() . ', 原始响应: ' . $response);
        }

        return $result;
    }

    /**
     * 生成API签名
     */
    private function generateSign($params)
    {
        // 移除sign参数
        unset($params['sign']);

        // 按键名排序
        ksort($params);

        // 构建签名字符串
        $signStr = '';
        foreach ($params as $key => $value) {
            if ($value !== '' && $value !== null) {
                $signStr .= $key . $value;
            }
        }

        // 添加app_secret
        $signStr = $this->config['app_secret'] . $signStr . $this->config['app_secret'];

        // 生成MD5签名并转大写
        return strtoupper(md5($signStr));
    }

    /**
     * 生成新验签方式的签名
     */
    private function generateNewSign($params)
    {
        // 新验签方式：appKey=xxx&timer=xxx&nonce=xxx&key=xxx
        $signStr = 'appKey=' . $this->config['app_key'] .
                   '&timer=' . $params['timer'] .
                   '&nonce=' . $params['nonce'] .
                   '&key=' . $this->config['app_secret'];

        // 生成MD5签名并转大写
        return strtoupper(md5($signStr));
    }

    /**
     * 获取平台商品素材 - 获取商品详细信息和推广素材
     */
    public function getGoodsMaterials($params = [])
    {
        // 参数验证
        if (empty($params['goodsId'])) {
            throw new \Exception('商品ID不能为空');
        }

        // 验证商品ID格式
        if (empty(trim($params['goodsId']))) {
            throw new \Exception('商品ID格式错误，不能为空');
        }

        // 生成缓存键
        $cacheKey = Helper::generateCacheKey('goods_detail_v2', $params);
        $cacheExpire = DTK_CONFIG['cache']['expire']['goods_detail'] ?? 3600; // 1小时缓存

        // 尝试从缓存获取
        $cachedData = $this->cache->get($cacheKey);
        if ($cachedData !== null) {
            $this->logger->info('商品素材缓存命中', ['goods_id' => $params['goodsId']]);
            return $cachedData;
        }

        try {
            $startTime = microtime(true);
            // 直接调用平台商品素材API，因为SDK中没有对应的类
            $result = $this->callGoodsDetailV2Api($params);
            $duration = Helper::calculateDuration($startTime);

            if ($result && isset($result['data'])) {
                // 处理返回数据，添加额外信息
                $data = $result['data'];

                // 处理图片数据
                if (!empty($data['imgs'])) {
                    $data['imageList'] = explode(',', $data['imgs']);
                }

                // 处理推广素材数据
                if (!empty($data['tbOriginalData'])) {
                    $data['promotionMaterials'] = $this->processPromotionMaterials($data['tbOriginalData']);
                }

                // 添加获取时间
                $data['fetchTime'] = date('Y-m-d H:i:s');
                $data['platform'] = $this->platform;

                // 缓存成功的结果
                $this->cache->set($cacheKey, $data, $cacheExpire);

                $this->logger->info('获取商品素材成功', [
                    'goods_id' => $params['goodsId'],
                    'duration' => $duration,
                    'has_promotion_materials' => !empty($data['tbOriginalData']),
                    'image_count' => count($data['imageList'] ?? [])
                ]);

                return $data;
            }

            // 处理API返回的错误信息
            $errorMsg = $result['msg'] ?? '未知错误';
            $errorCode = $result['code'] ?? -1;

            throw new \Exception("获取商品素材失败 (错误码: {$errorCode}): {$errorMsg}");

        } catch (\Exception $e) {
            $this->logger->error('获取商品素材失败', [
                'params' => $params,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            throw $e;
        }
    }

    /**
     * 处理推广素材数据
     */
    private function processPromotionMaterials($tbOriginalData)
    {
        $materials = [];

        foreach ($tbOriginalData as $group) {
            $groupName = $group['groupName'] ?? '未知群组';
            $content = $group['content'] ?? [];

            $processedContent = [];
            foreach ($content as $item) {
                $type = $item['type'] ?? 1;
                $content = $item['content'] ?? '';

                $processedContent[] = [
                    'type' => $type, // 1=文字, 2=图片
                    'content' => $content,
                    'isImage' => $type == 2,
                    'isText' => $type == 1
                ];
            }

            $materials[] = [
                'groupName' => $groupName,
                'content' => $processedContent,
                'contentCount' => count($processedContent)
            ];
        }

        return $materials;
    }

    /**
     * 大淘客搜索 - 根据关键词搜索商品
     */
    public function searchGoods($params = [])
    {
        // 设置默认参数
        $defaultParams = [
            'pageId' => 1,
            'pageSize' => 20,
            'keyWords' => '',
            'cids' => '',
            'subcid' => '',
            'juHuaSuan' => 0,
            'taoQiangGou' => 0,
            'tmall' => 0,
            'tchaoshi' => 0,
            'goldSeller' => 0,
            'haitao' => 0,
            'brand' => 0,
            'brandIds' => '',
            'priceLowerLimit' => '',
            'priceUpperLimit' => '',
            'couponPriceLowerLimit' => '',
            'commissionRateLowerLimit' => '',
            'monthSalesLowerLimit' => '',
            'sort' => '0',
            'freeshipRemoteDistrict' => 0
        ];

        $params = array_merge($defaultParams, $params);

        // 移除空值参数
        $params = array_filter($params, function($value) {
            return $value !== '' && $value !== null;
        });

        try {
            $result = $this->callApi('GetDtkSearchGoods', $params);

            if ($result && isset($result['data'])) {
                return $result['data'];
            }

            throw new \Exception('搜索失败：' . ($result['msg'] ?? '未知错误'));

        } catch (\Exception $e) {
            $this->logger->error('大淘客搜索失败', [
                'params' => $params,
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }
}
?>
