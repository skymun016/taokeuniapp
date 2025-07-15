<template>
	<view class="container">
		<tui-tabs :top="0" :tabs="statusType" :isFixed="scrollTop>=0" :currentTab="currentTab" selectedColor="#E41F19"
			sliderBgColor="#E41F19" @change="statusTap" itemWidth="20%"></tui-tabs>
		<!--选项卡逻辑自己实现即可，此处未做处理-->
		<view :class="{'tui-order-list':scrollTop>=0}">
			<view class="tui-order-item" v-for="(item,orderIndex) in orderList" :key="orderIndex">
				<tui-list-cell :hover="false" :lineLeft="false">
					<view class="tui-goods-title">
						<view>订单号：{{item.order_num_alias}}</view>
						<view class="tui-order-status">{{item.statusStr}}</view>
					</view>
				</tui-list-cell>
				<block v-if="item.cat_id">
					<tui-list-cell padding="0" @click="orderDetail(item.id)">
						<view class="tui-goods-item">
							<image :src="item.cateMap.image || '/static/images/default_img.png'" class="tui-goods-img"
								mode="widthFix"></image>
							<view class="tui-goods-center">
								<view class="tui-goods-name">{{item.pay_subject}}</view>
								<view v-if="item.remark" class="tui-goods-attr">{{item.remark}}</view>
							</view>
							<view class="tui-price-right">
								<view>x1</view>
							</view>
						</view>
					</tui-list-cell>
				</block>
				<block v-else-if="item.is_errands==1">
					<tui-list-cell padding="0" @click="orderDetail(item.id)">
						<view class="tui-goods-item">
							<image :src="item.image || '/static/images/default_img.png'" class="tui-goods-img"
								mode="widthFix"></image>
							<view class="tui-goods-center">
								<view class="tui-goods-name">{{item.pay_subject}}</view>
								<view v-if="item.remark" class="tui-goods-attr">{{item.remark}}</view>
								<view v-if="item.takerealdistance" class="distance">
									<image src="/static/images/location_fill.png"
										style="margin-right: 10rpx;vertical-align: middle;width:30rpx; height: 30rpx;">
									</image>取件：{{item.takerealdistance}}km
								</view>
								<view v-if="item.realdistance" class="distance">
									<image src="/static/images/location_fill.png"
										style="margin-right: 10rpx;vertical-align: middle;width:30rpx; height: 30rpx;">
									</image>送货：{{item.realdistance}}km
								</view>
				
							</view>
							<view class="tui-price-right">
								<view>x1</view>
							</view>
						</view>
					</tui-list-cell>
				</block>
				<block v-else>
					<block v-for="(goodsitem,index) in item.goodsMap" :key="index">
						<tui-list-cell padding="0" @click="orderDetail(item.id)">
							<view class="tui-goods-item">
								<image :src="goodsitem.image" mode="widthFix" class="tui-goods-img"></image>
								<view class="tui-goods-center">
									<view class="tui-goods-name">{{goodsitem.name}}</view>
									<view v-if="goodsitem.label" class="tui-goods-attr">{{goodsitem.label}}</view>
								</view>
								<view class="tui-price-right">
									<view>￥{{goodsitem.price}}</view>
									<view>x{{goodsitem.quantity}}</view>
								</view>
							</view>
						</tui-list-cell>
					</block>
				</block>
				<tui-list-cell :hover="false" unlined>
					<view class="tui-goods-price">
						<view>
							<!--共4件商品--> 合计：
						</view>
						<view class="tui-size-24">￥</view>
						<view class="tui-price-large">{{item.total}}</view>
						<view class="tui-size-24"></view>
					</view>
				</tui-list-cell>
				<view class="tui-order-btn">
					<view class="tui-btn-ml">
						<tui-button @click="orderDetail(item.id)" type="black" plain width="152rpx" height="56rpx"
							:size="26" shape="circle">订单详情</tui-button>
					</view>
				</view>
			</view>
		</view>
		<!--加载loadding-->
		<tui-loadmore v-if="loadding" :index="3" type="red"></tui-loadmore>
		<tui-nomore v-if="!pullUpOn" backgroundColor="#fafafa"></tui-nomore>
		<!--加载loadding-->
		<tui-tabbar mo='tuanzhang' :current="current">
		</tui-tabbar>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				tabBar: [],
				statusType: [{
					"id": 0,
					"name": "全部",
				}, {
					"id": 2,
					"name": "未开始",
				}, {
					"id": 3,
					"name": "进行中",
				}, {
					"id": 5,
					"name": "已完成",
				}, {
					"id": 4,
					"name": "已取消",
				}],
				ptype: 2,
				currentType: 0,
				currentTab: 0,
				pageIndex: 1,
				loadding: false,
				pullUpOn: true,
				scrollTop: 0,
				orderList: [],
				where: {
					status: 0,
					ptype:0,
					keyword: '',
					page: 1,
					limit: 10,
				},
			}
		},
		onLoad: function(e) {
			let _this = this
			if(e.currentTab){
				_this.currentTab = e.currentTab;
			}
			_this.currentType = _this.statusType[_this.currentTab].id;
		},
		onShow: function(e) {
			this.getorderlist(true);
		},
		methods: {
			statusTap: function(e) {
				this.currentTab = e.index
				this.currentType = this.statusType[this.currentTab].id;
				this.loadend = false;
				this.loading = false;
				this.where.page = 1;
				this.getorderlist(true);
			},
			getorderlist: function(isPage) {
				var _this = this
				console.log(_this.loadend);
				
				if (_this.loading) return;
				
				if (isPage === true) {
					_this.where.page = 1;
					_this.orderList = [];
				}else{
					if (_this.loadend) return;
				}
				
				_this.where.samkey = (new Date()).valueOf();
				_this.where.ptype = _this.ptype;
				_this.where.status = _this.currentType;
				_this.where.currentTab = _this.currentTab;
				//console.log(_this.where);
				_this.$request.post('order.tuanzhangorder', _this.where).then(res => {
					if (res.errno == 0) {
						_this.orderList = _this.orderList.concat(res.data.data);
						_this.where.page = _this.where.page + 1
						_this.loadend = _this.orderList.length < _this.where.limit;
					}
					_this.loading = false;
				})
			},
			orderDetail: function(orderid) {
				var url = '/pagesA/my/admintuanzhang/orderDetail?id=' + orderid;
				uni.navigateTo({
					url: url
				})
			},
		},
		/**
		 * 页面相关事件处理函数--监听用户下拉动作
		 */
		onPullDownRefresh: function() {			
			this.where.page = 1;
			this.loadend = false;
			this.orderList = [];
			this.getorderlist();
			setTimeout(() => {
				uni.stopPullDownRefresh()
			}, 200);
		},
		/**
		 * 页面上拉触底事件的处理函数
		 */
		onReachBottom() {
			//只是测试效果，逻辑以实际数据为准
			this.loadding = true
			this.pullUpOn = true
			this.getorderlist();
			setTimeout(() => {
				this.loadding = false
				this.pullUpOn = false
			}, 1000)
		},
		onPageScroll(e) {
			this.scrollTop = e.scrollTop;
		}
	}
