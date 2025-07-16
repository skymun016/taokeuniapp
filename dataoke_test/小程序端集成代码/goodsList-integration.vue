<!-- 
商品列表页面集成示例
在现有的 goodsList.vue 中添加大淘客商品展示
-->

<template>
	<view class="container">
		<!-- 原有的header和筛选部分保持不变 -->
		<!--header-->
		<view class="tui-header-box">
			<!-- 原有header代码 -->
		</view>
		<!--screen-->
		<view class="tui-header-screen">
			<!-- 原有筛选代码 -->
		</view>

		<!-- 商品列表区域 -->
		<view class="tui-product-list" :style="{ marginTop: px(dropScreenH + 18) }">
			
			<!-- 添加商品类型切换标签 -->
			<view class="goods-type-tabs">
				<view
					class="tab-item"
					:class="{ active: currentTab === 'local' }"
					@tap="switchTab('local')"
				>
					本地商品
				</view>
				<view
					class="tab-item"
					:class="{ active: currentTab === 'hot' }"
					@tap="switchTab('hot')"
				>
					热门优惠
				</view>
				<view
					class="tab-item"
					:class="{ active: currentTab === 'coupon' }"
					@tap="switchTab('coupon')"
				>
					优惠券商品
				</view>
			</view>

			<!-- 本地商品列表 -->
			<view v-show="currentTab === 'local'" class="local-goods-container">
				<view class="tui-product-container">
					<block v-for="(item, index) in goodsList" :key="index">
						<!-- 原有的商品列表项代码 -->
						<view class="tui-pro-item" :class="[isList ? 'tui-flex-list' : 'tui-flex-card']"
							hover-class="tui-hover" :hover-start-time="150"
							:data-url="'/pages/goodsDetail/goodsDetail?id=' + item.id" @tap="navigationTo">
							<image :src="item.pic" class="tui-pro-img" :class="[isList ? 'tui-proimg-list' : '']"
								mode="widthFix" />
							<view class="tui-pro-content">
								<view class="tui-pro-tit">{{ item.name }}</view>
								<view>
									<view class="tui-pro-price">
										<text v-if="item.is_points_goods==1"
											class="tui-sale-price">{{lang.points}}:{{ item.pay_points }}</text>
										<text v-if="item.is_points_goods!=1" class="tui-sale-price">￥{{ item.price }}</text>
										<text v-if="item.original_price>0"
											class="tui-factory-price">￥{{ item.original_price }}</text>
									</view>
									<view v-if="item.sale_count>30" class="tui-pro-pay">{{ item.sale_count }}人付款</view>
								</view>
							</view>
						</view>
					</block>
				</view>
			</view>

			<!-- 热门商品列表 -->
			<view v-show="currentTab === 'hot'" class="dataoke-goods-container">
				<view class="tui-product-container">
					<block v-for="(item, index) in hotGoodsList" :key="'hot_' + index">
						<view class="tui-pro-item dataoke-item" :class="[isList ? 'tui-flex-list' : 'tui-flex-card']"
							hover-class="tui-hover" :hover-start-time="150"
							@tap="openDataokeGoods(item)">

							<!-- 商品图片 -->
							<view class="goods-image-wrapper">
								<image :src="item.pic" class="tui-pro-img" :class="[isList ? 'tui-proimg-list' : '']"
									mode="widthFix" />
								<!-- 热门标签 -->
								<view class="hot-tag">🔥 热门</view>
								<!-- 优惠券标签 -->
								<view v-if="item.coupon_price > 0" class="coupon-tag">
									券¥{{ item.coupon_price }}
								</view>
								<!-- 天猫标签 -->
								<view v-if="item.shop_type == 1" class="tmall-tag">天猫</view>
							</view>

							<!-- 商品信息 -->
							<view class="tui-pro-content">
								<view class="tui-pro-tit dataoke-title">{{ item.title }}</view>
								<view class="dataoke-price-info">
									<view class="tui-pro-price">
										<text class="tui-sale-price dataoke-price">￥{{ formatPrice(item.actual_price) }}</text>
										<text v-if="item.original_price > item.actual_price"
											class="tui-factory-price">￥{{ formatPrice(item.original_price) }}</text>
									</view>
									<!-- 优惠信息 -->
									<view v-if="item.coupon_price > 0" class="coupon-info">
										<text class="coupon-text">券后¥{{ formatPrice(item.actual_price - item.coupon_price) }}</text>
									</view>
								</view>
								<!-- 销量和店铺信息 -->
								<view class="goods-meta">
									<text v-if="item.month_sales > 0" class="sale-count">{{ formatSaleCount(item.month_sales) }}人付款</text>
									<text v-if="item.shop_name" class="shop-name">{{ item.shop_name }}</text>
								</view>
							</view>
						</view>
					</block>
				</view>

				<!-- 加载更多 -->
				<view v-if="hotLoading" class="loading-more">
					<uni-load-more status="loading"></uni-load-more>
				</view>
				<view v-else-if="!hotHasMore && hotGoodsList.length > 0" class="no-more">
					没有更多商品了
				</view>
			</view>

			<!-- 优惠券商品列表 -->
			<view v-show="currentTab === 'coupon'" class="dataoke-goods-container">
				<view class="tui-product-container">
					<block v-for="(item, index) in couponGoodsList" :key="'coupon_' + index">
						<view class="tui-pro-item dataoke-item" :class="[isList ? 'tui-flex-list' : 'tui-flex-card']"
							hover-class="tui-hover" :hover-start-time="150"
							@tap="openDataokeGoods(item)">
							
							<!-- 商品图片 -->
							<view class="goods-image-wrapper">
								<image :src="item.pic" class="tui-pro-img" :class="[isList ? 'tui-proimg-list' : '']"
									mode="widthFix" />
								<!-- 优惠券标签 -->
								<view v-if="item.coupon_price > 0" class="coupon-tag">
									券¥{{ item.coupon_price }}
								</view>
								<!-- 天猫标签 -->
								<view v-if="item.shop_type == 1" class="tmall-tag">天猫</view>
								<!-- 转链状态 -->
								<view v-if="item.link_status == 1" class="converted-tag">已转链</view>
							</view>
							
							<!-- 商品信息 -->
							<view class="tui-pro-content">
								<view class="tui-pro-tit dataoke-title">{{ item.name }}</view>
								<view class="dataoke-price-info">
									<view class="tui-pro-price">
										<text class="tui-sale-price dataoke-price">￥{{ formatPrice(item.price) }}</text>
										<text v-if="item.original_price > item.price" 
											class="tui-factory-price">￥{{ formatPrice(item.original_price) }}</text>
									</view>
									<!-- 优惠信息 -->
									<view v-if="item.coupon_price > 0" class="coupon-info">
										<text class="coupon-text">券后¥{{ formatPrice(item.price - item.coupon_price) }}</text>
									</view>
								</view>
								<!-- 销量和店铺信息 -->
								<view class="goods-meta">
									<text v-if="item.sale_count > 0" class="sale-count">{{ formatSaleCount(item.sale_count) }}人付款</text>
									<text v-if="item.shop_name" class="shop-name">{{ item.shop_name }}</text>
								</view>
							</view>
						</view>
					</block>
				</view>

				<!-- 加载更多 -->
				<view v-if="dataokeLoading" class="loading-more">
					<uni-load-more status="loading"></uni-load-more>
				</view>
				<view v-else-if="!dataokeHasMore && dataokeGoodsList.length > 0" class="no-more">
					没有更多商品了
				</view>
			</view>
		</view>
	</view>
