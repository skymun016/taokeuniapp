<template>
	<view class="container">
		<tui-tabs v-if="module==version3" :top="0" :tabs="ptypeTabs" :isFixed="scrollTop>=0" :height="88"
			:currentTab="currentPtype" :sliderWidth="300" :sliderHeight="60" itemWidth="50%" bottom="50%" color="#888"
			selectedColor="#5677fc" :bold="true" sliderBgColor="#E5FAFF" @change="ptypechange">
		</tui-tabs>
		<tui-tabs :top="module==version3 ? 40 : 0" :tabs="statusType" :isFixed="scrollTop>=0" :currentTab="currentTab"
			selectedColor="#E41F19" sliderBgColor="#E41F19" @change="statusTap" itemWidth="20%"></tui-tabs>
		<!--选项卡逻辑自己实现即可，此处未做处理-->
		<view :style="'margin-top:'+ (module==version3 ? 160 : 80)+'rpx'" :class="{'tui-order-list':scrollTop>=0}">
			<view class="tui-order-item" v-for="(item,orderIndex) in orderList" :key="orderIndex">
				<tui-list-cell @click="orderDetail(item.id)" :hover="false" :lineLeft="false">
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
								<image :src="goodsitem.image" class="tui-goods-img" mode="widthFix"></image>
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
					<!--<view class="tui-btn-ml">
						<tui-button type="black" plain width="152rpx" height="56rpx" :size="26" shape="circle"
							@click="invoiceDetail">查看发票</tui-button>
					</view>-->
					<view v-if="item.order_status_id==1" class="tui-btn-ml">
						<tui-button @click="cancelOrderTap(item.id)" type="black" plain width="152rpx" height="56rpx"
							:size="26" shape="circle">取消订单</tui-button>
					</view>
					<view v-if="item.order_status_id==3 && item.ptype==1" class="tui-btn-ml">
						<tui-button @click="confirmBtnTap(item.id)" type="black" plain width="152rpx" height="56rpx"
							:size="26" shape="circle">确认收货</tui-button>
					</view>
					<view v-if="item.order_status_id==5 && item.ptype==2" class="tui-btn-ml">
						<tui-button @click="confirmBtnTap(item.id)" type="black" plain width="152rpx" height="56rpx"
							:size="26" shape="circle">确认验收</tui-button>
					</view>
					<view class="tui-btn-ml">
						<tui-button @click="orderDetail(item.id)" type="black" plain width="152rpx" height="56rpx"
							:size="26" shape="circle">订单详情</tui-button>
					</view>
					<view v-if="item.paymethod.code=='offline_pay' && item.is_offline_pay<1 && item.order_status_id==1"
						class="tui-btn-ml">
						<tui-button type="black" plain width="152rpx" height="56rpx" :size="26" shape="circle"
							@click="offlinepaymentTap(item.id)">支付凭证</tui-button>
					</view>
					<view v-if="item.paymethod.code=='wx_pay' && item.order_status_id==1" class="tui-btn-ml">
						<tui-button type="danger" plain width="152rpx" height="56rpx" :size="26" shape="circle"
							@click="toPayTap(item.total,item.id)">立即支付</tui-button>
					</view>
					<view v-if="item.order_status_id==5 && item.is_comment==0" class="tui-btn-ml">
						<tui-button type="black" plain width="152rpx" height="56rpx" :size="26" shape="circle"
							@click="addEvaluate(item.id)">评价</tui-button>
					</view>
					<!--<view class="tui-btn-ml">
						<tui-button type="danger" plain width="152rpx" height="56rpx" :size="26" shape="circle">再次购买
						</tui-button>
					</view>-->
				</view>
			</view>
		</view>
		<!--加载loadding-->
		<tui-loadmore v-if="loadding" :index="3" type="red"></tui-loadmore>
		<tui-nomore v-if="!pullUpOn" backgroundColor="#fafafa"></tui-nomore>
		<!--加载loadding-->
		<tui-tabbar v-if="from=='bottom'" :current="current">
		</tui-tabbar>
	</view>
</template>

