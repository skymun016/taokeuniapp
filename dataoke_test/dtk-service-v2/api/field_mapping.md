# 数据库字段映射文档

## 问题说明
API文件中使用的字段名与实际数据库表中的字段名不匹配，需要进行映射修正。

## 字段映射表

### dtk_goods 表字段映射

| API中使用的字段名 | 实际数据库字段名 | 说明 |
|------------------|-----------------|------|
| `price` | `actual_price` | ❌ 实际价格 |
| `coupon_amount` | `coupon_price` | ❌ 优惠券金额 |
| `image` | `main_pic` | ❌ 主图片 |
| `category_id` | `cid` | ❌ 分类ID |
| `month_sales` | `month_sales` | ✅ 正确 |
| `tier_level` | `tier_level` | ✅ 正确 |
| `link_status` | `link_status` | ✅ 正确 |
| `goods_id` | `goods_id` | ✅ 正确 |
| `title` | `title` | ✅ 正确 |
| `original_price` | `original_price` | ✅ 正确 |
| `shop_name` | `shop_name` | ✅ 正确 |
| `create_time` | `create_time` | ✅ 正确 |
| `update_time` | `update_time` | ✅ 正确 |

### 优惠券相关字段（实际存在的）
- `coupon_price` - 优惠券金额
- `coupon_conditions` - 优惠券使用条件
- `coupon_start_time` - 优惠券开始时间
- `coupon_end_time` - 优惠券结束时间
- `coupon_total_num` - 优惠券总数量
- `coupon_remain_count` - 优惠券剩余数量
- `coupon_link` - 优惠券链接
- `coupon_click_url` - 优惠券点击链接

## 修复指南

### 1. 需要修复的API文件
- `miniapp_goods.php`
- `miniapp_detail.php`
- `miniapp_search.php`
- `miniapp_convert.php`
- `miniapp_stats.php`

### 2. 修复步骤
1. 将所有 `coupon_amount` 替换为 `coupon_price`
2. 确保其他字段名正确
3. 更新响应数据格式化函数中的字段映射

### 3. 响应数据格式化
在返回给前端的数据中，可以将 `coupon_price` 重新映射为 `coupon_amount` 以保持API接口的一致性：

```php
'coupon_amount' => intval($goods['coupon_price']),
```

## 测试文件
- `miniapp_stats_fixed.php` - 已修复的统计API
- 可以作为其他API文件修复的参考模板