</template>

<script>
import dataokeApi from '@/common/dataoke-request.js'

export default {
	data() {
		return {
			// 原有数据保持不变
			goodsList: [],
			
			// 新增大淘客相关数据
			currentTab: 'local', // 当前选中的标签页
			dataokeGoodsList: [], // 大淘客商品列表
			dataokeLoading: false, // 大淘客商品加载状态
			dataokeHasMore: true, // 是否还有更多大淘客商品
			dataokeCurrentPage: 1, // 大淘客商品当前页码
			dataokePageSize: 20, // 大淘客商品每页数量
		}
	},
	
	onLoad(options) {
		// 原有的onLoad逻辑保持不变
		
		// 加载大淘客商品
		this.loadDataokeGoods();
	},
	
	onReachBottom() {
		// 根据当前标签页加载更多
		if (this.currentTab === 'local') {
			// 原有的加载更多逻辑
		} else if (this.currentTab === 'dataoke') {
			this.loadMoreDataokeGoods();
		}
	},
	
	methods: {
		// 原有方法保持不变
		
		/**
		 * 切换商品类型标签
		 */
		switchTab(tab) {
			this.currentTab = tab;
			
			// 如果切换到大淘客商品且还没有数据，则加载
			if (tab === 'dataoke' && this.dataokeGoodsList.length === 0) {
				this.loadDataokeGoods();
			}
		},
		
		/**
		 * 加载大淘客商品
		 */
		async loadDataokeGoods() {
			if (this.dataokeLoading) return;
			
			this.dataokeLoading = true;
			
			try {
				const params = {
					page: 1,
					pageSize: this.dataokePageSize,
					minCoupon: 10, // 只显示有10元以上优惠券的商品
					sort: 2, // 按销量排序
					showLoading: this.dataokeGoodsList.length === 0 // 首次加载显示loading
				};
				
				const result = await dataokeApi.request.getGoodsList(params);
				
				this.dataokeGoodsList = result.data.list || [];
				this.dataokeCurrentPage = 1;
				this.dataokeHasMore = result.data.hasMore || false;
				
				console.log('大淘客商品加载成功:', this.dataokeGoodsList.length);
				
			} catch (error) {
				console.error('加载大淘客商品失败:', error);
				uni.showToast({
					title: '加载优惠商品失败',
					icon: 'none'
				});
			} finally {
				this.dataokeLoading = false;
			}
		},
		
		/**
		 * 加载更多大淘客商品
		 */
		async loadMoreDataokeGoods() {
			if (this.dataokeLoading || !this.dataokeHasMore) return;
			
			this.dataokeLoading = true;
			
			try {
				const params = {
					page: this.dataokeCurrentPage + 1,
					pageSize: this.dataokePageSize,
					minCoupon: 10,
					sort: 2,
					showLoading: false
				};
				
				const result = await dataokeApi.request.getGoodsList(params);
				
				if (result.data.list && result.data.list.length > 0) {
					this.dataokeGoodsList.push(...result.data.list);
					this.dataokeCurrentPage++;
					this.dataokeHasMore = result.data.hasMore || false;
				} else {
					this.dataokeHasMore = false;
				}
				
			} catch (error) {
				console.error('加载更多大淘客商品失败:', error);
				uni.showToast({
					title: '加载失败',
					icon: 'none'
				});
			} finally {
				this.dataokeLoading = false;
			}
		},
		
		/**
		 * 打开大淘客商品详情
		 */
		openDataokeGoods(goods) {
			// 可以跳转到商品详情页面，或者打开优惠券链接
			if (goods.coupon_link) {
				// 复制优惠券链接到剪贴板
				uni.setClipboardData({
					data: goods.coupon_link,
					success: () => {
						uni.showToast({
							title: '优惠券链接已复制',
							icon: 'success'
						});
					}
				});
			} else if (goods.item_link) {
				// 复制商品链接到剪贴板
				uni.setClipboardData({
					data: goods.item_link,
					success: () => {
						uni.showToast({
							title: '商品链接已复制',
							icon: 'success'
						});
					}
				});
			}
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
		}
	}
}
</script>

