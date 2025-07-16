<template>
	<view class="container">
		<!--header-->
		<view class="tui-header-box">
			<view class="tui-header" :style="{ width: width + 'px', height: height + 'px' }">
				<view class="tui-back" :style="{ marginTop: arrowTop + 'px' }" @tap="back">
					<tui-icon name="arrowleft" color="#000"></tui-icon>
				</view>
				<view class="tui-title" :style="{ marginTop: titleTop + 'px' }">
					<text>优惠商品</text>
				</view>

			</view>
		</view>
		<!--header-->
		


		<!--商品列表-->
		<view class="tui-goods-list" :style="{ marginTop: px(height + 20) }">
			<!-- 统计信息 -->
			<view v-if="statsInfo" class="tui-stats-box">
				<view class="tui-stats-item">
					<text class="tui-stats-num">{{ statsInfo.total || 0 }}</text>
					<text class="tui-stats-label">总商品</text>
				</view>
				<view class="tui-stats-item">
					<text class="tui-stats-num">{{ statsInfo.converted || 0 }}</text>
					<text class="tui-stats-label">已转链</text>
				</view>
				<view class="tui-stats-item">
					<text class="tui-stats-num">{{ statsInfo.hot || 0 }}</text>
					<text class="tui-stats-label">热门商品</text>
				</view>
			</view>

			<!-- 商品容器 - 两列布局 -->
			<view class="tui-goods-container tui-goods-grid">
				<block v-for="(item, index) in goodsList" :key="index">
					<view class="tui-goods-item tui-goods-item-grid" hover-class="tui-hover" :hover-start-time="150"
						@tap="goToDetail(item)">
						
						<!-- 商品图片 -->
						<view class="tui-goods-image-wrapper">
							<image :src="item.image || item.pic" class="tui-goods-image" mode="aspectFill" />
							

						</view>
						
						<!-- 商品信息 -->
						<view class="tui-goods-content">
							<view class="tui-goods-title">{{ item.title }}</view>
							
							<!-- 价格信息 -->
							<view class="tui-price-box">
								<view class="tui-price-main">
									<text class="tui-price-symbol">¥</text>
									<text class="tui-price-num">{{ formatPrice(item.price || item.actual_price) }}</text>
									<text v-if="item.original_price > (item.price || item.actual_price)"
										class="tui-price-original">¥{{ formatPrice(item.original_price) }}</text>
								</view>
								

							</view>
							
							<!-- 销量和店铺信息 -->
							<view class="tui-goods-meta">
								<text v-if="(item.sales || item.month_sales) > 0" class="tui-sales">
									{{ formatSaleCount(item.sales || item.month_sales) }}人付款
								</text>
								<text v-if="item.shop_name" class="tui-shop">
									{{ item.shop_name }}
								</text>
							</view>
						</view>
					</view>
				</block>
			</view>

			<!-- 加载状态 -->
			<view v-if="loading" class="tui-loading">
				<tui-loading></tui-loading>
				<text>加载中...</text>
			</view>
			
			<!-- 没有更多 -->
			<view v-else-if="!hasMore && goodsList.length > 0" class="tui-no-more">
				<text>没有更多商品了</text>
			</view>
			
			<!-- 空状态 -->
			<view v-else-if="!loading && goodsList.length === 0" class="tui-empty">
				<image src="/static/images/default_img.png" class="tui-empty-image"></image>
				<text class="tui-empty-text">暂无商品数据</text>
				<view class="tui-empty-btn" @tap="refresh">
					<text>重新加载</text>
				</view>
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
			
			// 当前标签页
			currentTab: 'all', // 默认显示全部商品
			
			// 商品列表数据
			goodsList: [],
			loading: false,
			hasMore: true,
			currentPage: 1,
			pageSize: 20,
			
			// 统计信息
			statsInfo: null
		}
	},
	
	onLoad(options) {
		// 获取系统信息
		this.getSystemInfo();
		
		// 获取统计信息
		this.getStats();
		
		// 加载商品列表
		this.loadGoodsList();
	},
	
	onReachBottom() {
		// 加载更多
		this.loadMoreGoods();
	},
	
	onPullDownRefresh() {
		// 下拉刷新
		this.refresh();
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
		 * 获取统计信息
		 */
		async getStats() {
			try {
				console.log('获取统计信息...');

				const result = await dataokeApi.request.getSyncStats();
				console.log('统计API返回:', result);

				if (result && result.data) {
					const data = result.data;
					this.statsInfo = {
						total: data.goods ? data.goods.total : 0,
						converted: data.goods ? data.goods.converted : 0,
						hot: data.goods ? data.goods.tier1 : 0
					};
				} else {
					// 使用默认统计信息
					this.statsInfo = {
						total: 0,
						converted: 0,
						hot: 0
					};
				}

				console.log('统计信息加载成功:', this.statsInfo);
			} catch (error) {
				console.error('获取统计信息失败:', error);
				// 使用默认统计信息
				this.statsInfo = {
					total: 0,
					converted: 0,
					hot: 0
				};
			}
		},
		
		/**
		 * 加载商品列表
		 */
		async loadGoodsList() {
			if (this.loading) return;
			
			this.loading = true;
			
			try {
				const params = {
					page: 1,
					limit: this.pageSize,
					showLoading: false // 使用页面级加载动画，不使用API级加载动画
				};
				
				// 调用API获取全部商品列表
				console.log('调用商品列表API，参数:', params);

				const result = await dataokeApi.request.getGoodsList(params);

				console.log('API返回结果:', result);

				if (result && result.data) {
					this.goodsList = result.data.list || [];
					this.currentPage = 1;
					this.hasMore = result.data.pagination ? result.data.pagination.hasMore : false;

					console.log('商品列表加载成功:', this.goodsList.length, '个商品');
				} else {
					console.warn('API返回数据格式异常:', result);
					this.goodsList = [];
					this.hasMore = false;
				}
				
			} catch (error) {
				console.error('加载商品列表失败:', error);
				console.error('错误详情:', {
					message: error.message,
					stack: error.stack
				});

				// 显示具体错误信息
				let errorMessage = '加载失败';
				if (error.message.includes('URLSearchParams')) {
					errorMessage = 'API兼容性问题，正在修复...';
				} else if (error.message.includes('404')) {
					errorMessage = 'API接口不存在';
				} else if (error.message.includes('网络')) {
					errorMessage = '网络连接失败';
				}

				uni.showToast({
					title: errorMessage,
					icon: 'none',
					duration: 3000
				});
			} finally {
				this.loading = false;
				uni.stopPullDownRefresh();
			}
		},
		
		/**
		 * 加载更多商品
		 */
		async loadMoreGoods() {
			if (this.loading || !this.hasMore) return;
			
			this.loading = true;
			
			try {
				const params = {
					page: this.currentPage + 1,
					limit: this.pageSize,
					showLoading: false // 加载更多时不显示API级加载动画
				};
				
				// 调用API获取更多商品
				const result = await dataokeApi.request.getGoodsList(params);
				
				if (result.data.list && result.data.list.length > 0) {
					this.goodsList.push(...result.data.list);
					this.currentPage++;
					this.hasMore = result.data.hasMore !== false;
				} else {
					this.hasMore = false;
				}
				
			} catch (error) {
				console.error('加载更多商品失败:', error);
				uni.showToast({
					title: '加载失败',
					icon: 'none'
				});
			} finally {
				this.loading = false;
			}
		},
		
		/**
		 * 刷新数据
		 */
		refresh() {
			this.goodsList = [];
			this.currentPage = 1;
			this.hasMore = true;
			this.loadGoodsList();
			this.getStats();
		},
		
		/**
		 * 跳转到商品详情
		 */
		goToDetail(goods) {
			console.log('跳转商品：', goods.id, goods);
			uni.navigateTo({
				url: `/pages/dataokeGoodsDetail/dataokeGoodsDetail?id=${goods.id}`
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
		 * 转换rpx为px
		 */
		px(rpx) {
			return rpx * this.width / 750;
		}
	}
}
</script>

<style scoped>
.container {
	background: #f7f7f7;
	min-height: 100vh;
}

/* Header样式 */
.tui-header-box {
	position: fixed;
	top: 0;
	left: 0;
	right: 0;
	z-index: 999;
	background: #fff;
	border-bottom: 1px solid #f0f0f0;
}

.tui-header {
	display: flex;
	align-items: center;
	justify-content: space-between;
	padding: 0 30rpx;
	position: relative;
}

.tui-back {
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
	color: #333;
}



/* 统计信息样式 */
.tui-stats-box {
	display: flex;
	background: #fff;
	margin: 20rpx;
	border-radius: 12rpx;
	padding: 30rpx 0;
	box-shadow: 0 2rpx 12rpx rgba(0,0,0,0.1);
}

.tui-stats-item {
	flex: 1;
	display: flex;
	flex-direction: column;
	align-items: center;
}

.tui-stats-num {
	font-size: 36rpx;
	font-weight: bold;
	color: #e41f19;
}

.tui-stats-label {
	font-size: 24rpx;
	color: #999;
	margin-top: 8rpx;
}

/* 商品列表样式 */
.tui-goods-container {
	padding: 0 20rpx;
}

/* 两列网格布局 */
.tui-goods-grid {
	display: flex;
	flex-wrap: wrap;
	justify-content: space-between;
}

.tui-goods-item {
	background: #fff;
	border-radius: 12rpx;
	margin-bottom: 20rpx;
	overflow: hidden;
	box-shadow: 0 2rpx 12rpx rgba(0,0,0,0.08);
}

/* 网格模式下的商品项 */
.tui-goods-item-grid {
	width: 48%;
	margin-bottom: 20rpx;
}

.tui-hover {
	background: #f5f5f5;
}

.tui-goods-image-wrapper {
	position: relative;
	width: 100%;
	height: 300rpx;
}

/* 网格模式下的图片高度 */
.tui-goods-item-grid .tui-goods-image-wrapper {
	height: 250rpx;
}

.tui-goods-image {
	width: 100%;
	height: 100%;
}

/* 标签样式 */
.tui-tags {
	position: absolute;
	top: 20rpx;
	left: 20rpx;
	display: flex;
	flex-direction: column;
	gap: 8rpx;
}

.tui-tag {
	padding: 4rpx 12rpx;
	border-radius: 20rpx;
	font-size: 20rpx;
	color: #fff;
	font-weight: bold;
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

/* 商品内容样式 */
.tui-goods-content {
	padding: 24rpx;
}

.tui-goods-title {
	font-size: 28rpx;
	color: #333;
	line-height: 1.4;
	margin-bottom: 16rpx;
	display: -webkit-box;
	-webkit-box-orient: vertical;
	-webkit-line-clamp: 2;
	overflow: hidden;
}

/* 价格样式 */
.tui-price-box {
	margin-bottom: 16rpx;
}

.tui-price-main {
	display: flex;
	align-items: baseline;
	margin-bottom: 8rpx;
}

.tui-price-symbol {
	font-size: 24rpx;
	color: #e41f19;
	font-weight: bold;
}

.tui-price-num {
	font-size: 36rpx;
	color: #e41f19;
	font-weight: bold;
	margin-left: 4rpx;
}

.tui-price-original {
	font-size: 24rpx;
	color: #999;
	text-decoration: line-through;
	margin-left: 16rpx;
}

.tui-coupon-info {
	margin-top: 8rpx;
}

.tui-coupon-text {
	background: #fff2f0;
	color: #e41f19;
	font-size: 22rpx;
	padding: 4rpx 12rpx;
	border-radius: 8rpx;
	border: 1px solid #ffccc7;
}

/* 商品元信息样式 */
.tui-goods-meta {
	display: flex;
	justify-content: space-between;
	align-items: center;
	font-size: 24rpx;
	color: #999;
}

.tui-sales {
	color: #666;
}

.tui-shop {
	max-width: 200rpx;
	overflow: hidden;
	text-overflow: ellipsis;
	white-space: nowrap;
}

/* 加载状态样式 */
.tui-loading {
	display: flex;
	flex-direction: column;
	align-items: center;
	padding: 40rpx;
	color: #999;
	font-size: 28rpx;
}

.tui-no-more {
	text-align: center;
	padding: 40rpx;
	color: #999;
	font-size: 28rpx;
}

/* 空状态样式 */
.tui-empty {
	display: flex;
	flex-direction: column;
	align-items: center;
	padding: 100rpx 40rpx;
}

.tui-empty-image {
	width: 200rpx;
	height: 200rpx;
	margin-bottom: 40rpx;
}

.tui-empty-text {
	font-size: 28rpx;
	color: #999;
	margin-bottom: 40rpx;
}

.tui-empty-btn {
	background: #e41f19;
	color: #fff;
	padding: 16rpx 40rpx;
	border-radius: 40rpx;
	font-size: 28rpx;
}
</style>
