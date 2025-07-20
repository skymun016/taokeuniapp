<template>
	<view class="container">
		<view class="header">
			<text class="title">新淘客API测试</text>
		</view>
		
		<view class="test-section">
			<view class="section-title">API连接测试</view>
			<view class="test-item">
				<button @tap="testTaobaoProducts" :disabled="loading">测试淘宝商品列表</button>
				<text class="result">{{ taobaoResult }}</text>
			</view>
			<view class="test-item">
				<button @tap="testJdProducts" :disabled="loading">测试京东商品列表</button>
				<text class="result">{{ jdResult }}</text>
			</view>
			<view class="test-item">
				<button @tap="testSearch" :disabled="loading">测试搜索功能</button>
				<text class="result">{{ searchResult }}</text>
			</view>
		</view>
		
		<view class="test-section">
			<view class="section-title">转链测试</view>
			<view class="test-item">
				<input v-model="testProductId" placeholder="输入商品ID" class="input" />
				<button @tap="testTaobaoConvert" :disabled="loading || !testProductId">测试淘宝转链</button>
				<text class="result">{{ taobaoConvertResult }}</text>
			</view>
			<view class="test-item">
				<button @tap="testJdConvert" :disabled="loading || !testProductId">测试京东转链</button>
				<text class="result">{{ jdConvertResult }}</text>
			</view>
		</view>
		
		<view class="test-section">
			<view class="section-title">API配置信息</view>
			<view class="config-info">
				<text>基础URL: {{ apiConfig.baseUrl }}</text>
				<text>淘宝平台ID: {{ apiConfig.platforms.TAOBAO }}</text>
				<text>京东平台ID: {{ apiConfig.platforms.JD }}</text>
			</view>
		</view>
	</view>
</template>

<script>
import newTaokeApi from '@/common/newTaokeApi.js'

export default {
	data() {
		return {
			loading: false,
			testProductId: '',
			
			// 测试结果
			taobaoResult: '未测试',
			jdResult: '未测试',
			searchResult: '未测试',
			taobaoConvertResult: '未测试',
			jdConvertResult: '未测试',
			
			// API配置
			apiConfig: newTaokeApi.config
		}
	},
	
	methods: {
		/**
		 * 测试淘宝商品列表
		 */
		async testTaobaoProducts() {
			this.loading = true;
			this.taobaoResult = '测试中...';
			
			try {
				const result = await newTaokeApi.request.getTaobaoProducts({
					page: 1,
					page_size: 3,
					showLoading: false
				});
				
				if (result && result.data) {
					this.taobaoResult = `成功！获取到 ${result.data.length} 个商品，总计 ${result.total} 个`;
				} else {
					this.taobaoResult = '失败：返回数据格式异常';
				}
			} catch (error) {
				this.taobaoResult = `失败：${error.message}`;
			} finally {
				this.loading = false;
			}
		},
		
		/**
		 * 测试京东商品列表
		 */
		async testJdProducts() {
			this.loading = true;
			this.jdResult = '测试中...';
			
			try {
				const result = await newTaokeApi.request.getJdProducts({
					page: 1,
					page_size: 3,
					showLoading: false
				});
				
				if (result && result.data) {
					this.jdResult = `成功！获取到 ${result.data.length} 个商品，总计 ${result.total} 个`;
				} else {
					this.jdResult = '失败：返回数据格式异常';
				}
			} catch (error) {
				this.jdResult = `失败：${error.message}`;
			} finally {
				this.loading = false;
			}
		},
		
		/**
		 * 测试搜索功能
		 */
		async testSearch() {
			this.loading = true;
			this.searchResult = '测试中...';
			
			try {
				const result = await newTaokeApi.request.searchProducts({
					keyword: '手机',
					page: 1,
					size: 3,
					showLoading: false
				});
				
				if (result && result.data) {
					this.searchResult = `成功！搜索到 ${result.data.length} 个商品，总计 ${result.total} 个`;
				} else {
					this.searchResult = '失败：返回数据格式异常';
				}
			} catch (error) {
				this.searchResult = `失败：${error.message}`;
			} finally {
				this.loading = false;
			}
		},
		
		/**
		 * 测试淘宝转链
		 */
		async testTaobaoConvert() {
			this.loading = true;
			this.taobaoConvertResult = '测试中...';
			
			try {
				const result = await newTaokeApi.request.generateTaokoling(this.testProductId);
				
				if (result && result.data) {
					this.taobaoConvertResult = `成功！生成淘口令：${result.data.taokoling || '无'}`;
				} else {
					this.taobaoConvertResult = '失败：返回数据格式异常';
				}
			} catch (error) {
				this.taobaoConvertResult = `失败：${error.message}`;
			} finally {
				this.loading = false;
			}
		},
		
		/**
		 * 测试京东转链
		 */
		async testJdConvert() {
			this.loading = true;
			this.jdConvertResult = '测试中...';
			
			try {
				const result = await newTaokeApi.request.generateJdShortlink(this.testProductId);
				
				if (result && result.data) {
					this.jdConvertResult = `成功！生成短链：${result.data.short_url || '无'}`;
				} else {
					this.jdConvertResult = '失败：返回数据格式异常';
				}
			} catch (error) {
				this.jdConvertResult = `失败：${error.message}`;
			} finally {
				this.loading = false;
			}
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
	margin-bottom: 20rpx;
}

.test-item {
	margin-bottom: 30rpx;
}

.test-item button {
	width: 100%;
	background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
	color: #fff;
	border: none;
	border-radius: 8rpx;
	padding: 20rpx;
	font-size: 28rpx;
	margin-bottom: 16rpx;
}

.test-item button[disabled] {
	background: #ccc;
}

.input {
	width: 100%;
	border: 2rpx solid #ddd;
	border-radius: 8rpx;
	padding: 20rpx;
	font-size: 28rpx;
	margin-bottom: 16rpx;
}

.result {
	display: block;
	font-size: 24rpx;
	color: #666;
	line-height: 1.5;
	background: #f8f8f8;
	padding: 16rpx;
	border-radius: 8rpx;
}

.config-info {
	background: #f8f8f8;
	padding: 20rpx;
	border-radius: 8rpx;
}

.config-info text {
	display: block;
	font-size: 24rpx;
	color: #666;
	margin-bottom: 8rpx;
}
</style>
