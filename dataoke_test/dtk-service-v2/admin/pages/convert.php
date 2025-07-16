<?php
/**
 * 转链测试页面
 */

// 引入配置文件
require_once dirname(__DIR__, 2) . '/vendor/autoload.php';

use Services\LinkConvertService;
use Utils\Logger;
use Utils\Helper;

$logger = new Logger();
$message = '';
$messageType = '';
$convertResult = null;

// 处理转链测试请求
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $action = $_POST['action'] ?? '';
        $platform = $_POST['platform'] ?? 'taobao';
        
        $linkConvertService = new LinkConvertService($platform);
        
        switch ($action) {
            case 'privilege_link':
                $goodsId = $_POST['goods_id'] ?? '';
                if (empty($goodsId)) {
                    throw new \Exception('商品ID不能为空');
                }
                
                $params = ['goodsId' => $goodsId];
                if (!empty($_POST['pid'])) {
                    $params['pid'] = $_POST['pid'];
                }
                if (!empty($_POST['coupon_id'])) {
                    $params['couponId'] = $_POST['coupon_id'];
                }
                
                $convertResult = $linkConvertService->convertToPrivilegeLink($params);
                $message = '高效转链成功';
                $messageType = 'success';
                break;
                
            case 'taokouling':
                $text = $_POST['text'] ?? '';
                $url = $_POST['url'] ?? '';
                $goodsId = $_POST['goods_id_tkl'] ?? '';
                
                if (empty($text) && empty($url) && empty($goodsId)) {
                    throw new \Exception('文案、链接或商品ID至少填写一项');
                }
                
                $params = [];
                if (!empty($text)) $params['text'] = $text;
                if (!empty($url)) $params['url'] = $url;
                
                // 如果有商品ID但没有URL，先转链
                if (!empty($goodsId) && empty($url)) {
                    $privilegeResult = $linkConvertService->convertToPrivilegeLink(['goodsId' => $goodsId]);
                    $params['url'] = $privilegeResult['itemUrl'] ?? $privilegeResult['couponClickUrl'] ?? '';
                }
                
                $convertResult = $linkConvertService->createTaokouling($params);
                $message = '淘口令生成成功';
                $messageType = 'success';
                break;
                
            case 'smart_convert':
                $goodsId = $_POST['goods_id_smart'] ?? '';
                $needTaokouling = !empty($_POST['need_taokouling']);
                $taokoulingText = $_POST['taokouling_text'] ?? '';
                
                if (empty($goodsId)) {
                    throw new \Exception('商品ID不能为空');
                }
                
                $params = [
                    'goodsId' => $goodsId,
                    'needTaokouling' => $needTaokouling,
                    'taokoulingText' => $taokoulingText
                ];
                
                $convertResult = $linkConvertService->smartConvert($params);
                $message = '智能转链成功';
                $messageType = 'success';
                break;
                
            case 'batch_convert':
                $goodsIds = $_POST['goods_ids_batch'] ?? '';
                if (empty($goodsIds)) {
                    throw new \Exception('商品ID列表不能为空');
                }
                
                $goodsIds = explode(',', $goodsIds);
                $goodsIds = array_map('trim', $goodsIds);
                $goodsIds = array_filter($goodsIds);
                
                if (count($goodsIds) > 10) {
                    throw new \Exception('测试环境最多支持10个商品批量转链');
                }
                
                $options = [
                    'needTaokouling' => !empty($_POST['batch_need_taokouling'])
                ];
                
                $convertResult = $linkConvertService->batchConvert($goodsIds, $options);
                $message = '批量转链完成';
                $messageType = 'success';
                break;

            case 'materials':
                $goodsId = $_POST['goods_id_materials'] ?? '';
                if (empty($goodsId)) {
                    throw new \Exception('商品ID不能为空');
                }

                $params = ['goodsId' => $goodsId];

                $convertResult = $linkConvertService->getGoodsMaterials($params);
                $message = '获取商品素材成功';
                $messageType = 'success';
                break;

            case 'enhanced':
                $goodsId = $_POST['goods_id_enhanced'] ?? '';
                $includeMaterials = !empty($_POST['include_materials']);
                $needTaokouling = !empty($_POST['enhanced_need_taokouling']);
                $taokoulingText = $_POST['enhanced_taokouling_text'] ?? '';

                if (empty($goodsId)) {
                    throw new \Exception('商品ID不能为空');
                }

                $params = [
                    'goodsId' => $goodsId,
                    'includeMaterials' => $includeMaterials,
                    'needTaokouling' => $needTaokouling,
                    'taokoulingText' => $taokoulingText
                ];

                $convertResult = $linkConvertService->enhancedConvert($params);
                $message = '增强转链成功';
                $messageType = 'success';
                break;

            default:
                throw new \Exception('未知的操作类型');
        }
        
    } catch (\Exception $e) {
        $message = '操作失败: ' . $e->getMessage();
        $messageType = 'error';
        $logger->error('转链测试失败', [
            'action' => $action ?? '',
            'error' => $e->getMessage()
        ]);
    }
}

