<template>
	<view class="container">
		<view class="header">
			<text class="title">商品列表测试页面</text>
		</view>
		
		<view class="test-section">
			<view class="section-title">测试不同平台的商品列表</view>
			
			<view class="test-item">
				<button @tap="testAllProducts" class="test-btn">测试全平台商品列表</button>
			</view>
			
			<view class="test-item">
				<button @tap="testTaobaoProducts" class="test-btn taobao-btn">测试淘宝商品列表</button>
			</view>
			
			<view class="test-item">
				<button @tap="testJdProducts" class="test-btn jd-btn">测试京东商品列表</button>
			</view>
			
			<view class="test-item">
				<button @tap="testSearchProducts" class="test-btn search-btn">测试搜索功能（手机）</button>
			</view>
		</view>
		
		<view class="test-section">
			<view class="section-title">API连接状态</view>
			<view class="status-item">
				<text class="status-label">API基础URL:</text>
				<text class="status-value">{{ apiConfig.baseUrl }}</text>
			</view>
			<view class="status-item">
				<text class="status-label">淘宝平台ID:</text>
				<text class="status-value">{{ apiConfig.platforms.TAOBAO }}</text>
			</view>
			<view class="status-item">
				<text class="status-label">京东平台ID:</text>
				<text class="status-value">{{ apiConfig.platforms.JD }}</text>
			</view>
		</view>
	</view>
</template>

<script>
import newTaokeApi from '@/common/newTaokeApi.js'

export default {
	data() {
		return {
			apiConfig: newTaokeApi.config
		}
	},
	
	methods: {
		/**
		 * 测试全平台商品列表
		 */
		testAllProducts() {
			uni.navigateTo({
				url: '/pages/goodsList/goodsList?platform=0'
			});
		},
		
		/**
		 * 测试淘宝商品列表
		 */
		testTaobaoProducts() {
			uni.navigateTo({
				url: '/pages/goodsList/goodsList?platform=1'
			});
		},
		
		/**
		 * 测试京东商品列表
		 */
		testJdProducts() {
			uni.navigateTo({
				url: '/pages/goodsList/goodsList?platform=2'
			});
		},
		
		/**
		 * 测试搜索功能
		 */
		testSearchProducts() {
			uni.navigateTo({
				url: '/pages/goodsList/goodsList?keyword=手机&platform=0'
			});
		}
	}
}
</script>

<style scoped>
.container {
	padding: 20rpx;
	background: #f5f5f5;
	min-height: 100vh;
}

.header {
	text-align: center;
	padding: 40rpx 0;
	background: #fff;
	border-radius: 12rpx;
	margin-bottom: 30rpx;
}

.title {
	font-size: 36rpx;
	font-weight: bold;
	color: #333;
}

.test-section {
	background: #fff;
	margin-bottom: 30rpx;
	border-radius: 12rpx;
	padding: 30rpx;
}

.section-title {
	font-size: 32rpx;
	font-weight: bold;
	color: #333;
	margin-bottom: 30rpx;
}

.test-item {
	margin-bottom: 20rpx;
}

.test-btn {
	width: 100%;
	background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
	color: #fff;
	border: none;
	border-radius: 8rpx;
	padding: 25rpx;
	font-size: 28rpx;
}

.taobao-btn {
	background: linear-gradient(135deg, #ff6a00 0%, #ee0a24 100%);
}

.jd-btn {
	background: linear-gradient(135deg, #e93323 0%, #ed4014 100%);
}

.search-btn {
	background: linear-gradient(135deg, #52c41a 0%, #389e0d 100%);
}

.status-item {
	display: flex;
	justify-content: space-between;
	align-items: center;
	padding: 15rpx 0;
	border-bottom: 1rpx solid #f0f0f0;
}

.status-item:last-child {
	border-bottom: none;
}

.status-label {
	font-size: 28rpx;
	color: #666;
}

.status-value {
	font-size: 26rpx;
	color: #333;
	font-weight: bold;
}
</style>
