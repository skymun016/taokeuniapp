<?php

namespace Services;

use Utils\Logger;
use Utils\Helper;

/**
 * 商品同步服务 - 集成智能混合转链策略
 */
class GoodsSyncService
{
    private $dataokeService;
    private $db;
    private $logger;
    private $platform;
    private $smartLinkService;
    private $config;
    
    public function __construct($platform = 'taobao')
    {
        $this->platform = $platform;
        $this->dataokeService = new DataokeService($platform);
        $this->db = DatabaseService::getInstance();
        $this->logger = new Logger();

        // 初始化智能转链服务
        $this->smartLinkService = new SmartLinkService($platform);

        // 加载配置
        $this->config = [
            'smart_link_enabled' => $_ENV['SMART_LINK_ENABLED'] ?? true,
            'tier1_preconvert' => $_ENV['SMART_LINK_TIER1_PRECONVERT'] ?? true,
            'api_interval' => $_ENV['SMART_LINK_API_INTERVAL'] ?? 200, // 毫秒，减少间隔
            'hourly_limit' => $_ENV['SYNC_HOURLY_LIMIT'] ?? 200,
            'max_convert_per_sync' => $_ENV['SMART_LINK_MAX_CONVERT_PER_SYNC'] ?? 10 // 每次同步最多转链数量
        ];
    }
    
    /**
     * 每小时智能同步 - 200条商品 + 分层转链
     */
    public function hourlySmartSync()
    {
        $this->logger->info('开始每小时智能同步', [
            'platform' => $this->platform,
            'limit' => $this->config['hourly_limit']
        ]);

        $totalSynced = 0;
        $totalErrors = 0;
        $tier1Converted = 0;
        $startTime = microtime(true);

        try {
            // 计算需要同步的页数 (200条 ÷ 100条/页 = 2页)
            $maxPages = ceil($this->config['hourly_limit'] / 100);

            for ($page = 1; $page <= $maxPages; $page++) {
                $this->logger->info("同步第 {$page} 页商品数据");

                $params = [
                    'pageId' => $page,
                    'pageSize' => 100,
                ];

                $result = $this->dataokeService->getGoodsList($params);

                if (empty($result['list'])) {
                    $this->logger->info("第 {$page} 页无数据，停止同步");
                    break;
                }

                // 保存商品数据并执行智能转链
                $syncResult = $this->saveGoodsWithSmartLink($result['list']);
                $totalSynced += $syncResult['success'];
                $totalErrors += $syncResult['errors'];
                $tier1Converted += $syncResult['tier1_converted'];

                $this->logger->info("第 {$page} 页同步完成", [
                    'success' => $syncResult['success'],
                    'errors' => $syncResult['errors'],
                    'tier1_converted' => $syncResult['tier1_converted']
                ]);

                // 避免请求过于频繁
                sleep(1);
            }

            $duration = Helper::calculateDuration($startTime);

            // 记录同步日志
            $this->recordSyncLog('hourly_smart_sync', $totalSynced, $totalErrors, $duration, date('Y-m-d H:i:s', $startTime), date('Y-m-d H:i:s'));

            $this->logger->info('每小时智能同步完成', [
                'totalSynced' => $totalSynced,
                'totalErrors' => $totalErrors,
                'tier1Converted' => $tier1Converted,
                'duration' => $duration
            ]);

            return [
                'success' => true,
                'totalSynced' => $totalSynced,
                'totalErrors' => $totalErrors,
                'tier1Converted' => $tier1Converted,
                'duration' => $duration
            ];

        } catch (\Exception $e) {
            $this->logger->error('每小时智能同步失败', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'totalSynced' => $totalSynced,
                'totalErrors' => $totalErrors,
                'tier1Converted' => $tier1Converted
            ];
        }
    }

