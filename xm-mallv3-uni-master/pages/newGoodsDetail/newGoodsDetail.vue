<template>
	<view class="container">
		<!-- 自定义导航栏 -->
		<view class="tui-header-box">
			<view class="tui-header" :style="{ width: width + 'px', height: height + 'px' }">
				<view class="tui-back" :style="{ marginTop: arrowTop + 'px' }" @tap="back">
					<tui-icon name="arrowleft" color="#000"></tui-icon>
				</view>
				<view class="tui-title-absolute" :style="{ marginTop: titleTop + 'px', width: width + 'px' }">
					<text class="tui-title-text">商品详情</text>
				</view>
			</view>
		</view>

		<!-- 可滚动内容区域 -->
		<scroll-view
			scroll-y
			class="tui-scroll-content"
			:style="{ height: scrollHeight + 'px', marginTop: height + 'px' }"
			:enable-back-to-top="true"
			:scroll-with-animation="false"
			:enhanced="true"
			:bounces="false"
			@scroll="onScroll">

			<!-- 商品图片轮播 -->
			<view class="tui-banner-swiper">
				<swiper :autoplay="false" :interval="5000" :duration="300" :circular="true"
					class="tui-square-swiper" @change="bannerChange">
					<block v-for="(item, index) in goodsImages" :key="index">
						<swiper-item class="swiper" :data-index="index" @tap.stop="previewImage">
							<image mode="aspectFill" :src="item" class="tui-slide-image" />
						</swiper-item>
					</block>
				</swiper>

				<!-- 图片指示器 -->
				<view class="tui-banner-indicators" v-if="goodsImages.length > 1">
					<view class="tui-indicator"
						v-for="(item, index) in goodsImages"
						:key="index"
						:class="{ 'tui-indicator-active': index === bannerIndex }">
					</view>
				</view>

				<!-- 图片计数 -->
				<view class="tui-banner-count">
					<text class="tui-count-text">{{ bannerIndex + 1 }}/{{ goodsImages.length }}</text>
				</view>
			</view>

			<!-- 商品基本信息 -->
			<view class="tui-goods-info">
				<!-- 平台标识 -->
				<view class="tui-platform-badge" :class="'tui-platform-' + goodsInfo.platform" v-if="goodsInfo.platform">
					{{ goodsInfo.platform_name }}
				</view>

				<!-- 商品标题 -->
				<view class="tui-goods-title">{{ goodsInfo.title || goodsInfo.short_title }}</view>

				<!-- 价格信息 -->
				<view class="tui-price-section">
					<view class="tui-price-main">
						<text class="tui-price-symbol">¥</text>
						<text class="tui-price-num">{{ formatPrice(goodsInfo.coupon_price || goodsInfo.price) }}</text>
						<text v-if="goodsInfo.price > (goodsInfo.coupon_price || goodsInfo.price)"
							class="tui-price-original">¥{{ formatPrice(goodsInfo.price) }}</text>
					</view>
				</view>

				<!-- 优惠券信息 - 优惠券金额格式 -->
				<view class="tui-coupon-section" v-if="hasCoupon">
					<view class="tui-coupon-simple">
						<!-- 优惠券金额：¥xx 格式 -->
						<text class="tui-coupon-text" v-if="goodsInfo.coupon_amount && goodsInfo.coupon_amount > 0">优惠券金额：¥{{ formatPrice(goodsInfo.coupon_amount) }}</text>
						<text class="tui-coupon-text" v-else-if="goodsInfo.coupon_price && goodsInfo.price && goodsInfo.coupon_price < goodsInfo.price">优惠券金额：¥{{ formatPrice(goodsInfo.price - goodsInfo.coupon_price) }}</text>
						<text class="tui-coupon-text" v-else>优惠券</text>
					</view>
				</view>



				<!-- 商品信息 -->
				<view class="tui-goods-meta">
					<view class="tui-meta-item" v-if="goodsInfo.shop_name">
						<text class="tui-meta-label">店铺</text>
						<text class="tui-meta-value">{{ goodsInfo.shop_name }}</text>
					</view>
					<view class="tui-meta-item" v-if="goodsInfo.sales_volume > 0">
						<text class="tui-meta-label">销量</text>
						<text class="tui-meta-value">{{ goodsInfo.sales_volume }}人付款</text>
					</view>
					<view class="tui-meta-item" v-if="goodsInfo.category_name">
						<text class="tui-meta-label">分类</text>
						<text class="tui-meta-value">{{ goodsInfo.category_name }}</text>
					</view>
				</view>
			</view>

			<!-- 商品详情描述 -->
			<view class="tui-goods-desc" v-if="goodsInfo.title">
				<view class="tui-desc-title">商品详情</view>
				<view class="tui-desc-content">
					<text class="tui-desc-text">{{ goodsInfo.title }}</text>
				</view>
			</view>
		</scroll-view>

		<!-- 底部操作栏 -->
		<view class="tui-bottom-bar">
			<view class="tui-bar-buttons">
				<!-- 平台助手按钮 -->
				<!-- #ifdef MP-WEIXIN -->
				<button v-if="configkefu.minionline==1 || configkefu.minionline==3" @click="toCustomerService"
					class="tui-helper-btn" :class="'tui-helper-' + goodsInfo.platform">
					<text class="tui-helper-text">{{ getPlatformHelperText() }}</text>
				</button>
				<button v-else-if="configkefu.minionline==2" @click="toCustomerServiceWebview"
					class="tui-helper-btn" :class="'tui-helper-' + goodsInfo.platform">
					<text class="tui-helper-text">{{ getPlatformHelperText() }}</text>
				</button>
				<button v-else open-type="contact"
					class="tui-helper-btn" :class="'tui-helper-' + goodsInfo.platform">
					<text class="tui-helper-text">{{ getPlatformHelperText() }}</text>
				</button>
				<!-- #endif -->
				<!-- #ifndef MP-WEIXIN -->
				<button @click="toCustomerService"
					class="tui-helper-btn" :class="'tui-helper-' + goodsInfo.platform">
					<text class="tui-helper-text">{{ getPlatformHelperText() }}</text>
				</button>
				<!-- #endif -->

				<!-- 立即购买按钮 -->
				<view class="tui-buy-btn" @tap="buyNow" :class="{ 'tui-loading': loading }">
					<text class="tui-buy-text">{{ getBuyButtonText() }}</text>
				</view>
			</view>
		</view>

		<!-- 加载状态 -->
		<view class="tui-loading-overlay" v-if="pageLoading">
			<view class="tui-loading-content">
				<text class="tui-loading-text">加载中...</text>
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
			bannerIndex: 0,

			// 滚动相关
			scrollHeight: 0,
			scrollTop: 0,
			bottomBarHeight: 88, // 底部操作栏高度（实际高度）
			scrollTimer: null, // 滚动节流定时器

			// 商品信息
			productId: '',
			platform: 1,
			goodsInfo: {},
			goodsImages: [],
			linkInfo: null,

			// 页面状态
			pageLoading: false,
			loading: false,
			isCollected: false,

			// 客服配置
			configkefu: {},

			// 弹窗相关
			showModal: false,
			modalTitle: '',
			modalContent: ''
		}
	},

	onLoad(options) {
		console.log('新商品详情页接收参数:', options);

		this.productId = options.product_id || '';
		this.platform = parseInt(options.platform) || 1;

		if (!this.productId) {
			uni.showToast({
				title: '商品参数错误',
				icon: 'none'
			});
			setTimeout(() => {
				uni.navigateBack();
			}, 1500);
			return;
		}

		this.getSystemInfo();
		this.loadGoodsDetail();
		this.loadKefuConfig();
	},

	onUnload() {
		// 页面销毁时清理定时器，防止内存泄漏
		if (this.scrollTimer) {
			clearTimeout(this.scrollTimer);
			this.scrollTimer = null;
		}
	},

	computed: {
		/**
		 * 判断是否有优惠券 - 增强版本
		 */
		hasCoupon() {
			console.log('🎫 计算优惠券显示状态:', {
				coupon_amount: this.goodsInfo.coupon_amount,
				coupon_info: this.goodsInfo.coupon_info,
				coupon_condition: this.goodsInfo.coupon_condition,
				coupon_price: this.goodsInfo.coupon_price,
				price: this.goodsInfo.price
			});

			// 有优惠券金额
			if (this.goodsInfo.coupon_amount && this.goodsInfo.coupon_amount > 0) {
				console.log('✅ 有优惠券金额:', this.goodsInfo.coupon_amount);
				return true;
			}

			// 有优惠券信息且不是默认的"满0元减0元"
			if (this.goodsInfo.coupon_info &&
				this.goodsInfo.coupon_info !== '满0元减0元' &&
				this.goodsInfo.coupon_info.trim() !== '') {
				console.log('✅ 有优惠券信息:', this.goodsInfo.coupon_info);
				return true;
			}

			// 券后价小于原价
			if (this.goodsInfo.coupon_price && this.goodsInfo.price &&
				parseFloat(this.goodsInfo.coupon_price) < parseFloat(this.goodsInfo.price)) {
				console.log('✅ 券后价小于原价:', this.goodsInfo.coupon_price, '<', this.goodsInfo.price);
				return true;
			}

			// 有优惠券条件信息
			if (this.goodsInfo.coupon_condition && this.goodsInfo.coupon_condition > 0) {
				console.log('✅ 有优惠券条件:', this.goodsInfo.coupon_condition);
				return true;
			}

			console.log('❌ 没有优惠券信息');
			return false;
		}
	},

	methods: {
		/**
		 * 获取平台助手按钮文字
		 */
		getPlatformHelperText() {
			if (this.goodsInfo.platform === 1) {
				return '淘宝助手';
			} else if (this.goodsInfo.platform === 2) {
				return '京东助手';
			}
			return '平台助手';
		},

		/**
		 * 获取购买按钮文字
		 */
		getBuyButtonText() {
			if (this.loading) {
				return '转链中...';
			}
			return this.hasCoupon ? '领券购买' : '立即购买';
		},

		/**
		 * 客服功能 - 根据配置调用不同的客服方式
		 */
		toCustomerService() {
			if (this.configkefu.minionline == 2 && this.configkefu.kefuurl) {
				// 跳转到客服网页
				uni.navigateTo({
					url: `/pages/webview/h5?url=${this.configkefu.kefuurl}`
				});
			} else if (this.configkefu.minionline == 3) {
				// 拨打客服电话
				const telstr = this.configkefu.kefutel;
				uni.makePhoneCall({
					phoneNumber: telstr
				});
			} else {
				// 跳转到内置客服页面
				uni.navigateTo({
					url: '/pages/im/h5'
				});
			}
		},

		/**
		 * 客服网页版
		 */
		toCustomerServiceWebview() {
			if (this.configkefu.kefuurl) {
				uni.navigateTo({
					url: `/pages/webview/h5?url=${this.configkefu.kefuurl}`
				});
			}
		},

		/**
		 * 加载客服配置
		 */
		async loadKefuConfig() {
			try {
				// 使用与首页相同的API获取客服配置
				const response = await this.$request.get('diypage.indexv2', {
					samkey: (new Date()).valueOf(),
					id: 0 // 使用默认ID获取基础配置
				});

				if (response.errno === 0 && response.data && response.data.config) {
					this.configkefu = response.data.config.kefu || {};
					console.log('客服配置加载成功:', this.configkefu);
				}
			} catch (error) {
				console.error('加载客服配置失败:', error);
				// 设置默认配置，确保按钮可以正常显示
				this.configkefu = {
					minionline: 0 // 默认使用小程序原生客服
				};
			}
		},

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

			// 计算滚动区域高度：屏幕高度 - 导航栏高度 - 底部操作栏高度 - 安全区域
			const safeAreaBottom = systemInfo.safeAreaInsets ? systemInfo.safeAreaInsets.bottom : 0;
			// 底部操作栏实际高度 = padding(20rpx) + 内容高度(约48rpx) + padding-bottom(20rpx + 安全区域)
			const actualBottomBarHeight = uni.upx2px(40) + 48 + safeAreaBottom; // 约88rpx + 安全区域
			this.scrollHeight = systemInfo.windowHeight - this.height - actualBottomBarHeight;

			console.log('📐 页面尺寸信息:', {
				windowHeight: systemInfo.windowHeight,
				windowWidth: systemInfo.windowWidth,
				headerHeight: this.height,
				bottomBarHeight: this.bottomBarHeight,
				actualBottomBarHeight: actualBottomBarHeight,
				safeAreaBottom: safeAreaBottom,
				scrollHeight: this.scrollHeight
			});
		},

		/**
		 * 格式化价格
		 */
		formatPrice(price) {
			if (!price) return '0.00';
			return parseFloat(price).toFixed(2);
		},

		/**
		 * 加载商品详情 - 增强版本
		 */
		async loadGoodsDetail() {
			this.pageLoading = true;

			try {
				console.log('📦 加载商品详情，product_id:', this.productId, 'platform:', this.platform);

				// 直接从存储中获取商品信息
				const cachedGoodsInfo = uni.getStorageSync('currentGoodsInfo');

				if (cachedGoodsInfo && cachedGoodsInfo.product_id === this.productId) {
					console.log('📦 从缓存加载商品详情:', cachedGoodsInfo);

					// 详细的优惠券信息检查
					console.log('🎫 优惠券信息详细检查:', {
						coupon_amount: cachedGoodsInfo.coupon_amount,
						coupon_amount_type: typeof cachedGoodsInfo.coupon_amount,
						coupon_info: cachedGoodsInfo.coupon_info,
						coupon_condition: cachedGoodsInfo.coupon_condition,
						price: cachedGoodsInfo.price,
						price_type: typeof cachedGoodsInfo.price,
						coupon_price: cachedGoodsInfo.coupon_price,
						coupon_price_type: typeof cachedGoodsInfo.coupon_price
					});

					// 数据类型转换和验证
					this.goodsInfo = this.processGoodsData(cachedGoodsInfo);
					this.setupGoodsImages();

					// 清除缓存
					uni.removeStorageSync('currentGoodsInfo');

					console.log('✅ 商品详情加载成功:', this.goodsInfo);

					// 如果缓存中的优惠券信息不完整，尝试预加载完整信息
					if (!this.hasCoupon || this.needsMoreCouponInfo()) {
						console.log('🔄 优惠券信息不完整，尝试预加载...');
						this.preloadCouponInfo();
					}

					// 强制触发优惠券计算
					this.$nextTick(() => {
						console.log('🔄 强制更新优惠券显示状态:', this.hasCoupon);
					});
				} else {
					throw new Error('商品信息已过期，请重新选择商品');
				}

			} catch (error) {
				console.error('❌ 加载商品详情失败:', error);
				uni.showToast({
					title: error.message || '加载失败',
					icon: 'none'
				});
				setTimeout(() => {
					uni.navigateBack();
				}, 1500);
			} finally {
				this.pageLoading = false;
			}
		},

		/**
		 * 处理商品数据 - 确保数据类型正确
		 */
		processGoodsData(rawData) {
			const processedData = { ...rawData };

			// 确保数值类型字段是数字
			const numericFields = ['coupon_amount', 'price', 'coupon_price', 'coupon_condition', 'sales_volume'];
			numericFields.forEach(field => {
				if (processedData[field] !== undefined && processedData[field] !== null) {
					const numValue = parseFloat(processedData[field]);
					processedData[field] = isNaN(numValue) ? 0 : numValue;
				}
			});

			// 确保字符串类型字段是字符串
			const stringFields = ['coupon_info', 'title', 'short_title', 'shop_name'];
			stringFields.forEach(field => {
				if (processedData[field] !== undefined && processedData[field] !== null) {
					processedData[field] = String(processedData[field]);
				}
			});

			console.log('🔧 数据处理完成:', processedData);
			return processedData;
		},

		/**
		 * 设置商品图片
		 */
		setupGoodsImages() {
			this.goodsImages = [];

			// 添加主图
			if (this.goodsInfo.main_image) {
				this.goodsImages.push(this.goodsInfo.main_image);
			}

			// 如果没有图片，使用默认图片
			if (this.goodsImages.length === 0) {
				this.goodsImages.push('/static/images/default_img.png');
			}
		},

		/**
		 * 检查是否需要更多优惠券信息
		 */
		needsMoreCouponInfo() {
			// 如果没有任何优惠券相关信息，则需要预加载
			return !this.goodsInfo.coupon_amount &&
				   !this.goodsInfo.coupon_info &&
				   !this.goodsInfo.coupon_condition &&
				   !this.goodsInfo.coupon_price;
		},

		/**
		 * 预加载优惠券信息
		 */
		async preloadCouponInfo() {
			try {
				console.log('🔄 开始预加载优惠券信息...');

				// 调用转链接口获取完整信息（但不显示转链结果）
				const response = await newTaokeApi.request.convertSingleProduct(this.productId, this.platform);

				if (response) {
					console.log('✅ 预加载获取到数据:', response);

					// 提取优惠券相关信息并更新
					let productData = null;
					if (response.status === 'success' && response.data) {
						productData = response.data;
					} else if (response.price || response.taokouling) {
						productData = response;
					} else if (response.data) {
						productData = response.data;
					}

					if (productData) {
						// 只更新优惠券相关字段
						const couponFields = ['coupon_amount', 'coupon_info', 'coupon_condition', 'coupon_price'];
						couponFields.forEach(field => {
							if (productData[field] !== undefined) {
								console.log(`🎫 更新优惠券字段 ${field}:`, productData[field]);
								this.goodsInfo[field] = productData[field];
							}
						});

						// 强制更新UI
						this.$forceUpdate();
						console.log('✅ 优惠券信息预加载完成');
					}
				}
			} catch (error) {
				console.log('⚠️ 预加载优惠券信息失败，将在点击购买时获取:', error.message);
			}
		},

		/**
		 * 滚动事件处理 - 添加节流优化，减少闪动
		 */
		onScroll(e) {
			// 使用节流避免频繁更新导致的闪动
			if (this.scrollTimer) {
				clearTimeout(this.scrollTimer);
			}

			this.scrollTimer = setTimeout(() => {
				// 只在必要时更新 scrollTop，避免不必要的重渲染
				const newScrollTop = e.detail.scrollTop;
				if (Math.abs(newScrollTop - this.scrollTop) > 5) {
					this.scrollTop = newScrollTop;
				}
			}, 16); // 约60fps的更新频率
		},



		/**
		 * 轮播图切换
		 */
		bannerChange(e) {
			this.bannerIndex = e.detail.current;
		},

		/**
		 * 预览图片
		 */
		previewImage(e) {
			let index = e.currentTarget.dataset.index;
			uni.previewImage({
				current: this.goodsImages[index],
				urls: this.goodsImages
			});
		},

		/**
		 * 返回
		 */
		back() {
			uni.navigateBack();
		},



		/**
		 * 立即购买 - 转链
		 */
		async buyNow() {
			if (this.loading) return;

			console.log('开始转链，product_id:', this.productId, 'platform:', this.platform);

			if (!this.productId) {
				uni.showToast({
					title: '商品ID无效',
					icon: 'none',
					duration: 2000
				});
				return;
			}

			this.loading = true;

			try {
				// 显示加载中提示
				uni.showLoading({
					title: '转链中...',
					mask: true
				});

				// 调用转链接口
				console.log('调用convertSingleProduct，参数:', { 
					productId: this.productId, 
					platform: this.platform 
				});

				const response = await newTaokeApi.request.convertSingleProduct(this.productId, this.platform);
				console.log('转链API返回原始数据:', response);

				if (!response) {
					throw new Error('API返回数据为空');
				}

				// 保存完整的响应数据
				this.linkInfo = response;
				
				// 处理响应
				this.handlePurchaseResult();

			} catch (error) {
				console.error('转链失败:', error);
				uni.showToast({
					title: error.message || '转链失败，请重试',
					icon: 'none',
					duration: 3000
				});
			} finally {
				uni.hideLoading();
				this.loading = false;
			}
		},

		/**
		 * 处理购买结果
		 */
		handlePurchaseResult() {
			console.log('🔄 开始处理购买结果，原始数据:', this.linkInfo);
			console.log('🔄 当前平台:', this.platform, this.platform === 1 ? '(淘宝)' : this.platform === 2 ? '(京东)' : '(未知)');

			try {
				// 确保有响应数据
				if (!this.linkInfo) {
					throw new Error('linkInfo 为空');
				}

				// 根据API响应格式处理数据
				let productData = null;

				console.log('🔍 分析API响应格式...');
				console.log('🔍 linkInfo.status:', this.linkInfo.status);
				console.log('🔍 linkInfo.data 存在:', !!this.linkInfo.data);
				console.log('🔍 linkInfo.price 存在:', !!this.linkInfo.price);
				console.log('🔍 linkInfo.taokouling 存在:', !!this.linkInfo.taokouling);

				// 情况1: 标准成功响应 {status: 'success', data: {...}}
				if (this.linkInfo.status === 'success' && this.linkInfo.data) {
					console.log('✅ 使用标准成功响应格式');
					productData = this.linkInfo.data;
				}
				// 情况2: 直接返回数据对象
				else if (this.linkInfo.price || this.linkInfo.taokouling) {
					console.log('✅ 使用直接数据对象格式');
					productData = this.linkInfo;
				}
				// 情况3: 其他格式
				else if (this.linkInfo.data) {
					console.log('✅ 使用其他数据格式');
					productData = this.linkInfo.data;
				}

				if (!productData) {
					console.error('❌ 无法解析商品数据，linkInfo结构:', Object.keys(this.linkInfo));
					throw new Error('无法解析商品数据');
				}

				console.log('✅ 商品数据解析成功:', productData);
				console.log('🔍 关键字段检查:');
				console.log('  - taokouling:', productData.taokouling);
				console.log('  - taobao_command:', productData.taobao_command);
				console.log('  - title:', productData.title);
				console.log('  - price:', productData.price);

				// 更新商品信息
				const updateFields = [
					'price', 'coupon_price', 'coupon_amount', 'shop_name',
					'sales_volume', 'commission_rate', 'taokouling', 'short_url',
					'short_url2', 'item_url', 'coupon_click_url', 'title'
				];

				console.log('🔄 更新商品信息字段...');
				updateFields.forEach(field => {
					if (productData[field] !== undefined) {
						console.log(`  - 更新 ${field}:`, productData[field]);
						this.goodsInfo[field] = productData[field];
					}
				});

				// 更新UI
				this.$forceUpdate();

				// 根据平台调用对应的处理方法
				console.log('🚀 准备调用平台处理方法...');
				if (this.platform === 1) {
					console.log('🛒 调用淘宝商品处理方法');
					this.handleTaobaoPurchase(productData);
				} else if (this.platform === 2) {
					console.log('🛒 调用京东商品处理方法');
					this.handleJdPurchase(productData);
				} else {
					console.warn('⚠️ 未知平台类型:', this.platform);
					throw new Error('未知平台类型: ' + this.platform);
				}

			} catch (error) {
				console.error('❌ 处理购买结果失败:', error);
				console.error('❌ 错误堆栈:', error.stack);
				uni.showToast({
					title: error.message || '处理商品信息失败',
					icon: 'none',
					duration: 3000
				});
			}
		},

		/**
		 * 处理淘宝商品购买
	 * @param {Object} data - 商品数据
	 */
	handleTaobaoPurchase(data) {
		console.log('🎯 开始处理淘宝商品购买，转链结果:', data);

		// 调试：输出完整的API响应数据
		console.log('📋 完整的API响应数据:', JSON.stringify(data, null, 2));

		try {
			// 构建简洁的推广文案（类似京东处理方式）
			const title = data.title || this.goodsInfo.title || '淘宝好物';
			const price = this.formatPrice(data.coupon_price || data.price || this.goodsInfo.coupon_price || this.goodsInfo.price);

			// 简洁的推广文案
			let promoContent = '';

			// 如果有完整的淘宝口令，直接使用
			if (data.taobao_command) {
				promoContent = data.taobao_command;

				// 清理旧的使用提示，避免重复显示
				const oldTipPatterns = [
					/点击复制按钮后\s*\n\s*打开淘宝APP直接购买/g,
					/点击复制按钮后\s+打开淘宝APP直接购买/g,
					/点击复制按钮后打开淘宝APP直接购买/g,
					/点击复制按钮后\s*\n\s*打开助手并粘贴发送/g,
					/点击复制按钮后\s+打开助手并粘贴发送/g,
					/点击复制按钮后打开助手并粘贴发送/g
				];

				// 移除所有旧的使用提示
				oldTipPatterns.forEach(pattern => {
					promoContent = promoContent.replace(pattern, '');
				});

				// 清理可能的多余换行符
				promoContent = promoContent.replace(/\n\s*\n\s*\n/g, '\n\n').trim();
			}
			// 如果有淘口令，构建简单格式
			else if (data.taokouling || data.taoKouLing) {
				promoContent = `【淘宝】【${price}元】${title}\n${data.taokouling || data.taoKouLing}`;
			}
			// 其他情况，构建基本格式
			else {
				promoContent = `【淘宝】【${price}元】${title}`;
				if (data.short_url || data.shortUrl) {
					promoContent += `\n${data.short_url || data.shortUrl}`;
				}
			}

			// 在"点击复制按钮后"上方添加两行内容
			promoContent += '\n更多优惠\nhttps://s.click.taobao.com/geyPIIr\n点击复制按钮后\n打开助手并粘贴发送';

			console.log('✅ 淘宝文案生成完成');
			console.log('📄 推广内容长度:', promoContent.length);

			// 显示系统弹窗，提供复制选项
			uni.showModal({
				title: '淘口令',
				content: promoContent,
				confirmText: '复制',
				cancelText: '关闭',
				showCancel: true,
				success: (res) => {
					console.log('🎪 弹窗用户操作:', res.confirm ? '复制' : '关闭');
					if (res.confirm) {
						// 用户点击复制按钮，复制推广文案
						uni.setClipboardData({
							data: promoContent,
							success: () => {
								console.log('✅ 内容复制成功');
								uni.showToast({
									title: '文案已复制',
									icon: 'success'
								});
							},
							fail: (error) => {
								console.error('❌ 复制失败:', error);
								uni.showToast({
									title: '复制失败，请重试',
									icon: 'none'
								});
							}
						});
					}
					// 用户点击关闭按钮，不执行任何操作
				},
				fail: (error) => {
					console.error('❌ 弹窗显示失败:', error);
					// 弹窗失败时的备用方案：直接复制内容
					this.fallbackCopyContent(promoContent, '弹窗显示失败，已直接复制推广文案');
				}
			});

			console.log('🎪 系统弹窗调用完成');

		} catch (error) {
			console.error('❌ 处理淘宝商品购买失败:', error);
			uni.showToast({
				title: '处理失败: ' + error.message,
				icon: 'none',
				duration: 3000
			});
		}
	},

		/**
		 * 处理京东商品购买
	 * @param {Object} data - 商品数据
	 */
	handleJdPurchase(data) {
		console.log('处理京东商品购买，转链结果:', data);
		
		// 调试：输出完整的API响应数据
		console.log('完整的API响应数据:', JSON.stringify(data, null, 2));

			// 构建简洁的推广文案（类似淘宝淘口令格式）
			const title = data.title || this.goodsInfo.title || '京东好物';
			const price = this.formatPrice(data.coupon_price || data.price || this.goodsInfo.coupon_price || this.goodsInfo.price);

			// 简洁的推广文案 - 只使用短链接，不使用完整的京东口令
			let promoContent = `【京东】【${price}元】${title} `;

			// 只使用短链接，避免重复内容
			if (data.short_url || data.shortUrl) {
				promoContent += `${data.short_url || data.shortUrl}`;
			} else if (data.jd_command) {
				// 如果没有短链接，从京东口令中提取链接部分
				const urlMatch = data.jd_command.match(/https?:\/\/[^\s]+/);
				if (urlMatch) {
					promoContent += urlMatch[0];
				} else {
					promoContent += data.jd_command;
				}
			}

			// 添加更多优惠信息和使用提示
			promoContent += '\n更多优惠\nhttps://u.jd.com/2GNR2c2\n点击复制按钮后\n打开助手并粘贴发送';

			// 显示弹窗，提供复制选项
			uni.showModal({
				title: '京东转链',
				content: promoContent,
				confirmText: '复制',
				cancelText: '关闭',
				showCancel: true,
				success: (res) => {
					if (res.confirm) {
						// 用户点击复制按钮，复制推广文案
						const textToCopy = promoContent;
						uni.setClipboardData({
							data: textToCopy,
							success: () => {
								uni.showToast({
									title: '文案已复制',
									icon: 'success'
								});
							},
							fail: (error) => {
								console.error('❌ 复制失败:', error);
								uni.showToast({
									title: '复制失败，请重试',
									icon: 'none'
								});
							}
						});
					}
					// 用户点击关闭按钮，不执行任何操作
				}
			});
		},

		/**
		 * 生成淘宝购买文案
		 * @param {Object} data - 商品数据对象
		 * @returns {Object} 包含完整内容和推广文案的对象
		 */
		generateTaobaoContent(data) {
			// 商品信息
			const title = data.title || this.goodsInfo.title || this.goodsInfo.short_title || '精选好物';
			const price = this.formatPrice(data.coupon_price || data.price || this.goodsInfo.coupon_price || this.goodsInfo.price);
			const originalPrice = this.formatPrice(data.price || this.goodsInfo.price);
			const couponAmount = (data.coupon_price && data.price) ? 
				`【${this.formatPrice(parseFloat(data.price) - parseFloat(data.coupon_price))}元券】` : 
				(this.goodsInfo.coupon_amount ? `【${this.formatPrice(this.goodsInfo.coupon_amount)}元券】` : '');

			// 构建完整内容
			let resultText = `🎉 淘宝商品转链成功！\n\n`;
			resultText += `📦 商品：${title}\n`;
			resultText += `🏪 店铺：${data.shop_name || this.goodsInfo.shop_name || '未知店铺'}\n`;
			resultText += `💰 价格：¥${originalPrice}`;
			if (data.coupon_price && data.coupon_price < data.price) {
				resultText += ` → ¥${price}（券后价）`;
			}
			resultText += `\n`;

			// 佣金信息
			const commissionRate = data.commission_rate || this.goodsInfo.commission_rate;
			if (commissionRate) {
				const rate = parseFloat(commissionRate);
				const commission = (parseFloat(data.coupon_price || data.price || this.goodsInfo.coupon_price || this.goodsInfo.price) * rate / 100).toFixed(2);
				resultText += `💎 佣金：${rate}%（约¥${commission}）\n`;
			}

			// 优惠券信息
			if (data.coupon_info || (data.coupon_price && data.price)) {
				resultText += `🎫 优惠券：${data.coupon_info || `${parseFloat(data.price) - parseFloat(data.coupon_price)}元券`}\n`;
			}

			// 销量
			if (data.sales_volume || this.goodsInfo.sales_volume) {
				resultText += `📊 销量：${data.sales_volume || this.goodsInfo.sales_volume}+\n`;
			}

			resultText += `\n━━━━━━━━━━━━━━━━━━━━\n`;

			// 淘口令和链接
			if (data.taobao_command) {
				// 使用完整的淘宝口令格式，但格式化使用提示为两行显示
				resultText = data.taobao_command;

				// 格式化使用提示：确保"点击复制按钮后 打开助手并粘贴发送"显示为两行
				resultText = this.formatUsageTip(resultText);
			} else if (data.taokouling) {
				resultText += `📱 淘口令：${data.taokouling}\n`;

				// 只有在没有完整淘宝口令时才添加短链接
				if (data.short_url2) {
					resultText += `🔗 手淘短链：${data.short_url2}\n`;
				} else if (data.short_url) {
					resultText += `🔗 商品链接：${data.short_url}\n`;
				} else if (data.item_url) {
					resultText += `🔗 商品链接：${data.item_url}\n`;
				}

				// 添加更多优惠信息和格式化的使用提示
				resultText += '\n更多优惠\nhttps://s.click.taobao.com/geyPIIr\n点击复制按钮后\n打开助手并粘贴发送';
			}

			// 构建推广文案
			let copyText = '';
			if (data.taobao_command) {
				// 使用完整的淘宝口令，并格式化使用提示为两行显示
				copyText = data.taobao_command;
				copyText = this.formatUsageTip(copyText);
			} else {
				// 构建推广文案
				copyText = `🔥【限时特价】${title}\n\n`;
				copyText += `💰 到手价：¥${price}`;
				if (data.coupon_price && data.coupon_price < data.price) {
					copyText += `（原价¥${originalPrice}）`;
				}
				copyText += `\n`;
				if (data.coupon_info || (data.coupon_price && data.price)) {
					copyText += `🎫 ${data.coupon_info || `${parseFloat(data.coupon_price)}元券`}\n`;
				}
				copyText += `🏪 ${data.shop_name || this.goodsInfo.shop_name || ''}\n`;
				if (data.sales_volume || this.goodsInfo.sales_volume) {
					copyText += `📊 已售${data.sales_volume || this.goodsInfo.sales_volume}+件\n`;
				}
				copyText += `\n📱 复制这条信息，打开👉手机淘宝👈即可购买\n`;
				if (data.taokouling) {
					copyText += `${data.taokouling}`;
				}

				// 添加更多优惠信息和格式化的使用提示
				copyText += '\n更多优惠\nhttps://s.click.taobao.com/geyPIIr\n点击复制按钮后\n打开助手并粘贴发送';
			}

			return {
				fullContent: resultText,
				promoContent: copyText
			};
		},

		/**
		 * 生成京东购买文案
	 * @param {string} shortUrl - 短链接
	 * @returns {string} 京东商品购买文案
	 */
	generateJdContent(shortUrl) {
		// 此方法已不再使用，京东转链现在使用简洁格式
		return '';
		},

		/**
		 * 显示购买弹窗
	 * @param {string} content - 要显示的内容
	 * @param {string} title - 弹窗标题
	 */
	showPurchaseModal(content, title) {
		// 这个方法现在主要用于显示简单的购买信息
		// 淘宝和京东的复杂弹窗已经在各自的处理方法中实现
		console.log('显示购买弹窗:', { title, content: content.substring(0, 100) + '...' });
		
		// 使用系统弹窗
		const titleText = title || '购买信息';
		const contentText = content || '获取购买信息失败，请重试';
		
		// 格式化弹窗内容
		const formattedContent = `${contentText}\n\n请复制以上信息后打开对应APP使用`;
		
		uni.showModal({
			title: titleText,
			content: formattedContent,
			showCancel: false,
			confirmText: '复制内容',
			success: (res) => {
				if (res.confirm) {
					this.copyContent(contentText);
				}
			}
			});
		},

		/**
		 * 格式化使用提示为两行显示
		 * @param {string} text - 包含使用提示的文本
		 * @returns {string} 格式化后的文本
		 */
		formatUsageTip(text) {
			if (!text) return text;

			// 检查是否已经包含"更多优惠"信息，避免重复添加
			if (text.includes('更多优惠')) {
				// 如果已经包含"更多优惠"，只需要确保使用提示格式正确
				let formattedText = text;

				// 只替换使用提示部分，不添加新的"更多优惠"信息
				const simplePatterns = [
					/点击复制按钮后\s+打开淘宝APP直接购买/g,
					/点击复制按钮后打开淘宝APP直接购买/g,
					/点击复制按钮后\s*\n\s*打开淘宝APP直接购买/g,
					/点击复制按钮后\s+打开助手并粘贴发送/g,
					/点击复制按钮后打开助手并粘贴发送/g,
					/点击复制按钮后\s*\n\s*打开助手并粘贴发送/g
				];

				simplePatterns.forEach(pattern => {
					formattedText = formattedText.replace(pattern, '点击复制按钮后\n打开助手并粘贴发送');
				});

				return formattedText;
			}

			// 如果不包含"更多优惠"，则添加完整的信息
			const patterns = [
				// 匹配旧的淘宝APP提示格式
				/点击复制按钮后\s+打开淘宝APP直接购买/g,
				/点击复制按钮后打开淘宝APP直接购买/g,
				/点击复制按钮后\s*\n\s*打开淘宝APP直接购买/g,
				// 匹配新的助手提示格式
				/点击复制按钮后\s+打开助手并粘贴发送/g,
				/点击复制按钮后打开助手并粘贴发送/g,
				/点击复制按钮后\s*\n\s*打开助手并粘贴发送/g
			];

			let formattedText = text;

			// 替换所有匹配的模式为标准的格式（包含更多优惠信息）
			patterns.forEach(pattern => {
				formattedText = formattedText.replace(pattern, '更多优惠\nhttps://s.click.taobao.com/geyPIIr\n点击复制按钮后\n打开助手并粘贴发送');
			});

			return formattedText;
		},

		/**
		 * 备用复制内容方法（当弹窗失败时使用）
		 * @param {string} content - 要复制的内容
		 * @param {string} message - 提示消息
		 */
		fallbackCopyContent(content, message) {
			console.log('🔄 使用备用复制方案');
			uni.setClipboardData({
				data: content,
				success: () => {
					console.log('✅ 备用复制成功');
					uni.showToast({
						title: message || '内容已复制',
						icon: 'success',
						duration: 2000
					});
				},
				fail: (error) => {
					console.error('❌ 备用复制也失败:', error);
					uni.showModal({
						title: '复制失败',
						content: '无法自动复制，请手动复制以下内容：\n\n' + content.substring(0, 200) + '...',
						showCancel: false,
						confirmText: '知道了'
					});
				}
			});
		},

		/**
		 * 关闭弹窗
		 */
		closeModal() {
			console.log('关闭弹窗');
			this.showModal = false;
			// 清空内容，避免下次打开时闪现旧内容
			setTimeout(() => {
				this.modalTitle = '';
				this.modalContent = '';
			}, 300);
		},



		/**
		 * 打开淘宝APP
		 */
		openTaobao() {
			console.log('打开淘宝按钮被点击');
			// 先复制内容
			uni.setClipboardData({
				data: this.modalContent,
				success: () => {
					console.log('内容已复制，尝试打开淘宝APP');
					// 尝试打开淘宝APP
					// #ifdef APP-PLUS
					plus.runtime.openURL('taobao://');
					// #endif

					// #ifdef H5
					window.open('taobao://');
					// #endif

					// #ifdef MP-WEIXIN || MP-ALIPAY || MP-BAIDU || MP-TOUTIAO || MP-QQ
					uni.showModal({
						title: '提示',
						content: '内容已复制，请手动打开淘宝APP',
						showCancel: false
					});
					// #endif

					this.closeModal();
				},
				fail: () => {
					console.log('复制失败');
					uni.showToast({
						title: '复制失败',
						icon: 'none'
					});
				}
			});
		}
	}
}
</script>

