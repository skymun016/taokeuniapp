/**
 * 大淘客智能转链服务API封装
 * 用于小程序端调用大淘客独立服务
 */

// 大淘客服务端配置
const DATAOKE_CONFIG = {
    // 大淘客服务端地址 - 请根据实际部署地址修改
    baseUrl: 'https://dtkv2.zhishujiaoyu.com/api/',
    
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
                        if (res.data && res.data.code === 200) {
                            resolve(res.data);
                        } else {
                            // API业务错误
                            const error = new Error(res.data?.message || '请求失败');
                            error.code = res.data?.code || 500;
                            reject(error);
                        }
                    } else if (res.statusCode === 404) {
                        reject(new Error('API接口不存在'));
                    } else if (res.statusCode === 405) {
                        reject(new Error('请求方法不允许'));
                    } else {
                        // HTTP错误
                        reject(new Error(`HTTP ${res.statusCode}: ${res.data?.message || '网络错误'}`));
                    }
                },
                fail: (err) => {
                    // 隐藏加载提示
                    if (options.showLoading !== false && DATAOKE_CONFIG.showLoading) {
                        uni.hideLoading();
                    }

                    console.error('大淘客请求失败:', err);

                    // 根据错误类型提供更友好的错误信息
                    let errorMessage = '网络连接失败';
                    if (err.errMsg) {
                        if (err.errMsg.includes('timeout')) {
                            errorMessage = '请求超时，请检查网络连接';
                        } else if (err.errMsg.includes('fail')) {
                            errorMessage = '网络请求失败，请稍后重试';
                        } else if (err.errMsg.includes('abort')) {
                            errorMessage = '请求被取消';
                        } else {
                            errorMessage = err.errMsg;
                        }
                    }

                    reject(new Error(errorMessage));
                }
            });
        });
    },
    
    /**
     * 获取商品列表（从数据库）
     * @param {Object} params 请求参数
     */
    getGoodsList(params = {}) {
        const defaultParams = {
            page: 1,
            limit: 20,
            platform: 'taobao',
            ...params
        };

        // 构建查询参数 - 兼容小程序环境
        const queryParamsArray = [];
        Object.keys(defaultParams).forEach(key => {
            if (defaultParams[key] !== undefined && defaultParams[key] !== '') {
                queryParamsArray.push(`${encodeURIComponent(key)}=${encodeURIComponent(defaultParams[key])}`);
            }
        });

        return this.request({
            url: 'miniapp_goods_correct.php?' + queryParamsArray.join('&'),
            method: 'GET',
            showLoading: params.showLoading
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
        return this.getGoodsList({
            filter: 'coupon',
            ...params
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
     * 获取同步统计信息
     */
    getSyncStats() {
        return this.request({
            url: 'miniapp_stats_correct.php?platform=taobao',
            method: 'GET',
            showLoading: false
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
    },

    /**
     * 获取商品详情
     * @param {string} goodsId 商品ID
     */
    getGoodsDetail(goodsId) {
        return this.request({
            url: `miniapp_detail.php?goods_id=${encodeURIComponent(goodsId)}`,
            method: 'GET',
            showLoading: true
        });
    },

    /**
     * 搜索商品
     * @param {Object} params 搜索参数
     */
    searchGoods(params = {}) {
        const defaultParams = {
            page: 1,
            limit: 20,
            platform: 'taobao',
            sort: 'relevance',
            ...params
        };

        const queryParamsArray = [];
        Object.keys(defaultParams).forEach(key => {
            if (defaultParams[key] !== undefined && defaultParams[key] !== '') {
                queryParamsArray.push(`${encodeURIComponent(key)}=${encodeURIComponent(defaultParams[key])}`);
            }
        });

        return this.request({
            url: 'miniapp_search.php?' + queryParamsArray.join('&'),
            method: 'GET',
            showLoading: params.showLoading !== false
        });
    },

    /**
     * 转链接口
     * @param {string} goodsId 商品ID
     */
    convertLink(goodsId) {
        return this.request({
            url: 'miniapp_convert.php',
            method: 'POST',
            data: {
                goods_id: goodsId,
                platform: 'taobao'
            },
            showLoading: true
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
        return parseFloat(price || 0).toFixed(2);
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
