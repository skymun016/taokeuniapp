<template>
	<view class="container">
		<!--header-->
		<!-- #ifdef MP-WEIXIN || APP-PLUS -->
		<view class="tui-header-box">
			<view :style="{ height: height + 'px' }">
				<view class="tui-header"
					:style="{paddingTop: top + 'px' }">
					<view class="header-title"><text>{{lang.technical}}</text></view>
					<tui-tabs style="float: right;" :top="0" :tabs="maplistTabs" :height="88" :width="130"
						:currentTab="where.is_markers" :sliderWidth="100" :sliderHeight="40" itemWidth="50%" bottom="50%" color="#888"
						selectedColor="#ffffff" :sliderBgColor="pagestyleconfig.appstylecolor" @change="maplistchange">
					</tui-tabs>
				</view>
			</view>
		</view>
		<!-- #endif -->
		<!--header-->
		<view class="dataList">
			<view class="search">
				<view class="tui-rolling-search">
					<tui-icon @tap="onChangePosition" name="position-fill" color="#333" :size="30" unit="rpx">
					</tui-icon>
					<view @tap="onChangePosition" class="tui-city-name">{{cityName}}</view>
					<tui-icon @tap="onChangePosition" name="turningdown" :size="32" unit="rpx"></tui-icon>
					<view class="tui-city-line">|</view>
					<tui-icon name="search-2" :size="32" unit="rpx"></tui-icon>
					<form @submit="searchSubmit">
						<input class="searchinput" placeholder="搜索" placeholder-class="placeholder"
							@confirm="searchSubmit" confirm-type="search" name="search" :value="where.keyword">
					</form>
				</view>
			</view>
			<scroll-view class="navscroll" scroll-x="true">
				<view class="nav acea-row row-middle">
					<block v-for="(item, index) in category" :key="index">
						<view :class="'item ' + (cid==item.id ? 'font-color':'')" @tap="set_where" :data-cid="item.id">
							<view>{{item.title}}</view>
						</view>
					</block>
				</view>
			</scroll-view>
			<view v-if="where.is_markers!=1" class="paishu">
				<tui-button type="danger" :plain="where.orderby!=''" width="152rpx" height="50rpx" :size="26"
					shape="circle" @click="set_orderby('')">综合
				</tui-button>
				<tui-button type="danger" :plain="where.orderby!='service_times'" width="152rpx" height="50rpx"
					:size="26" shape="circle" @click="set_orderby('service_times')">接单量
				</tui-button>
				<tui-button type="danger" :plain="where.orderby!='comment'" width="152rpx" height="50rpx" :size="26"
					shape="circle" @click="set_orderby('comment')">好评多
				</tui-button>
				<tui-button type="danger" :plain="where.orderby!='distance'" width="152rpx" height="50rpx" :size="26"
					shape="circle" @click="set_orderby('distance')">距离
				</tui-button>
			</view>
			<!-- 地图找人 -->
			<view v-if="where.is_markers==1" class="mapbox">
				<xmmap :winHeight="winHeight" :latitude="where.latitude" :longitude="where.longitude"
					:scale="14" :markers="markers" @callouttap="callouttap"></xmmap>
			</view>
			<!-- 地图找人end -->
			<view v-else class="list acea-row row-between-wrapper">
				<block v-for="(item, index) in dataList" :key="index">
					<view @tap="details" :data-id="item.id" :data-uuid="item.uuid" :data-isbusiness="item.is_business"
						class="item">
						<view :class="'pictrue on'">
							<image :src="item.touxiang || '/static/images/my/mine_def_touxiang_3x.png'"></image>
						</view>
						<view class="text">
							<view class="nametitle">{{item.title}}</view>
							<view class="indicators">
								<tui-icon name="star-fill" :size="16" color="#ff1e02"></tui-icon>
								<text class="indctext" style="color: #ff1e02;">5.0</text>
								<text class="indctext">已服务: {{item.service_times}}单</text>
							</view>
							<view class="belong">
								<tui-icon name="shop" :size="16" color="#909090"></tui-icon>
								<text class="indctext"
									style="width: 100rpx;overflow-x: hidden;">{{item.storename}}</text>
								<tui-icon name="message" :size="16" color="#909090"></tui-icon>
								<text class="indctext"> {{item.comment}}</text>
								<tui-icon name="like" :size="16" color="#909090"></tui-icon>
								<text class="indctext"> {{item.viewed}}</text>
							</view>
						</view>
						<view class="buttonbox">
							<view class="distance">
								<tui-icon name="position-fill" :size="16"
									:color="pagestyleconfig.appstylecolor"></tui-icon><text v-if="item.distance"
									class="kmclass">{{item.distance}}km</text>
							</view>
							<button v-if="item.is_business==1" :style="'background:'+ pagestyleconfig.appstylecolor"
								class="itembutton">立即预约</button>
							<button v-else class="restitembutton">休息中</button>
						</view>
					</view>

				</block>

				<view class="loadingicon acea-row row-center-wrapper" v-if="dataList.length > 0">
					<text class="loading iconfont icon-jiazai" :hidden="loading==false"></text>
				</view>
			</view>
		</view>
		<view class="noCommodity" v-if="dataList.length==0 && where.page > 1">
			<view class="pictrue">

			</view>
		</view>
		<tui-modal :show="markersInfoShow" @cancel="hidemarkersInfo" padding="0rpx 0rpx" radius="60rpx" :custom="true" fadeIn>
			<image src="/static/images/mall/icon_popup_closed.png" class="tui-close__img"
				@tap.stop="hidemarkersInfo">
			</image>
			<view  @tap="details" :data-id="markersInfo.technical_id" :data-uuid="markersInfo.uuid" :data-isbusiness="markersInfo.is_business" class="tui-modal-custom">
				<image mode="widthFix" :src="markersInfo.touxiang" class="touxiang-image"/>
				<view class="sustombody">
					<view class="tui-prompt-title"><view class="text">{{markersInfo.title}}</view>
					<view class="infodistance">
						<tui-icon name="position-fill" :size="16"
							:color="pagestyleconfig.appstylecolor"></tui-icon><text v-if="markersInfo.distance"
							class="kmclass">{{markersInfo.distance}}km</text>
					</view>
					</view>
					<view class="tui-modal__btn">
						<view class="tui-box">
							<button class="tui-btn-danger">立即预约</button>
						</view>
					</view>
				</view>
			</view>
		</tui-modal>
		<tui-tabbar :current="current">
		</tui-tabbar>
	</view>
