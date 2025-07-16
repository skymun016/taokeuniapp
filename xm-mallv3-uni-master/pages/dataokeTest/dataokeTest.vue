<template>
	<view class="container">
		<view class="header">
			<text class="title">大淘客商品测试</text>
		</view>
		
		<view class="content">
			<!-- API连接测试 -->
			<view class="test-section">
				<view class="section-title">API连接测试</view>
				<view class="test-item" @tap="testConnection">
					<text class="test-label">测试服务连接</text>
					<text class="test-status" :class="{ success: connectionStatus === 'success', error: connectionStatus === 'error' }">
						{{ connectionText }}
					</text>
				</view>
			</view>

			<!-- 统计信息 -->
			<view class="test-section">
				<view class="section-title">统计信息</view>
				<view v-if="statsInfo" class="stats-grid">
					<view class="stats-item">
						<text class="stats-num">{{ statsInfo.total || 0 }}</text>
						<text class="stats-label">总商品</text>
					</view>
					<view class="stats-item">
						<text class="stats-num">{{ statsInfo.converted || 0 }}</text>
						<text class="stats-label">已转链</text>
					</view>
					<view class="stats-item">
						<text class="stats-num">{{ statsInfo.hot || 0 }}</text>
						<text class="stats-label">热门商品</text>
					</view>
				</view>
				<view v-else class="no-data">
					<text>暂无统计数据</text>
				</view>
			</view>

			<!-- 功能测试 -->
			<view class="test-section">
				<view class="section-title">功能测试</view>
				<view class="button-group">
					<view class="test-btn" @tap="goToGoodsList">
						<text>🛍️ 商品列表</text>
					</view>
					<view class="test-btn" @tap="testHotGoods">
						<text>🔥 热门商品</text>
					</view>
					<view class="test-btn" @tap="testCouponGoods">
						<text>🎫 优惠券商品</text>
					</view>
					<view class="test-btn" @tap="refreshStats">
						<text>📊 刷新统计</text>
					</view>
				</view>
			</view>

			<!-- 最新商品预览 -->
			<view class="test-section">
				<view class="section-title">最新商品预览</view>
				<view v-if="previewGoods.length > 0" class="preview-list">
					<view v-for="(item, index) in previewGoods" :key="index" class="preview-item" @tap="goToDetail(item)">
						<image :src="item.pic" class="preview-image" mode="aspectFill" />
						<view class="preview-content">
							<text class="preview-title">{{ item.title }}</text>
							<view class="preview-price">
								<text class="price-current">¥{{ formatPrice(item.actual_price) }}</text>
								<text v-if="item.coupon_price > 0" class="price-coupon">券¥{{ item.coupon_price }}</text>
							</view>
						</view>
					</view>
				</view>
				<view v-else class="no-data">
					<text>暂无商品数据</text>
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
			connectionStatus: '', // '', 'success', 'error'
			connectionText: '点击测试',
			statsInfo: null,
			previewGoods: []
		}
	},
	
	onLoad() {
		// 自动测试连接
		this.testConnection();
		// 获取统计信息
		this.getStats();
		// 获取预览商品
		this.getPreviewGoods();
	},
	
	methods: {
		/**
		 * 测试API连接
		 */
		async testConnection() {
			this.connectionStatus = '';
			this.connectionText = '测试中...';
			
			try {
				await dataokeApi.request.testConnection();
				this.connectionStatus = 'success';
				this.connectionText = '连接成功';
				
				uni.showToast({
					title: '连接成功',
					icon: 'success'
				});
			} catch (error) {
				this.connectionStatus = 'error';
				this.connectionText = '连接失败';
				
				console.error('连接测试失败:', error);
				uni.showToast({
					title: '连接失败',
					icon: 'none'
				});
			}
		},
		
		/**
		 * 获取统计信息
		 */
		async getStats() {
			try {
				const result = await dataokeApi.request.getSyncStats();
				if (result.data && result.data.length > 0) {
					// 计算统计信息
					let total = 0, converted = 0, hot = 0;
					result.data.forEach(item => {
						total += parseInt(item.total || 0);
						converted += parseInt(item.converted || 0);
						if (item.tier_level == 1) {
							hot = parseInt(item.total || 0);
						}
					});
					
					this.statsInfo = { total, converted, hot };
				}
			} catch (error) {
				console.error('获取统计信息失败:', error);
			}
		},
		
		/**
		 * 获取预览商品
		 */
		async getPreviewGoods() {
			try {
				const result = await dataokeApi.request.getGoodsList({
					page: 1,
					limit: 3,
					showLoading: false
				});
				
				this.previewGoods = result.data.list || [];
			} catch (error) {
				console.error('获取预览商品失败:', error);
			}
		},
		
		/**
		 * 跳转到商品列表
		 */
		goToGoodsList() {
			uni.navigateTo({
				url: '/pages/dataokeGoodsList/dataokeGoodsList'
			});
		},
		
		/**
		 * 测试热门商品
		 */
		async testHotGoods() {
			try {
				uni.showLoading({ title: '加载中...' });
				const result = await dataokeApi.request.getHotGoods({ limit: 1 });
				uni.hideLoading();
				
				uni.showModal({
					title: '热门商品测试',
					content: `获取到 ${result.data.list?.length || 0} 个热门商品`,
					showCancel: false
				});
			} catch (error) {
				uni.hideLoading();
				uni.showToast({
					title: '测试失败',
					icon: 'none'
				});
			}
		},
		
		/**
		 * 测试优惠券商品
		 */
		async testCouponGoods() {
			try {
				uni.showLoading({ title: '加载中...' });
				const result = await dataokeApi.request.getCouponGoods({ limit: 1 });
				uni.hideLoading();
				
				uni.showModal({
					title: '优惠券商品测试',
					content: `获取到 ${result.data.list?.length || 0} 个优惠券商品`,
					showCancel: false
				});
			} catch (error) {
				uni.hideLoading();
				uni.showToast({
					title: '测试失败',
					icon: 'none'
				});
			}
		},
		
		/**
		 * 刷新统计
		 */
		refreshStats() {
			this.getStats();
			this.getPreviewGoods();
			
			uni.showToast({
				title: '刷新成功',
				icon: 'success'
			});
		},
		
		/**
		 * 跳转到商品详情
		 */
		goToDetail(goods) {
			uni.navigateTo({
				url: `/pages/dataokeGoodsDetail/dataokeGoodsDetail?goods_id=${goods.goods_id}&id=${goods.id}`
			});
		},
		
		/**
		 * 格式化价格
		 */
		formatPrice(price) {
			return dataokeApi.utils.formatPrice(price);
		}
	}
}
</script>

