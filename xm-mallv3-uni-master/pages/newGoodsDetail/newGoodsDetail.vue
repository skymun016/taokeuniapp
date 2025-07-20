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

		<!-- 商品图片轮播 -->
		<view class="tui-banner-swiper" :style="{ marginTop: height + 'px' }">
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

			<!-- 优惠券信息 -->
			<view class="tui-coupon-section" v-if="hasCoupon">
				<view class="tui-coupon-card">
					<view class="tui-coupon-left">
						<text class="tui-coupon-amount" v-if="goodsInfo.coupon_amount > 0">¥{{ formatPrice(goodsInfo.coupon_amount) }}</text>
						<text class="tui-coupon-amount" v-else-if="goodsInfo.coupon_info && goodsInfo.coupon_info !== '满0元减0元'">{{ goodsInfo.coupon_info }}</text>
						<text class="tui-coupon-condition" v-if="goodsInfo.coupon_condition">满{{ formatPrice(goodsInfo.coupon_condition) }}可用</text>
						<text class="tui-coupon-condition" v-else-if="goodsInfo.coupon_amount > 0">无门槛</text>
						<text class="tui-coupon-condition" v-else>优惠券</text>
					</view>
					<view class="tui-coupon-divider"></view>
					<view class="tui-coupon-right">
						<text class="tui-coupon-btn">领券</text>
					</view>
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

		<!-- 底部操作栏 -->
		<view class="tui-bottom-bar">
			<view class="tui-bar-left">
				<view class="tui-bar-item" @tap="collect">
					<tui-icon :name="isCollected ? 'heart-fill' : 'heart'" :color="isCollected ? '#e41f19' : '#666'" :size="20"></tui-icon>
					<text class="tui-bar-text">收藏</text>
				</view>
				<view class="tui-bar-item" @tap="share">
					<tui-icon name="share" color="#666" :size="20"></tui-icon>
					<text class="tui-bar-text">分享</text>
				</view>
			</view>
			<view class="tui-bar-right">
				<view class="tui-buy-btn" @tap="buyNow" :class="{ 'tui-loading': loading }">
					<text class="tui-buy-text">{{ loading ? '转链中...' : '立即购买' }}</text>
				</view>
			</view>
		</view>

		<!-- 使用系统弹窗 -->

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
	},

	computed: {
		/**
		 * 判断是否有优惠券
		 */
		hasCoupon() {
			// 有优惠券金额
			if (this.goodsInfo.coupon_amount && this.goodsInfo.coupon_amount > 0) {
				return true;
			}
			// 有优惠券信息且不是默认的"满0元减0元"
			if (this.goodsInfo.coupon_info &&
				this.goodsInfo.coupon_info !== '满0元减0元' &&
				this.goodsInfo.coupon_info.trim() !== '') {
				return true;
			}
			// 券后价小于原价
			if (this.goodsInfo.coupon_price && this.goodsInfo.price &&
				this.goodsInfo.coupon_price < this.goodsInfo.price) {
				return true;
			}
			return false;
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
		 * 格式化价格
		 */
		formatPrice(price) {
			if (!price) return '0.00';
			return parseFloat(price).toFixed(2);
		},

		/**
		 * 加载商品详情
		 */
		async loadGoodsDetail() {
			this.pageLoading = true;

			try {
				console.log('加载商品详情，product_id:', this.productId, 'platform:', this.platform);

				// 直接从存储中获取商品信息
				const cachedGoodsInfo = uni.getStorageSync('currentGoodsInfo');

				if (cachedGoodsInfo && cachedGoodsInfo.product_id === this.productId) {
					console.log('从缓存加载商品详情:', cachedGoodsInfo);
					console.log('优惠券信息检查:', {
						coupon_amount: cachedGoodsInfo.coupon_amount,
						coupon_info: cachedGoodsInfo.coupon_info,
						coupon_condition: cachedGoodsInfo.coupon_condition,
						price: cachedGoodsInfo.price,
						coupon_price: cachedGoodsInfo.coupon_price
					});

					this.goodsInfo = cachedGoodsInfo;
					this.setupGoodsImages();

					// 清除缓存
					uni.removeStorageSync('currentGoodsInfo');

					console.log('商品详情加载成功:', this.goodsInfo);
				} else {
					throw new Error('商品信息已过期，请重新选择商品');
				}

			} catch (error) {
				console.error('加载商品详情失败:', error);
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
		 * 收藏
		 */
		collect() {
			this.isCollected = !this.isCollected;
			uni.showToast({
				title: this.isCollected ? '收藏成功' : '取消收藏',
				icon: 'none'
			});
		},

		/**
		 * 分享
		 */
		share() {
			// 这里可以实现分享功能
			uni.showToast({
				title: '分享功能开发中',
				icon: 'none'
			});
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
			// 生成购买文案
			console.log('📝 开始生成淘宝购买文案...');
			const contentData = {
				...data,
				title: data.title || this.goodsInfo.title,
				shop_name: data.shop_name || this.goodsInfo.shop_name,
				price: data.price || this.goodsInfo.price,
				coupon_price: data.coupon_price || this.goodsInfo.coupon_price,
				coupon_amount: data.coupon_amount || this.goodsInfo.coupon_amount,
				commission_rate: data.commission_rate || this.goodsInfo.commission_rate,
				sales_volume: data.sales_volume || this.goodsInfo.sales_volume,
				taokouling: data.taokouling || data.taoKouLing || '',
				short_url: data.short_url || data.shortUrl || '',
				short_url2: data.short_url2 || data.shortUrl2 || '',
				item_url: data.item_url || data.itemUrl || data.url || '',
				coupon_click_url: data.coupon_click_url || data.couponClickUrl || ''
			};

			console.log('🔧 文案生成数据:', contentData);
			const { fullContent, promoContent } = this.generateTaobaoContent(contentData);

			console.log('✅ 文案生成完成');
			console.log('📄 完整内容长度:', fullContent.length);
			console.log('📄 推广内容长度:', promoContent.length);

			// 检查内容是否过长（uni.showModal 有内容长度限制）
			const maxContentLength = 1000; // 设置最大内容长度
			let displayContent = fullContent;

			if (fullContent.length > maxContentLength) {
				console.log('⚠️ 内容过长，进行截取处理');
				displayContent = fullContent.substring(0, maxContentLength) + '...\n\n点击按钮复制完整内容';
			}

			console.log('🎪 准备显示系统弹窗...');
			console.log('🎪 弹窗标题: 淘口令转换成功');
			console.log('🎪 弹窗内容预览:', displayContent.substring(0, 100) + '...');

			// 显示系统弹窗，提供复制选项
			uni.showModal({
				title: '淘口令',
				content: displayContent,
				confirmText: '复制文案',
				cancelText: '复制详情',
				showCancel: true,
				success: (res) => {
					console.log('🎪 弹窗用户操作:', res.confirm ? '确认' : '取消');
					const textToCopy = res.confirm ? promoContent : fullContent;
					console.log('📋 准备复制内容长度:', textToCopy.length);

					uni.setClipboardData({
						data: textToCopy,
						success: () => {
							console.log('✅ 内容复制成功');
							uni.showToast({
								title: res.confirm ? '文案已复制' : '详情已复制',
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

			// 生成购买文案
			const fullContent = this.generateJdContent(data.short_url || data.shortUrl || '');
			
			// 构建推广文案
			const title = data.title || this.goodsInfo.title || '京东好物';
			let promoContent = `🔥【京东好物】${title}\n\n`;
			
			// 价格信息
			const price = this.formatPrice(data.coupon_price || data.price || this.goodsInfo.coupon_price || this.goodsInfo.price);
			const originalPrice = this.formatPrice(data.price || this.goodsInfo.price);
			
			promoContent += `💰 到手价：¥${price}`;
			if (data.coupon_price && data.coupon_price < data.price) {
				promoContent += `（原价¥${originalPrice}）`;
			}
			promoContent += '\n';
			
			// 优惠券信息
			if (data.coupon_info || (data.coupon_price && data.price)) {
				promoContent += `🎫 ${data.coupon_info || `${parseFloat(data.price) - parseFloat(data.coupon_price)}元券`}\n`;
			}
			
			// 店铺名称
			promoContent += `🏪 ${data.shop_name || this.goodsInfo.shop_name || ''}\n`;
			
			// 销量
			if (data.sales_volume || this.goodsInfo.sales_volume) {
				promoContent += `📊 已售${data.sales_volume || this.goodsInfo.sales_volume}+件\n`;
			}
			
			// 购买链接或口令
			promoContent += '\n📱 复制这条信息，打开👉京东APP👈即可购买\n';
			
			// 优先使用京东口令
			if (data.jd_command) {
				promoContent += `${data.jd_command}`;
			} else if (data.short_url || data.shortUrl) {
				promoContent += `${data.short_url || data.shortUrl}`;
			}

			// 显示弹窗，提供复制选项
			uni.showModal({
				title: '京东商品转链成功',
				content: fullContent,
				confirmText: '复制文案',
				cancelText: '复制链接',
				showCancel: true,
				success: (res) => {
					const textToCopy = res.confirm ? promoContent : (data.short_url || data.shortUrl || '');
					uni.setClipboardData({
						data: textToCopy,
						success: () => {
							uni.showToast({
								title: res.confirm ? '文案已复制' : '链接已复制',
								icon: 'success'
							});
						}
					});
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

				// 格式化使用提示：确保"点击复制按钮后 打开淘宝APP直接购买"显示为两行
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

				// 添加格式化的使用提示
				resultText += '\n点击复制按钮后\n打开淘宝APP直接购买';
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

				// 添加格式化的使用提示
				copyText += '\n点击复制按钮后\n打开淘宝APP直接购买';
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
		// 商品信息
		const title = this.goodsInfo.title || this.goodsInfo.short_title || '精选好物';
		const price = this.formatPrice(this.goodsInfo.coupon_price || this.goodsInfo.price);
		const originalPrice = this.formatPrice(this.goodsInfo.price);
		const couponAmount = this.goodsInfo.coupon_amount ? 
			`【${this.formatPrice(this.goodsInfo.coupon_amount)}元券】` : '';
		
		// 构建完整内容
		let resultText = `🎉 京东商品转链成功！\n\n`;
		resultText += `📦 商品：${title}\n`;
		resultText += `🏪 店铺：${this.goodsInfo.shop_name || '未知店铺'}\n`;
		resultText += `💰 价格：¥${originalPrice}`;
		
		// 券后价
		if (this.goodsInfo.coupon_price && this.goodsInfo.coupon_price < this.goodsInfo.price) {
			resultText += ` → ¥${price}（券后价）`;
		}
		resultText += `\n`;

		// 佣金信息
		if (this.goodsInfo.commission_rate) {
			const rate = parseFloat(this.goodsInfo.commission_rate);
			if (!isNaN(rate) && rate > 0) {
				const commission = (parseFloat(this.goodsInfo.coupon_price || this.goodsInfo.price) * rate / 100).toFixed(2);
				resultText += `💎 佣金：${rate}%（约¥${commission}）\n`;
			}
		}

		// 优惠券信息
		if (couponAmount) {
			resultText += `🎫 优惠券：${this.formatPrice(this.goodsInfo.coupon_amount)}元券\n`;
		}

		// 销量
		if (this.goodsInfo.sales_volume) {
			resultText += `📊 销量：${this.goodsInfo.sales_volume}+\n`;
		}

		resultText += `\n━━━━━━━━━━━━━━━━━━━━\n`;

		// 商品链接
		if (shortUrl) {
			resultText += `🔗 商品链接：${shortUrl}\n`;
		}

		// 添加操作提示
		resultText += '\n📱 复制链接，打开「京东APP」直接购买';
		
		return resultText;
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

			// 匹配各种可能的使用提示格式并替换为标准的两行格式
			const patterns = [
				// 匹配 "点击复制按钮后 打开淘宝APP直接购买" (一行格式)
				/点击复制按钮后\s+打开淘宝APP直接购买/g,
				// 匹配 "点击复制按钮后打开淘宝APP直接购买" (无空格)
				/点击复制按钮后打开淘宝APP直接购买/g,
				// 匹配已经是两行但可能有多余空格的格式
				/点击复制按钮后\s*\n\s*打开淘宝APP直接购买/g
			];

			let formattedText = text;

			// 替换所有匹配的模式为标准的两行格式
			patterns.forEach(pattern => {
				formattedText = formattedText.replace(pattern, '点击复制按钮后\n打开淘宝APP直接购买');
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
	background: #f5f5f5;
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
	margin: 20rpx;
	border-radius: 16rpx;
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

/* 优惠券信息 */
.tui-coupon-section {
	margin-bottom: 30rpx;
}

.tui-coupon-card {
	background: linear-gradient(135deg, #ff4757, #ff3742);
	border-radius: 12rpx;
	display: flex;
	align-items: center;
	overflow: hidden;
	box-shadow: 0 4rpx 12rpx rgba(255, 71, 87, 0.3);
	border: 2rpx solid rgba(255,255,255,0.2);
}

.tui-coupon-left {
	flex: 1;
	padding: 25rpx 30rpx;
	display: flex;
	flex-direction: column;
	align-items: flex-start;
}

.tui-coupon-amount {
	color: #fff;
	font-size: 36rpx;
	font-weight: 700;
	margin-bottom: 8rpx;
}

.tui-coupon-condition {
	color: rgba(255,255,255,0.9);
	font-size: 22rpx;
}

.tui-coupon-divider {
	width: 2rpx;
	height: 80rpx;
	background: rgba(255,255,255,0.3);
	position: relative;
}

.tui-coupon-divider::before,
.tui-coupon-divider::after {
	content: '';
	position: absolute;
	width: 20rpx;
	height: 20rpx;
	background: #f5f5f5;
	border-radius: 50%;
	left: 50%;
	transform: translateX(-50%);
}

.tui-coupon-divider::before {
	top: -10rpx;
}

.tui-coupon-divider::after {
	bottom: -10rpx;
}

.tui-coupon-right {
	padding: 25rpx 30rpx;
	display: flex;
	align-items: center;
	justify-content: center;
}

.tui-coupon-btn {
	color: #fff;
	font-size: 28rpx;
	font-weight: 700;
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
	margin: 20rpx;
	border-radius: 16rpx;
	padding: 30rpx;
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
	display: flex;
	align-items: center;
	justify-content: space-between;
	z-index: 999;
}

.tui-bar-left {
	display: flex;
	align-items: center;
}

.tui-bar-item {
	display: flex;
	flex-direction: column;
	align-items: center;
	margin-right: 40rpx;
}

.tui-bar-text {
	font-size: 20rpx;
	color: #666;
	margin-top: 8rpx;
}

.tui-bar-right {
	flex: 1;
	display: flex;
	justify-content: flex-end;
}

.tui-buy-btn {
	background: linear-gradient(135deg, #e41f19, #ff6034);
	border-radius: 50rpx;
	padding: 20rpx 60rpx;
	min-width: 200rpx;
	text-align: center;
}

.tui-buy-btn.tui-loading {
	background: #ccc;
}

.tui-buy-text {
	color: #fff;
	font-size: 28rpx;
	font-weight: bold;
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
