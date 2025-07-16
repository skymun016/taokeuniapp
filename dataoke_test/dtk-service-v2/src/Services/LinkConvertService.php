<?php

namespace Services;

use Utils\Logger;
use Utils\Helper;

/**
 * 统一转链服务类
 * 提供高效转链、淘口令生成等功能的统一接口
 */
class LinkConvertService
{
    private $dataokeService;
    private $logger;
    private $platform;
    private $cache;
    
    public function __construct($platform = 'taobao')
    {
        $this->platform = $platform;
        $this->dataokeService = new DataokeService($platform);
        $this->logger = new Logger();
        $this->cache = CacheService::getInstance();
    }
    
    /**
     * 智能转链 - 自动选择最优转链方式
     */
    public function smartConvert($params = [])
    {
        try {
            // 参数验证
            if (empty($params['goodsId']) && empty($params['url'])) {
                throw new \Exception('商品ID或链接不能为空');
            }
            
            $result = [];
            
            // 如果有商品ID，执行高效转链
            if (!empty($params['goodsId'])) {
                $privilegeResult = $this->convertToPrivilegeLink($params);
                $result['privilegeLink'] = $privilegeResult;
                
                // 如果需要淘口令，基于转链结果生成
                if ($params['needTaokouling'] ?? false) {
                    $taokoulingParams = [
                        'url' => $privilegeResult['itemUrl'] ?? $privilegeResult['couponClickUrl'] ?? '',
                        'text' => $params['taokoulingText'] ?? $this->generateDefaultTaokoulingText($privilegeResult)
                    ];
                    
                    $taokoulingResult = $this->createTaokouling($taokoulingParams);
                    $result['taokouling'] = $taokoulingResult;
                }
            }
            // 如果只有链接，直接生成淘口令
            elseif (!empty($params['url'])) {
                $taokoulingParams = [
                    'url' => $params['url'],
                    'text' => $params['text'] ?? '好物推荐'
                ];
                
                $taokoulingResult = $this->createTaokouling($taokoulingParams);
                $result['taokouling'] = $taokoulingResult;
            }
            
            $this->logger->info('智能转链完成', [
                'platform' => $this->platform,
                'goodsId' => $params['goodsId'] ?? '',
                'hasPrivilegeLink' => isset($result['privilegeLink']),
                'hasTaokouling' => isset($result['taokouling'])
            ]);
            
            return $result;
            
        } catch (\Exception $e) {
            $this->logger->error('智能转链失败', [
                'params' => $params,
                'error' => $e->getMessage()
            ]);
            
            throw $e;
        }
    }
    
    /**
     * 高效转链
     */
    public function convertToPrivilegeLink($params = [])
    {
        try {
            $result = $this->dataokeService->getPrivilegeLink($params);
            
            // 添加额外的处理逻辑
            $result['convertTime'] = date('Y-m-d H:i:s');
            $result['platform'] = $this->platform;
            $result['goodsId'] = $params['goodsId'];
            
            // 判断是否有优惠券
            $result['hasCoupon'] = !empty($result['couponInfo']);
            
            // 计算预估佣金（如果有价格和佣金率信息）
            if (!empty($result['actualPrice']) && !empty($result['maxCommissionRate'])) {
                $result['estimatedCommission'] = round(
                    floatval($result['actualPrice']) * floatval($result['maxCommissionRate']) / 100, 
                    2
                );
            }
            
            return $result;
            
        } catch (\Exception $e) {
            throw new \Exception('高效转链失败: ' . $e->getMessage());
        }
    }
    
    /**
     * 生成淘口令
     */
    public function createTaokouling($params = [])
    {
        try {
            $result = $this->dataokeService->createTaokouling($params);
            
            // 添加额外信息
            $result['createTime'] = date('Y-m-d H:i:s');
            $result['platform'] = $this->platform;
            $result['originalText'] = $params['text'] ?? '';
            $result['originalUrl'] = $params['url'] ?? '';
            
            return $result;
            
        } catch (\Exception $e) {
            throw new \Exception('淘口令生成失败: ' . $e->getMessage());
        }
    }
    
