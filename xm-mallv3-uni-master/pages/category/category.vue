<template>
	<view class="container">
		<view class="tui-searchbox">
			<view class="tui-search-input" @tap="search">
				<icon type="search" :size="13" color="#999"></icon>
				<text class="tui-search-text">搜索</text>
			</view>
		</view>

		<scroll-view scroll-y scroll-with-animation class="tab-view" :scroll-into-view="scrollViewId"
			:style="{ height: height + 'px', top: top + 'px' }">
			<view :id="`id_${index}`" v-for="(item, index) in Category" :key="item.id" class="tab-bar-item"
				:class="[currentTab == index ? 'active' : '']" :data-current="index"
				@tap="swichNav(item.id,index)">
				<text>{{ item.title }}</text>
			</view>
		</scroll-view>

		<scroll-view @scrolltolower="tolower" scroll-y class="right-box" :scroll-into-view="floorNum"
			:style="{ height: height + 'px', top: top + 'px' }">
			<view class="page-view" id="scroll-panel">
				<swiper v-if="banners.length>0" indicator-dots autoplay circular :interval="5000" :duration="150"
					class="swiper">
					<swiper-item v-for="(item, index) in banners" :key="index">
						<image :data-url="item.url" @tap="navigationTo" :src="item.pic" mode="widthFix"
							class="slide-image" />
					</swiper-item>
				</swiper>
				<view class="class-box">
					<block v-if="categorystyle==1">
						<block v-for="(goods, index) in goodsList" :key="index">
							<view class="goods-item" @tap="toDetailsTap" :data-id="goods.id">
								<image class="goods-pic" mode="widthFix" lazy-load="true" :src="goods.pic"></image>
								<view class="goods-info">
									<text>{{goods.name}}</text>
									<text class="price" style="color:red;"><text class="f-24">￥</text>{{goods.price}}
										<block v-if="goods.ptype==2 && goods.quantity_unit">
											<text v-if="goods.time_amount > 0"
												class="f-24">/{{goods.time_amount}}{{goods.quantity_unit}}</text>
											<text v-else class="f-24">
												/<text v-if="goods.is_times">{{goods.timesmum}}</text>
												次</text>
										</block>
									</text>
								</view>
							</view>
						</block>
					</block>
					<block v-if="categorystyle==2">
						<view class="class-item" :id="'b'+item.id" v-if="Category.length>0"
							v-for="(item, index) in Category" :key="index">
							<view class="class-name">{{ item.title }}</view>
							<view class="g-container">
								<view class="g-box" v-for="(son, i1) in item.son" :key="i1" @tap.stop="gogoodsList"
									:data-cid="son.id" :data-key="son.title">
									<image :src="son.icon" class="c-image" />
									<view class="g-title">{{ son.title }}</view>
								</view>
							</view>
						</view>
					</block>
					<block v-if="categorystyle==3">
						<view class="class-item main-item">
							<view class="g-container">
								<view class="g-box" v-for="(goods, i1) in goodsList" :key="i2"
									@tap="toDetailsTap" :data-id="goods.id">
									<image :src="goods.image" class="g-image" mode="widthFix" />
									<view class="g-title">{{ goods.name }}</view>
									<view class="price" style="color:red;">
										<text class="f-24">￥</text>{{goods.price}}
										<block v-if="goods.ptype==2 && goods.quantity_unit">
											<text v-if="goods.time_amount > 0"
												class="f-24">/{{goods.time_amount}}{{goods.quantity_unit}}</text>
											<text v-else class="f-24"><text
													v-if="goods.is_times && goods.timesmum">/{{goods.timesmum}}</text>{{goods.quantity_unit}}</text>
										</block>
									</view>
								</view>
							</view>
						</view>
						<view class="fill-last" :style="{ 'height':fillHeight + 'px' }"></view>
					</block>
				</view>
			</view>
			<view :style='"height:"+(height-300)+"rpx;"' v-if="number<15"></view>
		</scroll-view>
		<tui-tabbar :current="current">
		</tui-tabbar>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				current: '',
				banners: [],
				Category: [],
				ptype: '',
				categoryId: "",
				categorystyle: "1",
				goodsList: [],
				page: 1,
				getSon: [],
				height: 0, //scroll-view高度
				top: 0,
				currentTab: 0, //预设当前项的值
				floorNum: '',
				number: "",
				scrollViewId: "id_0",
				scrollHeight: 400,
				scrollTopSize: 0,
				fillHeight: 0, // 填充高度，用于最后一项低于滚动区域时使用
				topArr: [], // 左侧列表元素
			};
		},
		onLoad: function(e) {
			let _this = this
			uni.setStorageSync('NewMessage', '');
			// #ifdef MP-WEIXIN
			this.current = "/" + this.__route__;
			// #endif
			//#ifdef H5
			this.current = this.$route.path;
			//#endif
			if (e.ptype) {
				_this.ptype = e.ptype;
			}

			setTimeout(() => {
				uni.getSystemInfo({
					success: res => {
						let header = 92;
						let top = 0;
						console.log(res.windowHeight);
						this.height = res.windowHeight - uni.upx2px(header) - 90;
						this.top = top + uni.upx2px(header);
					}
				});
			}, 50);
			this.getbanners()
			this.getCategoryall();

			if (_this.ptype) {
				this.current = this.current + '?ptype=' + _this.ptype;
			}
		},
		onShow() {
			let _this = this;
			_this.$request.post('config', {
				mo: 'pagestyle',
			}).then(res => {
				if (res.errno == 0) {
					if (res.data.categorystyle) {
						_this.categorystyle = res.data.categorystyle
					}
				}
			});
		},
		methods: {
			// 点击标题切换当前页时改变样式
			swichNav: function(Categoryid, e) {
				let cur = e;
				if (this.currentTab == cur) {
					return false;
				} else {
					this.currentTab = cur;
					this.checkCor();
				}

				if (this.categorystyle == 1 || this.categorystyle == 3) {
					this.categoryId = Categoryid;
					this.page = 1;
					this.getGoodsList();
				} else {
					this.floorNum = 'b' + Categoryid;
				}

			},
			/* 获取元素顶部信息 */
			getElementTop() {
				new Promise((resolve, reject) => {
					let view = uni.createSelectorQuery().selectAll('.main-item');
					view.boundingClientRect(data => {
						resolve(data);
					}).exec();
				}).then((res) => {
					console.log('res', res);
					let topArr = res.map((item) => {
						return item.top - this.scrollTopSize; /* 减去滚动容器距离顶部的距离 */
					});
					this.topArr = topArr;
					/* 获取最后一项的高度，设置填充高度。判断和填充时做了 +-20 的操作，是为了滚动时更好的定位 */
					let last = res[res.length - 1].height;
					if (last - 20 < this.scrollHeight) {
						this.fillHeight = this.scrollHeight - last + 20;
					}
				});
			},
			/* 主区域滚动监听 */
			mainScroll(e) {
				// 节流方法
				clearTimeout(this.mainThrottle);
				this.mainThrottle = setTimeout(() => {
					scrollFn();
				}, 10);
				let scrollFn = () => {
					let top = e.detail.scrollTop;
					let index = 0;
					/* 查找当前滚动距离 */
					this.topArr.forEach((item, id) => {
						/*
						console.log('------------**');
						console.log(top);
						console.log('------------');
						console.log(item);
						console.log('------------+++');
						console.log(id);
						*/
						if ((top + 2) >= item) {
							index = id;
							this.currentTab = (index < 0 ? 0 : index);
							//console.log(index);
						}
					})
				}
			},
			//判断当前滚动超过一屏时，设置tab标题滚动条。
			checkCor: function() {
				if (this.currentTab > 6) {
					this.scrollViewId = `id_${this.currentTab - 2}`;
				} else {
					this.scrollViewId = `id_0`;
				}
			},
			gogoodsList(e) {
				let key = e.currentTarget.dataset.key;
				let cid = e.currentTarget.dataset.cid;
				uni.navigateTo({
					url: '/pages/goodsList/goodsList?cid=' + cid + '&searchKey=' + key
				});
			},
			toDetailsTap: function(e) {
				uni.navigateTo({
					url: "/pages/goodsDetail/goodsDetail?id=" + e.currentTarget.dataset.id
				});
			},
			navigationTo: function(e) {
				this.sam.navigateTo(e.currentTarget.dataset.url)
			},
			search: function() {
				uni.navigateTo({
					url: '/pages/common/search/search'
				});
			},
			getbanners: function() {
				let _this = this;
				this.$request.get('ad.indexlist').then(res => {
					if (res.errno == 0) {
						this.banners = res.data;
					}
				})
			},
			getCategoryall: function() {
				let _this = this;
				this.$request.get('Category.all', {
					ptype: _this.ptype,
					showLoading: true
				}).then(res => {
					if (res.errno == 0) {
						_this.Category = res.data;
						_this.categoryId = _this.Category[0].id;
						_this.getGoodsList();
						setTimeout(function() {
							_this.infoScroll();
						}, 500)
						console.info("services---request---getsonList--右侧二级三级分类成功")
					}
				})
			},
			getGoodsList: function() {
				let that = this;
				if (that.page == 1) {
					that.goodsList = [];
				}

				this.$request.post('goods', {
					categoryId: that.categoryId,
					page: that.page,
					pageSize: 1000
				}).then(res => {
					that.goodsList = that.goodsList.concat(res.data.data);
					that.page = that.page + 1
					//console.log(goodsList);
					uni.hideNavigationBarLoading();
				}).catch(e => {
					uni.hideNavigationBarLoading();
				});
			},
			infoScroll: function() {
				let _this = this;
				let len = _this.Category.length;
				if (_this.Category[len - 1].son) {
					this.number = _this.Category[len - 1].son.length;
				}
			},
			/* 初始化滚动区域 */
			initScrollView() {
				return new Promise((resolve, reject) => {
					let view = uni.createSelectorQuery().select('#scroll-panel');
					view.boundingClientRect(res => {
						this.scrollTopSize = res.top;
						this.scrollHeight = res.height;
						this.$nextTick(() => {
							resolve();
						});
					}).exec();
				});
			},
			tolower: function() {
				if (this.categorystyle == 1) {
					this.getGoodsList();
				}
			}
		},
		/**
		 * 页面相关事件处理函数--监听用户下拉动作
		 */
		onPullDownRefresh: function() {
			setTimeout(() => {
				uni.stopPullDownRefresh()
			}, 200);
		},
	};
