<template>
	<view class="container">
		<!--header-->
		<view class="tui-header-box">
			<view class="tui-header" :style="{ width: width + 'px', height: height + 'px' }">
				<view class="tui-back" :style="{ marginTop: arrowTop + 'px' }" @tap="back">
					<tui-icon name="arrowleft" color="#fff"></tui-icon>
				</view>
				<view class="tui-title" :style="{ marginTop: titleTop + 'px' }">
					<text>商品详情</text>
				</view>
				<view class="tui-share" :style="{ marginTop: arrowTop + 'px' }" @tap="share">
					<tui-icon name="share" color="#fff"></tui-icon>
				</view>
			</view>
		</view>
		<!--header-->

		<scroll-view scroll-y class="tui-scroll-view" :style="{ marginTop: height + 'px' }">
			<!-- 商品图片 -->
			<view class="tui-goods-images">
				<swiper class="tui-swiper" :indicator-dots="true" :autoplay="false" :circular="true"
					indicator-color="rgba(255, 255, 255, 0.5)" indicator-active-color="#fff">
					<swiper-item>
						<image :src="goodsInfo.pic" class="tui-goods-image" mode="aspectFill" />
					</swiper-item>
				</swiper>
				
				<!-- 去掉标签组 -->
			</view>

			<!-- 商品信息 -->
			<view class="tui-goods-info">
				<view class="tui-goods-title">{{ goodsInfo.title }}</view>
				
				<!-- 价格信息 -->
				<view class="tui-price-section">
					<view class="tui-price-main">
						<text class="tui-price-symbol">¥</text>
						<text class="tui-price-num">{{ formatPrice(goodsInfo.actual_price) }}</text>
						<text v-if="goodsInfo.original_price > goodsInfo.actual_price" 
							class="tui-price-original">¥{{ formatPrice(goodsInfo.original_price) }}</text>
					</view>
					
					<!-- 优惠券信息 - 简化版 -->
					<view v-if="goodsInfo.coupon_price > 0" class="tui-coupon-section">
						<view class="tui-coupon-card-simple">
							<text class="tui-coupon-label">优惠券</text>
							<text class="tui-coupon-price">¥{{ goodsInfo.coupon_price }}</text>
						</view>
					</view>
				</view>

				<!-- 商品属性 -->
				<view class="tui-goods-attrs">
					<view class="tui-attr-item">
						<text class="tui-attr-label">月销量</text>
						<text class="tui-attr-value">{{ formatSaleCount(goodsInfo.month_sales) }}件</text>
					</view>
					<view class="tui-attr-item">
						<text class="tui-attr-label">店铺</text>
						<text class="tui-attr-value">{{ goodsInfo.shop_name || '官方店铺' }}</text>
					</view>
					<view class="tui-attr-item">
						<text class="tui-attr-label">平台</text>
						<text class="tui-attr-value">{{ goodsInfo.shop_type == 1 ? '天猫' : '淘宝' }}</text>
					</view>
					<view class="tui-attr-item">
						<text class="tui-attr-label">商品层级</text>
						<text class="tui-attr-value" :style="{ color: getTierColor(goodsInfo.tier_level) }">
							{{ getTierLabel(goodsInfo.tier_level) }}
						</text>
					</view>
				</view>
			</view>

			<!-- 转链信息 -->
			<view v-if="linkInfo" class="tui-link-section">
				<view class="tui-section-title">
					<tui-icon name="link" color="#e41f19"></tui-icon>
					<text>转链信息</text>
				</view>
				<view class="tui-link-content">
					<view class="tui-link-item">
						<text class="tui-link-label">淘口令</text>
						<text class="tui-link-value" @tap="copyText(linkInfo.tpwd)">{{ linkInfo.tpwd }}</text>
					</view>
					<view class="tui-link-item">
						<text class="tui-link-label">优惠券链接</text>
						<text class="tui-link-value" @tap="copyText(linkInfo.couponClickUrl)">点击复制</text>
					</view>
				</view>
			</view>

			<!-- 商品描述 -->
			<view class="tui-goods-desc">
				<view class="tui-section-title">
					<tui-icon name="document" color="#e41f19"></tui-icon>
					<text>商品描述</text>
				</view>
				<view class="tui-desc-content">
					<text>{{ goodsInfo.title }}</text>
				</view>
			</view>

			<!-- 底部占位 -->
			<view class="tui-bottom-placeholder"></view>
		</scroll-view>

		<!-- 底部操作栏 - 简化版 -->
		<view class="tui-bottom-bar">
			<view class="tui-bottom-center">
				<view v-if="goodsInfo.link_status != 1" class="tui-btn tui-btn-convert" @tap="convertLink">
					<text>领取优惠</text>
				</view>
				<view v-else class="tui-btn tui-btn-buy" @tap="buyNow">
					<text>立即购买</text>
				</view>
			</view>
		</view>

		<!-- 加载遮罩 -->
		<view v-if="loading" class="tui-loading-mask">
			<view class="tui-loading-content">
				<tui-loading></tui-loading>
				<text>加载中...</text>
			</view>
		</view>
	</view>