</template>
<script>
	import xmmap from '@/components/xmmap/xmmap.vue';
	var app = getApp();
	export default {
		data() {
			return {
				maplistTabs: [{
					name: "列表"
				}, {
					name: "地图"
				}],
				lang: {},
				pagestyleconfig: [],
				current: '',
				category: [],
				dataList: [],
				winHeight: 0,
				height: 64, //header高度
				top: 26, //标题图标距离顶部距离
				scrollH: 0, //滚动总高度
				markers: [],
				cityName: '',
				navH: "",
				goodsid: '',
				markersInfoShow: false,
				markersInfo:{},
				where: {
					is_markers: 1,
					keyword: '',
					priceOrder: '',
					salesOrder: '',
					orderby: '',
					news: 0,
					page: 1,
					limit: 10,
					cid: 0,
					latitude: "",
					longitude: ""
				},
				price: 0,
				stock: 0,
				nows: false,
				loadend: false,
				loading: false,
				bottommenulist: [],
				host_product: "",
				title: "",
				position: "",
				cid: ""
			};
		},

		components: {
			xmmap
		},
		props: {},

		/**
		 * 生命周期函数--监听页面加载
		 */
		onLoad: function(options) {
			const _this = this;
			let obj = {};
			uni.setStorageSync('NewMessage', '');
			setTimeout(() => {
				uni.getSystemInfo({
					success: res => {
						this.width = obj.left || res.windowWidth;
						this.height = obj.top ? obj.top + obj.height + 8 : res.statusBarHeight +
							44;
						this.top = obj.top ? obj.top + (obj.height - 32) / 2 : res
							.statusBarHeight + 6;
						this.scrollH = res.windowWidth;
						this.windowWidth = res.windowWidth;
						this.winHeight = res.windowHeight - uni.upx2px(204);
					}
				});
			}, 0);
			_this.$request.get('Lang.getlang').then(res => {
				if (res.errno == 0) {
					_this.lang = res.data;
					uni.setNavigationBarTitle({
						title: _this.lang.technical
					});
				}
			});

			_this.$request.post('config', {
				mo: 'pagestyle'
			}).then(res => {
				if (res.errno == 0) {
					_this.pagestyleconfig = res.data
					if(_this.pagestyleconfig.technicalliststyle==1){
						_this.where.is_markers = 1;
					}else{
						_this.where.is_markers = 0;
					}
				}
			});

			this.where.cid = options.cid || ''
			this.goodsid = options.goodsid || ''
			// #ifdef MP-WEIXIN
			this.current = "/" + this.__route__;
			// #endif
			//#ifdef H5
			this.current = this.$route.path;
			//#endif
			//获取地置位置
			_this.sam.getCityPosition().then(res => {
				_this.cityName = res.cityName;
			});
			_this.title = options.title || '';
			_this.where.keyword = options.searchValue || '';
			this.getPosition();
			this.get_cate_list();
		},

		/**
		 * 生命周期函数--监听页面显示
		 */
		onShow: function() {},

		/**
		 * 页面相关事件处理函数--监听用户下拉动作
		 */
		onPullDownRefresh: function() {
			this.where.page = 1;
			this.loadend = false;
			this.dataList = [];
			this.get_data_list();
			uni.stopPullDownRefresh();
		},

		/**
		 * 页面上拉触底事件的处理函数
		 */
		onReachBottom: function() {
			this.get_data_list();
		},
		methods: {
			//获取定位信息
			getPosition() {
				const _this = this;
				// #ifdef MP-WEIXIN
				wx.authorize({
					scope: 'scope.userFuzzyLocation',
					success: res => {
						//console.log(res)
						wx.getFuzzyLocation({
							type: 'wgs84',
							success(res) {
								_this.position = res.address;
								_this.where.latitude = res.latitude;
								_this.where.longitude = res.longitude;
								_this.loadend = false;
								_this.get_data_list();

							}
						});
					},
					fail: res => {
						//console.log('失败：', res);
					}
				});
				// #endif

				//#ifdef H5 || APP-PLUS
				uni.getLocation({
					type: 'wgs84',
					success: function(lb) {
						_this.where.latitude = lb.latitude;
						_this.where.longitude = lb.longitude;
						_this.get_data_list();
					},
				})
				//#endif

			},
			details: function(e) {
				if (e.currentTarget.dataset.isbusiness == 1) {
					if (this.goodsid) {
						this.sam.navigateTo('/pages/goodsDetail/goodsDetail?id=' + this.goodsid + '&uuid=' + e
							.currentTarget
							.dataset.uuid);
					} else {
						this.sam.navigateTo('/pages/technical/details?id=' + e.currentTarget.dataset.id);
					}
				}
			},
			onChangePosition: function(e) {
				const _this = this;
				uni.chooseLocation({
					success(res) {
						_this.cityName = res.name;
						uni.setStorageSync('cityName', _this.cityName);
						_this.position = res.address;
						_this.where.latitude = res.latitude;
						_this.where.longitude = res.longitude;
						_this.loadend = false;
						_this.get_data_list(true);
					}

				});
			},
			searchSubmit: function(e) {
				var that = this;
				that.where.keyword = e.detail.value;
				that.where.page = 1;
				that.loadend = false;
				this.get_data_list(true);
			},
			//点击事件处理
			set_orderby: function(e) {
				this.where.orderby = e;
				this.loadend = false;
				this.where.page = 1;
				this.get_data_list(true);
			},
			//点击事件处理
			set_where: function(e) {
				this.cid = e.currentTarget.dataset.cid;
				this.loadend = false;
				this.where.page = 1;

				this.get_data_list(true);
			},
			//设置where条件
			setWhere: function() {
				this.where.salesOrder = 'asc';

				if (this.cid) {
					this.where.cid = this.cid;
				}
			},
			get_cate_list: function() {
				var that = this;
				that.$request.get('category.list', {
					ptype: 2
				}).then(res => {
					that.category = res.data;
				});
			},
			//查找
			get_data_list: function(isPage) {
				var that = this;
				this.setWhere();
				if (that.loadend) return;
				if (that.loading) return;
				if (isPage === true) {
					that.dataList = [];
					that.where.page = 1;
				}
				that.where.showLoading = true;
				//console.log(that.where);
				that.$request.post('technical.list', that.where).then(res => {
					if (res.errno == 0) {
						if(that.where.is_markers==1){
							that.markers = res.data.data;
						}else{
							that.dataList = that.dataList.concat(res.data.data);
							that.loadend = that.dataList.length < that.where.limit;
							that.where.page = that.where.page + 1
						}
						that.loading = false;
					}
				});
			},
			callouttap(e) {
				var _this = this;
				_this.markersInfo = _this.markers[e.markerId];
				console.log(_this.markersInfo);
				_this.markersInfoShow = true;
			},
			hidemarkersInfo() {
				this.markersInfoShow = false;
			},
			maplistchange: function(e) {
				console.log(e.index);
				this.where.is_markers = e.index
				this.get_data_list(true);
			},
		}
	};
