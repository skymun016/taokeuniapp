<?php

namespace JdAffiliateService\Services;

use JdAffiliate\Utils\Logger;
use Exception;

/**
 * 缓存管理器
 * 提供不同类型数据的缓存策略和优化
 */
class CacheManager
{
    private $cacheService;
    private $logger;
    
    // 缓存策略配置
    private $strategies = [
        // 商品数据缓存策略
        'product_list' => [
            'ttl' => 1800,      // 30分钟
            'prefix' => 'products_',
            'preload' => true,   // 支持预加载
            'batch_size' => 50   // 批量处理大小
        ],
        'product_detail' => [
            'ttl' => 3600,      // 1小时
            'prefix' => 'product_',
            'preload' => false,
            'batch_size' => 20
        ],
        // 搜索结果缓存策略
        'search_results' => [
            'ttl' => 900,       // 15分钟
            'prefix' => 'search_',
            'preload' => false,
            'batch_size' => 30
        ],
        // 分类数据缓存策略
        'categories' => [
            'ttl' => 7200,      // 2小时
            'prefix' => 'categories_',
            'preload' => true,
            'batch_size' => 100
        ],
        // 联盟链接缓存策略
        'affiliate_links' => [
            'ttl' => 86400,     // 24小时
            'prefix' => 'link_',
            'preload' => false,
            'batch_size' => 10
        ],
        // API响应缓存策略
        'api_responses' => [
            'ttl' => 600,       // 10分钟
            'prefix' => 'api_',
            'preload' => false,
            'batch_size' => 20
        ]
    ];
    
    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
        $this->logger = new Logger();
    }
    
    /**
     * 获取商品列表缓存
     */
    public function getProductList($params, $callback = null)
    {
        $key = $this->generateProductListKey($params);
        $strategy = $this->strategies['product_list'];
        
        return $this->getWithStrategy('product_list', $key, $callback, $strategy['ttl']);
    }
    
    /**
     * 设置商品列表缓存
     */
    public function setProductList($params, $data)
    {
        $key = $this->generateProductListKey($params);
        $strategy = $this->strategies['product_list'];
        
        return $this->setWithStrategy('product_list', $key, $data, $strategy['ttl']);
    }
    
    /**
     * 获取商品详情缓存
     */
    public function getProductDetail($productId, $callback = null)
    {
        $key = 'detail_' . $productId;
        $strategy = $this->strategies['product_detail'];
        
        return $this->getWithStrategy('product_detail', $key, $callback, $strategy['ttl']);
    }
    
    /**
     * 设置商品详情缓存
     */
    public function setProductDetail($productId, $data)
    {
        $key = 'detail_' . $productId;
        $strategy = $this->strategies['product_detail'];
        
        return $this->setWithStrategy('product_detail', $key, $data, $strategy['ttl']);
    }
    
    /**
     * 获取搜索结果缓存
     */
    public function getSearchResults($keyword, $params, $callback = null)
    {
        $key = $this->generateSearchKey($keyword, $params);
        $strategy = $this->strategies['search_results'];
        
        return $this->getWithStrategy('search_results', $key, $callback, $strategy['ttl']);
    }
    
    /**
     * 设置搜索结果缓存
     */
    public function setSearchResults($keyword, $params, $data)
    {
        $key = $this->generateSearchKey($keyword, $params);
        $strategy = $this->strategies['search_results'];
        
        return $this->setWithStrategy('search_results', $key, $data, $strategy['ttl']);
    }
    
    /**
     * 获取分类数据缓存
     */
    public function getCategories($callback = null)
    {
        $key = 'all';
        $strategy = $this->strategies['categories'];
        
        return $this->getWithStrategy('categories', $key, $callback, $strategy['ttl']);
    }
    
    /**
     * 设置分类数据缓存
     */
    public function setCategories($data)
    {
        $key = 'all';
        $strategy = $this->strategies['categories'];
        
        return $this->setWithStrategy('categories', $key, $data, $strategy['ttl']);
    }
    
    /**
     * 获取联盟链接缓存
     */
    public function getAffiliateLink($productId, $params, $callback = null)
    {
        $key = $this->generateLinkKey($productId, $params);
        $strategy = $this->strategies['affiliate_links'];
        
        return $this->getWithStrategy('affiliate_links', $key, $callback, $strategy['ttl']);
    }
    
    /**
     * 设置联盟链接缓存
     */
    public function setAffiliateLink($productId, $params, $data)
    {
        $key = $this->generateLinkKey($productId, $params);
        $strategy = $this->strategies['affiliate_links'];
        
        return $this->setWithStrategy('affiliate_links', $key, $data, $strategy['ttl']);
    }
    
    /**
     * 获取API响应缓存
     */
    public function getApiResponse($endpoint, $params, $callback = null)
    {
        $key = $this->generateApiKey($endpoint, $params);
        $strategy = $this->strategies['api_responses'];
        
        return $this->getWithStrategy('api_responses', $key, $callback, $strategy['ttl']);
    }
    
    /**
     * 设置API响应缓存
     */
    public function setApiResponse($endpoint, $params, $data)
    {
        $key = $this->generateApiKey($endpoint, $params);
        $strategy = $this->strategies['api_responses'];
        
        return $this->setWithStrategy('api_responses', $key, $data, $strategy['ttl']);
    }
    
    /**
     * 通用缓存获取方法
     */
    private function getWithStrategy($type, $key, $callback, $ttl)
    {
        $strategy = $this->strategies[$type];
        $fullKey = $strategy['prefix'] . $key;
        
        $startTime = microtime(true);
        $data = $this->cacheService->get($fullKey);
        $duration = round((microtime(true) - $startTime) * 1000, 2);
        
        if ($data !== null) {
            $this->logger->debug("Cache hit for {$type}", [
                'key' => $key,
                'duration' => $duration . 'ms'
            ]);
            return $data;
        }
        
        $this->logger->debug("Cache miss for {$type}", [
            'key' => $key,
            'duration' => $duration . 'ms'
        ]);
        
        // 如果提供了回调函数，执行并缓存结果
        if ($callback && is_callable($callback)) {
            try {
                $startTime = microtime(true);
                $data = $callback();
                $callbackDuration = round((microtime(true) - $startTime) * 1000, 2);
                
                if ($data !== null) {
                    $this->setWithStrategy($type, $key, $data, $ttl);
                    $this->logger->debug("Cache populated for {$type}", [
                        'key' => $key,
                        'callback_duration' => $callbackDuration . 'ms'
                    ]);
                }
                
                return $data;
            } catch (Exception $e) {
                $this->logger->error("Cache callback failed for {$type}", [
                    'key' => $key,
                    'error' => $e->getMessage()
                ]);
                return null;
            }
        }
        
        return null;
    }
    
    /**
     * 通用缓存设置方法
     */
    private function setWithStrategy($type, $key, $data, $ttl)
    {
        $strategy = $this->strategies[$type];
        $fullKey = $strategy['prefix'] . $key;
        
        $startTime = microtime(true);
        $result = $this->cacheService->set($fullKey, $data, $ttl);
        $duration = round((microtime(true) - $startTime) * 1000, 2);
        
        if ($result) {
            $this->logger->debug("Cache set for {$type}", [
                'key' => $key,
                'ttl' => $ttl,
                'duration' => $duration . 'ms'
            ]);
        } else {
            $this->logger->warning("Cache set failed for {$type}", [
                'key' => $key,
                'duration' => $duration . 'ms'
            ]);
        }
        
        return $result;
    }
    
    /**
     * 批量预热缓存
     */
    public function warmupCache($type, $items, $callback)
    {
        if (!isset($this->strategies[$type])) {
            throw new Exception("Unknown cache type: {$type}");
        }
        
        $strategy = $this->strategies[$type];
        if (!$strategy['preload']) {
            $this->logger->info("Cache warmup skipped for {$type} (preload disabled)");
            return false;
        }
        
        $batchSize = $strategy['batch_size'];
        $batches = array_chunk($items, $batchSize);
        $totalWarmed = 0;
        
        $this->logger->info("Starting cache warmup for {$type}", [
            'total_items' => count($items),
            'batch_size' => $batchSize,
            'total_batches' => count($batches)
        ]);
        
        foreach ($batches as $batchIndex => $batch) {
            try {
                $startTime = microtime(true);
                $batchData = $callback($batch);
                $duration = round((microtime(true) - $startTime) * 1000, 2);
                
                if (is_array($batchData)) {
                    foreach ($batchData as $key => $data) {
                        $this->setWithStrategy($type, $key, $data, $strategy['ttl']);
                        $totalWarmed++;
                    }
                }
                
                $this->logger->debug("Cache warmup batch completed", [
                    'type' => $type,
                    'batch' => $batchIndex + 1,
                    'items' => count($batch),
                    'duration' => $duration . 'ms'
                ]);
                
                // 避免过度占用资源
                if ($batchIndex < count($batches) - 1) {
                    usleep(100000); // 100ms延迟
                }
                
            } catch (Exception $e) {
                $this->logger->error("Cache warmup batch failed", [
                    'type' => $type,
                    'batch' => $batchIndex + 1,
                    'error' => $e->getMessage()
                ]);
            }
        }
        
        $this->logger->info("Cache warmup completed for {$type}", [
            'total_warmed' => $totalWarmed,
            'success_rate' => round(($totalWarmed / count($items)) * 100, 2) . '%'
        ]);
        
        return $totalWarmed;
    }
    
    /**
     * 批量更新缓存
     */
    public function batchUpdate($type, $updates)
    {
        if (!isset($this->strategies[$type])) {
            throw new Exception("Unknown cache type: {$type}");
        }
        
        $strategy = $this->strategies[$type];
        $updated = 0;
        
        foreach ($updates as $key => $data) {
            if ($this->setWithStrategy($type, $key, $data, $strategy['ttl'])) {
                $updated++;
            }
        }
        
        $this->logger->info("Batch cache update completed", [
            'type' => $type,
            'total_updates' => count($updates),
            'successful_updates' => $updated
        ]);
        
        return $updated;
    }
    
    /**
     * 清理特定类型的缓存
     */
    public function clearByType($type)
    {
        if (!isset($this->strategies[$type])) {
            throw new Exception("Unknown cache type: {$type}");
        }
        
        $strategy = $this->strategies[$type];
        $prefix = $strategy['prefix'];
        
        // 这里需要扩展CacheService来支持按前缀删除
        // 暂时记录日志
        $this->logger->info("Cache clear requested for type: {$type}");
        
        return true;
    }
    
    /**
     * 获取缓存统计信息
     */
    public function getCacheStats()
    {
        $stats = [
            'strategies' => $this->strategies,
            'cache_service_stats' => $this->cacheService->getStats()
        ];
        
        // 添加每种类型的统计信息
        foreach ($this->strategies as $type => $strategy) {
            $stats['types'][$type] = [
                'ttl' => $strategy['ttl'],
                'prefix' => $strategy['prefix'],
                'preload_enabled' => $strategy['preload'],
                'batch_size' => $strategy['batch_size']
            ];
        }
        
        return $stats;
    }
    
    /**
     * 生成商品列表缓存键
     */
    private function generateProductListKey($params)
    {
        $keyParts = [
            'page' => $params['page'] ?? 1,
            'pageSize' => $params['pageSize'] ?? 20,
            'categoryId' => $params['categoryId'] ?? '',
            'minPrice' => $params['minPrice'] ?? '',
            'maxPrice' => $params['maxPrice'] ?? '',
            'sortBy' => $params['sortBy'] ?? '',
            'sortOrder' => $params['sortOrder'] ?? ''
        ];
        
        return 'list_' . md5(json_encode($keyParts));
    }
    
    /**
     * 生成搜索缓存键
     */
    private function generateSearchKey($keyword, $params)
    {
        $keyParts = [
            'keyword' => $keyword,
            'page' => $params['page'] ?? 1,
            'pageSize' => $params['pageSize'] ?? 20,
            'categoryId' => $params['categoryId'] ?? '',
            'sortBy' => $params['sortBy'] ?? ''
        ];
        
        return 'search_' . md5(json_encode($keyParts));
    }
    
    /**
     * 生成联盟链接缓存键
     */
    private function generateLinkKey($productId, $params)
    {
        $keyParts = [
            'productId' => $productId,
            'unionId' => $params['unionId'] ?? '',
            'positionId' => $params['positionId'] ?? ''
        ];
        
        return 'link_' . md5(json_encode($keyParts));
    }
    
    /**
     * 生成API缓存键
     */
    private function generateApiKey($endpoint, $params)
    {
        $keyParts = [
            'endpoint' => $endpoint,
            'params' => $params
        ];
        
        return 'api_' . md5(json_encode($keyParts));
    }
    
    /**
     * 更新缓存策略配置
     */
    public function updateStrategy($type, $config)
    {
        if (!isset($this->strategies[$type])) {
            throw new Exception("Unknown cache type: {$type}");
        }
        
        $this->strategies[$type] = array_merge($this->strategies[$type], $config);
        
        $this->logger->info("Cache strategy updated", [
            'type' => $type,
            'config' => $config
        ]);
        
        return true;
    }
}