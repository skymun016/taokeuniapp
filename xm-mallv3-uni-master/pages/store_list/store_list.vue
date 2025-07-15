<template>
	<view class="container">
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
					<block v-for="(item, index) in storecateList" :key="index">
						<view :class="'item ' + (cid==item.id ? 'font-color':'')" @tap="set_where" :data-cid="item.id">
							<view class="item-image">
								<image :src="item.icon"></image>
							</view>
							<view>{{item.title}}</view>
						</view>
					</block>
				</view>
			</scroll-view>
			<view :class="'list acea-row row-between-wrapper ' + (is_switch==true?'':'on')">
				<navigator v-for="(item, index) in dataList" :key="index" :class="'item ' + (is_switch==true?'':'on')"
					hover-class="none" :url="'/pages/store_details/store_details?id=' + item.id">
					<view :class="'pictrue ' + (is_switch==true?'':'on')">
						<image :src="item.store_logo" :class="is_switch==true?'':'on'"></image>
					</view>
					<view :class="'text ' + (is_switch==true?'':'on')">
						<view class="name line1">{{item.title}}</view>
						<view class="store-star">
							<image src="/static/images/star_32px.png"></image>
							<image src="/static/images/star_32px.png"></image>
							<image src="/static/images/star_32px.png"></image>
							<image src="/static/images/star_32px.png"></image>
							<image src="/static/images/star_32px.png"></image>
						</view>
						<view class="address">
							<image src="/static/images/address.png"
								style="margin-right: 10rpx;vertical-align: middle;width:30rpx; height: 30rpx;"></image>
							<text v-if="item.distance" class="kmclass">[{{item.distance}}km]</text>
							<block v-if="item.region_name">{{item.region_name}}</block>{{item.address}}
						</view>
					</view>
				</navigator>
				<view class="loadingicon acea-row row-center-wrapper" v-if="dataList.length > 0">
					<text class="loading iconfont icon-jiazai" :hidden="loading==false"></text>
				</view>
			</view>
		</view>
		<view class="noCommodity" v-if="dataList.length==0 && where.page > 1">
			<view class="pictrue">

			</view>
		</view>
		<tui-tabbar :current="current" backgroundColor="#FFFFFF" color="#666" selectedColor="#EB0909">
		</tui-tabbar>
	</view>
</template>

