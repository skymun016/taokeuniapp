<template>
	<view class="container">
		<view class="tui-order-header">
			<image src="/static/images/mall/order/img_detail_bg.png" mode="widthFix" class="tui-img-bg"></image>
			<view class="tui-header-content">
				<view>
					<view class="tui-status-text">{{orderDetail.orderInfo.orderStatus.name}}</view>
				</view>
				<image v-if="orderDetail.orderInfo.order_status_id==1"
					src="/static/images/mall/order/img_order_payment3x.png" class="tui-status-img" mode="widthFix">
				</image>
				<image v-if="orderDetail.orderInfo.order_status_id==2"
					src="/static/images/mall/order/img_order_send3x.png" class="tui-status-img" mode="widthFix"></image>
				<image v-if="orderDetail.orderInfo.order_status_id==3"
					src="/static/images/mall/order/img_order_received3x.png" class="tui-status-img" mode="widthFix">
				</image>
				<image v-if="orderDetail.orderInfo.order_status_id==5"
					src="/static/images/mall/order/img_order_signed3x.png" class="tui-status-img" mode="widthFix">
				</image>
				<image v-if="orderDetail.orderInfo.order_status_id==4"
					src="/static/images/mall/order/img_order_closed3x.png" class="tui-status-img" mode="widthFix">
				</image>
			</view>
		</view>
		<tui-list-cell v-if="orderDetail.orderInfo.is_errands==1" unlined :hover="false">
			<view class="tui-flex-box">
				<image src="/static/images/mall/order/img_order_address3x.png" class="tui-icon-img"></image>
				<view class="tui-addr">
					<view class="tui-addr-text">取件地址</view>
					<view class="tui-addr-userinfo">联系人：{{orderDetail.orderInfo.take_address.name}}<text
							class="tui-addr-tel">{{orderDetail.orderInfo.take_address.telephone}}</text></view>
					<view class="tui-addr-text">
						地址：{{orderDetail.orderInfo.take_address.province_name}}{{orderDetail.orderInfo.take_address.city_name}}{{orderDetail.orderInfo.take_address.district_name}}{{orderDetail.orderInfo.take_address.address}}
					</view>
				</view>
			</view>
		</tui-list-cell>
		<tui-list-cell v-if="orderDetail.orderInfo.deliverymode==1 || orderDetail.orderInfo.deliverymode==3" unlined
			:hover="false">
			<view class="tui-flex-box">
				<image src="/static/images/mall/order/img_order_address3x.png" class="tui-icon-img"></image>
				<view class="tui-addr">
					<view v-if="orderDetail.orderInfo.is_errands==1" class="tui-addr-text">收件地址</view>
					<view class="tui-addr-userinfo">联系人：{{orderDetail.orderInfo.address.name}}<text
							class="tui-addr-tel">{{orderDetail.orderInfo.address.telephone}}</text></view>
					<view class="tui-addr-text">
						地址：{{orderDetail.orderInfo.address.alladdress}}

					</view>
					<view v-if="orderDetail.orderInfo.is_errands!=1" class="tui-addr-text">
						预约时间：{{orderDetail.orderInfo.ServiceTime}}</view>
					<view class="tui-Technical" @tap="toTel(orderDetail.orderInfo.Technical.tel)" v-if="orderDetail.orderInfo.Technical">接单{{lang.technical}}：{{orderDetail.orderInfo.Technical.title}}<text class="tui-addr-tel" style="color: #5454f3;">{{orderDetail.orderInfo.Technical.tel}}</text></view>
				</view>
			</view>
		</tui-list-cell>
		<tui-list-cell v-if="orderDetail.orderInfo.deliverymode==2" unlined :hover="false">
			<view class="tui-flex-box">
				<image src="/static/images/mall/order/img_order_address3x.png" class="tui-icon-img"></image>
				<view class="tui-addr">
					<view class="tui-addr-userinfo">门店：{{orderDetail.orderInfo.store.title}}<text
							class="tui-addr-tel">{{orderDetail.orderInfo.address.telephone}}</text></view>
					<view class="tui-addr-text" @tap="storeopenLocation">
						地址：{{orderDetail.orderInfo.store.province_name}}{{orderDetail.orderInfo.store.city_name}}{{orderDetail.orderInfo.store.district_name}}{{orderDetail.orderInfo.store.region_name}}
						<image src="/static/images/map.png" class="map-img"> </image><text
							style="color: #5454f3;">导航到这里</text>
					</view>
					<view class="tui-addr-text">电话：{{orderDetail.orderInfo.store.tel}}</view>
				</view>
			</view>
		</tui-list-cell>
		<view class="tui-order-item">
			<tui-list-cell :hover="false" :lineLeft="false">
				<view class="tui-goods-title">
					服务内容
				</view>
			</tui-list-cell>
			<block v-if="orderDetail.orderInfo.cat_id">
				<tui-list-cell padding="0">
					<view class="tui-goods-item">
						<image :src="orderDetail.orderInfo.cateMap.image || '/static/images/default_img.png'"
							class="tui-goods-img" mode="widthFix"></image>
						<view class="tui-goods-center">
							<view class="tui-goods-name">{{orderDetail.orderInfo.pay_subject}}</view>
						</view>
						<view class="tui-price-right">
							<view>x1</view>
						</view>
					</view>
				</tui-list-cell>
			</block>
			<block v-else>
				<block v-for="(item,index) in orderDetail.goods" :key="index">
					<tui-list-cell padding="0">
						<view class="tui-goods-item">
							<image :src="item.image" class="tui-goods-img" mode="widthFix"></image>
							<view class="tui-goods-center">
								<view class="tui-goods-name">{{item.name}}</view>
								<view v-if="index ==0 && orderDetail.orderInfo.timesmum>0" class="tui-goods-attr">次卡：{{orderDetail.orderInfo.timesmum}}次,剩余：{{orderDetail.orderInfo.remain}}次</view>
								<view v-if="item.label" class="tui-goods-attr">{{item.label}}</view>
							</view>
							<view class="tui-price-right">
								<view>￥{{item.price}}</view>
								<view>x{{item.quantity}}</view>
							</view>
						</view>
					</tui-list-cell>
				</block>
			</block>
			<tui-list-cell :hover="false" :lineLeft="false">
				<view class="tui-goods-title">
					订单备注
				</view>
			</tui-list-cell>
			<block v-if="orderDetail.orderInfo.remark">
				<tui-list-cell padding="0">
					<view class="tui-goods-item" style="color: #999;">
					  {{orderDetail.orderInfo.remark}}
					</view>
				</tui-list-cell>
			</block>
			<view class="tui-img__box"
				v-if="OrderImage && OrderImage.length>0">
				<block v-for="(src,index) in OrderImage" :key="index">
					<image @tap.stop="previewImage(index)"
						:class="{'tui-image':OrderImage.length===1}" :src="src"
						:mode="OrderImage.length===1?'widthFix':'aspectFill'"></image>
				</block>
			</view>
				<view>
					<diyfieldsview ptype="complete" :orderid="orderid"></diyfieldsview>
				</view>
			<view v-if="orderDetail.orderInfo.is_additional==1" class="tui-goods-info">
				<view class="tui-price-flex tui-size32 tui-pbtm20">
					<view class="tui-flex-shrink">预付金额</view>
					<view class="tui-goods-price">
						<view class="tui-size-24">￥</view>
						<view class="tui-price-large"> {{orderDetail.orderInfo.total}}</view>
					</view>
				</view>
				<view v-if="orderDetail.orderInfo.additional>0" class="tui-price-flex tui-size32 tui-pbtm20">
					<view class="tui-flex-shrink">尾款</view>
					<view class="tui-goods-price">
						<view class="tui-size-24">
							<text v-if="orderDetail.orderInfo.additional_pay_time>0">已支付</text>
							<text v-if="orderDetail.orderInfo.additional_pay_time==0">未支付</text>
							￥
						</view>
						<view class="tui-price-large"> {{orderDetail.orderInfo.additional}}</view>
					</view>
				</view>
			</view>
			<view v-else class="tui-goods-info">
				<view class="tui-price-flex tui-size32 tui-pbtm20">
					<view class="tui-flex-shrink">合计</view>
					<view class="tui-goods-price">
						<view class="tui-size-24">￥</view>
						<view class="tui-price-large"> {{orderDetail.orderInfo.total}}</view>
					</view>
				</view>
			</view>
			<view v-if="orderDetail.orderInfo.deliverymode!=4" class="tui-order-item">
				<tui-list-cell :hover="false" :lineLeft="false">
					<view class="tui-goods-title">
						核销
					</view>
				</tui-list-cell>
				<tui-list-cell padding="0">
					<view class="qrcodebox">
						<image :src="qrcode" style="width: 300rpx;height: 300rpx;"></image>
						<view style="padding: 10rpx;">核销二维码</view>
					</view>
				</tui-list-cell>
			</view>
		</view>

		<view class="tui-safe-area"></view>
		<view class="tui-tabbar tui-order-btn">
			<view v-if="orderDetail.orderInfo.order_status_id==2 || orderDetail.orderInfo.order_status_id==3" class="tui-btn-mr">
				<tui-button type="black" :plain="true" width="152rpx" height="56rpx" :size="26" shape="circle"
					@click="refund">申请退款</tui-button>
			</view>
			<view v-if="orderDetail.orderInfo.order_status_id==5" class="tui-btn-mr">
				<tui-button type="black" :plain="true" width="152rpx" height="56rpx" :size="26" shape="circle"
					@click="confirmBtnTap">确认验收</tui-button>
			</view>
			<view v-if="orderDetail.orderInfo.order_status_id==7 && orderDetail.orderInfo.is_comment==0"
				class="tui-btn-mr">
				<tui-button type="black" :plain="true" width="152rpx" height="56rpx" :size="26" shape="circle"
					@click="addEvaluate">评价</tui-button>
			</view>
			<view v-if="orderDetail.orderInfo.order_status_id==5" class="tui-btn-mr">
				<tui-button type="black" :plain="true" width="152rpx" height="56rpx" :size="26" shape="circle"
					@click="refund">申请售后</tui-button>
			</view>
			<view v-if="orderDetail.orderInfo.order_status_id==6" class="tui-btn-mr">
				<tui-button type="black" :plain="true" width="152rpx" height="56rpx" :size="26" shape="circle"
					@click="refundDetail">售后详情</tui-button>
			</view>
			<view
				v-if="orderDetail.orderInfo.payment_code=='offline_pay' && orderDetail.orderInfo.is_offline_pay<1 && orderDetail.orderInfo.order_status_id==1"
				class="tui-btn-mr">
				<tui-button type="danger" :plain="true" width="152rpx" height="56rpx" :size="26" shape="circle"
					@click="offlinepaymentTap(orderDetail.orderInfo.id)">支付凭证</tui-button>
			</view>
			<view v-if="orderDetail.orderInfo.payment_code=='wx_pay' && orderDetail.orderInfo.order_status_id==1"
				class="tui-btn-mr">
				<tui-button type="danger" :plain="true" width="152rpx" height="56rpx" :size="26" shape="circle"
					@click="toPayTap(orderDetail.orderInfo.total,orderDetail.orderInfo.id)">立即支付</tui-button>
			</view>
			<view v-if="orderDetail.orderInfo.additional>0 && orderDetail.orderInfo.additional_pay_time==0"
				class="tui-btn-mr">
				<tui-button type="danger" :plain="true" width="152rpx" height="56rpx" :size="26" shape="circle"
					@click="toadditionalPayTap(orderDetail.orderInfo.additional,orderDetail.orderInfo.id)">支付尾款
				</tui-button>
			</view>
			<view v-if="orderDetail.orderInfo.deliverymode==4 && orderDetail.orderInfo.order_status_id==3"
				class="tui-btn-mr">
				<tui-button type="danger" :plain="true" width="152rpx" height="56rpx" :size="26" shape="circle"
					@click="toim">立即咨询</tui-button>
			</view>
		</view>
	</view>