</template>

<script>
import dataokeApi from '@/common/dataokeApi.js'

export default {
	data() {
		return {
			// 页面基础数据
			width: 0,
			height: 0,
			arrowTop: 0,
			titleTop: 0,
			
			// 商品信息
			goodsInfo: {},
			linkInfo: null,
			
			// 页面状态
			loading: false,
			
			// 页面参数
			goodsId: '',
			id: ''
		}
	},
	
	onLoad(options) {
		console.log('页面参数:', options);
		// 优先使用id，然后是goods_id，最后是goodsId
		this.goodsId = options.id || options.goods_id || options.goodsId || '';
		this.id = options.id || '';

		console.log('解析后的goodsId:', this.goodsId);

		// 获取系统信息
		this.getSystemInfo();

		// 加载商品详情
		this.loadGoodsDetail();
	},
	
	methods: {
		/**
		 * 获取系统信息
		 */
		getSystemInfo() {
			const systemInfo = uni.getSystemInfoSync();
			this.width = systemInfo.windowWidth;
			
			// #ifdef MP-WEIXIN
			this.height = systemInfo.statusBarHeight + 44;
			this.arrowTop = systemInfo.statusBarHeight + 10;
			this.titleTop = systemInfo.statusBarHeight + 13;
			// #endif
			
			// #ifdef H5
			this.height = 44;
			this.arrowTop = 10;
			this.titleTop = 13;
			// #endif
		},
		
		/**
		 * 加载商品详情
		 */
		async loadGoodsDetail() {
			console.log('开始加载商品详情，goodsId:', this.goodsId);

			if (!this.goodsId || this.goodsId === 'undefined') {
				console.error('商品ID无效:', this.goodsId);
				uni.showToast({
					title: '商品ID不能为空',
					icon: 'none'
				});
				return;
			}

			this.loading = true;
			
			try {
				// 这里可以调用商品详情API，暂时使用商品列表API获取单个商品
				const result = await dataokeApi.request.getGoodsList({
					goods_id: this.goodsId,
					limit: 1,
					showLoading: true
				});
				
				if (result.data.list && result.data.list.length > 0) {
					this.goodsInfo = result.data.list[0];
					
					// 如果商品已转链，获取转链信息
					if (this.goodsInfo.link_status == 1) {
						this.getLinkInfo();
					}
				} else {
					uni.showToast({
						title: '商品不存在',
						icon: 'none'
					});
				}
				
			} catch (error) {
				console.error('加载商品详情失败:', error);
				uni.showToast({
					title: '加载失败',
					icon: 'none'
				});
			} finally {
				this.loading = false;
			}
		},
		
		/**
		 * 获取转链信息
		 */
		async getLinkInfo() {
			try {
				const result = await dataokeApi.request.getConvertLink(this.goodsId);
				if (result.data) {
					this.linkInfo = result.data;
				}
			} catch (error) {
				console.error('获取转链信息失败:', error);
			}
		},
		
		/**
		 * 转链 - 完善领取优惠功能
		 */
		async convertLink() {
			if (this.loading) return;

			console.log('开始转链，goodsId:', this.goodsId);

			if (!this.goodsId || this.goodsId === 'undefined') {
				console.error('转链失败：商品ID无效:', this.goodsId);
				uni.showToast({
					title: '商品ID无效，无法转链',
					icon: 'none'
				});
				return;
			}

			this.loading = true;

			try {
				const result = await dataokeApi.request.getConvertLink(this.goodsId);
				console.log('转链结果:', result);

				if (result.data) {
					this.linkInfo = result.data;
					this.goodsInfo.link_status = 1;

					console.log('转链成功，返回数据:', result.data);
					console.log('淘口令字段:', {
						tpwd: result.data.tpwd,
						taokouling: result.data.taokouling,
						tkl: result.data.tkl
					});

					// 显示转链成功信息，包含优惠券信息
					let message = '转链成功！';
					if (this.goodsInfo.coupon_price > 0) {
						message += `\n优惠券：¥${this.goodsInfo.coupon_price}`;
					}

					uni.showModal({
						title: '领取优惠成功',
						content: message + '\n\n淘口令已生成，点击"立即购买"可复制淘口令到淘宝购买',
						showCancel: false,
						confirmText: '知道了'
					});
				} else {
					uni.showToast({
						title: '获取优惠失败',
						icon: 'none'
					});
				}
			} catch (error) {
				console.error('转链失败:', error);
				uni.showToast({
					title: '获取优惠失败，请重试',
					icon: 'none'
				});
			} finally {
				this.loading = false;
			}
		},
		
		/**
		 * 立即购买 - 生成完整淘口令并弹窗显示
		 */
		buyNow() {
			console.log('buyNow - linkInfo:', this.linkInfo);

			if (this.linkInfo) {
				// 尝试多个可能的淘口令字段名
				const taokouling = this.linkInfo.tpwd || this.linkInfo.taokouling || this.linkInfo.tkl || '';

				console.log('找到的淘口令:', taokouling);

				if (taokouling) {
					// 生成完整的淘口令文案
					const fullTaokouling = this.generateFullTaokouling(taokouling);

					// 显示淘口令弹窗
					this.showTaokoulingModal(fullTaokouling);
				} else {
					// 如果没有淘口令，尝试使用推广链接
					const link = this.linkInfo.privilegeLink || this.linkInfo.itemUrl || this.linkInfo.couponClickUrl || '';
					if (link) {
						this.showTaokoulingModal(link, '推广链接');
					} else {
						uni.showToast({
							title: '未找到有效的购买链接',
							icon: 'none'
						});
					}
				}
			} else {
				uni.showToast({
					title: '请先获取优惠',
					icon: 'none'
				});
			}
		},

		/**
		 * 生成完整的淘口令文案
		 */
		generateFullTaokouling(taokouling) {
			const title = this.goodsInfo.title || '精选好物';
			const price = this.goodsInfo.actual_price || this.goodsInfo.original_price || '';
			const couponInfo = this.goodsInfo.coupon_price > 0 ? `领券立减¥${this.goodsInfo.coupon_price}` : '';

			// 构建完整淘口令
			let fullText = `【淘宝】${title.substring(0, 30)}`;
			if (title.length > 30) fullText += '...';

			if (price) {
				fullText += ` ¥${price}`;
			}

			if (couponInfo) {
				fullText += ` ${couponInfo}`;
			}

			fullText += ` ${taokouling}`;
			fullText += '\n点击链接直接打开 或者 淘宝搜索直接打开';

			return fullText;
		},

		/**
		 * 显示淘口令弹窗
		 */
		showTaokoulingModal(content, type = '淘口令') {
			const that = this;
			uni.showModal({
				title: type,
				content: content,
				showCancel: true,
				cancelText: '关闭',
				confirmText: '复制',
				success: function(res) {
					if (res.confirm) {
						// 点击复制按钮
						that.copyToClipboard(content);
					}
				}
			});
		},

		/**
		 * 复制到剪贴板
		 */
		copyToClipboard(text) {
			uni.setClipboardData({
				data: text,
				success: () => {
					console.log('复制成功:', text);
					uni.showToast({
						title: '复制成功',
						icon: 'success'
					});
				},
				fail: (err) => {
					console.error('复制失败:', err);
					uni.showToast({
						title: '复制失败，请重试',
						icon: 'none'
					});
				}
			});
		},
		
		/**
		 * 复制文本
		 */
		copyText(text) {
			if (!text) return;
			
			uni.setClipboardData({
				data: text,
				success: () => {
					uni.showToast({
						title: '复制成功',
						icon: 'success'
					});
				}
			});
		},
		
		/**
		 * 分享
		 */
		share() {
			uni.showActionSheet({
				itemList: ['分享给朋友', '分享到朋友圈'],
				success: (res) => {
					if (res.tapIndex === 0) {
						// 分享给朋友
						uni.showToast({
							title: '分享功能开发中',
							icon: 'none'
						});
					} else if (res.tapIndex === 1) {
						// 分享到朋友圈
						uni.showToast({
							title: '分享功能开发中',
							icon: 'none'
						});
					}
				}
			});
		},
		

		
		/**
		 * 返回
		 */
		back() {
			uni.navigateBack();
		},
		
		/**
		 * 格式化价格
		 */
		formatPrice(price) {
			return dataokeApi.utils.formatPrice(price);
		},
		
		/**
		 * 格式化销量
		 */
		formatSaleCount(count) {
			return dataokeApi.utils.formatSaleCount(count);
		},
		
		/**
		 * 获取层级标签
		 */
		getTierLabel(level) {
			const labels = { 1: '热门商品', 2: '普通商品', 3: '冷门商品' };
			return labels[level] || '普通商品';
		},
		
		/**
		 * 获取层级颜色
		 */
		getTierColor(level) {
			const colors = { 1: '#ff4757', 2: '#ffa502', 3: '#747d8c' };
			return colors[level] || '#ffa502';
		}
	}
}
</script>

