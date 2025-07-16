/**
 * 大淘客服务端请求封装
 * 用于小程序端调用大淘客独立服务
 */

// 大淘客智能转链服务端配置
const DATAOKE_CONFIG = {
    // 大淘客服务端地址 - 请根据实际部署地址修改
    baseUrl: 'https://dtkv2.zhishujiaoyu.com/dtk-service-v2/api/',

    // 请求超时时间
    timeout: 15000,

    // 是否显示加载提示
    showLoading: true,

    // API版本
    version: 'v2.0'
};

/**
 * 大淘客请求封装
 */
const dataokeRequest = {
    
    /**
     * 基础请求方法
     * @param {Object} options 请求选项
     */
    request(options) {
        return new Promise((resolve, reject) => {
            // 显示加载提示
            if (options.showLoading !== false && DATAOKE_CONFIG.showLoading) {
                uni.showLoading({
                    title: '加载中...',
                    mask: true
                });
            }
            
            // 发起请求
            uni.request({
                url: DATAOKE_CONFIG.baseUrl + options.url,
                method: options.method || 'GET',
                data: options.data || {},
                timeout: options.timeout || DATAOKE_CONFIG.timeout,
                header: {
                    'Content-Type': 'application/json',
                    ...options.header
                },
                success: (res) => {
                    // 隐藏加载提示
                    if (options.showLoading !== false && DATAOKE_CONFIG.showLoading) {
                        uni.hideLoading();
                    }
                    
                    // 检查响应状态
                    if (res.statusCode === 200) {
                        if (res.data.code === 200) {
                            resolve(res.data);
                        } else {
                            // API业务错误
                            const error = new Error(res.data.message || '请求失败');
                            error.code = res.data.code;
                            reject(error);
                        }
                    } else {
                        // HTTP错误
                        reject(new Error(`HTTP ${res.statusCode}: ${res.data.message || '网络错误'}`));
                    }
                },
                fail: (err) => {
                    // 隐藏加载提示
                    if (options.showLoading !== false && DATAOKE_CONFIG.showLoading) {
                        uni.hideLoading();
                    }
                    
                    console.error('大淘客请求失败:', err);
                    reject(new Error(err.errMsg || '网络连接失败'));
                }
            });
        });
    },
    
    /**
     * 获取商品列表（从数据库）
     * @param {Object} params 请求参数
     * @param {Number} params.page 页码，默认1
     * @param {Number} params.limit 每页数量，默认20
     * @param {String} params.platform 平台，默认taobao
     * @param {Number} params.tier_level 商品层级 1=热门 2=普通 3=冷门
     * @param {Number} params.link_status 转链状态 0=未转链 1=已转链
     * @param {String} params.keyword 搜索关键词
     * @param {String} params.sort 排序方式 sales_desc=销量降序 price_asc=价格升序
     */
    getGoodsList(params = {}) {
        const defaultParams = {
            page: 1,
            limit: 20,
            platform: 'taobao',
            ...params
        };

        const queryParams = new URLSearchParams();
        Object.keys(defaultParams).forEach(key => {
            if (defaultParams[key] !== undefined && defaultParams[key] !== '') {
                queryParams.append(key, defaultParams[key]);
            }
        });

        return this.request({
            url: 'goods.php?' + queryParams.toString(),
            method: 'GET',
            showLoading: params.showLoading
        });
    },
    
    /**
     * 获取商品详情
     * @param {String} goodsId 商品ID
     */
    getGoodsDetail(goodsId) {
        if (!goodsId) {
            return Promise.reject(new Error('商品ID不能为空'));
        }

        return this.request({
            url: `goods.php?action=detail&goods_id=${goodsId}`,
            method: 'GET'
        });
    },

    /**
     * 获取热门商品（Tier 1）
     * @param {Object} params 请求参数
     */
    getHotGoods(params = {}) {
        return this.getGoodsList({
            tier_level: 1,
            link_status: 1, // 只获取已转链的热门商品
            sort: 'sales_desc',
            ...params
        });
    },

    /**
     * 获取优惠商品（有优惠券的商品）
     * @param {Object} params 请求参数
     */
    getCouponGoods(params = {}) {
        return this.request({
            url: 'goods.php?filter=coupon&' + new URLSearchParams({
                page: params.page || 1,
                limit: params.limit || 20,
                platform: params.platform || 'taobao'
            }).toString(),
            method: 'GET',
            showLoading: params.showLoading
        });
    },

    /**
     * 获取转链链接
     * @param {String} goodsId 商品ID
     * @param {String} platform 平台
     */
    getConvertLink(goodsId, platform = 'taobao') {
        if (!goodsId) {
            return Promise.reject(new Error('商品ID不能为空'));
        }

        return this.request({
            url: 'link.php',
            method: 'POST',
            data: {
                goods_id: goodsId,
                platform: platform
            }
        });
    },
    
    /**
     * 获取分类列表
     */
    getCategoryList() {
        return this.request({
            url: 'category.php',
            method: 'GET',
            showLoading: false
        });
    },

    /**
     * 获取同步统计信息
     */
    getSyncStats() {
        return this.request({
            url: 'sync.php?type=stats&platform=taobao',
            method: 'GET',
            showLoading: false
        });
    },
    
    /**
     * 搜索商品
     * @param {String} keyword 搜索关键词
     * @param {Object} params 其他参数
     */
    searchGoods(keyword, params = {}) {
        if (!keyword) {
            return Promise.reject(new Error('搜索关键词不能为空'));
        }

        return this.getGoodsList({
            keyword: keyword,
            page: params.page || 1,
            limit: params.limit || 20,
            platform: params.platform || 'taobao',
            showLoading: params.showLoading
        });
    },
    
    /**
     * 测试服务连接
     */
    testConnection() {
        return this.request({
            url: 'test.php',
            method: 'GET',
            showLoading: false
        });
    }
};