</script>

<style>
	.container {
		padding-bottom: env(safe-area-inset-bottom);
	}

	.tui-order-list {
		margin-top: 80rpx;
	}

	.tui-order-item {
		margin-top: 20rpx;
		border-radius: 10rpx;
		overflow: hidden;
	}

	.tui-goods-title {
		width: 100%;
		font-size: 28rpx;
		display: flex;
		align-items: center;
		justify-content: space-between;
	}

	.tui-order-status {
		color: #888;
		font-size: 26rpx;
	}

	.tui-goods-item {
		width: 100%;
		padding: 20rpx 30rpx;
		box-sizing: border-box;
		display: flex;
		justify-content: space-between;
	}

	.tui-goods-img {
		width: 180rpx;
		height: 180rpx;
		display: block;
		flex-shrink: 0;
	}

	.tui-goods-center {
		flex: 1;
		padding: 20rpx 8rpx;
		box-sizing: border-box;
	}

	.tui-goods-name {
		max-width: 310rpx;
		word-break: break-all;
		overflow: hidden;
		text-overflow: ellipsis;
		display: -webkit-box;
		-webkit-box-orient: vertical;
		-webkit-line-clamp: 2;
		font-size: 26rpx;
		line-height: 32rpx;
	}

	.tui-goods-attr {
		font-size: 22rpx;
		color: #888888;
		line-height: 32rpx;
		padding-top: 20rpx;
		word-break: break-all;
		overflow: hidden;
		text-overflow: ellipsis;
		display: -webkit-box;
		-webkit-box-orient: vertical;
		-webkit-line-clamp: 2;
	}

	.tui-price-right {
		text-align: right;
		font-size: 24rpx;
		color: #888888;
		line-height: 30rpx;
		padding-top: 20rpx;
	}

	.tui-color-red {
		color: #E41F19;
		padding-right: 30rpx;
	}

	.tui-goods-price {
		width: 100%;
		display: flex;
		align-items: flex-end;
		justify-content: flex-end;
		font-size: 24rpx;
	}

	.tui-size-24 {
		font-size: 24rpx;
		line-height: 24rpx;
	}

	.tui-price-large {
		font-size: 32rpx;
		line-height: 30rpx;
		font-weight: 500;
	}

	.tui-order-btn {
		width: 100%;
		display: flex;
		align-items: center;
		justify-content: flex-end;
		background: #fff;
		padding: 10rpx 30rpx 20rpx;
		box-sizing: border-box;
	}

	.tui-btn-ml {
		margin-left: 20rpx;
	}
</style>