<style scoped>
.container {
	background: #f7f7f7;
	height: 100vh;
	overflow: hidden;
}

/* Header样式 */
.tui-header-box {
	position: fixed;
	top: 0;
	left: 0;
	right: 0;
	z-index: 999;
	background: linear-gradient(135deg, rgba(228, 31, 25, 0.9), rgba(255, 107, 107, 0.9));
}

.tui-header {
	display: flex;
	align-items: center;
	justify-content: space-between;
	padding: 0 30rpx;
	position: relative;
}

.tui-back, .tui-share {
	width: 60rpx;
	height: 60rpx;
	display: flex;
	align-items: center;
	justify-content: center;
}

.tui-title {
	flex: 1;
	text-align: center;
	font-size: 32rpx;
	font-weight: bold;
	color: #fff;
}

/* 滚动视图 */
.tui-scroll-view {
	height: calc(100vh - 120rpx);
}

/* 商品图片样式 */
.tui-goods-images {
	position: relative;
	height: 750rpx;
}

.tui-swiper {
	width: 100%;
	height: 100%;
}

.tui-goods-image {
	width: 100%;
	height: 100%;
}

/* 标签样式 */
.tui-tags {
	position: absolute;
	top: 30rpx;
	left: 30rpx;
	display: flex;
	flex-direction: column;
	gap: 12rpx;
}