<style scoped>
.container {
	background: #fff;
	min-height: 100vh;
}

/* 自定义导航栏 */
.tui-header-box {
	position: fixed;
	top: 0;
	left: 0;
	right: 0;
	z-index: 1000;
	background-color: #fff;
	border-bottom: 1rpx solid #f0f0f0;
	/* 添加硬件加速，防止滚动时闪动 */
	transform: translateZ(0);
	-webkit-transform: translateZ(0);
	-webkit-backface-visibility: hidden;
	backface-visibility: hidden;
}

.tui-header {
	background: #fff;
	display: flex;
	align-items: center;
	padding: 0 30rpx;
	position: relative;
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

.tui-title-text {
	color: #333;
	font-size: 36rpx;
	font-weight: 600;
}

/* 滚动内容区域 */
.tui-scroll-content {
	position: fixed;
	left: 0;
	right: 0;
	background: #fff;
	overflow-y: auto;
	-webkit-overflow-scrolling: touch;
	/* 添加硬件加速，减少滚动闪动 */
	transform: translateZ(0);
	-webkit-transform: translateZ(0);
	/* 优化滚动性能 */
	will-change: scroll-position;
	/* 防止滚动时的闪烁 */
	-webkit-backface-visibility: hidden;
	backface-visibility: hidden;
	/* 平滑滚动 */
	scroll-behavior: smooth;
}

/* 商品图片轮播 */
.tui-banner-swiper {
	position: relative;
	background: #fff;
	width: 100%;
}

.tui-square-swiper {
	width: 100%;
	height: 750rpx; /* 正方形高度，与屏幕宽度相等 */
}

.swiper {
	width: 100%;
	height: 100%;
}

.tui-slide-image {
	width: 100%;
	height: 100%;
	display: block;
}

/* 图片指示器 */
.tui-banner-indicators {
	position: absolute;
	bottom: 30rpx;
	left: 50%;
	transform: translateX(-50%);
	display: flex;
	gap: 12rpx;
}

.tui-indicator {
	width: 12rpx;
	height: 12rpx;
	border-radius: 50%;
	background: rgba(255,255,255,0.5);
	transition: all 0.3s ease;
}

.tui-indicator-active {
	background: #fff;
	width: 24rpx;
	border-radius: 6rpx;
}

/* 图片计数 */
.tui-banner-count {
	position: absolute;
	top: 30rpx;
	right: 30rpx;
	background: rgba(0,0,0,0.6);
	border-radius: 20rpx;
	padding: 8rpx 16rpx;
}

.tui-count-text {
	color: #fff;
	font-size: 24rpx;
}

/* 商品基本信息 */
.tui-goods-info {
	background: #fff;
	margin: 0;
	border-radius: 0;
	padding: 30rpx;
	position: relative;
}

.tui-platform-badge {
	position: absolute;
	top: 30rpx;
	right: 30rpx;
	padding: 8rpx 16rpx;
	border-radius: 12rpx;
	font-size: 22rpx;
	color: #fff;
	font-weight: bold;
}

.tui-platform-1 {
	background: linear-gradient(135deg, #ff6a00, #ee0a24);
}

.tui-platform-2 {
	background: linear-gradient(135deg, #e93323, #ed4014);
}

.tui-goods-title {
	font-size: 32rpx;
	color: #333;
	line-height: 1.5;
	margin-bottom: 20rpx;
	padding-right: 120rpx;
}

/* 价格信息 */
.tui-price-section {
	margin-bottom: 30rpx;
}

.tui-price-main {
	display: flex;
	align-items: baseline;
	margin-bottom: 15rpx;
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
	margin-left: 5rpx;
}

.tui-price-original {
	font-size: 24rpx;
	color: #999;
	text-decoration: line-through;
	margin-left: 20rpx;
}

/* 优惠券信息 - 优惠券金额格式 */
.tui-coupon-section {
	margin-bottom: 30rpx;
}

.tui-coupon-simple {
	background: linear-gradient(135deg, #ff4757, #ff3742);
	border-radius: 12rpx;
	padding: 20rpx 30rpx;
	display: flex;
	align-items: center;
	justify-content: center;
	box-shadow: 0 4rpx 12rpx rgba(255, 71, 87, 0.3);
	border: 2rpx solid rgba(255,255,255,0.2);
}

.tui-coupon-text {
	color: #fff;
	font-size: 30rpx;
	font-weight: 700;
	text-align: center;
}

/* 商品信息 */
.tui-goods-meta {
	border-top: 1rpx solid #f0f0f0;
	padding-top: 20rpx;
}

.tui-meta-item {
	display: flex;
	justify-content: space-between;
	align-items: center;
	padding: 12rpx 0;
}

.tui-meta-label {
	font-size: 28rpx;
	color: #666;
}

.tui-meta-value {
	font-size: 28rpx;
	color: #333;
}

/* 商品详情描述 */
.tui-goods-desc {
	background: #fff;
	margin: 0;
	border-radius: 0;
	padding: 30rpx;
	border-top: 1rpx solid #f0f0f0;
}

.tui-desc-title {
	font-size: 32rpx;
	color: #333;
	font-weight: bold;
	margin-bottom: 20rpx;
}

.tui-desc-content {
	line-height: 1.6;
}

.tui-desc-text {
	font-size: 28rpx;
	color: #666;
}





/* 底部操作栏 */
.tui-bottom-bar {
	position: fixed;
	bottom: 0;
	left: 0;
	right: 0;
	background: #fff;
	border-top: 1rpx solid #f0f0f0;
	padding: 20rpx 30rpx;
	padding-bottom: calc(20rpx + env(safe-area-inset-bottom));
	z-index: 999;
	/* 添加硬件加速，防止滚动时闪动 */
	transform: translateZ(0);
	-webkit-transform: translateZ(0);
	-webkit-backface-visibility: hidden;
	backface-visibility: hidden;
}

.tui-bar-buttons {
	display: flex;
	gap: 20rpx;
	width: 100%;
}

/* 平台助手按钮 */
.tui-helper-btn {
	flex: 1;
	border-radius: 50rpx;
	padding: 0;
	height: 88rpx;
	text-align: center;
	border: none;
	font-size: 30rpx;
	font-weight: bold;
	color: #fff;
	background: none;
	margin: 0;
	line-height: 88rpx;
	display: flex;
	align-items: center;
	justify-content: center;
}

/* 淘宝助手按钮样式 */
.tui-helper-1 {
	background: linear-gradient(135deg, #ff6a00, #ff8f00);
}

/* 京东助手按钮样式 */
.tui-helper-2 {
	background: linear-gradient(135deg, #007acc, #0099ff);
}

/* 默认助手按钮样式 */
.tui-helper-btn:not(.tui-helper-1):not(.tui-helper-2) {
	background: linear-gradient(135deg, #666, #888);
}

.tui-helper-text {
	color: #fff;
	font-size: 30rpx;
	font-weight: bold;
	line-height: 1;
}

/* 立即购买按钮 */
.tui-buy-btn {
	flex: 1;
	background: linear-gradient(135deg, #e41f19, #ff6034);
	border-radius: 50rpx;
	padding: 0;
	height: 88rpx;
	text-align: center;
	display: flex;
	align-items: center;
	justify-content: center;
}

.tui-buy-btn.tui-loading {
	background: linear-gradient(135deg, #ccc, #ddd);
}

.tui-buy-text {
	color: #fff;
	font-size: 30rpx;
	font-weight: bold;
	line-height: 1;
}

/* 加载状态 */
.tui-loading-overlay {
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
	border-radius: 12rpx;
	padding: 40rpx 60rpx;
}

.tui-loading-text {
	font-size: 28rpx;
	color: #333;
}

/* 响应式适配 */
@media screen and (max-width: 750rpx) {
	.tui-goods-info {
		margin: 10rpx;
		padding: 20rpx;
	}

	.tui-goods-desc {
		margin: 10rpx;
		padding: 20rpx;
	}

	.tui-modal-content {
		margin: 20rpx;
		padding: 30rpx;
	}
}
</style>