// 获取启用的平台列表
$enabledPlatforms = Helper::getEnabledPlatforms();
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>转链测试 - 大淘客管理后台</title>
    <style>
        .container { max-width: 1200px; margin: 0 auto; padding: 20px; }
        .card { background: white; border-radius: 8px; padding: 20px; margin-bottom: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-group input, .form-group select, .form-group textarea { 
            width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; 
        }
        .form-group textarea { height: 80px; resize: vertical; }
        .btn { padding: 10px 20px; background: #007cba; color: white; border: none; border-radius: 4px; cursor: pointer; }
        .btn:hover { background: #005a87; }
        .btn-secondary { background: #6c757d; }
        .btn-secondary:hover { background: #545b62; }
        .alert { padding: 15px; margin-bottom: 20px; border-radius: 4px; }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .result-box { background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 4px; padding: 15px; margin-top: 15px; }
        .result-item { margin-bottom: 10px; }
        .result-item strong { color: #495057; }
        .tabs { border-bottom: 1px solid #dee2e6; margin-bottom: 20px; }
        .tab { display: inline-block; padding: 10px 20px; cursor: pointer; border-bottom: 2px solid transparent; }
        .tab.active { border-bottom-color: #007cba; color: #007cba; }
        .tab-content { display: none; }
        .tab-content.active { display: block; }
        .checkbox-group { display: flex; align-items: center; gap: 10px; }
        .checkbox-group input[type="checkbox"] { width: auto; }
    </style>
</head>
<body>
    <div class="container">
        <h1>转链功能测试</h1>
        
        <?php if ($message): ?>
            <div class="alert alert-<?php echo $messageType; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        
        <div class="tabs">
            <div class="tab active" onclick="showTab('privilege')">高效转链</div>
            <div class="tab" onclick="showTab('taokouling')">淘口令生成</div>
            <div class="tab" onclick="showTab('smart')">智能转链</div>
            <div class="tab" onclick="showTab('batch')">批量转链</div>
            <div class="tab" onclick="showTab('materials')">商品素材</div>
            <div class="tab" onclick="showTab('enhanced')">增强转链</div>
        </div>
        
        <!-- 高效转链 -->
        <div id="privilege-tab" class="tab-content active">
            <div class="card">
                <h3>高效转链测试</h3>
                <form method="post">
                    <input type="hidden" name="action" value="privilege_link">
                    
                    <div class="form-group">
                        <label>平台:</label>
                        <select name="platform">
                            <?php foreach ($enabledPlatforms as $platform): ?>
                                <option value="<?php echo $platform; ?>"><?php echo ucfirst($platform); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>商品ID:</label>
                        <input type="text" name="goods_id" placeholder="请输入商品ID" required>
                    </div>
                    
                    <div class="form-group">
                        <label>PID (可选):</label>
                        <input type="text" name="pid" placeholder="推广位ID">
                    </div>
                    
                    <div class="form-group">
                        <label>优惠券ID (可选):</label>
                        <input type="text" name="coupon_id" placeholder="优惠券ID">
                    </div>
                    
                    <button type="submit" class="btn">开始转链</button>
                </form>
            </div>
        </div>
        
        <!-- 淘口令生成 -->
        <div id="taokouling-tab" class="tab-content">
            <div class="card">
                <h3>淘口令生成测试</h3>
                <form method="post">
                    <input type="hidden" name="action" value="taokouling">
                    
                    <div class="form-group">
                        <label>平台:</label>
                        <select name="platform">
                            <?php foreach ($enabledPlatforms as $platform): ?>
                                <option value="<?php echo $platform; ?>"><?php echo ucfirst($platform); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>商品ID (可选，如果填写会自动获取推广链接):</label>
                        <input type="text" name="goods_id_tkl" placeholder="商品ID">
                    </div>
                    
                    <div class="form-group">
                        <label>推广链接 (可选):</label>
                        <input type="url" name="url" placeholder="https://...">
                    </div>
                    
                    <div class="form-group">
                        <label>淘口令文案:</label>
                        <textarea name="text" placeholder="请输入淘口令文案，如：好物推荐"></textarea>
                    </div>
                    
                    <button type="submit" class="btn">生成淘口令</button>
                </form>
            </div>
        </div>
        
        <!-- 智能转链 -->
        <div id="smart-tab" class="tab-content">
            <div class="card">
                <h3>智能转链测试</h3>
                <form method="post">
                    <input type="hidden" name="action" value="smart_convert">
                    
                    <div class="form-group">
                        <label>平台:</label>
                        <select name="platform">
                            <?php foreach ($enabledPlatforms as $platform): ?>
                                <option value="<?php echo $platform; ?>"><?php echo ucfirst($platform); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>商品ID:</label>
                        <input type="text" name="goods_id_smart" placeholder="请输入商品ID" required>
                    </div>
                    
                    <div class="form-group checkbox-group">
                        <input type="checkbox" name="need_taokouling" id="need_taokouling">
                        <label for="need_taokouling">同时生成淘口令</label>
                    </div>
                    
                    <div class="form-group">
                        <label>淘口令文案 (可选):</label>
                        <input type="text" name="taokouling_text" placeholder="留空将自动生成">
                    </div>
                    
                    <button type="submit" class="btn">智能转链</button>
                </form>
            </div>
        </div>
        
        <!-- 批量转链 -->
        <div id="batch-tab" class="tab-content">
            <div class="card">
                <h3>批量转链测试</h3>
                <form method="post">
                    <input type="hidden" name="action" value="batch_convert">
                    
                    <div class="form-group">
                        <label>平台:</label>
                        <select name="platform">
                            <?php foreach ($enabledPlatforms as $platform): ?>
                                <option value="<?php echo $platform; ?>"><?php echo ucfirst($platform); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>商品ID列表 (用逗号分隔，测试环境最多10个):</label>
                        <textarea name="goods_ids_batch" placeholder="例如：123456,789012,345678" required></textarea>
                    </div>
                    
                    <div class="form-group checkbox-group">
                        <input type="checkbox" name="batch_need_taokouling" id="batch_need_taokouling">
                        <label for="batch_need_taokouling">同时生成淘口令</label>
                    </div>
                    
                    <button type="submit" class="btn">批量转链</button>
                </form>
            </div>
        </div>

        <!-- 商品素材 -->
        <div id="materials-tab" class="tab-content">
            <div class="card">
                <h3>商品素材获取测试</h3>
                <form method="post">
                    <input type="hidden" name="action" value="materials">

                    <div class="form-group">
                        <label>平台:</label>
                        <select name="platform">
                            <?php foreach ($enabledPlatforms as $platform): ?>
                                <option value="<?php echo $platform; ?>"><?php echo ucfirst($platform); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>商品ID:</label>
                        <input type="text" name="goods_id_materials" placeholder="请输入商品ID" required>
                    </div>

                    <button type="submit" class="btn">获取商品素材</button>
                </form>
            </div>
        </div>

        <!-- 增强转链 -->
        <div id="enhanced-tab" class="tab-content">
            <div class="card">
                <h3>增强转链测试</h3>
                <form method="post">
                    <input type="hidden" name="action" value="enhanced">

                    <div class="form-group">
                        <label>平台:</label>
                        <select name="platform">
                            <?php foreach ($enabledPlatforms as $platform): ?>
                                <option value="<?php echo $platform; ?>"><?php echo ucfirst($platform); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>商品ID:</label>
                        <input type="text" name="goods_id_enhanced" placeholder="请输入商品ID" required>
                    </div>

                    <div class="form-group checkbox-group">
                        <input type="checkbox" name="include_materials" id="include_materials">
                        <label for="include_materials">包含商品素材</label>
                    </div>

                    <div class="form-group checkbox-group">
                        <input type="checkbox" name="enhanced_need_taokouling" id="enhanced_need_taokouling">
                        <label for="enhanced_need_taokouling">生成淘口令</label>
                    </div>

                    <div class="form-group">
                        <label>淘口令文案 (可选):</label>
                        <input type="text" name="enhanced_taokouling_text" placeholder="留空将自动生成">
                    </div>

                    <button type="submit" class="btn">增强转链</button>
                </form>
            </div>
        </div>

        <?php if ($convertResult): ?>
            <div class="card">
                <h3>转链结果</h3>
                <div class="result-box">
                    <pre><?php echo json_encode($convertResult, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT); ?></pre>
                </div>
            </div>
        <?php endif; ?>
    </div>
    
    <script>
        function showTab(tabName) {
            // 隐藏所有标签页内容
            const contents = document.querySelectorAll('.tab-content');
            contents.forEach(content => content.classList.remove('active'));
            
            // 移除所有标签的激活状态
            const tabs = document.querySelectorAll('.tab');
            tabs.forEach(tab => tab.classList.remove('active'));
            
            // 显示选中的标签页内容
            document.getElementById(tabName + '-tab').classList.add('active');
            
            // 激活选中的标签
            event.target.classList.add('active');
        }
    </script>
</body>
</html>