    /**
     * 全量同步商品数据（首次使用）
     */
    public function fullSync($maxPages = 10)
    {
        $this->logger->info('开始全量同步商品数据', ['platform' => $this->platform, 'maxPages' => $maxPages]);
        
        $totalSynced = 0;
        $totalErrors = 0;
        $startTime = microtime(true);
        
        try {
            for ($page = 1; $page <= $maxPages; $page++) {
                $this->logger->info("同步第 {$page} 页商品数据");
                
                $params = [
                    'pageId' => $page,
                    'pageSize' => 100, // 每页100个商品
                ];
                
                $result = $this->dataokeService->getGoodsList($params);
                
                if (empty($result['list'])) {
                    $this->logger->info("第 {$page} 页无数据，停止同步");
                    break;
                }
                
                $syncResult = $this->saveGoodsToDatabase($result['list']);
                $totalSynced += $syncResult['success'];
                $totalErrors += $syncResult['errors'];
                
                $this->logger->info("第 {$page} 页同步完成", [
                    'success' => $syncResult['success'],
                    'errors' => $syncResult['errors']
                ]);
                
                // 避免请求过于频繁
                sleep(1);
            }
            
            $duration = Helper::calculateDuration($startTime);
            
            // 记录同步日志
            $this->recordSyncLog('full_sync', $totalSynced, $totalErrors, $duration, date('Y-m-d H:i:s', $startTime), date('Y-m-d H:i:s'));
            
            $this->logger->info('全量同步完成', [
                'totalSynced' => $totalSynced,
                'totalErrors' => $totalErrors,
                'duration' => $duration
            ]);
            
            return [
                'success' => true,
                'totalSynced' => $totalSynced,
                'totalErrors' => $totalErrors,
                'duration' => $duration
            ];
            
        } catch (\Exception $e) {
            $this->logger->error('全量同步失败', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'totalSynced' => $totalSynced,
                'totalErrors' => $totalErrors
            ];
        }
    }
    
    /**
     * 增量同步商品数据（定时调用）
     */
    public function incrementalSync($startTime = null, $endTime = null)
    {
        if (!$startTime) {
            // 获取上次同步时间
            $lastSync = $this->getLastSyncTime();
            $startTime = $lastSync ?: date('Y-m-d H:i:s', strtotime('-1 hour'));
        }
        
        if (!$endTime) {
            $endTime = date('Y-m-d H:i:s');
        }
        
        $this->logger->info('开始增量同步商品数据', [
            'platform' => $this->platform,
            'startTime' => $startTime,
            'endTime' => $endTime
        ]);
        
        $totalSynced = 0;
        $totalErrors = 0;
        $startTimeMs = microtime(true);
        
        try {
            $page = 1;
            $hasMore = true;
            
            while ($hasMore) {
                $this->logger->info("增量同步第 {$page} 页");
                
                $result = $this->dataokeService->callApi('GetPullGoodsByTime', [
                    'pageId' => $page,
                    'pageSize' => 100,
                    'startTime' => $startTime,
                    'endTime' => $endTime
                ]);
                
                if (empty($result['data']['list'])) {
                    $hasMore = false;
                    break;
                }
                
                $syncResult = $this->saveGoodsToDatabase($result['data']['list']);
                $totalSynced += $syncResult['success'];
                $totalErrors += $syncResult['errors'];
                
                $this->logger->info("增量同步第 {$page} 页完成", [
                    'success' => $syncResult['success'],
                    'errors' => $syncResult['errors']
                ]);
                
                $page++;
                
                // 避免请求过于频繁
                sleep(1);
                
                // 防止无限循环
                if ($page > 100) {
                    $this->logger->warning('增量同步页数超过100页，停止同步');
                    break;
                }
            }
            
            $duration = Helper::calculateDuration($startTimeMs);
            
            // 记录同步日志
            $this->recordSyncLog('incremental_sync', $totalSynced, $totalErrors, $duration, $startTime, $endTime);
            
            $this->logger->info('增量同步完成', [
                'totalSynced' => $totalSynced,
                'totalErrors' => $totalErrors,
                'duration' => $duration
            ]);
            
            return [
                'success' => true,
                'totalSynced' => $totalSynced,
                'totalErrors' => $totalErrors,
                'duration' => $duration,
                'startTime' => $startTime,
                'endTime' => $endTime
            ];
            
        } catch (\Exception $e) {
            $this->logger->error('增量同步失败', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'totalSynced' => $totalSynced,
                'totalErrors' => $totalErrors
            ];
        }
    }
    
