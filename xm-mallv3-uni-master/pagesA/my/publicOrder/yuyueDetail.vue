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
					<view class="tui-addr-text">
						地址：{{orderDetail.orderInfo.take_address.province_name}}{{orderDetail.orderInfo.take_address.city_name}}{{orderDetail.orderInfo.take_address.district_name}}{{orderDetail.orderInfo.take_address.address}}
					</view>
				</view>
			</view>
		</tui-list-cell>
		<tui-list-cell v-if="orderDetail.orderInfo.deliverymode==1 || orderDetail.orderInfo.deliverymode==3" unlined :hover="false">
			<view class="tui-flex-box">
				<image src="/static/images/mall/order/img_order_address3x.png" class="tui-icon-img"></image>
				<view class="tui-addr">
					<view v-if="orderDetail.orderInfo.is_errands==1" class="tui-addr-text">收件地址</view>
					<view class="tui-addr-text">
						地址：{{orderDetail.orderInfo.address.alladdress}}
					</view>
					<view v-if="orderDetail.orderInfo.is_errands!=1" class="tui-addr-text">
						预约时间：{{orderDetail.orderInfo.ServiceTime}}</view>
				</view>
			</view>
		</tui-list-cell>
		<tui-list-cell v-if="orderDetail.orderInfo.deliverymode==2" unlined :hover="false">
			<view class="tui-flex-box">
				<image src="/static/images/mall/order/img_order_address3x.png" class="tui-icon-img"></image>
				<view class="tui-addr">
					<view class="tui-addr-userinfo">门店：{{orderDetail.orderInfo.store.title}}<text
							class="tui-addr-tel">{{orderDetail.orderInfo.address.telephone}}</text></view>
					<view class="tui-addr-text">
						地址：{{orderDetail.orderInfo.store.province_name}}{{orderDetail.orderInfo.store.city_name}}{{orderDetail.orderInfo.store.district_name}}{{orderDetail.orderInfo.store.region_name}}
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
							<view v-if="orderDetail.orderInfo.remark" class="tui-goods-attr">
								{{orderDetail.orderInfo.remark}}</view>
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
			<view v-if="orderDetail.orderInfo.remark" class="tui-goods-attr">
				{{orderDetail.orderInfo.remark}}
			</view>
			<view class="tui-img__box"
				v-if="OrderImage && OrderImage.length>0">
				<block v-for="(src,index) in OrderImage" :key="index">
					<image @tap.stop="previewImage(index)"
						:class="{'tui-image':OrderImage.length===1}" :src="src"
						:mode="OrderImage.length===1?'widthFix':'aspectFill'"></image>
				</block>
			</view>
			<view class="tui-goods-info">
				<view v-if="orderDetail.order_total.sub_total.value" class="tui-price-flex tui-size24">
					<view>总额</view>
					<view>￥{{orderDetail.order_total.sub_total.value}}</view>
				</view>
				<view class="tui-price-flex tui-size32 tui-pbtm20">
					<view class="tui-flex-shrink">合计</view>
					<view class="tui-goods-price">
						<view class="tui-size-24">￥</view>
						<view class="tui-price-large"> {{orderDetail.orderInfo.total}}</view>
					</view>
				</view>
			</view>
		</view>

		<view class="tui-safe-area"></view>
		<view class="tui-tabbar tui-order-btn">
			<view v-if="orderDetail.orderInfo.order_status_id==2" class="tui-btn-mr">
				<tui-button type="danger" :plain="true" width="152rpx" height="56rpx" :size="26" shape="circle"
					@click="staff(orderDetail.orderInfo.id)">接单</tui-button>
			</view>
		</view>
	</view>
</template>
<script>
	export default {
		components: {},
		data() {
			return {
				//1-待付款 2-付款成功 3-待收货 4-订单已完成 5-交易关闭
				identity: '',
				status: 1,
				orderDetail: [],
				OrderImage:[]
			}
		},
		onLoad: function(options) {
			let _this = this
			var orderid = options.id;
			_this.identity = options.identity

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
		},
		methods: {
			staff: function(orderid) {
				let _this = this
				let redirectUrl = "/pagesA/my/admintechnical/order?currentTab=1&ptype=2";
				if (_this.identity == 'store') {
					redirectUrl = '/pagesA/my/adminstore/order?currentTab=3&ptype=2';
				}
				_this.$request.post('order.staff', {
					samkey: (new Date()).valueOf(),
					identity: _this.identity,
					id: orderid
				}).then(function(res) {
					if (res.errno == 0) {
						uni.showModal({
							title: '提示',
							content: res.msg,
							showCancel: false,
							success: function(res) {
								if (res.confirm) {
									_this.sam.navigateTo(redirectUrl);
									//uni.navigateBack({});
								}
							}
						})
					}
				});
			},
			previewImage(current) {
				let imgs = this.OrderImage
				console.log(imgs);
				uni.previewImage({
					current: current,
					urls: imgs
				})
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
