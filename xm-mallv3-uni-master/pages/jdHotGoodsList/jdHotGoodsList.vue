<template>
	<view class="container">
		<!--header-->
		<view class="tui-header-box">
			<view class="tui-header" :style="{ width: width + 'px', height: height + 'px' }">
				<view class="tui-back" :style="{ marginTop: arrowTop + 'px' }" @tap="back">
					<tui-icon name="arrowleft" color="#000"></tui-icon>
				</view>
				<view class="tui-title-absolute" :style="{ marginTop: titleTop + 'px', width: width + 'px' }">
					<text>京东商品</text>
				</view>
			</view>
		</view>
		<!--header-->

		<!-- 搜索栏 -->
		<view class="tui-search-box" :style="{ marginTop: (height + 10) + 'px' }">
			<view class="tui-search-input-wrapper">
				<tui-icon name="search" color="#999" size="32"></tui-icon>
				<input
					class="tui-search-input"
					type="text"
					placeholder="搜索京东商品..."
					v-model="searchKeyword"
					@confirm="onSearchConfirm"
					@input="onSearchInput"
				/>
				<view v-if="searchKeyword" class="tui-search-clear" @tap="clearSearch">
					<tui-icon name="close" color="#999" size="28"></tui-icon>
				</view>
			</view>
			<view class="tui-search-btn" @tap="onSearchConfirm">
				<text>搜索</text>
			</view>
		</view>



		<!--商品列表-->
		<view class="tui-goods-list">


			<!-- 商品容器 - 两列布局 -->
			<view class="tui-goods-container tui-goods-grid">
				<block v-for="(item, index) in goodsList" :key="index">
					<view class="tui-goods-item tui-goods-item-grid" hover-class="tui-hover" :hover-start-time="150"
						@tap="goToDetail(item)">

						<!-- 商品图片 -->
						<view class="tui-goods-image-wrapper">
							<image :src="item.main_image || item.image" class="tui-goods-image" mode="aspectFill" />

							<!-- 优惠券标识 -->
							<view v-if="item.coupon_amount > 0" class="tui-coupon-tag">
								<text>券{{ formatPrice(item.coupon_amount) }}</text>
							</view>
						</view>

						<!-- 商品信息 -->
						<view class="tui-goods-content">
							<view class="tui-goods-title">{{ item.title || item.short_title }}</view>

							<!-- 价格信息 -->
							<view class="tui-price-box">
								<view class="tui-price-main">
									<text class="tui-price-symbol">¥</text>
									<text class="tui-price-num">{{ formatPrice(item.coupon_price || item.price) }}</text>
									<text v-if="item.price > item.coupon_price" class="tui-price-original">
										¥{{ formatPrice(item.price) }}
									</text>
								</view>

								<!-- 佣金信息 -->
								<view class="tui-commission-box">
									<text class="tui-commission-rate">{{ formatCommissionRate(item.commission_rate) }}</text>
									<text class="tui-commission-amount">赚{{ formatPrice(item.commission_amount) }}</text>
								</view>
							</view>

							<!-- 销量和店铺信息 -->
							<view class="tui-goods-meta">
								<text v-if="item.sales_volume > 0" class="tui-sales">
									{{ formatSaleCount(item.sales_volume) }}人付款
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
import newTaokeApi from '@/common/newTaokeApi.js'