<style>
/* 原有样式保持不变 */

/* 新增大淘客相关样式 */
.goods-type-tabs {
	display: flex;
	background: #fff;
	border-bottom: 1px solid #f0f0f0;
	position: sticky;
	top: 0;
	z-index: 10;
}

.tab-item {
	flex: 1;
	text-align: center;
	padding: 15px 0;
	font-size: 14px;
	color: #666;
	position: relative;
}

.tab-item.active {
	color: #e41f19;
	font-weight: bold;
}

.tab-item.active::after {
	content: '';
	position: absolute;
	bottom: 0;
	left: 50%;
	transform: translateX(-50%);
	width: 30px;
	height: 2px;
	background: #e41f19;
}

.dataoke-item {
	position: relative;
}

.goods-image-wrapper {
	position: relative;
}

.coupon-tag {
	position: absolute;
	top: 5px;
	left: 5px;
	background: linear-gradient(135deg, #ff6b6b, #ff8e8e);
	color: white;
	font-size: 10px;
	padding: 2px 6px;
	border-radius: 8px;
	z-index: 2;
}

.tmall-tag {
	position: absolute;
	top: 5px;
	right: 5px;
	background: #ff5000;
	color: white;
	font-size: 10px;
	padding: 2px 6px;
	border-radius: 8px;
	z-index: 2;
}

.dataoke-title {
	color: #333;
	line-height: 1.4;
}

.dataoke-price-info {
	margin-top: 8px;
}

.dataoke-price {
	color: #e41f19;
	font-weight: bold;
}

.coupon-info {
	margin-top: 4px;
}

.coupon-text {
	background: #fff2f0;
	color: #e41f19;
	font-size: 12px;
	padding: 2px 6px;
	border-radius: 4px;
	border: 1px solid #ffccc7;
}

.goods-meta {
	display: flex;
	justify-content: space-between;
	align-items: center;
	margin-top: 8px;
	font-size: 12px;
	color: #999;
}

.sale-count {
	color: #666;
}

.shop-name {
	color: #999;
	max-width: 100px;
	overflow: hidden;
	text-overflow: ellipsis;
	white-space: nowrap;
}

.loading-more, .no-more {
	text-align: center;
	padding: 20px;
	color: #999;
	font-size: 14px;
}
</style>
