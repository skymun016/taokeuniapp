<?php

namespace Services;

use Utils\Logger;
use Utils\Helper;

/**
 * 智能转链服务
 * 实现分层转链策略，根据商品热度采用不同转链时机
 */
class SmartLinkService
{
    private $db;
    private $linkConvertService;
    private $cache;
    private $logger;
    private $platform;
    
    // 商品层级常量
    const TIER_HOT = 1;      // 热门商品：月销量≥1000
    const TIER_NORMAL = 2;   // 普通商品：月销量100-999
    const TIER_COLD = 3;     // 冷门商品：月销量<100
    
    // 转链状态常量
    const LINK_STATUS_NONE = 0;     // 未转链
    const LINK_STATUS_ACTIVE = 1;   // 已转链
    const LINK_STATUS_EXPIRED = 2;  // 已过期
    
    public function __construct($platform = 'taobao')
    {
        $this->platform = $platform;
        $this->db = DatabaseService::getInstance();
        $this->linkConvertService = new LinkConvertService($platform);
        $this->cache = CacheService::getInstance();
        $this->logger = new Logger();
    }
    
    /**
     * 智能获取商品转链数据
     * 根据商品层级采用不同策略
     */
    public function getSmartLinkData($goodsId, $forceRefresh = false)
    {
        try {
            // 获取商品基础信息
            $goods = $this->getGoodsInfo($goodsId);
            if (!$goods) {
                throw new \Exception('商品不存在');
            }
            
            // 检查是否需要转链
            if (!$forceRefresh && $this->isLinkValid($goods)) {
                // 返回已有的转链数据
                return $this->formatLinkData($goods);
            }
            
            // 根据层级执行转链策略
            switch ($goods['tier_level']) {
                case self::TIER_HOT:
                    return $this->handleHotGoods($goods);
                    
                case self::TIER_NORMAL:
                    return $this->handleNormalGoods($goods);
                    
                case self::TIER_COLD:
                default:
                    return $this->handleColdGoods($goods);
            }
            
        } catch (\Exception $e) {
            $this->logger->error('智能转链失败', [
                'goodsId' => $goodsId,
                'error' => $e->getMessage()
            ]);
            
            // 降级策略：返回原始商品信息
            return $this->getFallbackData($goodsId);
        }
    }

    /**
     * 转链单个商品 - 为API接口提供
     * @param string $goodsId 商品ID
     * @return array|false 转链结果
     */
    public function convertSingleGoods($goodsId)
    {
        try {
            $this->logger->info('开始转链单个商品', ['goods_id' => $goodsId]);

            // 获取智能转链数据
            $linkData = $this->getSmartLinkData($goodsId, true); // 强制刷新

            if ($linkData && isset($linkData['tpwd'])) {
                $this->logger->info('单个商品转链成功', [
                    'goods_id' => $goodsId,
                    'has_tpwd' => !empty($linkData['tpwd'])
                ]);

                return $linkData;
            } else {
                $this->logger->warning('单个商品转链失败', ['goods_id' => $goodsId]);
                return false;
            }

        } catch (\Exception $e) {
            $this->logger->error('单个商品转链异常', [
                'goods_id' => $goodsId,
                'error' => $e->getMessage()
            ]);

            return false;
        }
    }

    /**
     * 批量预转链热门商品
     */
    public function preConvertHotGoods($limit = 100)
    {
        $this->logger->info('开始批量预转链热门商品', ['limit' => $limit]);
        
        $sql = "SELECT goods_id, tier_level FROM dtk_goods
                WHERE platform = ? AND tier_level = ?
                AND (link_status = ? OR link_expire_time < NOW() OR link_expire_time IS NULL)
                ORDER BY month_sales DESC
                LIMIT " . intval($limit);

        $hotGoods = $this->db->fetchAll($sql, [
            $this->platform,
            self::TIER_HOT,
            self::LINK_STATUS_NONE
        ]);
        
        $success = 0;
        $errors = 0;
        
        foreach ($hotGoods as $goods) {
            try {
                $this->convertAndSaveLink($goods['goods_id']);
                $success++;
                
                // 避免API调用过于频繁
                usleep(100000); // 0.1秒间隔
                
            } catch (\Exception $e) {
                $errors++;
                $this->logger->warning('热门商品转链失败', [
                    'goodsId' => $goods['goods_id'],
                    'error' => $e->getMessage()
                ]);
            }
        }
        
        $this->logger->info('批量预转链完成', [
            'total' => count($hotGoods),
            'success' => $success,
            'errors' => $errors
        ]);
        
        return ['success' => $success, 'errors' => $errors];
    }
    