/**
 * 大淘客数据处理工具
 */
const dataokeUtils = {
    
    /**
     * 格式化价格显示
     * @param {Number} price 价格
     */
    formatPrice(price) {
        return parseFloat(price).toFixed(2);
    },
    
    /**
     * 计算优惠信息
     * @param {Object} goods 商品信息
     */
    getDiscountInfo(goods) {
        const originalPrice = parseFloat(goods.original_price || 0);
        const actualPrice = parseFloat(goods.actual_price || goods.price || 0);
        const couponPrice = parseFloat(goods.coupon_price || 0);

        return {
            hasDiscount: originalPrice > actualPrice,
            discountAmount: originalPrice - actualPrice,
            discountPercent: originalPrice > 0 ? Math.round((originalPrice - actualPrice) / originalPrice * 100) : 0,
            hasCoupon: couponPrice > 0,
            finalPrice: actualPrice - couponPrice,
            saveAmount: originalPrice - actualPrice + couponPrice,
            tierLevel: goods.tier_level || 3,
            isConverted: goods.link_status == 1
        };
    },
    
    /**
     * 格式化销量显示
     * @param {Number} saleCount 销量
     */
    formatSaleCount(saleCount) {
        const count = parseInt(saleCount || 0);
        if (count >= 10000) {
            return Math.floor(count / 10000) + '万+';
        } else if (count >= 1000) {
            return Math.floor(count / 1000) + 'k+';
        } else {
            return count + '';
        }
    },

    /**
     * 获取商品层级标签
     * @param {Number} tierLevel 层级
     */
    getTierLabel(tierLevel) {
        const labels = {
            1: { text: '热门', color: '#ff4757' },
            2: { text: '普通', color: '#ffa502' },
            3: { text: '冷门', color: '#747d8c' }
        };
        return labels[tierLevel] || labels[3];
    },

    /**
     * 检查商品是否已转链
     * @param {Object} goods 商品信息
     */
    isConverted(goods) {
        return goods.link_status == 1;
    },
    
    /**
     * 检查优惠券是否有效
     * @param {Object} goods 商品信息
     */
    isCouponValid(goods) {
        if (!goods.coupon_price || goods.coupon_price <= 0) {
            return false;
        }
        
        const now = new Date().getTime();
        const startTime = goods.coupon_start_time ? new Date(goods.coupon_start_time).getTime() : 0;
        const endTime = goods.coupon_end_time ? new Date(goods.coupon_end_time).getTime() : 0;
        
        if (startTime > 0 && now < startTime) {
            return false; // 还未开始
        }
        
        if (endTime > 0 && now > endTime) {
            return false; // 已过期
        }
        
        return true;
    }
};

// 导出模块
export default {
    request: dataokeRequest,
    utils: dataokeUtils,
    config: DATAOKE_CONFIG
};
