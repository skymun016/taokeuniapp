<?php
/**
 * 商品管理页面
 */

try {
    $db = \Services\DatabaseService::getInstance();
    
    // 获取商品列表
    $page = max(1, intval($_GET['p'] ?? 1));
    $limit = 20;
    $offset = ($page - 1) * $limit;
    
    $goods = $db->fetchAll("SELECT * FROM dtk_goods ORDER BY update_time DESC LIMIT {$limit} OFFSET {$offset}");
    $totalCount = $db->fetchOne("SELECT COUNT(*) as count FROM dtk_goods")['count'] ?? 0;
    $totalPages = ceil($totalCount / $limit);

    // 获取今日更新统计
    $todayUpdated = $db->fetchOne("SELECT COUNT(*) as count FROM dtk_goods WHERE DATE(update_time) = CURDATE()")['count'] ?? 0;
    $todayCreated = $db->fetchOne("SELECT COUNT(*) as count FROM dtk_goods WHERE DATE(create_time) = CURDATE()")['count'] ?? 0;
    
} catch (Exception $e) {
    $goods = [];
    $totalCount = 0;
    $totalPages = 0;
    $error = $e->getMessage();
}

?>

<div class="card">
    <h3>商品管理</h3>
    
    <?php if (isset($error)): ?>
        <div style="color: red; margin-bottom: 15px;">错误: <?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    
    <div style="margin-bottom: 15px; display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
        <div style="padding: 10px; background: #f8f9fa; border-radius: 5px; text-align: center;">
            <div style="font-size: 18px; font-weight: bold; color: #2c3e50;"><?php echo number_format($totalCount); ?></div>
            <div style="font-size: 12px; color: #7f8c8d;">总商品数</div>
        </div>
        <div style="padding: 10px; background: #d5f4e6; border-radius: 5px; text-align: center;">
            <div style="font-size: 18px; font-weight: bold; color: #27ae60;"><?php echo number_format($todayUpdated); ?></div>
            <div style="font-size: 12px; color: #27ae60;">今日更新</div>
        </div>
        <div style="padding: 10px; background: #e3f2fd; border-radius: 5px; text-align: center;">
            <div style="font-size: 18px; font-weight: bold; color: #3498db;"><?php echo number_format($todayCreated); ?></div>
            <div style="font-size: 12px; color: #3498db;">今日新增</div>
        </div>
        <div style="padding: 10px; background: #fff3cd; border-radius: 5px; text-align: center;">
            <div style="font-size: 18px; font-weight: bold; color: #856404;">第 <?php echo $page; ?> 页</div>
            <div style="font-size: 12px; color: #856404;">共 <?php echo $totalPages; ?> 页</div>
        </div>
    </div>
    
    <?php if (empty($goods)): ?>
        <p>暂无商品数据</p>
        <a href="/api/goods/sync" class="btn">同步商品数据</a>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th style="width: 300px;">商品名称</th>
                    <th style="width: 120px;">价格信息</th>
                    <th style="width: 100px;">佣金信息</th>
                    <th>销量</th>
                    <th>店铺</th>
                    <th>平台</th>
                    <th>状态</th>
                    <th>更新时间</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($goods as $item): ?>
                <tr>
                    <td><?php echo $item['id']; ?></td>
                    <td><?php echo htmlspecialchars(mb_substr($item['title'] ?? '', 0, 30)); ?>...</td>
                    <td>
                        <?php
                        $originalPrice = $item['original_price'] ?? 0;
                        $actualPrice = $item['actual_price'] ?? 0;
                        $couponPrice = $item['coupon_price'] ?? 0;
                        ?>
                        <div style="font-size: 12px;">
                            <div>原价: ¥<?php echo number_format($originalPrice, 2); ?></div>
                            <?php if ($couponPrice > 0): ?>
                            <div style="color: #e74c3c;">券后: ¥<?php echo number_format($actualPrice, 2); ?></div>
                            <div style="color: #27ae60;">优惠: ¥<?php echo number_format($couponPrice, 2); ?></div>
                            <?php else: ?>
                            <div>售价: ¥<?php echo number_format($actualPrice > 0 ? $actualPrice : $originalPrice, 2); ?></div>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td>
                        <?php
                        $commissionRate = $item['commission_rate'] ?? 0;
                        $basePrice = $actualPrice > 0 ? $actualPrice : $originalPrice;
                        $estimatedCommission = $basePrice * ($commissionRate / 100);
                        ?>
                        <div style="font-size: 12px;">
                            <div><?php echo number_format($commissionRate, 2); ?>%</div>
                            <div style="color: #f39c12;">≈¥<?php echo number_format($estimatedCommission, 2); ?></div>
                        </div>
                    </td>
                    <td>
                        <?php
                        $monthSales = $item['month_sales'] ?? 0;
                        $dailySales = $item['daily_sales'] ?? 0;
                        ?>
                        <div style="font-size: 12px;">
                            <div>月销: <?php echo number_format($monthSales); ?></div>
                            <?php if ($dailySales > 0): ?>
                            <div style="color: #e67e22;">日销: <?php echo number_format($dailySales); ?></div>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td>
                        <?php
                        $shopName = $item['shop_name'] ?? '';
                        $shopType = $item['shop_type'] ?? 0;
                        $shopTypeText = $shopType == 1 ? '天猫' : '淘宝';
                        ?>
                        <div style="font-size: 12px;">
                            <div><?php echo htmlspecialchars(mb_substr($shopName, 0, 15)); ?></div>
                            <div style="color: <?php echo $shopType == 1 ? '#e74c3c' : '#3498db'; ?>;"><?php echo $shopTypeText; ?></div>
                        </div>
                    </td>
                    <td><?php echo htmlspecialchars($item['platform'] ?? ''); ?></td>
                    <td>
                        <?php
                        $isLive = $item['is_live'] ?? 1;
                        $statusText = $isLive ? '有效' : '失效';
                        $statusColor = $isLive ? '#27ae60' : '#e74c3c';
                        ?>
                        <span style="color: <?php echo $statusColor; ?>; font-weight: bold;"><?php echo $statusText; ?></span>
                    </td>
                    <td>
                        <div style="font-size: 12px;">
                            <div><?php echo date('m-d H:i', strtotime($item['update_time'] ?? '')); ?></div>
                            <?php if (date('Y-m-d', strtotime($item['update_time'] ?? '')) == date('Y-m-d')): ?>
                                <div style="color: #27ae60; font-weight: bold;">今日更新</div>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td>
                        <a href="#" class="btn" style="font-size: 12px; padding: 4px 8px;">查看</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <!-- 分页 -->
        <?php if ($totalPages > 1): ?>
        <div style="margin-top: 20px; text-align: center;">
            <?php if ($page > 1): ?>
                <a href="?page=goods&p=<?php echo $page - 1; ?>" class="btn">上一页</a>
            <?php endif; ?>
            
            <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                <?php if ($i == $page): ?>
                    <span style="padding: 8px 12px; background: #3498db; color: white; margin: 0 2px;"><?php echo $i; ?></span>
                <?php else: ?>
                    <a href="?page=goods&p=<?php echo $i; ?>" class="btn" style="margin: 0 2px;"><?php echo $i; ?></a>
                <?php endif; ?>
            <?php endfor; ?>
            
            <?php if ($page < $totalPages): ?>
                <a href="?page=goods&p=<?php echo $page + 1; ?>" class="btn">下一页</a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    <?php endif; ?>
</div>