<script>
	var app = getApp();
	export default {
		data() {
			return {
				lang:{},
				current: '',
				storecateList: [],
				dataList: [],
				cityName: '',
				navH: "",
				is_switch: false,
				where: {
					cid: 0,
					keyword: '',
					priceOrder: '',
					salesOrder: '',
					news: 0,
					page: 1,
					limit: 10,
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
				latitude: "",
				longitude: "",
				cid: ""
			};
		},

		components: {},
		props: {},

		/**
		 * 生命周期函数--监听页面加载
		 */
		onLoad: function(options) {
			const _this = this;
			uni.setStorageSync('NewMessage', '');
			this.where.cid = options.cid || ''
			// #ifdef MP-WEIXIN
			this.current = "/" + this.__route__;
			// #endif
			//#ifdef H5
			this.current = this.$route.path;
			//#endif
			if (uni.getStorageSync('cityName')) {
				this.cityName = uni.getStorageSync('cityName');
			}
			_this.$request.get('Lang.getlang').then(res => {
				if (res.errno == 0) {
					_this.lang = res.data;
					uni.setNavigationBarTitle({
						title: _this.lang.store
					});
				}
			});
			//获取地置位置
			_this.sam.getCityPosition().then(res => {
				_this.cityName = res.cityName;
			});

			// #ifdef MP-WEIXIN
			wx.authorize({
				scope: 'scope.userFuzzyLocation',
				success: res => {
					//console.log(res)
					wx.getFuzzyLocation({
						type: 'wgs84',
						success(res) {
							_this.where.latitude = res.latitude;
							_this.where.longitude = res.longitude;
							_this.loadend = false;

							_this.title = options.title || '';
							_this.where.keyword = options.searchValue || '';

							_this.get_data_list();
						}
					});
				},
				fail: res => {
					//console.log('失败：', res);
				}
			});
			//#endif

			//#ifdef H5 || APP-PLUS
			uni.getLocation({
				type: 'wgs84',
				success: function(res) {
					//alert(res.latitude);
					_this.where.latitude = res.latitude;
					_this.where.longitude = res.longitude;
					_this.loadend = false;

					_this.title = options.title || '';
					_this.where.keyword = options.searchValue || '';

					_this.get_data_list();

				},
			})
			//#endif

			this.get_storecate_list();
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
						_this.latitude = res.latitude;
						_this.longitude = res.longitude;
						_this.get_data_list(true);
					}

				});
			},

			Changswitch: function() {
				var that = this;
				that.is_switch = !this.is_switch;
			},
			searchSubmit: function(e) {
				var that = this;
				that.where.keyword = e.detail.value;
				that.where.page = 1;
				that.loadend = false;
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
			get_storecate_list: function() {
				var that = this;
				that.$request.get('category.list').then(res => {
					that.storecateList = res.data;
				});
			},
			//查找店铺
			get_data_list: function(isPage) {
				var that = this;
				this.setWhere();
				if (that.loadend) return;
				if (that.loading) return;
				if (isPage === true) {
					that.dataList = [];
					that.where.page =1;
				}
				that.where.showLoading = true;
				//console.log(that.where);
				that.$request.post('store.list', that.where).then(res => {
					if (res.errno == 0) {
						that.dataList = that.dataList.concat(res.data);
						that.loadend = that.dataList.length < that.where.limit;
						that.loading = false;
						that.where.page = that.where.page + 1
					}
				});
			}
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
		left: 0;
		z-index: 99999;
	}

	.searchinput {
		padding-left: 10rpx;
	}

	.tui-rolling-search {
		width: 100%;
		height: 66rpx;
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

	.navscroll {
		z-index: 99999;
		height: 188rpx;
		top: 91rpx;
		position: fixed;
		display: flex;
		white-space: nowrap;
		left: 0;
		background-color: #fff;
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

	.dataList .list {
		padding: 0 20rpx;
		padding-top: 276rpx;
		margin-top: 6rpx;
	}

	.kmclass {
		font-size: 28rpx;
		font-weight: normal;
		color: #909090;
	}

	.address {
		color: #909090;
	}

	.dataList .list.on {
		background-color: #fff;
		border-top: 1px solid #f6f6f6;
	}

	.dataList .list .item {
		width: 345rpx;
		margin-top: 20rpx;
		background-color: #fff;
		border-radius: 10rpx;
	}

	.dataList .list .item.on {
		width: 100%;
		display: flex;
		border-bottom: 1rpx solid #f6f6f6;
		padding: 30rpx 0;
		margin: 0;
	}

	.dataList .list .item .pictrue {
		width: 100%;
		height: 345rpx;
	}

	.dataList .list .item .pictrue.on {
		width: 180rpx;
		height: 180rpx;
	}

	.dataList .list .item .pictrue image {
		width: 100%;
		height: 100%;
		border-radius: 10rpx 10rpx 0 0;
	}

	.dataList .list .item .pictrue image.on {
		border-radius: 6rpx;
	}

	.dataList .list .item .text {
		padding: 20rpx 17rpx 26rpx 17rpx;
		font-size: 28rpx;
		color: #373333;
	}

	.dataList .list .item .text .name {
		font-size: 32rpx;
		font-weight: bold;
	}

	.dataList .list .item .text.on {
		width: 508rpx;
		padding: 0 0 0 22rpx;
	}

	.dataList .list .item .text .money {
		font-size: 23rpx;
		margin-top: 8rpx;
	}

	.dataList .list .item .text .money.on {
		margin-top: 50rpx;
	}

	.dataList .list .item .text .money .num {
		font-size: 32rpx;
	}

	.dataList .list .item .text .vip {
		font-size: 22rpx;
		color: #aaa;
		margin-top: 7rpx;
	}

	.dataList .list .item .text .vip.on {
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

	.noCommodity {
		background-color: #fff;
	}

	.store-star {
		margin-top: 10rpx;
	}

	.store-star image {
		width: 28rpx;
		height: 28rpx;
	}
</style>