</script>
<style>
	page {
		font-size: 28rpx;
		background-color: #f5f5f5;
		color: #333;
	}

	.container {
		padding-bottom: 228rpx;
		color: #333;
	}
	.tui-header-box {
		width: 100%;
		position: fixed;
		left: 0;
		top: 0;
		z-index: 995;
		background-color: #ffffff;
	}
	.tui-header {
		width: 100%;
		padding-left: 26rpx;
		font-size: 18px;
		line-height: 18px;
		font-weight: 500;
		height: 32px;
		display: flex;
		align-items: center;
		justify-content: left;
	}
	.header-title {
		width: 280rpx;
	}
	.header-tabs {
		border-radius: 60rpx;
	}
	.dataList .search {
		width: 100%;
		padding-top: 13rpx;
		padding-bottom: 13rpx;
		padding-left: 15rpx;
		padding-right: 15rpx;
		box-sizing: border-box;
		align-items: center;
		background-color: #f5f5f5;
		position: fixed;
		display: flex;
		/* #ifdef MP-WEIXIN || APP-PLUS */
		top: 178rpx;
		/* #endif */
		/* #ifdef H5 */
		top: 0rpx;
		/* #endif */
		left: 0;
		z-index: 99999;
	}
	.navscroll {
		z-index: 99999;
		height: 86rpx;
		/* #ifdef MP-WEIXIN || APP-PLUS */
		top: 268rpx;
		/* #endif */
		/* #ifdef H5 */
		top: 91rpx;
		/* #endif */
		
		position: fixed;
		display: flex;
		white-space: nowrap;
		left: 0;
		background-color: #fff;
	}

	.searchinput {
		padding-left: 10rpx;
	}

	.tui-rolling-search {
		width: 100%;
		height: 68rpx;
		border-radius: 35rpx;
		padding: 0 40rpx 0 30rpx;
		box-sizing: border-box;
		background-color: #fff;
		display: flex;
		align-items: center;
		flex-wrap: nowrap;
		color: #999;
	}

	.tui-city-name {
		padding-left: 6rpx;
		padding-right: 0rpx;
		color: #333;
		font-size: 24rpx;
		line-height: 24rpx;
	}

	.tui-city-line {
		color: #d3d3d3;
		padding-left: 16rpx;
		padding-right: 20rpx;
		font-size: 24rpx;
		line-height: 24rpx;
	}

	.dataList .bg-color {
		background-color: #f5f5f5;
	}

	.dataList .search .location {
		color: #333;
		font-size: 28rpx;
		flex: 1;
		display: flex;
		margin-top: 22rpx;
		line-height: 40rpx;
	}

	.paishu {
		z-index: 2;
		width: 100%;
		height: 88rpx;
		top: 177rpx;
		background-color: #fff;
		border-top: 1rpx solid #f5f5f5;
		border-bottom: 1rpx solid #f5f5f5;
		display: flex;
		align-items: center;
		justify-content: space-between;
		padding-top: 12rpx;
		padding-left: 18rpx;
		padding-right: 18rpx;
		padding-bottom: 12rpx;
		box-sizing: border-box;
		position: fixed;
	}

	.dataList .nav {
		width: 1000rpx;
		display: flex;
		color: #454545;
		font-size: 28rpx;
		background-color: #fff;
	}

	.dataList .nav .item {
		text-align: center;
		height: 100%;
		padding: 25rpx;
	}

	.dataList .nav .item.font-color {
		font-weight: bold;
	}

	.dataList .nav .item image {
		width: 88rpx;
		height: 88rpx;
		margin-left: 10rpx;
	}

	.dataList .mapbox {
		padding-top: 120rpx;
	}

	.dataList .list {
		padding: 20rpx;
		/* #ifdef MP-WEIXIN || APP-PLUS */
		padding-top: 346rpx;
		/* #endif */
		/* #ifdef H5 */
		padding-top: 255rpx;
		/* #endif */
		margin-top: 6rpx;
	}

	.kmclass {
		padding-left: 5rpx;
		color: #333333;
	}

	.dataList .list .item {
		background-color: #fff;
		display: flex;
		margin-top: 20rpx;
		padding: 20rpx;
		border-radius: 18rpx;
	}

	.dataList .list .item .pictrue {
		width: 180rpx;
		height: 180rpx;
	}

	.dataList .list .item .pictrue image {
		width: 100%;
		height: 100%;
		border-radius: 28rpx;
	}

	.dataList .list .item .text {
		width: 308rpx;
		padding: 0 0 0 22rpx;
		font-size: 28rpx;
		color: #373333;
	}

	.dataList .list .item .text .money {
		font-size: 23rpx;
		margin-top: 50rpx;
	}

	.dataList .list .item .text .money .num {
		font-size: 32rpx;
	}

	.dataList .list .item .text .vip {
		font-size: 22rpx;
		color: #aaa;
		margin-top: 12rpx;
	}

	.dataList .list .item .text .vip .vip-money {
		font-size: 24rpx;
		color: #282828;
		font-weight: bold;
	}

	.dataList .list .item .text .vip .vip-money image {
		width: 46rpx;
		height: 21rpx;
		margin-left: 4rpx;
	}

	.dataList .list .item .buttonbox {
		margin-top: 40rpx;
		width: 200rpx;
		padding: 0 0 0 22rpx;
		font-size: 28rpx;
		color: #373333;
	}

	.nametitle {
		font-size: 32rpx;
		padding-top: 2rpx;
		word-break: break-all;
		font-weight: bold;
		overflow: hidden;
		text-overflow: ellipsis;
		display: -webkit-box;
		-webkit-box-orient: vertical;
		-webkit-line-clamp: 2;
	}

	.distance {
		text-align: center;
	}

	.itembutton {
		margin-top: 10rpx;
		font-size: 24rpx;
		color: #ffffff;
		align-items: center;
	}

	.restitembutton {
		margin-top: 10rpx;
		font-size: 24rpx;
		color: #ffffff;
		align-items: center;
		background-color: #999999;
	}

	.noCommodity {
		background-color: #fff;
	}

	.indicators {
		color: #909090;
		margin-top: 20rpx;
	}

	.belong {
		color: #909090;
		margin-top: 20rpx;
	}

	.indctext {
		padding-left: 3rpx;
		padding-right: 15rpx;
	}
	/*师傅modal弹层*/
	.tui-close__img {
		width: 48rpx;
		height: 48rpx;
		position: absolute;
		right: 0;
		top: -60rpx;
	}
	.tui-modal-custom {
		padding-top: 0rpx;
		padding-bottom: 50rpx;
	}
	.sustombody {
		padding-top: 10rpx;
		padding-left: 20rpx;
		padding-right: 20rpx;
	}
	.tui-prompt-title {
		display: flex;
		padding-bottom: 20rpx;
	}
	.tui-prompt-title .text {
		width: 60%;
		font-size: 34rpx;
		color: #373333;
	}
	.tui-prompt-title .infodistance {
		width: 40%;
		text-align: right;
	}
	.tui-modal__btn {
		align-items: center;
		justify-content: space-between;
	}
	.tui-box {
		padding: 15rpx 20rpx;
		box-sizing: border-box;
	}
	.tui-btn-danger {
		height: 80rpx;
		line-height: 80rpx;
		background: #eb0909 !important;
		border-radius: 98rpx;
		color: #fff;
	}
	.touxiang-image {
		width: 100%;
		border-radius: 60rpx 60rpx 0rpx 0rpx;
	}
	/*师傅modal弹层end*/
</style>