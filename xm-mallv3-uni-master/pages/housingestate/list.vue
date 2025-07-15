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
							@confirm="searchSubmit" confirm-type="search" name="search"
							:value="where.keyword">
					</form>
				</view>
			</view>
			<view class="list acea-row row-between-wrapper">
				<block v-for="(item, index) in dataList" :key="index">
					<view @tap="selectTap" :data-item="item" class="item">
						<view :class="'pictrue on'">
							<image :src="item.image || '/static/images/default_img.png'"></image>
						</view>
						<view class="text">
							<view class="nametitle">{{item.title}}</view>
							<view class="tztitle">{{item.community_title}} 站长: {{item.tz_title}}</view>
							<view class="indicators">
								<text class="indctext">地址: {{item.area_name}}{{item.house_number}}</text>
							</view>
						</view>
						<view class="buttonbox">
							<view class="distance">
								<tui-icon name="position-fill" :size="16" color="#ff1e02"></tui-icon><text
									v-if="item.distance" class="kmclass">{{item.distance}}km</text>
							</view>
							<button class="itembutton">选择</button>
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
		<tui-tabbar :current="current">
		</tui-tabbar>
	</view>
</template>

<script>
	var app = getApp();
	export default {
		data() {
			return {
				current: '',
				dataList: [],
				cityName: '',
				navH: "",
				goodsid: '',
				where: {
					keyword: '',
					priceOrder: '',
					salesOrder: '',
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
			this.where.cid = options.cid || ''
			this.goodsid = options.goodsid || ''
			// #ifdef MP-WEIXIN
			this.current = "/" + this.__route__;
			// #endif
			//#ifdef H5
			this.current = this.$route.path;
			//#endif
			_this.title = options.title || '';
			_this.where.keyword = options.searchValue || '';
			
		},

		/**
		 * 生命周期函数--监听页面显示
		 */
		onShow: function() {
			const _this = this; 
			//获取地置位置
			_this.sam.getCityPosition().then(res => {
				_this.cityName = res.cityName;
				_this.getPosition();
			});
		},

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
						console.log(lb.longitude, lb.latitude);
					},
				})
				//#endif

			},
			selectTap: function(e) {
				const _this = this;
				var item = e.currentTarget.dataset.item;
				_this.$request.post('operatingcity.getcity', {
					samkey: (new Date()).valueOf(),
					he_id:item.id
				}).then(res => {
					uni.navigateBack({});
				});
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
						_this.latitude = res.latitude;
						_this.longitude = res.longitude;
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
			//查找
			get_data_list: function(isPage) {
				var that = this;
				this.setWhere();
				if (that.loadend) return;
				if (that.loading) return;
				if (isPage === true) {
					that.dataList = [];
					that.where.page =1;
				}
				that.where.samkey= (new Date()).valueOf()
				//console.log(that.where);
				that.$request.post('housingestate.list', that.where).then(res => {
					var dataList = res.data.data;
					var loadend = dataList.length < that.where.limit;
					that.where.page = that.where.page + 1;
					that.loadend = loadend;
					that.loading = false;
					that.dataList = dataList;
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
		padding: 20rpx;
		padding-top: 80rpx;
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
	.tztitle {
		font-size: 28rpx;
		margin-top: 10rpx;
		padding-top: 2rpx;
		word-break: break-all;
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
		background-color: #52c276;
	}

	.noCommodity {
		background-color: #fff;
	}

	.indicators {
		color: #909090;
		margin-top: 10rpx;
	}

	.belong {
		color: #909090;
		margin-top: 20rpx;
	}

	.indctext {
		padding-left: 3rpx;
		padding-right: 15rpx;
	}
</style>