.tui-tag {
	padding: 8rpx 16rpx;
	border-radius: 24rpx;
	font-size: 22rpx;
	color: #fff;
	font-weight: bold;
	backdrop-filter: blur(10rpx);
}

.tui-tag-hot {
	background: linear-gradient(135deg, #ff6b6b, #ff8e8e);
}

.tui-tag-coupon {
	background: linear-gradient(135deg, #ffa502, #ffb142);
}

.tui-tag-tmall {
	background: #ff5000;
}

.tui-tag-converted {
	background: #2ed573;
}

/* 商品信息样式 */
.tui-goods-info {
	background: #fff;
	margin: -20rpx 20rpx 20rpx;
	border-radius: 20rpx;
	padding: 40rpx;
	box-shadow: 0 -10rpx 30rpx rgba(0,0,0,0.1);
}

.tui-goods-title {
	font-size: 32rpx;
	color: #333;
	line-height: 1.5;
	margin-bottom: 30rpx;
	font-weight: bold;
}

/* 价格区域样式 */
.tui-price-section {
	margin-bottom: 40rpx;
}

.tui-price-main {
	display: flex;
	align-items: baseline;
	margin-bottom: 20rpx;
}

.tui-price-symbol {
	font-size: 28rpx;
	color: #e41f19;
	font-weight: bold;
}

.tui-price-num {
	font-size: 48rpx;
	color: #e41f19;
	font-weight: bold;
	margin-left: 8rpx;
}

.tui-price-original {
	font-size: 28rpx;
	color: #999;
	text-decoration: line-through;
	margin-left: 20rpx;
}

/* 优惠券卡片样式 - 简化版 */
.tui-coupon-section {
	margin-top: 20rpx;
}

.tui-coupon-card-simple {
	background: linear-gradient(135deg, #ff6b6b, #ff8e8e);
	border-radius: 16rpx;
	padding: 24rpx;
	display: flex;
	align-items: center;
	justify-content: center;
	color: #fff;
	gap: 20rpx;
}

.tui-coupon-label {
	font-size: 28rpx;
	font-weight: bold;
}

.tui-coupon-price {
	font-size: 36rpx;
	font-weight: bold;
}

/* 商品属性样式 */
.tui-goods-attrs {
	border-top: 1px solid #f0f0f0;
	padding-top: 30rpx;
}

.tui-attr-item {
	display: flex;
	justify-content: space-between;
	align-items: center;
	padding: 20rpx 0;
	border-bottom: 1px solid #f8f8f8;
}

.tui-attr-item:last-child {
	border-bottom: none;
}

.tui-attr-label {
	font-size: 28rpx;
	color: #666;
}

.tui-attr-value {
	font-size: 28rpx;
	color: #333;
	font-weight: bold;
}

/* 区块样式 */
.tui-link-section, .tui-goods-desc {
	background: #fff;
	margin: 0 20rpx 20rpx;
	border-radius: 16rpx;
	padding: 30rpx;
}

.tui-section-title {
	display: flex;
	align-items: center;
	font-size: 30rpx;
	font-weight: bold;
	color: #333;
	margin-bottom: 30rpx;
}

.tui-section-title text {
	margin-left: 12rpx;
}

/* 转链信息样式 */
.tui-link-content {
	background: #f8f9fa;
	border-radius: 12rpx;
	padding: 24rpx;
}

.tui-link-item {
	display: flex;
	justify-content: space-between;
	align-items: center;
	margin-bottom: 20rpx;
}

.tui-link-item:last-child {
	margin-bottom: 0;
}

.tui-link-label {
	font-size: 26rpx;
	color: #666;
}

.tui-link-value {
	font-size: 26rpx;
	color: #e41f19;
	font-weight: bold;
}

/* 商品描述样式 */
.tui-desc-content {
	font-size: 28rpx;
	color: #666;
	line-height: 1.6;
}

/* 底部占位 */
.tui-bottom-placeholder {
	height: 120rpx;
}

/* 底部操作栏样式 */
.tui-bottom-bar {
	position: fixed;
	bottom: 0;
	left: 0;
	right: 0;
	background: #fff;
	border-top: 1px solid #f0f0f0;
	padding: 20rpx 30rpx;
	display: flex;
	align-items: center;
	justify-content: space-between;
	z-index: 999;
}

.tui-bottom-center {
	flex: 1;
	display: flex;
	justify-content: center;
}

.tui-btn {
	padding: 24rpx 60rpx;
	border-radius: 50rpx;
	font-size: 30rpx;
	font-weight: bold;
	text-align: center;
	min-width: 200rpx;
}

.tui-btn-convert {
	background: linear-gradient(135deg, #ffa502, #ffb142);
	color: #fff;
}

.tui-btn-buy {
	background: linear-gradient(135deg, #e41f19, #ff6b6b);
	color: #fff;
}

/* 加载遮罩样式 */
.tui-loading-mask {
	position: fixed;
	top: 0;
	left: 0;
	right: 0;
	bottom: 0;
	background: rgba(0,0,0,0.5);
	display: flex;
	align-items: center;
	justify-content: center;
	z-index: 9999;
}

.tui-loading-content {
	background: #fff;
	border-radius: 16rpx;
	padding: 60rpx;
	display: flex;
	flex-direction: column;
	align-items: center;
	gap: 20rpx;
}

.tui-loading-content text {
	font-size: 28rpx;
	color: #666;
}
</style>