    /**
     * 保存商品数据并执行智能转链
     */
    private function saveGoodsWithSmartLink($goodsList)
    {
        $success = 0;
        $errors = 0;
        $tier1Converted = 0;
        $maxConvertPerSync = $this->config['max_convert_per_sync'];

        foreach ($goodsList as $goods) {
            try {
                // 转换数据格式
                $data = $this->convertGoodsDataWithTier($goods);

                // 检查商品是否已存在
                $existing = $this->db->fetchOne(
                    "SELECT id FROM dtk_goods WHERE goods_id = ? AND platform = ?",
                    [$data['goods_id'], $this->platform]
                );

                if ($existing) {
                    // 更新现有商品
                    $this->updateGoods($existing['id'], $data);
                } else {
                    // 插入新商品
                    $this->insertGoods($data);
                }

                // 智能转链处理（限制数量避免超时）
                if ($this->config['smart_link_enabled'] && $this->config['tier1_preconvert']) {
                    if ($data['tier_level'] == 1 && $tier1Converted < $maxConvertPerSync) { // Tier 1 热门商品，限制数量
                        try {
                            $this->preConvertTier1Goods($data['goods_id']);
                            $tier1Converted++;

                            // API调用间隔控制（减少间隔）
                            usleep($this->config['api_interval'] * 1000); // 转换为微秒

                            $this->logger->info('Tier 1商品预转链成功', [
                                'goods_id' => $data['goods_id'],
                                'converted_count' => $tier1Converted,
                                'max_limit' => $maxConvertPerSync
                            ]);

                        } catch (\Exception $e) {
                            $this->logger->warning('Tier 1商品预转链失败', [
                                'goods_id' => $data['goods_id'],
                                'error' => $e->getMessage()
                            ]);
                        }
                    } elseif ($data['tier_level'] == 1 && $tier1Converted >= $maxConvertPerSync) {
                        $this->logger->info('已达到本次同步转链上限，跳过转链', [
                            'goods_id' => $data['goods_id'],
                            'converted_count' => $tier1Converted,
                            'max_limit' => $maxConvertPerSync
                        ]);
                    }
                }

                $success++;

            } catch (\Exception $e) {
                $errors++;
                $this->logger->error('保存商品失败', [
                    'goods_id' => $goods['goodsId'] ?? 'unknown',
                    'error' => $e->getMessage()
                ]);
            }
        }

        return [
            'success' => $success,
            'errors' => $errors,
            'tier1_converted' => $tier1Converted
        ];
    }

    /**
     * 保存商品数据到数据库（原方法保持兼容）
     */
    private function saveGoodsToDatabase($goodsList)
    {
        $success = 0;
        $errors = 0;
        
        foreach ($goodsList as $goods) {
            try {
                // 转换数据格式
                $data = $this->convertGoodsData($goods);
                
                // 检查商品是否已存在
                $existing = $this->db->fetchOne(
                    "SELECT id FROM dtk_goods WHERE goods_id = ? AND platform = ?",
                    [$data['goods_id'], $this->platform]
                );
                
                if ($existing) {
                    // 更新现有商品
                    $this->updateGoods($existing['id'], $data);
                } else {
                    // 插入新商品
                    $this->insertGoods($data);
                }
                
                $success++;
                
            } catch (\Exception $e) {
                $errors++;
                $this->logger->error('保存商品失败', [
                    'goods_id' => $goods['goodsId'] ?? 'unknown',
                    'error' => $e->getMessage()
                ]);
            }
        }
        
        return ['success' => $success, 'errors' => $errors];
    }
    
    /**
     * 转换商品数据格式（包含层级判断）
     */
    public function convertGoodsDataWithTier($goods)
    {
        $data = $this->convertGoodsData($goods);

        // 计算商品层级
        $monthSales = $goods['month_sales'] ?? 0;
        if ($monthSales >= 1000) {
            $tierLevel = 1; // 热门商品
        } elseif ($monthSales >= 100) {
            $tierLevel = 2; // 普通商品
        } else {
            $tierLevel = 3; // 冷门商品
        }

        // 计算预估佣金
        $basePrice = $data['actual_price'] > 0 ? $data['actual_price'] : $data['original_price'];
        $estimatedCommission = round($basePrice * ($data['commission_rate'] / 100), 2);

        // 添加智能转链相关字段
        $data['tier_level'] = $tierLevel;
        $data['estimated_commission'] = $estimatedCommission;
        $data['link_status'] = 0; // 默认未转链
        $data['convert_count'] = 0;
        $data['sync_time'] = date('Y-m-d H:i:s');

        return $data;
    }