    /**
     * 批量转链普通商品
     */
    public function batchConvertNormalGoods($limit = 200)
    {
        $this->logger->info('开始批量转链普通商品', ['limit' => $limit]);
        
        // 优先转链即将过期的商品
        $sql = "SELECT goods_id FROM dtk_goods
                WHERE platform = ? AND tier_level = ?
                AND (link_expire_time < DATE_ADD(NOW(), INTERVAL 1 HOUR)
                     OR link_status = ? OR link_expire_time IS NULL)
                ORDER BY link_expire_time ASC, month_sales DESC
                LIMIT " . intval($limit);

        $normalGoods = $this->db->fetchAll($sql, [
            $this->platform,
            self::TIER_NORMAL,
            self::LINK_STATUS_NONE
        ]);
        
        $success = 0;
        $errors = 0;
        
        foreach ($normalGoods as $goods) {
            try {
                $this->convertAndSaveLink($goods['goods_id']);
                $success++;
                
                // 控制API调用频率
                usleep(200000); // 0.2秒间隔
                
            } catch (\Exception $e) {
                $errors++;
                $this->logger->warning('普通商品转链失败', [
                    'goodsId' => $goods['goods_id'],
                    'error' => $e->getMessage()
                ]);
            }
        }
        
        $this->logger->info('批量转链普通商品完成', [
            'total' => count($normalGoods),
            'success' => $success,
            'errors' => $errors
        ]);
        
        return ['success' => $success, 'errors' => $errors];
    }
    
    /**
     * 更新商品层级
     */
    public function updateGoodsTiers()
    {
        $this->logger->info('开始更新商品层级');
        
        $sql = "UPDATE dtk_goods SET 
                tier_level = CASE 
                    WHEN month_sales >= 1000 THEN ?
                    WHEN month_sales >= 100 THEN ?
                    ELSE ?
                END,
                estimated_commission = ROUND(
                    COALESCE(actual_price, original_price, 0) * 
                    COALESCE(commission_rate, 0) / 100, 
                    2
                )
                WHERE platform = ?";
        
        $affected = $this->db->execute($sql, [
            self::TIER_HOT,
            self::TIER_NORMAL, 
            self::TIER_COLD,
            $this->platform
        ]);
        
        // 统计各层级商品数量
        $stats = $this->getTierStats();
        
        $this->logger->info('商品层级更新完成', [
            'affected' => $affected,
            'stats' => $stats
        ]);
        
        return $stats;
    }
    
    /**
     * 获取转链统计信息
     */
    public function getLinkStats()
    {
        $sql = "SELECT 
                    tier_level,
                    COUNT(*) as total,
                    SUM(CASE WHEN link_status = ? THEN 1 ELSE 0 END) as converted,
                    SUM(CASE WHEN link_status = ? THEN 1 ELSE 0 END) as expired,
                    AVG(month_sales) as avg_sales,
                    AVG(estimated_commission) as avg_commission
                FROM dtk_goods 
                WHERE platform = ?
                GROUP BY tier_level
                ORDER BY tier_level";
        
        $stats = $this->db->fetchAll($sql, [
            self::LINK_STATUS_ACTIVE,
            self::LINK_STATUS_EXPIRED,
            $this->platform
        ]);
        
        return $stats;
    }
    
    /**
     * 处理热门商品
     */
    private function handleHotGoods($goods)
    {
        // 热门商品应该已经预转链，直接返回
        if ($this->isLinkValid($goods)) {
            return $this->formatLinkData($goods);
        }
        
        // 如果没有转链数据，立即转链
        return $this->convertAndSaveLink($goods['goods_id']);
    }
    
    /**
     * 处理普通商品
     */
    private function handleNormalGoods($goods)
    {
        // 检查缓存
        $cacheKey = "normal_link_{$goods['goods_id']}";
        $cached = $this->cache->get($cacheKey);
        if ($cached) {
            return $cached;
        }
        
        // 实时转链并缓存
        $linkData = $this->convertAndSaveLink($goods['goods_id']);
        $this->cache->set($cacheKey, $linkData, 3600 * 6); // 6小时缓存
        
        return $linkData;
    }
    
    /**
     * 处理冷门商品
     */
    private function handleColdGoods($goods)
    {
        // 检查短期缓存
        $cacheKey = "cold_link_{$goods['goods_id']}";
        $cached = $this->cache->get($cacheKey);
        if ($cached) {
            return $cached;
        }
        
        // 实时转链并短期缓存
        $linkData = $this->convertAndSaveLink($goods['goods_id']);
        $this->cache->set($cacheKey, $linkData, 3600); // 1小时缓存
        
        return $linkData;
    }
    