export default {
	data() {
		return {
			// 页面基础数据
			width: 0,
			height: 0,
			arrowTop: 0,
			titleTop: 0,

			// 搜索相关
			searchKeyword: '',
			searchTimer: null,

			// 商品列表数据
			goodsList: [],
			loading: false,
			hasMore: true,
			currentPage: 1,
			pageSize: 20
		}
	},
	
	onLoad(options) {
		// 获取系统信息
		this.getSystemInfo();

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

	onUnload() {
		// 清理搜索定时器
		if (this.searchTimer) {
			clearTimeout(this.searchTimer);
		}
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
		 * 搜索输入事件
		 */
		onSearchInput(e) {
			this.searchKeyword = e.detail.value;

			// 清除之前的定时器
			if (this.searchTimer) {
				clearTimeout(this.searchTimer);
			}

			// 设置新的定时器，延迟搜索
			this.searchTimer = setTimeout(() => {
				if (this.searchKeyword.trim()) {
					this.performSearch();
				}
			}, 500);
		},

		/**
		 * 搜索确认事件
		 */
		onSearchConfirm() {
			if (this.searchTimer) {
				clearTimeout(this.searchTimer);
			}
			this.performSearch();
		},

		/**
		 * 清除搜索
		 */
		clearSearch() {
			this.searchKeyword = '';
			this.performSearch();
		},

		/**
		 * 执行搜索
		 */
		performSearch() {
			console.log('执行京东商品搜索，关键词:', this.searchKeyword);
			this.currentPage = 1;
			this.goodsList = [];
			this.hasMore = true;
			this.loadGoodsList();
		},



		/**
		 * 加载商品列表
		 */
		async loadGoodsList() {
			if (this.loading) return;

			this.loading = true;

			try {
				// 构建请求参数
				const params = {
					page: 1,
					size: this.pageSize,
					platform: newTaokeApi.config.platforms.JD, // 只获取京东商品
					showLoading: false
				};

				// 添加搜索关键词
				if (this.searchKeyword && this.searchKeyword.trim()) {
					params.keyword = this.searchKeyword.trim();
				}

				console.log('调用京东商品搜索API，参数:', params);

				// 使用搜索API而不是列表API，支持更多筛选功能
				const result = await newTaokeApi.request.searchProducts(params);

				console.log('API返回结果:', result);

				if (result && result.data) {
					let goodsList = result.data || [];

					this.goodsList = goodsList;
					this.currentPage = 1;
					this.hasMore = result.page < result.pages;

					console.log('京东商品列表加载成功:', this.goodsList.length, '个商品');
				} else {
					console.warn('API返回数据格式异常:', result);
					this.goodsList = [];
					this.hasMore = false;
				}
				
			} catch (error) {
				console.error('加载京东商品列表失败:', error);

				// 显示错误信息
				let errorMessage = '加载失败';
				if (error.message.includes('timeout')) {
					errorMessage = '请求超时，请检查网络';
				} else if (error.message.includes('网络')) {
					errorMessage = '网络连接失败';
				} else if (error.message.includes('404')) {
					errorMessage = 'API接口不存在';
				}

				uni.showToast({
					title: errorMessage,
					icon: 'none',
					duration: 3000
				});

				// 设置空数据
				this.goodsList = [];
				this.hasMore = false;
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
				// 构建请求参数
				const params = {
					page: this.currentPage + 1,
					size: this.pageSize,
					platform: newTaokeApi.config.platforms.JD,
					showLoading: false
				};

				// 添加搜索关键词
				if (this.searchKeyword && this.searchKeyword.trim()) {
					params.keyword = this.searchKeyword.trim();
				}

				// 调用API获取更多商品
				const result = await newTaokeApi.request.searchProducts(params);

				if (result && result.data && result.data.length > 0) {
					let newGoods = result.data;

					this.goodsList.push(...newGoods);
					this.currentPage++;
					this.hasMore = result.page < result.pages;


				} else {
					this.hasMore = false;
				}

			} catch (error) {
				console.error('加载更多京东商品失败:', error);
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
		},
		
		/**
		 * 跳转到商品详情
		 */
		goToDetail(goods) {
			console.log('跳转京东商品详情：', goods.product_id, goods);

			// 将完整的商品信息存储到全局状态中
			uni.setStorageSync('currentGoodsInfo', goods);

			// 直接跳转到新的商品详情页面
			uni.navigateTo({
				url: `/pages/newGoodsDetail/newGoodsDetail?product_id=${goods.product_id}&platform=2`
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
			return newTaokeApi.utils.formatPrice(price);
		},

		/**
		 * 格式化佣金比例
		 */
		formatCommissionRate(rate) {
			return newTaokeApi.utils.formatCommissionRate(rate);
		},

		/**
		 * 格式化销量
		 */
		formatSaleCount(count) {
			if (!count || count <= 0) return '0';
			if (count >= 10000) {
				return (count / 10000).toFixed(1) + '万';
			} else if (count >= 1000) {
				return (count / 1000).toFixed(1) + 'k';
			}
			return count.toString();
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
	background-color: #f5f5f5;
	min-height: 100vh;
}

/* 头部样式 */
.tui-header-box {
	position: fixed;
	top: 0;
	left: 0;
	right: 0;
	z-index: 999;
	background-color: #fff;
	border-bottom: 1rpx solid #eee;
}

.tui-header {
	position: relative;
	display: flex;
	align-items: center;
	padding: 0 30rpx;
	background-color: #fff;
}

.tui-back {
	width: 80rpx;
	height: 80rpx;
	display: flex;
	align-items: center;
	justify-content: center;
}

.tui-title-absolute {
	position: absolute;
	left: 0;
	top: 0;
	text-align: center;
	font-size: 36rpx;
	font-weight: 600;
	color: #333;
	pointer-events: none;
}

/* 搜索栏样式 */
.tui-search-box {
	display: flex;
	align-items: center;
	padding: 20rpx;
	background: #fff;
	margin: 0 20rpx 20rpx;
	border-radius: 12rpx;
	box-shadow: 0 2rpx 12rpx rgba(0,0,0,0.1);
}

.tui-search-input-wrapper {
	flex: 1;
	display: flex;
	align-items: center;
	background: #f5f5f5;
	border-radius: 50rpx;
	padding: 0 24rpx;
	height: 72rpx;
	margin-right: 20rpx;
}

.tui-search-input {
	flex: 1;
	font-size: 28rpx;
	color: #333;
	margin-left: 16rpx;
}

.tui-search-clear {
	width: 40rpx;
	height: 40rpx;
	display: flex;
	align-items: center;
	justify-content: center;
	background: #ddd;
	border-radius: 50%;
}

.tui-search-btn {
	background: linear-gradient(135deg, #ff6b35 0%, #f7931e 100%);
	color: #fff;
	padding: 0 32rpx;
	height: 72rpx;
	border-radius: 36rpx;
	display: flex;
	align-items: center;
	justify-content: center;
	font-size: 28rpx;
	font-weight: bold;
}





/* 商品列表样式 */
.tui-goods-list {
	padding: 10rpx 20rpx 20rpx 20rpx;
}

/* 商品网格布局 */
.tui-goods-grid {
	display: flex;
	flex-wrap: wrap;
	justify-content: space-between;
}

.tui-goods-item-grid {
	width: 48%;
	background-color: #fff;
	border-radius: 12rpx;
	margin-bottom: 20rpx;
	overflow: hidden;
	box-shadow: 0 2rpx 12rpx rgba(0,0,0,0.08);
}

.tui-hover {
	transform: scale(0.98);
	transition: transform 0.2s;
}

/* 商品图片样式 */
.tui-goods-image-wrapper {
	position: relative;
	width: 100%;
	height: 280rpx;
}

.tui-goods-image {
	width: 100%;
	height: 100%;
	border-radius: 12rpx 12rpx 0 0;
}



/* 优惠券标识 */
.tui-coupon-tag {
	position: absolute;
	top: 16rpx;
	right: 16rpx;
	background: linear-gradient(135deg, #ff6b35, #f7931e);
	color: #fff;
	padding: 6rpx 12rpx;
	border-radius: 20rpx;
	font-size: 20rpx;
	font-weight: bold;
}

/* 商品内容样式 */
.tui-goods-content {
	padding: 16rpx;
}

.tui-goods-title {
	font-size: 24rpx;
	color: #333;
	line-height: 1.3;
	height: 62rpx;
	overflow: hidden;
	display: -webkit-box;
	-webkit-line-clamp: 2;
	-webkit-box-orient: vertical;
	margin-bottom: 12rpx;
}

/* 价格样式 */
.tui-price-box {
	margin-bottom: 12rpx;
}

.tui-price-main {
	display: flex;
	align-items: baseline;
}

.tui-price-symbol {
	font-size: 20rpx;
	color: #ff4142;
	font-weight: 600;
}

.tui-price-num {
	font-size: 32rpx;
	color: #ff4142;
	font-weight: 600;
	margin-right: 8rpx;
}

.tui-price-original {
	font-size: 20rpx;
	color: #999;
	text-decoration: line-through;
}

/* 佣金信息样式 */
.tui-commission-box {
	display: flex;
	align-items: center;
	gap: 12rpx;
	margin-top: 8rpx;
}

.tui-commission-rate {
	background: linear-gradient(135deg, #ff6b35, #f7931e);
	color: #fff;
	font-size: 20rpx;
	padding: 4rpx 8rpx;
	border-radius: 6rpx;
	font-weight: bold;
}

.tui-commission-amount {
	background: #fff2e8;
	color: #ff6b35;
	font-size: 20rpx;
	padding: 4rpx 8rpx;
	border-radius: 6rpx;
	font-weight: bold;
}



/* 商品元信息 */
.tui-goods-meta {
	display: flex;
	justify-content: space-between;
	align-items: center;
	font-size: 20rpx;
	color: #999;
}

.tui-sales {
	color: #999;
	flex: 1;
}

.tui-shop {
	max-width: 120rpx;
	overflow: hidden;
	text-overflow: ellipsis;
	white-space: nowrap;
	color: #666;
}

/* 加载状态 */
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

/* 空状态 */
.tui-empty {
	display: flex;
	flex-direction: column;
	align-items: center;
	padding: 100rpx 40rpx;
}

.tui-empty-image {
	width: 200rpx;
	height: 200rpx;
	margin-bottom: 30rpx;
	opacity: 0.5;
}

.tui-empty-text {
	font-size: 28rpx;
	color: #999;
	margin-bottom: 40rpx;
}

.tui-empty-btn {
	background-color: #ff6b35;
	color: #fff;
	padding: 20rpx 40rpx;
	border-radius: 50rpx;
	font-size: 28rpx;
}
</style>
