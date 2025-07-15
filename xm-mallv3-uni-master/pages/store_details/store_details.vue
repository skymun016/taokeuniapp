<template>
	<view class="container">
		<view class="tui-header">
			<view class="tui-shop__info">
				<image :src="storeDetail.store_logo" class="tui-logo"></image>
				<view class="tui-shop__desc">
					<view class="tui-shop__name">
						<text>{{storeDetail.title}}</text>
					</view>
					<view class="tui-shoprate" @tap="storeopenLocation">
						<!--<view class="tui-rate__box"><tui-rate :current="5" :score="0.6" disabled :size="12"></tui-rate></view>-->
						<view class="tui-shop__address">{{storeDetail.address}}
							<image src="/static/images/map.png" class="map-img"> </image><text
								style="color: #fff;">导航到这里</text>
						</view>
					</view>
				</view>
				<!--
			<view class="tui-btn__follow">
				<tui-button :size="24" width="116rpx" height="48rpx" shape="circle" type="danger">
					<tui-icon name="like" color="#fff" :size="24" unit="rpx"></tui-icon>
					<text class="tui-follow__text">关注</text>
				</tui-button>
				<tui-button :size="24" width="116rpx" height="48rpx" shape="circle" type="white" disabled>已关注</tui-button>
			</view>-->
			</view>
			<tui-tabs @change="change" :currentTab="current" :tabs="tabs" itemWidth="33%" backgroundColor="#5796fd"
				color="rgba(255,255,255,.7)" selectedColor="#fff" sliderBgColor="#fff" bottom="8rpx" unlined :size="30"
				:sliderWidth="60"></tui-tabs>
		</view>

		<view class="tui-body">
			<view class="tui-items__1 tui-padding" v-if="current == 0">
				<!--=======猜你喜欢 start=======-->
				<block v-if="is_recommendgoods==1 && Category.length<1">
					<view class="tui-ranking__header">
						<view class="tui-ranking__title">猜你喜欢</view>
					</view>
					<view class="tui-ranking__list">
						<view class="tui-ranking__item" @tap="toDetailsTap" v-for="(item, index) in recommendgoods"
						:data-id="item.id" v-if="index<3" :key="index">
							<image :src="item.pic"></image>
							<view class="tui-ranking__gtitle">{{ item.name }}</view>
							<view class="tui-sale-price">￥{{ item.price }}</view>
						</view>
					</view>
				</block>
				<!--=======猜你喜欢 end=======-->
				<block v-if="Category.length>0">
					<scroll-view scroll-y scroll-with-animation class="tab-view" :scroll-into-view="scrollViewId"
						:style="{ height: height + 'px', top: top + 'px' }">
						<view :id="`id_${index}`" v-for="(item, index) in Category" :key="item.id" class="tab-bar-item"
							:class="[currentTab == index ? 'active' : '']" :data-current="index"
							@tap="swichNav(item.id,index)">
							<text>{{ item.title }}</text>
						</view>
					</scroll-view>
					<scroll-view @scrolltolower="tolower" scroll-y class="right-box" :scroll-into-view="floorNum"
						:style="{ height: height + 'px', top: top + 'px' }" @scroll="mainScroll">
						<view class="page-view" id="scroll-panel">
							<view class="class-box">
								<view class="class-item main-item" :id="'b'+item.id" v-if="Category.length>0"
									v-for="(item, index) in Category" :key="index">
									<view class="class-name">{{ item.title }}</view>
									<view class="g-container">
										<view class="g-box" v-for="(goods, i1) in item.goodslist" :key="i2"
											 @tap="toDetailsTap" :data-id="goods.id">
											<image :src="goods.image" class="g-image" mode="widthFix" />
											<view class="g-title">{{ goods.name }}</view>
											<view class="price" style="color:red;">￥{{goods.price}}
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
							</view>
						</view>
						<view :style='"height:"+(height-300)+"rpx;"' v-if="number<15"></view>
					</scroll-view>
				</block>
			</view>

			<!--=======用户评价 start=======-->
			<view class="tui-items__3 tui-padding" v-if="current == 1">
				<view class="tui-activity__box">
					<view class="tui-activity__title">用户评价</view>
					<!-- 
				<view class="tui-activity__item" @tap="detail" v-for="(item, index) in 12" :key="index">
					<image :src="`/static/images/product/${index % 2 == 0 ? 4 : 3}.jpg`"></image>
					<view class="tui-activity__right">
						<view class="tui-ag__title">水星家纺 豪华大床四件套豪华大床四件套豪华大床四件套</view>
						<view class="tui-flex tui-ag__tag"><tui-tag plain type="danger" padding="8rpx 12rpx" size="24rpx" margin="0 8rpx 0 0">优惠促销</tui-tag></view>
						<view class="tui-ag__bottom">
							<view class="tui-flex">
								<view class="tui-ag__discount">
									￥
									<text>4.5</text>
								</view>
								<view class="tui-ag__original">￥10</view>
							</view>
							<view class="tui-ag__btn">
								<view>已低至4.5折</view>
								<view class="tui-btn__text">立即抢购</view>
							</view>
						</view>
					</view>
				</view>-->
				</view>
			</view>
			<!--=======用户评价 end=======-->
			<!--=======店铺 start=======-->
			<view class="tui-items__4" :style="{ marginTop: top + 'px' }" v-if="current == 2">
				<view class="tui-score__bg"></view>
				<view class="tui-info__box">
					<view class="tui-score__box tui-common__box">
						<view class="tui-flex__center">
							<text>店铺星级</text>
							<view class="tui-rate__box">
								<tui-rate :current="5" :score="0.6" disabled :size="12"></tui-rate>
							</view>
						</view>
						<view class="tui-flex__center">
							<text>用户评价</text>
							<view class="tui-score tui-color__red">
								<text>9.90分</text>
								<text>高</text>
							</view>
						</view>
						<view class="tui-flex__center">
							<text>专业程度</text>
							<view class="tui-score tui-color__red">
								<text>9.99分</text>
								<text>高</text>
							</view>
						</view>
						<view class="tui-flex__center">
							<text>服务态度</text>
							<view class="tui-score tui-color__green">
								<text>9.95分</text>
								<text>高</text>
							</view>
						</view>
					</view>
					<view class="tui-common__box tui-top__20">
						<!--<tui-list-cell arrow>证照信息</tui-list-cell>-->
						<tui-list-cell arrow unlined @click="qrcode">店铺二维码</tui-list-cell>
					</view>
					<view class="tui-common__box tui-top__20">
						<view><diyfieldsview ptype="store" update="1" geturl="registerfield.view" :sid="storeDetail.id"></diyfieldsview></view>
					</view>
				</view>
			</view>
			<!--=======店铺 end=======-->
		</view>
		<!--=======二维码 start=======-->
		<tui-modal custom :show="modalShow" backgroundColor="transparent" @cancel="hideModal">
			<view class="tui-poster__box">
				<image src="/static/images/mall/icon_popup_closed.png" class="tui-close__img" @tap.stop="hideModal">
				</image>
				<image :src="qrcodeImg" v-if="qrcodeImg" class="tui-poster__img"></image>
				<tui-button type="danger" width="460rpx" height="80rpx" shape="circle" @click="savePic">保存图片
				</tui-button>
				<view class="tui-share__tips">保存二维图片到手机相册后，分享到您的圈子</view>
			</view>
		</tui-modal>
		<!--=======二维码 end=======-->
	</view>