</template>
<script>
	import pay from '@/common/pay.js'
	import diyfieldsview from "@/components/views/diyfields/diyfieldsview"
	export default {
		components: {diyfieldsview},
		data() {
			return {
				lang: {},
				orderid: '',
				//1-待付款 2-付款成功 3-待收货 4-订单已完成 5-交易关闭
				status: 1,
				orderDetail: [],
				OrderImage:[],
				qrcode: '',
				is_show: 0,
			}
		},
		onLoad: function(options) {
			let _this = this
			_this.$request.get('Lang.getlang').then(res => {
				if (res.errno == 0) {
					_this.lang = res.data;
				}
			});
			_this.orderid = options.id;
			_this.detail();
		},
		methods: {
			detail() {
				let _this = this
				let orderid = _this.orderid;
				_this.$request.get('order.detail', {
					samkey: (new Date()).valueOf(),
					id: orderid
				}).then(function(res) {
					if (res.errno != 0) {
						uni.showModal({
							title: '错误',
							content: res.msg,
							showCancel: false
						})
						return;
					}
					_this.orderDetail = res.data;
					if(_this.orderDetail){
						if(_this.orderDetail.orderInfo){
							_this.OrderImage = _this.orderDetail.orderInfo.OrderImage
						}
					}
				});
				_this.$request.post('qrcode.yuyue', {
					orderid: orderid,
					is_hyaline: true,
					expireHours: 1
				}).then(function(res) {
					_this.qrcode = res.data;
				});
			},
			getAuthMsg() {
				return new Promise(resolve => {
					console.log('sssss')
					if (this.orderDetail.tmplIds.length > 0) {
						uni.requestSubscribeMessage({
							tmplIds: this.orderDetail.tmplIds,
							fail(res) {
								console.log(res);
							},

							complete() {
								resolve();
							}

						});

					} else {
						resolve();
					}
				});
			},
			toadditionalPayTap: function(total, orderid) {
				// #ifdef MP-WEIXIN
				if (this.orderDetail.tmplIds) {
					this.getAuthMsg()
				}
				//#endif

				uni.showModal({
					title: '提示',
					content: '需要支付￥' + total,
					confirmText: '支付',
					showCancel: true,
					success: function(res) {
						const redirectUrl = "/pagesA/my/myOrder/yuyueDetail?id=" + orderid;
						pay.wxpay('additional', total, orderid, redirectUrl);
					}
				})

			},
			toPayTap: function(total, orderid) {
				const redirectUrl = "/pagesA/my/myOrder/myOrder?ptype=" + this.orderDetail.orderInfo.ptype;
				pay.wxpay('order', total, orderid, redirectUrl);
			},
			storeopenLocation: function() {
				uni.openLocation({
					latitude: Number(this.orderDetail.orderInfo.store.latitude), //要去的纬度-地址
					longitude: Number(this.orderDetail.orderInfo.store.longitude), //要去的经度-地址
					address: this.orderDetail.orderInfo.store.region_name,
					success: function() {
						console.log('success');
					},
					fail: function() {
						console.log('fail');
					}
				})

			},
			toTel: function(tel) {
				console.log(tel);
				//电话
				uni.makePhoneCall({
					phoneNumber: tel
				});
			},
			openLocation: function() {
				uni.openLocation({
					latitude: Number(this.orderDetail.orderInfo.address.latitude), //要去的纬度-地址
					longitude: Number(this.orderDetail.orderInfo.address.longitude), //要去的经度-地址
					address: this.orderDetail.orderInfo.address.address,
					success: function() {
						console.log('success');
					},
					fail: function() {
						console.log('fail');
					}
				})

			},
			//线下付款处理
			offlinepaymentTap: function(orderid) {
				uni.navigateTo({
					url: '/pagesA/submitOrder/offlinepayment?id=' + orderid
				})
			},
			confirmBtnTap: function(e) {
				let _this = this;
				let orderid = this.orderDetail.orderInfo.id;
				uni.showModal({
					title: '确认验收？',
					content: '',
					success: function(res) {
						if (res.confirm) {
							_this.$request.post('order.delivery', {
								orderid: orderid
					 	}).then(res => {
					  	if (res.errno == 0) {
									_this.detail(orderid);
								}
					  });
						}
					}
				});
			},
			refund() {
				this.tui.href("/pagesA/my/myOrder/refund?id=" + this.orderDetail.orderInfo.id)
			},
			refundDetail() {
				this.tui.href("/pagesA/my/myOrder/refundDetail?id=" + this.orderDetail.orderInfo.id)
			},
			toim() {
				this.tui.href("/pages/im/h5?orderid=" + this.orderDetail.orderInfo.id)
			},
			addEvaluate() {
				this.tui.href('/pagesA/my/addEvaluate/addEvaluate?id=' + this.orderDetail.orderInfo.id)
			},
			previewImage(current) {
				let imgs = this.OrderImage
				console.log(imgs);
				uni.previewImage({
					current: current,
					urls: imgs
				})
			},
		},
		/**
		 * 页面相关事件处理函数--监听用户下拉动作
		 */
		onPullDownRefresh: function() {
			setTimeout(() => {
				uni.stopPullDownRefresh()
			}, 200);
		},
	}
