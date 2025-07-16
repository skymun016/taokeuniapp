<?php
/**
 * 大淘客API使用指南
 * 包含商品列表、商品详情、生成口令等核心功能的正确调用方法
 */

require_once 'openapi-sdk-php/vendor/autoload.php';

class DataokeAPIGuide
{
    private $appKey;
    private $appSecret;
    private $version;
    private $pid;

    public function __construct($appKey, $appSecret, $pid, $version = 'v1.2.4')
    {
        $this->appKey = $appKey;
        $this->appSecret = $appSecret;
        $this->pid = $pid;
        $this->version = $version;
    }

    /**
     * 1. 获取商品列表 - 核心功能
     * @param array $params 查询参数
     * @return array
     */
    public function getGoodsList($params = [])
    {
        $client = new GetGoodsList();
        $client->setAppKey($this->appKey);
        $client->setAppSecret($this->appSecret);
        $client->setVersion($this->version);

        // 默认参数
        $defaultParams = [
            'pageId' => 1,           // 页码，必填
            'pageSize' => 20,        // 每页数量，可选：10,50,100,200
            'sort' => 0,             // 排序：0-综合，1-上架时间，2-销量，3-领券量，4-佣金比例，5-价格高到低，6-价格低到高
            'cids' => '',            // 一级分类ID，多个用逗号分隔
            'subcid' => '',          // 二级分类ID
            'tmall' => 0,            // 1-天猫商品，0-所有商品
            'juHuaSuan' => 0,        // 1-聚划算商品，0-所有商品
            'taoQiangGou' => 0,      // 1-淘抢购商品，0-所有商品
            'couponPriceLowerLimit' => 0,  // 最低优惠券面额
            'priceLowerLimit' => 0,  // 价格下限
            'priceUpperLimit' => 0,  // 价格上限
            'commissionRateLowerLimit' => 0, // 最低佣金比率
            'monthSalesLowerLimit' => 0,     // 最低月销量
        ];

        $params = array_merge($defaultParams, $params);
        $result = $client->setParams($params)->request();
        return json_decode($result, true);
    }

    /**
     * 2. 获取商品详情
     * @param string $goodsId 商品ID
     * @return array
     */
    public function getGoodsDetails($goodsId)
    {
        $client = new GetGoodsDetails();
        $client->setAppKey($this->appKey);
        $client->setAppSecret($this->appSecret);
        $client->setVersion($this->version);

        $params = [
            'goodsId' => $goodsId  // 商品ID，必填
        ];

        $result = $client->setParams($params)->request();
        return json_decode($result, true);
    }

    /**
     * 3. 生成淘口令 - 需要特殊权限
     * @param string $text 口令文案
     * @param string $url 商品链接
     * @return array
     */
    public function createTkl($text, $url)
    {
        $client = new GetCreateTkl();
        $client->setAppKey($this->appKey);
        $client->setAppSecret($this->appSecret);
        $client->setVersion($this->version);

        $params = [
            'text' => $text,  // 口令文案，必填
            'url' => $url     // 商品链接，必填
        ];

        $result = $client->setParams($params)->request();
        return json_decode($result, true);
    }

    /**
     * 4. 获取超级分类
     * @return array
     */
    public function getSuperCategory()
    {
        $client = new GetSuperCategory();
        $client->setAppKey($this->appKey);
        $client->setAppSecret($this->appSecret);
        $client->setVersion($this->version);

        $result = $client->setParams([])->request();
        return json_decode($result, true);
    }

    /**
     * 5. 搜索商品
     * @param string $keyword 搜索关键词
     * @param array $params 其他参数
     * @return array
     */
    public function searchGoods($keyword, $params = [])
    {
        $client = new GetDtkSearchGoods();
        $client->setAppKey($this->appKey);
        $client->setAppSecret($this->appSecret);
        $client->setVersion($this->version);

        $defaultParams = [
            'keyWords' => $keyword,  // 搜索关键词，必填
            'pageId' => 1,
            'pageSize' => 20,
            'sort' => 0
        ];

        $params = array_merge($defaultParams, $params);
        $result = $client->setParams($params)->request();
        return json_decode($result, true);
    }

    /**
     * 6. 生成推广链接 - 需要渠道专属推广位
     * @param string $goodsId 商品ID
     * @param string $couponId 优惠券ID（可选）
     * @return array
     */
    public function getPrivilegeLink($goodsId, $couponId = '')
    {
        $client = new GetPrivilegeLink();
        $client->setAppKey($this->appKey);
        $client->setAppSecret($this->appSecret);
        $client->setVersion($this->version);

        $params = [
            'goodsId' => $goodsId,
            'pid' => $this->pid,
            'channelId' => 'common'
        ];

        if (!empty($couponId)) {
            $params['couponId'] = $couponId;
        }

        $result = $client->setParams($params)->request();
        return json_decode($result, true);
    }