</template>

<script>
	import diyfieldsview from "@/components/views/diyfields/diyfieldsview"
	export default {
		components: {diyfieldsview},
		data() {
			return {
				uid: '',
				modalShow: false,
				qrcodeImg: '',
				statusBarHeight: 20,
				tabs: [{
						name: '服务/商品'
					},
					{
						name: '评价'
					},
					{
						name: '商家简介'
					}
				],
				is_recommendgoods: '',
				storeDetail: {},
				current: 0,
				recommendgoods: [],
				rankingTab: 0,
				scrollViewId: "id_0",
				height: 0, //scroll-view高度
				top: 0,
				Category: [],
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
			const _this = this;
			setTimeout(() => {
				uni.getSystemInfo({
					success: res => {
						let header = 182;
						let top = 0;
						console.log(res.windowHeight);
						this.height = res.windowHeight - uni.upx2px(header);
						this.top = top + uni.upx2px(header);
					}
				});
			}, 50);

			if (e && e.scene) {
				const scene = decodeURIComponent(e.scene) // 处理扫码进商品详情页面的逻辑
				if (scene) {
					e.id = scene.split(',')[0]
					if (scene.split(',')[1]) {
						e.reid = scene.split(',')[1];
					}
				}
			}

			if (e.reid) {
				uni.setStorageSync('reid', e.reid)
			}
			_this.sam.login().then(res => {
				if (res.uid > 0) {
					_this.uid = res.uid;
					if (uni.getStorageSync('reid')) {
						_this.$request.get('member.bindpid', {
							samkey: (new Date()).valueOf(),
							pid: uni.getStorageSync('reid')
						}).then(res => {
							if (res.errno == 0) {
								console.log('pid绑定成功');
							}
						})
					}
				}
			});
			this.getCategoryall(e.id);
			this.getStoreDetail(e.id);

			_this.$request.get('Goods.recommend').then(res => {
				if (res.errno == 0) {
					_this.recommendgoods = res.data;
				}
			});
		},
		methods: {
			getStoreDetail(sId) {
				const _this = this;
				_this.$request.get('store.detail', {
					id: sId
				}).then(res => {
					if (res.errno == 0) {
						_this.storeDetail = res.data
						if (_this.storeDetail.storeconfig) {
							_this.is_recommendgoods = _this.storeDetail.storeconfig.is_recommendgoods
						}
					}
				});
			},
			storeopenLocation: function() {
				uni.openLocation({
					latitude: Number(this.storeDetail.latitude), //要去的纬度-地址
					longitude: Number(this.storeDetail.longitude), //要去的经度-地址
					address: this.storeDetail.region_name,
					success: function() {
						console.log('success');
					},
					fail: function() {
						console.log('fail');
					}
				})

			},
			getCategoryall: function(sId) {
				let _this = this;
				this.$request.get('store.goodslist', {
					sid: sId,
					showLoading: true
				}).then(res => {
					if (res.errno == 0) {
						_this.Category = res.data;
						_this.categoryId = _this.Category[0].id;
						setTimeout(function() {
							_this.infoScroll();
							_this.getElementTop();
						}, 500)
						console.info("services---request---getsonList--右侧二级三级分类成功")
					}
				})
			},
			initHeader(e) {
				this.width = Number(e.left);
				this.height = Number(e.height);
				this.statusBarHeight = Number(e.statusBarHeight);
				this.bubbleTop = this.height + 6 + 'px';
			},
			change(e) {
				this.current = e.index;
			},
			detail(id) {
				this.tui.href('/pages/goodsDetail/goodsDetail?id=' + id);
			},
			toDetailsTap: function(e) {
				uni.navigateTo({
					url: "/pages/goodsDetail/goodsDetail?id=" + e.currentTarget.dataset.id
				});
			},
			async qrcode() {
				const _this = this;
				if (this.qrcodeImg) {
					this.modalShow = true;
					return;
				}
				uni.showLoading({
					mask: true,
					title: '二维生成中...'
				});
				let qrdata = await _this.$request.post('qrcode.store', {
					sid: _this.storeDetail.id,
					page: '',
					is_hyaline: true,
					expireHours: 1
				})

				//console.log(qrdata);

				if (qrdata.errno == 0) {
					uni.hideLoading();
					this.qrcodeImg = qrdata.data;
					setTimeout(() => {
						this.modalShow = true;
					}, 60);

				} else {
					uni.hideLoading();
					this.tui.toast('生成二维图片失败,请稍后再试');
				}
			},
			hideModal() {
				this.modalShow = false;
			},
			savePic() {
				if (this.qrcodeImg) {
					// #ifdef H5
					uni.previewImage({
						urls: [this.qrcodeImg]
					});
					// #endif

					// #ifndef H5
					this.sam.saveImage(this.qrcodeImg);
					//console.log(this.qrcodeImg);
					// #endif

					this.hideModal();
				}
			},
			// 点击标题切换当前页时改变样式
			swichNav: function(Categoryid, e) {
				let cur = e;
				if (this.currentTab == cur) {
					return false;
				} else {
					this.currentTab = cur;
					this.checkCor();
				}
				this.floorNum = 'b' + Categoryid;

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
						if ((top + 100) >= item) {
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
		//发送给朋友
		onShareAppMessage: function() {
			let _this = this;
			//console.log(_this.uid);
			return {
				title: _this.storeDetail.title || "",
				path: "/pages/store_details/store_details?id=" + _this.storeDetail.id + "&reid=" + _this.uid,
			};
		},

		onShareTimeline(res) { //分享到朋友圈
			return {}
		},
	};
</script>

<style>
	.tui-header {
		width: 100%;
		height: 182rpx;
		background-color: #5796fd;
		align-items: center;
		padding: 0;
		box-sizing: border-box;
		position: fixed;
		overflow: hidden;
		left: 0;
		top: 0;
		z-index: 999;
	}

	.tui-body {
		
	}

	.tui-back__box {
		padding-right: 12rpx;
		padding-left: 4rpx;
		flex-shrink: 0;
	}

	.tui-menu__box {
		padding-right: 26rpx;
		padding-left: 24rpx;
		flex-shrink: 0;
	}

	.tui-flex__center {
		display: flex;
		align-items: center;
	}

	.tui-shop__info {
		width: 100%;
		height: 103rpx;
		overflow: hidden;
		background-color: #5796fd;
		padding-top: 0;
		padding-left: 25rpx;
		padding-right: 25rpx;
		padding-bottom: 0;
		box-sizing: border-box;
		display: flex;
		align-items: center;
		color: #fff;
	}

	.tui-logo {
		width: 80rpx;
		height: 80rpx;
		border-radius: 6rpx;
		display: block;
		margin-right: 12rpx;
		flex-shrink: 0;
	}

	.tui-shop__name {
		font-size: 30rpx;
		font-weight: 500;
		white-space: nowrap;
		overflow: hidden;
		text-overflow: ellipsis;
	}

	.tui-shoprate {
		padding: 0;
		margin: 0;
		display: flex;
		align-items: center;
	}

	.tui-rate__box {
		background-color: rgba(255, 255, 255, 0.3);
		display: flex;
		align-items: center;
		border-radius: 20px;
		padding: 0 6rpx;
		margin-right: 12rpx;
		line-height: 13px;
	}

	.tui-shop__address {
		font-size: 24rpx;
	}

	.tui-shop__follow {
		font-size: 25rpx;
		transform: scale(0.8);
		transform-origin: 0 center;
	}

	.tui-btn__follow {
		margin-left: auto;
	}

	.tui-follow__text {
		padding-left: 6rpx;
	}

	.tui-padding {
		width: 100%;
		padding: 0 25rpx 30rpx;
		box-sizing: border-box;
	}

	.tui-flex {
		display: flex;
		align-items: center;
	}

	/*========推荐 start=========*/
	.tui-ranking__header {
		width: 100%;
		padding: 30rpx 4rpx 24rpx;
		display: flex;
		align-items: center;
		justify-content: space-between;
		box-sizing: border-box;
	}

	.tui-ranking__title {
		font-size: 30rpx;
	}

	.tui-ranking__tabs {
		display: flex;
		align-items: center;
		justify-content: space-between;
		font-size: 26rpx;
		color: #999;
	}

	.tui-ranking__tabs view {
		margin-left: 40rpx;
	}

	.tui-ranking__active {
		color: #000;
		position: relative;
		transition: all 0.1s;
	}

	.tui-ranking__active::after {
		content: ' ';
		width: 60%;
		position: absolute;
		border-bottom: 2px solid #eb0909;
		border-radius: 4px;
		left: 20%;
		bottom: -10rpx;
	}

	.tui-ranking__list {
		width: 100%;
		display: flex;
		align-items: center;
		justify-content: space-between;
	}

	.tui-justify__start {
		justify-content: flex-start !important;
	}

	.tui-item-mr__16 {
		margin-right: 16rpx;
	}

	.tui-ranking__item {
		width: 224rpx;
		border-radius: 12rpx;
		overflow: hidden;
		background-color: #fff;
		padding-bottom: 20rpx;
		box-shadow: 0 3rpx 20rpx rgba(183, 183, 183, 0.1);
	}

	.tui-ranking__item image {
		width: 224rpx;
		height: 224rpx;
		display: block;
	}

	.tui-ranking__gtitle {
		font-size: 24rpx;
		line-height: 24rpx;
		padding: 24rpx 12rpx 8rpx;
		box-sizing: border-box;
		white-space: nowrap;
		overflow: hidden;
		text-overflow: ellipsis;
	}

	.tui-ranking__sub {
		font-size: 25rpx;
		line-height: 25rpx;
		padding: 8rpx 18rpx 8rpx;
		transform: scale(0.8);
		transform-origin: 0 center;
		color: #999;
	}

	.tui-recommend__title {
		padding: 30rpx 4rpx 24rpx;
		font-size: 30rpx;
		box-sizing: border-box;
	}

	.tui-recommend__item {
		width: 100%;
		border-radius: 24rpx;
		background-color: #ffffff;
		padding: 25rpx;
		box-sizing: border-box;
		display: flex;
		margin-bottom: 20rpx;
		box-shadow: 0 3rpx 20rpx rgba(183, 183, 183, 0.1);
	}

	.tui-recommend__right {
		width: 60%;
		position: relative;
	}

	.tui-rg__img {
		width: 240rpx;
		height: 240rpx;
		border-radius: 12rpx;
		flex-shrink: 0;
		margin-right: 20rpx;
	}

	.tui-rg__title {
		width: 98%;
		font-size: 26rpx;
		overflow: hidden;
		text-overflow: ellipsis;
	}

	.tui-sale-price {
		font-size: 34rpx;
		margin-top: 12rpx;
		font-weight: 500;
		color: #e41f19;
	}

	.tui-rg__attr {
		font-size: 24rpx;
		color: #999;
		background-color: #f5f5f5;
		display: inline-flex;
		align-items: center;
		justify-content: center;
		padding: 6rpx 16rpx;
		border-radius: 20px;
		transform: scale(0.8);
		transform-origin: 0 center;
		margin-top: 12rpx;
	}

	.tui-rg__interested {
		font-size: 24rpx;
		margin-top: 12rpx;
	}

	.tui-interested__num {
		padding-left: 8rpx;
		color: #999;
	}

	.tui-rg__imgbox {
		display: flex;
		align-items: center;
		position: absolute;
		bottom: 2rpx;
		left: 0;
	}

	.tui-rg__imgbox image {
		width: 84rpx;
		height: 84rpx;
		border-radius: 12rpx;
		margin-right: 20rpx;
		flex-shrink: 0;
	}

	/*=======推荐 end=======*/

	/*====活动========= start*/
	.tui-activity__box {
		width: 100%;
		padding: 25rpx;
		box-sizing: border-box;
		border-radius: 24rpx;
		background-color: #fff;
		margin-top: 20rpx;
		box-shadow: 0 3rpx 20rpx rgba(183, 183, 183, 0.1);
	}

	.tui-activity__title {
		font-size: 32rpx;
		line-height: 32rpx;
		padding-bottom: 25rpx;
	}

	.tui-activity__item {
		width: 100%;
		display: flex;
		margin-bottom: 32rpx;
	}

	.tui-activity__item image {
		width: 180rpx;
		height: 180rpx;
		border-radius: 8rpx;
		margin-right: 20rpx;
		flex-shrink: 0;
	}

	.tui-activity__right {
		flex: 1;
		overflow: hidden;
		position: relative;
	}

	.tui-ag__title {
		width: 100%;
		white-space: nowrap;
		overflow: hidden;
		text-overflow: ellipsis;
		font-size: 28rpx;
	}

	.tui-ag__bottom {
		width: 100%;
		display: flex;
		align-items: flex-end;
		justify-content: space-between;
		position: absolute;
		left: 0;
		bottom: 0;
	}

	.tui-ag__discount {
		color: #eb0909;
		font-size: 24rpx;
	}

	.tui-ag__discount text {
		font-size: 32rpx;
		line-height: 32rpx;
	}

	.tui-ag__original {
		color: #999;
		font-size: 24rpx;
		line-height: 24rpx;
		text-decoration: line-through;
		padding-left: 10rpx;
	}

	.tui-ag__tag {
		padding: 12rpx 0;
	}

	.tui-ag__btn {
		display: flex;
		flex-direction: column;
		justify-content: center;
		align-items: center;
		font-size: 24rpx;
		line-height: 24rpx;
		color: #fff;
		background: linear-gradient(90deg, rgb(255, 89, 38), rgb(240, 14, 44));
		padding: 8rpx 24rpx;
		border-radius: 8rpx;
	}

	.tui-btn__text {
		font-size: 28rpx;
		line-height: 28rpx;
		padding-top: 8rpx;
	}

	/*=====活动======== end*/

	/*=====新品===== start*/
	.tui-ptop__0 {
		padding-top: 0 !important;
	}

	/*======新品======= end*/

	.tui-score__bg {
		width: 100%;
		height: 100rpx;
	}

	.tui-score__box {
		width: 100%;
		padding: 10rpx 30rpx 30rpx;
	}

	.tui-rate__box {
		display: flex;
		align-items: center;
		border-radius: 20px;
		padding: 0 6rpx;
		margin-right: 12rpx;
		background-color: rgba(83, 83, 83, .1);
		line-height: 13px;
	}

	.tui-flex__center {
		display: flex;
		align-items: center;
		margin-top: 20rpx;
	}

	.tui-flex__center text {
		font-size: 24rpx;
		color: #999;
		padding-right: 50rpx;
	}

	.tui-score text {
		padding-right: 25rpx;
	}

	.tui-color__red text {
		color: #EB0909;
	}

	.tui-color__green text {
		color: #19be6b;
	}

	.tui-info__box {
		width: 100%;
		padding: 0 25rpx 100rpx;
		box-sizing: border-box;
		margin-top: -100rpx;
	}

	.tui-top__20 {
		margin-top: 20rpx;
	}

	.tui-flex {
		display: flex;
	}

	.tui-text__shrink {
		flex-shrink: 0;
	}

	.tui-sub__info {
		color: #999;
		padding-left: 40rpx;
		word-break: break-all;


	}

	.map-img {
		width: 50rpx;
		height: 50rpx;
		margin-left: 10rpx;
		margin-right: 10rpx;
		margin-top: -30rpx;
		margin-bottom: -10rpx;
	}

	/*二维码modal弹层*/
	.tui-poster__box {
		width: 100%;
		position: relative;
		display: flex;
		justify-content: center;
		align-items: center;
		flex-direction: column;
	}

	.tui-close__img {
		width: 48rpx;
		height: 48rpx;
		position: absolute;
		right: 0;
		top: -60rpx;
	}

	.tui-poster__img {
		width: 560rpx;
		height: 560rpx;
		border-radius: 20rpx;
		margin-bottom: 40rpx;
	}

	.tui-share__tips {
		font-size: 24rpx;
		transform: scale(0.8);
		transform-origin: center center;
		color: #ffffff;
		padding-top: 12rpx;
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
		font-size: 26rpx;
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
		width: 48%;
		margin-left: 1%;
		margin-right: 1%;
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
		width: 100%;
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