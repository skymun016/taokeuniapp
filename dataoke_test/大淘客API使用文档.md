# 大淘客API使用文档

## 📋 目录
1. [环境配置](#环境配置)
2. [基础配置](#基础配置)
3. [核心功能](#核心功能)
4. [代码示例](#代码示例)
5. [常见问题](#常见问题)
6. [功能状态](#功能状态)

## 🔧 环境配置

### 1. 引入官方SDK
```php
require_once 'openapi-sdk-php/vendor/autoload.php';
```

### 2. 基础配置信息
```php
$appKey = '678fc81d72259';
$appSecret = '6fd2acba8bce6c039ab276256f003ced';
$pid = 'mm_52162983_2267550029_112173400498';
$version = 'v1.2.4';
```

## 🎯 核心功能

### 1. 获取商品列表 ✅

**功能说明：** 获取大淘客平台的商品列表，支持多种筛选条件

**代码示例：**
```php
$client = new GetGoodsList();
$client->setAppKey($appKey);
$client->setAppSecret($appSecret);
$client->setVersion($version);

$params = [
    'pageId' => 1,                    // 页码（必填）
    'pageSize' => 20,                 // 每页数量：10,50,100,200
    'sort' => 0,                      // 排序方式
    'couponPriceLowerLimit' => 10,    // 最低优惠券面额
    'tmall' => 1,                     // 1-天猫商品，0-所有商品
    'juHuaSuan' => 0,                 // 1-聚划算商品
    'taoQiangGou' => 0,               // 1-淘抢购商品
    'priceLowerLimit' => 0,           // 价格下限
    'priceUpperLimit' => 0,           // 价格上限
    'commissionRateLowerLimit' => 0,  // 最低佣金比率
];

$result = $client->setParams($params)->request();
$data = json_decode($result, true);
```

**排序方式说明：**
- `0` - 综合排序
- `1` - 商品上架时间从高到低
- `2` - 销量从高到低
- `3` - 领券量从高到低
- `4` - 佣金比例从高到低
- `5` - 价格（券后价）从高到低
- `6` - 价格（券后价）从低到高

**返回数据结构：**
```php
[
    'code' => 0,
    'data' => [
        'list' => [
            [
                'goodsId' => '商品ID',
                'title' => '商品标题',
                'originalPrice' => 50.75,      // 原价
                'actualPrice' => 19.75,        // 券后价
                'couponPrice' => 31,           // 优惠券面额
                'couponConditions' => '50',    // 使用条件
                'couponId' => '优惠券ID',
                'couponLink' => '优惠券领取链接',
                'couponShare' => '优惠券分享链接',
                'itemLink' => '商品链接',
                'mainPic' => '商品主图',
                'shopName' => '店铺名称',
                'monthSales' => 80000,         // 月销量
                'commissionRate' => 15,        // 佣金比例
                // ... 更多字段
            ]
        ]
    ]
]
```

### 2. 获取商品详情 ✅

**功能说明：** 根据商品ID获取商品的详细信息

**代码示例：**
```php
$client = new GetGoodsDetails();
$client->setAppKey($appKey);
$client->setAppSecret($appSecret);
$client->setVersion($version);

$params = [
    'goodsId' => $goodsId  // 商品ID（必填）
];

$result = $client->setParams($params)->request();
$data = json_decode($result, true);
```

### 3. 搜索商品 ✅

**功能说明：** 根据关键词搜索商品

**代码示例：**
```php
$client = new GetDtkSearchGoods();
$client->setAppKey($appKey);
$client->setAppSecret($appSecret);
$client->setVersion($version);

$params = [
    'keyWords' => '搜索关键词',  // 必填
    'pageId' => 1,
    'pageSize' => 20,
    'sort' => 0
];

$result = $client->setParams($params)->request();
$data = json_decode($result, true);
```

### 4. 获取超级分类 ✅

**功能说明：** 获取大淘客的商品分类列表

**代码示例：**
```php
$client = new GetSuperCategory();
$client->setAppKey($appKey);
$client->setAppSecret($appSecret);
$client->setVersion($version);

$result = $client->setParams([])->request();
$data = json_decode($result, true);
```

### 5. 解析淘口令 ✅

**功能说明：** 解析淘口令获取商品信息

**代码示例：**
```php
$client = new GetParseTpwd();
$client->setAppKey($appKey);
$client->setAppSecret($appSecret);
$client->setVersion($version);

$params = [
    'content' => '淘口令内容'  // 必填
];

$result = $client->setParams($params)->request();
$data = json_decode($result, true);
```

## 💰 优惠券信息获取

通过商品列表API可以直接获取完整的优惠券信息：

```php
// 从商品列表中获取优惠券信息
$goods = $goodsList['data']['list'][0];

$couponInfo = [
    'couponPrice' => $goods['couponPrice'],           // 优惠券面额：¥31
    'couponConditions' => $goods['couponConditions'], // 使用条件：满¥50可用
    'couponId' => $goods['couponId'],                 // 优惠券ID
    'couponStartTime' => $goods['couponStartTime'],   // 开始时间
    'couponEndTime' => $goods['couponEndTime'],       // 结束时间
    'couponRemainCount' => $goods['couponRemainCount'], // 剩余数量
    'couponTotalNum' => $goods['couponTotalNum'],     // 总数量
    'couponReceiveNum' => $goods['couponReceiveNum'], // 已领取数量
    'couponLink' => $goods['couponLink'],             // 领取链接
    'couponShare' => $goods['couponShare'],           // 分享链接
];
```

## 🚫 受限功能

### 1. 生成淘口令 ❌ (需要特殊权限)

```php
$client = new GetCreateTkl();
$client->setAppKey($appKey);
$client->setAppSecret($appSecret);
$client->setVersion($version);

$params = [
    'text' => '商品标题',
    'url' => '商品链接'
];

$result = $client->setParams($params)->request();
// 返回：生成淘口令失败 - 需要特殊权限
```

### 2. 生成推广链接 ❌ (需要渠道专属推广位)

```php
$client = new GetPrivilegeLink();
$client->setAppKey($appKey);
$client->setAppSecret($appSecret);
$client->setVersion($version);

$params = [
    'goodsId' => $goodsId,
    'pid' => $pid,
    'channelId' => 'common'
];

$result = $client->setParams($params)->request();
// 返回：当前pid非渠道专属推广位
```

## 📊 功能状态总览

| 功能 | 状态 | 说明 |
|------|------|------|
| 获取商品列表 | ✅ 可用 | 支持多种筛选条件 |
| 获取商品详情 | ✅ 可用 | 根据商品ID获取详情 |
| 搜索商品 | ✅ 可用 | 关键词搜索 |
| 获取分类 | ✅ 可用 | 获取超级分类列表 |
| 解析淘口令 | ✅ 可用 | 解析现有淘口令 |
| 优惠券信息 | ✅ 可用 | 完整的优惠券数据 |
| 生成淘口令 | ❌ 受限 | 需要特殊权限 |
| 生成推广链接 | ❌ 受限 | 需要渠道专属推广位 |

## 🔍 常见问题

### Q1: 如何获取有优惠券的商品？
A: 在获取商品列表时设置 `couponPriceLowerLimit` 参数：
```php
$params = ['couponPriceLowerLimit' => 10]; // 只要10元以上优惠券的商品
```

### Q2: 如何获取天猫商品？
A: 设置 `tmall` 参数为 1：
```php
$params = ['tmall' => 1]; // 只要天猫商品
```

### Q3: 为什么生成淘口令失败？
A: 生成淘口令需要特殊的API权限，当前账号暂不支持此功能。

### Q4: 如何获取推广佣金？
A: 虽然无法生成专属推广链接，但可以使用商品列表中提供的 `couponShare` 链接进行推广。

## 📝 使用建议

1. **优先使用商品列表API** - 包含了最完整的商品和优惠券信息
2. **合理设置筛选条件** - 使用优惠券面额、价格区间等条件筛选高质量商品
3. **利用现有推广链接** - 使用API返回的 `couponShare` 和 `itemLink` 进行推广
4. **定期更新商品数据** - 商品价格和优惠券信息会实时变化

---

**文档版本：** v1.0  
**更新时间：** 2025-07-16  
**API版本：** v1.2.4