    /**
     * Tier 1商品预转链
     */
    public function preConvertTier1Goods($goodsId)
    {
        $this->logger->info('开始Tier 1商品预转链', ['goods_id' => $goodsId]);

        try {
            // 调用智能转链服务
            $linkData = $this->smartLinkService->getSmartLinkData($goodsId, true);

            $this->logger->info('Tier 1商品预转链成功', [
                'goods_id' => $goodsId,
                'has_privilege_link' => !empty($linkData['privilegeLink']),
                'has_coupon_link' => !empty($linkData['couponClickUrl']),
                'has_tpwd' => !empty($linkData['tpwd'])
            ]);

            return $linkData;

        } catch (\Exception $e) {
            $this->logger->error('Tier 1商品预转链失败', [
                'goods_id' => $goodsId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * 转换商品数据格式（原方法保持兼容）
     */
    private function convertGoodsData($goods)
    {
        return [
            'goods_id' => $goods['goods_id'],
            'title' => $goods['title'],
            'dtitle' => $goods['dtitle'] ?? '',
            'original_price' => $goods['original_price'],
            'actual_price' => $goods['actual_price'],
            'coupon_price' => $goods['coupon_price'] ?? 0,
            'commission_rate' => $goods['commission_rate'],
            'month_sales' => $goods['month_sales'] ?? 0,
            'main_pic' => $goods['main_pic'],
            'item_link' => $goods['item_link'],
            'coupon_link' => $goods['coupon_link'] ?? '',
            'shop_name' => $goods['shop_name'],
            'shop_type' => $goods['shop_type'] ?? 1,
            'brand_name' => $goods['brand_name'] ?? '',
            'cid' => $goods['cid'],
            'desc_score' => $goods['desc_score'] ?? 0,
            'service_score' => $goods['service_score'] ?? 0,
            'ship_score' => $goods['ship_score'] ?? 0,
            'platform' => $this->platform,
            'create_time' => $goods['created_at'] ?? date('Y-m-d H:i:s'),
            'update_time' => date('Y-m-d H:i:s')
        ];
    }
    
    /**
     * 插入新商品
     */
    private function insertGoods($data)
    {
        $fields = array_keys($data);
        $placeholders = array_fill(0, count($fields), '?');
        
        $sql = "INSERT INTO dtk_goods (" . implode(', ', $fields) . ") VALUES (" . implode(', ', $placeholders) . ")";
        
        return $this->db->execute($sql, array_values($data));
    }
    
    /**
     * 更新现有商品
     */
    private function updateGoods($id, $data)
    {
        unset($data['create_time']); // 不更新创建时间
        
        $fields = array_keys($data);
        $setClause = implode(' = ?, ', $fields) . ' = ?';
        
        $sql = "UPDATE dtk_goods SET {$setClause} WHERE id = ?";
        $params = array_merge(array_values($data), [$id]);
        
        return $this->db->execute($sql, $params);
    }
    
    /**
     * 记录同步日志
     */
    private function recordSyncLog($type, $success, $errors, $duration, $startTime = null, $endTime = null)
    {
        $data = [
            'sync_type' => $type,
            'platform' => $this->platform,
            'success_count' => $success,
            'error_count' => $errors,
            'duration' => $duration,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'create_time' => date('Y-m-d H:i:s')
        ];
        
        $fields = array_keys($data);
        $placeholders = array_fill(0, count($fields), '?');
        
        $sql = "INSERT INTO dtk_sync_logs (" . implode(', ', $fields) . ") VALUES (" . implode(', ', $placeholders) . ")";
        
        return $this->db->execute($sql, array_values($data));
    }
    
    /**
     * 获取上次同步时间
     */
    private function getLastSyncTime()
    {
        $result = $this->db->fetchOne(
            "SELECT end_time FROM dtk_sync_logs WHERE platform = ? AND sync_type = 'incremental_sync' ORDER BY create_time DESC LIMIT 1",
            [$this->platform]
        );
        
        return $result ? $result['end_time'] : null;
    }
    
    /**
     * 清理过期商品
     */
    public function cleanExpiredGoods($days = 30)
    {
        $cutoffDate = date('Y-m-d H:i:s', strtotime("-{$days} days"));
        
        $result = $this->db->execute(
            "DELETE FROM dtk_goods WHERE platform = ? AND update_time < ?",
            [$this->platform, $cutoffDate]
        );
        
        $this->logger->info('清理过期商品完成', [
            'platform' => $this->platform,
            'cutoffDate' => $cutoffDate,
            'deletedCount' => $result
        ]);
        
        return $result;
    }

    /**
     * 获取智能转链统计信息
     */
    public function getSmartLinkStats()
    {
        return $this->smartLinkService->getLinkStats();
    }

    /**
     * 手动更新商品层级
     */
    public function updateGoodsTiers()
    {
        return $this->smartLinkService->updateGoodsTiers();
    }

    /**
     * 批量预转链热门商品
     */
    public function batchPreConvertHotGoods($limit = 100)
    {
        return $this->smartLinkService->preConvertHotGoods($limit);
    }

    /**
     * 批量转链普通商品
     */
    public function batchConvertNormalGoods($limit = 200)
    {
        return $this->smartLinkService->batchConvertNormalGoods($limit);
    }
}