<script>
	import pay from '@/common/pay.js'
	export default {
		data() {
			return {
				ptypeTabs: [{
					name: "服务订单"
				}, {
					name: "商品订单"
				}],
				current: '',
				from: '',
				tabBar: [],
				module: this.$module,
				version2: this.$version2,
				version3: this.$version3,
				statusType: [],
				ptype: 2,
				ispoints: '',
				currentPtype: 0,
				orderStatus: 0,
				currentTab: 0,
				pageIndex: 1,
				loadding: false,
				pullUpOn: true,
				scrollTop: 0,
				orderList: [],
				where: {
					status: 0,
					ptype: 0,
					keyword: '',
					page: 1,
					limit: 10,
				},
			}
		},
		onLoad: function(e) {
			let _this = this
			// #ifdef MP-WEIXIN
			this.current = "/" + this.__route__;
			// #endif
			//#ifdef H5
			this.current = this.$route.path;
			//#endif
			_this.from = e.from;
			if (e.currentTab) {
				_this.currentTab = e.currentTab;
			}
			if (e.ispoints) {
				_this.ispoints = e.ispoints;
			}
			if (e.ptype) {
				_this.ptype = e.ptype;
			}
			if (_this.ptype == 2) {
				_this.currentPtype = 0;
			} else {
				_this.currentPtype = 1;
			}
			//console.log(e);
			_this.getorderstatus();

		},
		onShow: function(e) {
			let _this = this
			_this.$util.getUserInfo(function(userInfo) {
				console.log(userInfo);
				_this.getorderlist(true);
			});
		},
		methods: {
			statusTap: function(e) {
				this.currentTab = e.index
				this.orderStatus = this.statusType[this.currentTab].id;
				this.getorderlist(true);
			},
			ptypechange: function(e) {
				this.currentPtype = e.index

				if (this.currentPtype == 0) {
					this.ptype = 2;
				} else {
					this.ptype = 1;
				}
				this.getorderstatus();
				this.getorderlist(true);
			},
			getorderstatus: function() {
				let _this = this
				_this.$request.get('orderstatus.listname', {
					ptype: _this.ptype,
					samkey: (new Date()).valueOf(),
				}).then(res => {
					if (res.errno == 0) {
						_this.statusType = res.data;
						_this.orderStatus = _this.statusType[_this.currentTab].id;
					}
				})
			},
			getorderlist: function(isPage) {
				var _this = this
				console.log(_this.loadend);

				if (_this.loading) return;

				if (isPage === true) {
					_this.where.page = 1;
					_this.orderList = [];
				} else {
					if (_this.loadend) return;
				}

				_this.where.samkey = (new Date()).valueOf();
				_this.where.ptype = _this.ptype;
				_this.where.status = _this.orderStatus;
				_this.where.currentTab = _this.currentTab;
				_this.where.ispoints = _this.ispoints;

				console.log(_this.where);
				_this.$request.post('order.myorder', _this.where).then(res => {
					if (res.errno == 0) {
						_this.orderList = _this.orderList.concat(res.data.data);
						_this.where.page = _this.where.page + 1
						_this.loadend = _this.orderList.length < _this.where.limit;
					}
					_this.loading = false;
				})
			},
			toPayTap: function(total, orderid) {
				const redirectUrl = "/pagesA/my/myOrder/myOrder?ptype=" + this.ptype + "&ispoints=" + this.ispoints;
				pay.wxpay('order', total, orderid, redirectUrl);
			},
			cancelOrderTap: function(orderid) {
				var _this = this;
				uni.showModal({
					title: '确定要取消该订单吗？',
					content: '',
					success: function(res) {
						if (res.confirm) {
							_this.$request.post('order.cancel', {
								orderid: orderid
							}).then(res => {
								if (res.errno == 0) {
									_this.getorderlist(true);
								}
							})
						}
					}
				})
			},
			confirmBtnTap: function(orderid) {
				let _this = this;
				uni.showModal({
					title: _this.module == _this.version3 ? '确认验收？' : '确认您已收到商品？',
					content: '',
					success: function(res) {
						if (res.confirm) {
							_this.$request.post('order.delivery', {
								orderid: orderid
							}).then(res => {
								if (res.errno == 0) {
									_this.getorderlist(true);
								}
							});
						}
					}
				});
			},
			orderDetail: function(orderid) {
				var url = '/pagesA/my/myOrder/orderDetail?id=' + orderid;
				if (this.ptype == 2) {
					url = '/pagesA/my/myOrder/yuyueDetail?id=' + orderid;
				}

				uni.navigateTo({
					url: url
				})
			},
			//线下付款处理
			offlinepaymentTap: function(orderid) {
				uni.navigateTo({
					url: '/pagesA/submitOrder/offlinepayment?id=' + orderid
				})
			},
			invoiceDetail() {
				this.tui.href('/pagesA/my/invoiceDetail/invoiceDetail')
			},
			addEvaluate(orderid) {
				this.tui.href('/pagesA/my/addEvaluate/addEvaluate?id=' + orderid)
			}
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

	.tui-order-list {}

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