<style scoped>
.container {
	background: #f7f7f7;
	min-height: 100vh;
	padding: 20rpx;
}

.header {
	background: linear-gradient(135deg, #e41f19, #ff6b6b);
	border-radius: 16rpx;
	padding: 40rpx;
	margin-bottom: 30rpx;
	text-align: center;
}

.title {
	color: #fff;
	font-size: 36rpx;
	font-weight: bold;
}

.test-section {
	background: #fff;
	border-radius: 16rpx;
	padding: 30rpx;
	margin-bottom: 30rpx;
}

.section-title {
	font-size: 32rpx;
	font-weight: bold;
	color: #333;
	margin-bottom: 30rpx;
}

.test-item {
	display: flex;
	justify-content: space-between;
	align-items: center;
	padding: 20rpx 0;
}

.test-label {
	font-size: 28rpx;
	color: #666;
}

.test-status {
	font-size: 28rpx;
	color: #999;
}

.test-status.success {
	color: #2ed573;
}

.test-status.error {
	color: #ff4757;
}

.stats-grid {
	display: grid;
	grid-template-columns: repeat(3, 1fr);
	gap: 30rpx;
}

.stats-item {
	text-align: center;
	padding: 30rpx 0;
	background: #f8f9fa;
	border-radius: 12rpx;
}

.stats-num {
	display: block;
	font-size: 36rpx;
	font-weight: bold;
	color: #e41f19;
	margin-bottom: 8rpx;
}

.stats-label {
	font-size: 24rpx;
	color: #999;
}

.button-group {
	display: grid;
	grid-template-columns: repeat(2, 1fr);
	gap: 20rpx;
}

.test-btn {
	background: linear-gradient(135deg, #e41f19, #ff6b6b);
	color: #fff;
	padding: 30rpx;
	border-radius: 12rpx;
	text-align: center;
	font-size: 28rpx;
	font-weight: bold;
}

.preview-list {
	display: flex;
	flex-direction: column;
	gap: 20rpx;
}

.preview-item {
	display: flex;
	background: #f8f9fa;
	border-radius: 12rpx;
	padding: 20rpx;
	align-items: center;
}

.preview-image {
	width: 120rpx;
	height: 120rpx;
	border-radius: 8rpx;
	margin-right: 20rpx;
}

.preview-content {
	flex: 1;
}

.preview-title {
	font-size: 26rpx;
	color: #333;
	line-height: 1.4;
	margin-bottom: 12rpx;
	display: -webkit-box;
	-webkit-box-orient: vertical;
	-webkit-line-clamp: 2;
	overflow: hidden;
}

.preview-price {
	display: flex;
	align-items: center;
	gap: 12rpx;
}

.price-current {
	font-size: 28rpx;
	color: #e41f19;
	font-weight: bold;
}

.price-coupon {
	background: #ffa502;
	color: #fff;
	font-size: 20rpx;
	padding: 4rpx 8rpx;
	border-radius: 6rpx;
}

.no-data {
	text-align: center;
	padding: 60rpx 0;
	color: #999;
	font-size: 28rpx;
}
</style>