    /**
     * 执行转链并保存结果
     */
    private function convertAndSaveLink($goodsId)
    {
        $startTime = microtime(true);
        
        // 调用转链服务
        $linkResult = $this->linkConvertService->convertToPrivilegeLink(['goodsId' => $goodsId]);
        
        // 保存转链结果到数据库
        $expireTime = date('Y-m-d H:i:s', time() + 24 * 3600); // 24小时后过期
        
        $sql = "UPDATE dtk_goods SET
                privilege_link = ?,
                short_url = ?,
                coupon_click_url = ?,
                tpwd = ?,
                link_expire_time = ?,
                link_status = ?,
                last_convert_time = NOW(),
                convert_count = convert_count + 1
                WHERE goods_id = ? AND platform = ?";

        $this->db->execute($sql, [
            $linkResult['itemUrl'] ?? '',
            $linkResult['shortUrl'] ?? '',
            $linkResult['couponClickUrl'] ?? '',
            $linkResult['tpwd'] ?? '',
            $expireTime,
            self::LINK_STATUS_ACTIVE,
            $goodsId,
            $this->platform
        ]);
        
        $duration = Helper::calculateDuration($startTime);
        
        $this->logger->info('商品转链成功', [
            'goodsId' => $goodsId,
            'duration' => $duration,
            'hasCoupon' => !empty($linkResult['couponClickUrl'])
        ]);
        
        // 重新获取完整数据
        $goods = $this->getGoodsInfo($goodsId);
        return $this->formatLinkData($goods);
    }
    
    /**
     * 检查转链是否有效
     */
    private function isLinkValid($goods)
    {
        return $goods['link_status'] == self::LINK_STATUS_ACTIVE 
               && !empty($goods['privilege_link'])
               && strtotime($goods['link_expire_time']) > time();
    }
    
    /**
     * 获取商品信息
     */
    private function getGoodsInfo($goodsId)
    {
        $sql = "SELECT * FROM dtk_goods WHERE goods_id = ? AND platform = ?";
        return $this->db->fetchOne($sql, [$goodsId, $this->platform]);
    }
    
    /**
     * 格式化转链数据
     */
    private function formatLinkData($goods)
    {
        return [
            'goodsId' => $goods['goods_id'],
            'title' => $goods['title'],
            'originalPrice' => $goods['original_price'],
            'actualPrice' => $goods['actual_price'],
            'couponPrice' => $goods['coupon_price'],
            'commissionRate' => $goods['commission_rate'],
            'estimatedCommission' => $goods['estimated_commission'],
            'privilegeLink' => $goods['privilege_link'],
            'shortUrl' => $goods['short_url'],
            'couponClickUrl' => $goods['coupon_click_url'],
            'tpwd' => $goods['tpwd'],
            'tierLevel' => $goods['tier_level'],
            'linkStatus' => $goods['link_status'],
            'linkExpireTime' => $goods['link_expire_time'],
            // 兼容字段
            'privilege_link' => $goods['privilege_link']
        ];
    }
    
    /**
     * 降级数据
     */
    private function getFallbackData($goodsId)
    {
        $goods = $this->getGoodsInfo($goodsId);
        if (!$goods) {
            throw new \Exception('商品不存在');
        }
        
        return [
            'goodsId' => $goods['goods_id'],
            'title' => $goods['title'],
            'originalPrice' => $goods['original_price'],
            'actualPrice' => $goods['actual_price'],
            'couponPrice' => $goods['coupon_price'],
            'commissionRate' => $goods['commission_rate'],
            'estimatedCommission' => $goods['estimated_commission'],
            'privilegeLink' => $goods['item_link'], // 使用原始链接
            'shortUrl' => '', // 降级时没有短链接
            'couponClickUrl' => $goods['coupon_link'],
            'tpwd' => '',
            'tierLevel' => $goods['tier_level'],
            'linkStatus' => self::LINK_STATUS_NONE,
            'linkExpireTime' => null,
            'fallback' => true,
            // 兼容字段
            'privilege_link' => $goods['item_link']
        ];
    }
    
    /**
     * 获取层级统计
     */
    private function getTierStats()
    {
        $sql = "SELECT tier_level, COUNT(*) as count 
                FROM dtk_goods 
                WHERE platform = ? 
                GROUP BY tier_level 
                ORDER BY tier_level";
        
        return $this->db->fetchAll($sql, [$this->platform]);
    }
}