</script>

<style>
	page {
		background: #fcfcfc;
	}

	/* 左侧导航布局 start*/

	/* 隐藏scroll-view滚动条*/

	::-webkit-scrollbar {
		width: 0;
		height: 0;
		color: transparent;
	}

	.tui-searchbox {
		width: 100%;
		height: 92rpx;
		padding: 0 30rpx;
		box-sizing: border-box;
		background: #fff;
		display: flex;
		align-items: center;
		justify-content: center;
		position: fixed;
		left: 0;
		top: 0;
		z-index: 100;
	}

	.tui-searchbox::after {
		content: '';
		position: absolute;
		border-bottom: 1rpx solid #d2d2d2;
		-webkit-transform: scaleY(0.5);
		transform: scaleY(0.5);
		bottom: 0;
		right: 0;
		left: 0;
	}

	.tui-search-input {
		width: 100%;
		height: 60rpx;
		background: #f1f1f1;
		border-radius: 30rpx;
		font-size: 26rpx;
		color: #999;
		display: flex;
		align-items: center;
		justify-content: center;
	}

	.tui-search-text {
		padding-left: 16rpx;
	}

	.tab-view {
		/* height: 100%; */
		background: #f6f6f6;
		width: 200rpx;
		position: fixed;
		left: 0;
		z-index: 10;
		padding-bottom: 100rpx;
	}

	.tab-bar-item {
		width: 200rpx;
		height: 110rpx;
		background: #f6f6f6;
		box-sizing: border-box;
		display: flex;
		align-items: center;
		justify-content: center;
		font-size: 26rpx;
		color: #444;
		font-weight: 400;
	}

	.active {
		position: relative;
		color: #000;
		font-size: 30rpx;
		font-weight: 600;
		background: #fcfcfc;
	}

	.active::before {
		content: '';
		position: absolute;
		border-left: 8rpx solid #e41f19;
		height: 30rpx;
		left: 0;
	}

	/* 左侧导航布局 end*/

	.right-box {
		width: 100%;
		position: fixed;
		padding-left: 220rpx;
		box-sizing: border-box;
		left: 0;
	}

	.page-view {
		width: 100%;
		overflow: hidden;
		padding-top: 20rpx;
		padding-right: 20rpx;
		box-sizing: border-box;
		padding-bottom: env(safe-area-inset-bottom);
	}

	.swiper {
		width: 100%;
		height: 150rpx;
		border-radius: 12rpx;
		overflow: hidden;
		transform: translateZ(0);
	}

	/* #ifdef MP-WEIXIN */
	.swiper .wx-swiper-dot {
		width: 8rpx;
		height: 8rpx;
		display: inline-flex;
		background: none;
		justify-content: space-between;
	}

	.swiper .wx-swiper-dot::before {
		content: '';
		flex-grow: 1;
		background: rgba(255, 255, 255, 0.8);
		border-radius: 16rpx;
		overflow: hidden;
	}

	.swiper .wx-swiper-dot-active::before {
		background: #fff;
	}

	.swiper .wx-swiper-dot.wx-swiper-dot-active {
		width: 16rpx;
	}

	/* #endif */

	/* #ifndef MP-WEIXIN */
	>>>.swiper .uni-swiper-dot {
		width: 8rpx;
		height: 8rpx;
		display: inline-flex;
		background: none;
		justify-content: space-between;
	}

	>>>.swiper .uni-swiper-dot::before {
		content: '';
		flex-grow: 1;
		background: rgba(255, 255, 255, 0.8);
		border-radius: 16rpx;
		overflow: hidden;
	}

	>>>.swiper .uni-swiper-dot-active::before {
		background: #fff;
	}

	>>>.swiper .uni-swiper-dot.uni-swiper-dot-active {
		width: 16rpx;
	}

	/* #endif */

	.slide-image {
		width: 100%;
		height: 100%;
	}

	.class-box {
		padding-top: 10rpx;
	}

	.class-item {
		background: #fff;
		width: 100%;
		box-sizing: border-box;
		padding: 20rpx;
		margin-bottom: 20rpx;
		border-radius: 12rpx;
	}

	.class-name {
		font-size: 22rpx;
	}

	.g-container {
		/* padding-top: 20rpx; */
		display: flex;
		display: -webkit-flex;
		justify-content: flex-start;
		flex-direction: row;
		flex-wrap: wrap;
	}

	.g-box {
		width: 33.3333%;
		text-align: center;
		padding-top: 40rpx;
	}

	.price {
		font-weight: normal;
		font-size: 36rpx;
	}

	.c-image {
		width: 120rpx;
		height: 120rpx;
		border-radius: 12rpx;
	}

	.g-image {
		width: 100rpx;
		height: 100rpx;
		border-radius: 50rpx;
	}

	.g-title {
		font-size: 22rpx;
		padding-right: 8rpx;
		padding-left: 8rpx;
		word-break: break-all;
		overflow: hidden;
		text-overflow: ellipsis;
		display: -webkit-box;
		-webkit-box-orient: vertical;
		-webkit-line-clamp: 2;
	}

	.goods-item {
		width: 96%;
		height: 150rpx;
		display: flex;
		padding-left: 5px;
		padding-right: 5px;
		padding-top: 12px;
		padding-bottom: 12px;
		flex-direction: row;
		margin-left: 20rpx;
		align-items: center;
		justify-content: flex-start;
		border-bottom: 1rpx dotted #f2f2f2;
	}

	.goods-pic {
		width: 80px;
		height: 80px;
		flex-shrink: 0;
	}

	.goods-info {
		display: flex;
		font-weight: bold;
		flex-direction: column;
		padding: 6px 5px 0px 5px;
		height: 70px;
		font-size: 28rpx;
		justify-content: space-between;
	}

	.fill-last {
		height: 0;
		width: 100%;
		background: none;
	}
</style>