    /**
     * 批量转链
     */
    public function batchConvert($goodsIds = [], $options = [])
    {
        if (empty($goodsIds)) {
            throw new \Exception('商品ID列表不能为空');
        }
        
        $results = [];
        $successCount = 0;
        $errorCount = 0;
        
        foreach ($goodsIds as $goodsId) {
            try {
                $params = array_merge($options, ['goodsId' => $goodsId]);
                $result = $this->smartConvert($params);
                
                $results[] = [
                    'goodsId' => $goodsId,
                    'success' => true,
                    'data' => $result
                ];
                
                $successCount++;
                
                // 避免请求过于频繁
                usleep(200000); // 200ms延迟
                
            } catch (\Exception $e) {
                $results[] = [
                    'goodsId' => $goodsId,
                    'success' => false,
                    'error' => $e->getMessage()
                ];
                
                $errorCount++;
                
                $this->logger->error('批量转链单个商品失败', [
                    'goodsId' => $goodsId,
                    'error' => $e->getMessage()
                ]);
            }
        }
        
        $this->logger->info('批量转链完成', [
            'total' => count($goodsIds),
            'success' => $successCount,
            'error' => $errorCount
        ]);
        
        return [
            'total' => count($goodsIds),
            'success' => $successCount,
            'error' => $errorCount,
            'results' => $results
        ];
    }
    
    /**
     * 生成默认淘口令文案
     */
    private function generateDefaultTaokoulingText($privilegeData)
    {
        $text = '好物推荐';
        
        if (!empty($privilegeData['title'])) {
            $text = mb_substr($privilegeData['title'], 0, 20) . '...';
        }
        
        if (!empty($privilegeData['couponInfo'])) {
            $text .= ' ' . $privilegeData['couponInfo'];
        }
        
        return $text;
    }
    
    /**
     * 获取商品推广素材
     */
    public function getGoodsMaterials($params = [])
    {
        try {
            // 参数验证
            if (empty($params['goodsId'])) {
                throw new \Exception('商品ID不能为空');
            }

            $result = $this->dataokeService->getGoodsMaterials($params);

            // 添加额外的处理逻辑
            $result['platform'] = $this->platform;
            $result['fetchTime'] = date('Y-m-d H:i:s');

            // 提取关键信息用于快速访问
            $result['summary'] = [
                'title' => $result['title'] ?? '',
                'originalPrice' => $result['originalPrice'] ?? 0,
                'actualPrice' => $result['actualPrice'] ?? 0,
                'couponPrice' => $result['couponPrice'] ?? 0,
                'commissionRate' => $result['commissionRate'] ?? 0,
                'monthSales' => $result['monthSales'] ?? 0,
                'shopName' => $result['shopName'] ?? '',
                'hasPromotion' => !empty($result['promotionMaterials']),
                'imageCount' => count($result['imageList'] ?? [])
            ];

            return $result;

        } catch (\Exception $e) {
            throw new \Exception('获取商品素材失败: ' . $e->getMessage());
        }
    }

    /**
     * 增强转链 - 转链+素材一体化
     */
    public function enhancedConvert($params = [])
    {
        try {
            // 参数验证
            if (empty($params['goodsId'])) {
                throw new \Exception('商品ID不能为空');
            }

            $result = [];

            // 1. 执行高效转链
            $privilegeResult = $this->convertToPrivilegeLink($params);
            $result['privilegeLink'] = $privilegeResult;

            // 2. 获取商品素材（如果需要）
            if ($params['includeMaterials'] ?? false) {
                try {
                    $materialsResult = $this->getGoodsMaterials($params);
                    $result['materials'] = $materialsResult;
                } catch (\Exception $e) {
                    $this->logger->warning('获取商品素材失败', [
                        'goodsId' => $params['goodsId'],
                        'error' => $e->getMessage()
                    ]);
                    $result['materials'] = null;
                    $result['materialsError'] = $e->getMessage();
                }
            }

            // 3. 生成淘口令（如果需要）
            if ($params['needTaokouling'] ?? false) {
                $taokoulingParams = [
                    'goodsId' => $params['goodsId'],
                    'text' => $params['taokoulingText'] ?? $this->generateDefaultTaokoulingText($privilegeResult)
                ];

                $taokoulingResult = $this->createTaokouling($taokoulingParams);
                $result['taokouling'] = $taokoulingResult;
            }

            $this->logger->info('增强转链完成', [
                'platform' => $this->platform,
                'goodsId' => $params['goodsId'],
                'hasPrivilegeLink' => isset($result['privilegeLink']),
                'hasMaterials' => isset($result['materials']),
                'hasTaokouling' => isset($result['taokouling'])
            ]);

            return $result;

        } catch (\Exception $e) {
            $this->logger->error('增强转链失败', [
                'params' => $params,
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }

    /**
     * 获取转链统计信息
     */
    public function getConvertStats($startDate = null, $endDate = null)
    {
        // 这里可以添加统计逻辑，比如从数据库获取转链记录
        // 暂时返回基础信息
        return [
            'platform' => $this->platform,
            'supportedFeatures' => [
                'privilegeLink' => true,
                'taokouling' => true,
                'batchConvert' => true,
                'smartConvert' => true
            ],
            'timestamp' => date('Y-m-d H:i:s')
        ];
    }
}