    /**
     * 7. 解析淘口令
     * @param string $content 淘口令内容
     * @return array
     */
    public function parseTpwd($content)
    {
        $client = new GetParseTpwd();
        $client->setAppKey($this->appKey);
        $client->setAppSecret($this->appSecret);
        $client->setVersion($this->version);

        $params = [
            'content' => $content  // 淘口令内容，必填
        ];

        $result = $client->setParams($params)->request();
        return json_decode($result, true);
    }

    /**
     * 8. 获取9.9包邮商品
     * @param array $params 查询参数
     * @return array
     */
    public function getNineGoods($params = [])
    {
        $client = new GetNineOpGoodsList();
        $client->setAppKey($this->appKey);
        $client->setAppSecret($this->appSecret);
        $client->setVersion($this->version);

        $defaultParams = [
            'pageId' => 1,
            'pageSize' => 20,
            'nineCid' => 1  // 9.9包邮分类ID，必填
        ];

        $params = array_merge($defaultParams, $params);
        $result = $client->setParams($params)->request();
        return json_decode($result, true);
    }
}

// 使用示例
echo "=== 大淘客API使用指南 ===\n\n";

// 配置信息
$appKey = '678fc81d72259';
$appSecret = '6fd2acba8bce6c039ab276256f003ced';
$pid = 'mm_52162983_2267550029_112173400498';

// 创建API实例
$api = new DataokeAPIGuide($appKey, $appSecret, $pid);

// 示例1：获取商品列表
echo "1. 获取商品列表示例\n";
$goodsList = $api->getGoodsList([
    'pageId' => 1,
    'pageSize' => 5,
    'couponPriceLowerLimit' => 10  // 只要有10元以上优惠券的商品
]);

if ($goodsList && $goodsList['code'] == 0) {
    echo "✅ 成功获取商品列表，共 " . count($goodsList['data']['list']) . " 个商品\n";
    $firstGoods = $goodsList['data']['list'][0];
    echo "第一个商品：" . $firstGoods['title'] . "\n";
    echo "原价：¥" . $firstGoods['originalPrice'] . "，券后价：¥" . $firstGoods['actualPrice'] . "\n";
    echo "优惠券：¥" . $firstGoods['couponPrice'] . "\n\n";
    
    // 示例2：获取商品详情
    echo "2. 获取商品详情示例\n";
    $goodsDetail = $api->getGoodsDetails($firstGoods['goodsId']);
    if ($goodsDetail && $goodsDetail['code'] == 0) {
        echo "✅ 成功获取商品详情\n";
        echo "商品描述：" . mb_substr($goodsDetail['data']['desc'], 0, 50) . "...\n\n";
    } else {
        echo "❌ 获取商品详情失败：" . ($goodsDetail['msg'] ?? '未知错误') . "\n\n";
    }
    
    // 示例3：尝试生成淘口令
    echo "3. 生成淘口令示例\n";
    $tkl = $api->createTkl($firstGoods['title'], $firstGoods['itemLink']);
    if ($tkl && $tkl['code'] == 0) {
        echo "✅ 成功生成淘口令：" . $tkl['data'] . "\n\n";
    } else {
        echo "❌ 生成淘口令失败：" . ($tkl['msg'] ?? '需要特殊权限') . "\n\n";
    }
    
} else {
    echo "❌ 获取商品列表失败：" . ($goodsList['msg'] ?? '未知错误') . "\n";
}

// 示例4：获取分类
echo "4. 获取超级分类示例\n";
$categories = $api->getSuperCategory();
if ($categories && $categories['code'] == 0) {
    echo "✅ 成功获取分类，共 " . count($categories['data']) . " 个分类\n";
    echo "分类列表：";
    foreach (array_slice($categories['data'], 0, 5) as $category) {
        echo $category['cname'] . " ";
    }
    echo "\n\n";
} else {
    echo "❌ 获取分类失败：" . ($categories['msg'] ?? '未知错误') . "\n\n";
}

// 示例5：搜索商品
echo "5. 搜索商品示例\n";
$searchResult = $api->searchGoods('手机', ['pageSize' => 3]);
if ($searchResult && $searchResult['code'] == 0) {
    echo "✅ 搜索成功，找到 " . count($searchResult['data']['list']) . " 个相关商品\n";
    if (!empty($searchResult['data']['list'])) {
        echo "第一个搜索结果：" . $searchResult['data']['list'][0]['title'] . "\n";
    }
} else {
    echo "❌ 搜索失败：" . ($searchResult['msg'] ?? '未知错误') . "\n";
}

echo "\n=== 使用指南完成 ===\n";