</script>

<style>
	.container {
		padding-bottom: 118rpx;
	}

	.tui-order-header {
		width: 100%;
		height: 160rpx;
		position: relative;
		background-color: #EB0909;
	}

	.tui-img-bg {
		width: 100%;
		height: 160rpx;
	}

	.tui-header-content {
		width: 100%;
		height: 160rpx;
		position: absolute;
		z-index: 10;
		left: 0;
		top: 0;
		display: flex;
		align-items: center;
		justify-content: space-between;
		padding: 0 70rpx;
		box-sizing: border-box;
	}

	.tui-status-text {
		font-size: 34rpx;
		line-height: 34rpx;
		color: #FEFEFE;
	}

	.tui-reason {
		font-size: 24rpx;
		line-height: 24rpx;
		color: rgba(254, 254, 254, 0.75);
		padding-top: 15rpx;
		display: flex;
		align-items: center;
	}

	.tui-reason-text {
		padding-right: 12rpx;
	}

	.tui-status-img {
		width: 80rpx;
		height: 80rpx;
		display: block;
	}

	.tui-flex-box {
		width: 100%;
		display: flex;
		align-items: center;
	}

	.tui-icon-img {
		width: 44rpx;
		height: 44rpx;
		flex-shrink: 0;
	}

	.map-img {
		width: 50rpx;
		height: 50rpx;
		margin-left: 10rpx;
		margin-right: 10rpx;
		margin-top: -30rpx;
		margin-bottom: -10rpx;
	}

	.tui-addr {
		display: flex;
		flex-direction: column;
		justify-content: center;
		padding-left: 20rpx;
		box-sizing: border-box;
	}

	.tui-addr-userinfo {
		font-size: 30rpx;
		line-height: 30rpx;
		font-weight: bold;
	}
	.tui-Technical {
		padding-top: 16rpx;
		font-size: 30rpx;
		line-height: 30rpx;
		font-weight: bold;
	}
	.tui-addr-text {
		font-size: 24rpx;
		line-height: 32rpx;
		padding-top: 16rpx;
	}

	.tui-addr-tel {
		padding-left: 40rpx;
	}

	.tui-order-item {
		margin-top: 20rpx;
		border-radius: 10rpx;
		overflow: hidden;
	}

	.tui-goods-title {
		width: 100%;
		font-size: 28rpx;
		line-height: 28rpx;
		display: flex;
		align-items: center;
		justify-content: space-between;
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
	}

	.tui-goods-info {
		width: 100%;
		padding: 30rpx;
		box-sizing: border-box;
		background: #fff;
	}

	.tui-price-flex {
		display: flex;
		align-items: center;
		justify-content: space-between;
	}

	.tui-size24 {
		padding-bottom: 20rpx;
		font-size: 24rpx;
		line-height: 24rpx;
		color: #888;
	}

	.tui-size32 {
		font-size: 32rpx;
		line-height: 32rpx;
		font-weight: 500;
	}

	.tui-pbtm20 {
		padding-bottom: 20rpx;
	}

	.tui-flex-shrink {
		flex-shrink: 0;
	}

	.tui-primary-color {
		color: #EB0909;
	}

	.tui-order-info {
		margin-top: 20rpx;
	}

	.tui-order-title {
		position: relative;
		font-size: 28rpx;
		line-height: 28rpx;
		padding-left: 12rpx;
		box-sizing: border-box;
	}

	.tui-order-title::before {
		content: '';
		position: absolute;
		left: 0;
		top: 0;
		border-left: 4rpx solid #EB0909;
		height: 100%;
	}

	.tui-order-content {
		width: 100%;
		padding: 24rpx 30rpx;
		box-sizing: border-box;
		background: #fff;
		font-size: 24rpx;
		line-height: 30rpx;
	}

	.tui-order-flex {
		display: flex;
		padding-top: 18rpx;
	}

	.tui-order-flex:first-child {
		padding-top: 0
	}

	.tui-item-title {
		width: 132rpx;
		flex-shrink: 0;
	}

	.tui-item-content {
		color: #666;
		line-height: 32rpx;
	}

	.tui-safe-area {
		height: 1rpx;
		padding-bottom: env(safe-area-inset-bottom);
	}

	.tui-tabbar {
		width: 100%;
		height: 98rpx;
		background: #fff;
		position: fixed;
		left: 0;
		bottom: 0;
		display: flex;
		align-items: center;
		justify-content: flex-end;
		font-size: 26rpx;
		box-shadow: 0 0 1px rgba(0, 0, 0, .3);
		padding-bottom: env(safe-area-inset-bottom);
		z-index: 996;
	}

	.tui-btn-mr {
		margin-right: 30rpx;
	}

	.tui-contact {
		display: flex;
		align-items: center;
		justify-content: center;
		font-size: 28rpx;
	}

	.tui-contact image {
		width: 36rpx;
		height: 36rpx;
		margin-right: 16rpx;
	}

	.qrcodebox {
		color: #999;
		padding: 20rpx;
		text-align: center;
	}

	.tui-img__box {
		width: 100%;
		font-size: 0;
		padding-top: 4rpx;
	}

	.tui-img__box image {
		width: 200rpx;
		height: 200rpx;
		margin-right: 12rpx;
		margin-top: 12rpx;
	}

	.tui-image {
		width: 400rpx !important;
		height: auto;
	}
</style>
