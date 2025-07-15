<template>
	<view class="container">
		<form>
			<view class="tui-box">
				<tui-list-cell :arrow="true" unlined :radius="true" @click="takechooseAddr">
					<view class="tui-address">
						<label class="serviceaddr">取件地址</label>
						<view v-if="takeAddressData.address">
							<view class="tui-userinfo">
								<text class="tui-name">{{takeAddressData.name}}</text>{{takeAddressData.telephone}}
							</view>
							<view class="tui-addr">
								<text>{{takeAddressData.address_default}}{{takeAddressData.address}}</text>
							</view>
						</view>
						<view class="tui-none-addr" v-else>
							<image src="/static/images/location_fill.png" class="tui-addr-img" mode="widthFix"></image>
							<text>请选择地址</text>
						</view>
					</view>
				</tui-list-cell>
				<tui-list-cell :arrow="true" unlined :radius="true" @click="curchooseAddr">
					<view class="tui-address">
						<label class="serviceaddr">收件地址</label>
						<view v-if="curAddressData.address">
							<view class="tui-userinfo">
								<text class="tui-name">{{curAddressData.name}}</text>{{curAddressData.telephone}}
							</view>
							<view class="tui-addr">
								<text>{{curAddressData.address_default}}{{curAddressData.address}}</text>
							</view>
						</view>
						<view class="tui-none-addr" v-else>
							<image src="/static/images/location_fill.png" class="tui-addr-img" mode="widthFix"></image>
							<text>请选择地址</text>
						</view>
					</view>
				</tui-list-cell>
				<tui-list-cell :hover="false" :lineLeft="false" padding="0">
					<view class="tui-remark-box tui-padding tui-flex">
						<view>路程</view>
						{{distance}}公里
					</view>
				</tui-list-cell>
				<tui-list-cell :hover="false" :lineLeft="false" padding="0">
					<view class="tui-remark-box tui-padding tui-flex">
						<view>跑腿费</view>
						{{amountTotle}}元
					</view>
				</tui-list-cell>
				<tui-list-cell :hover="false" :lineLeft="false" padding="0">
					<view class="tui-remark-box tui-padding tui-flex">
						<view>备注</view>
						<input type="text" @input="remarkInput" name="remark" class="tui-remark" placeholder="请输入您的要求"
							placeholder-class="tui-phcolor"></input>
					</view>
				</tui-list-cell>
			</view>
			<view class="tui-safe-area"></view>
			<view class="tui-tabbar">
				<view class="tui-flex-end tui-color-red tui-pr-20">
					<view class="tui-black">应付金额: </view>
					<view class="tui-size-26">￥</view>
					<view class="tui-price-large">{{amountTotle}}</view>
					<!--<view class="tui-size-26"></view>-->
				</view>
				<view class="tui-pr25">
					<button class="paybutt" @click="btnPay" :style="'background:'+ pagestyleconfig.appstylecolor">确认下单</button>
				</view>
			</view>
			<t-pay-way :show="show" :stylecolor="pagestyleconfig.appstylecolor" :amuont="amountTotle" @goPay="goPay" :paymethod="paymethod" @close="popupClose">
			</t-pay-way>
		</form>
	</view>
</template>

<script>
	import tPayWay from "@/components/views/t-pay-way/t-pay-way"
	import pay from '@/common/pay.js'
	export default {
		components: {
			tPayWay
		},
		data() {
			return {
				show: false,
				pagestyleconfig: [],
				distance: 0,
				amountTotle: 0,
				islogin: 1, //是否要需要登录，需要在onLoad加截 app.page.onLoad(this,e);
				servicetime: '',
				address_id: '',
				take_address_id: '',
				curAddressData: [],
				takeAddressData: [],
				total: 0,
				remark: "",
				selIndex: 0,
				payment: '0', //是否支持货到付款
				chargeset: '0', //是否开启服务费
				paymentType: "",
				deliverymode: '1',
				ptype: 2,
				service: 0,
				paymethod: {},
			}
		},
		onLoad: function(e) {
			let _this = this
			uni.setStorageSync('gotopage', '/pagesA/submitOrder/errandsOrder');
			this.sam.checktelephone().then(res => {});
			_this.$request.post('config', {
				mo: 'pagestyle'
			}).then(res => {
				if (res.errno == 0) {
					_this.pagestyleconfig = res.data
				}
			});
			_this.$request.get('Paymethod.list').then(res => {
				if (res.errno == 0) {
					_this.paymethod = res.data;
					_this.paymentType = res.data[0].default;
					//console.log(_this.paymentType);
				}
			})
		},
		onShow: function() {
			let _this = this
			_this.address_id = uni.getStorageSync('address_id');
			_this.take_address_id = uni.getStorageSync('take_address_id');

			if (_this.address_id) {
				_this.$request.get('address.getinfobyid', {
					samkey: (new Date()).valueOf(),
					id: _this.address_id
				}).then(res => {
					if (res.errno === 0) {
						_this.curAddressData = res.data;
					}
				})
			}

			if (_this.take_address_id) {
				_this.$request.get('address.getinfobyid', {
					samkey: (new Date()).valueOf(),
					id: _this.take_address_id
				}).then(res => {
					if (res.errno === 0) {
						_this.takeAddressData = res.data;
					}
				})
			}

			if (_this.address_id && _this.take_address_id) {
				_this.$request.post('order.taketotal', {
					samkey: (new Date()).valueOf(),
					address_id: _this.address_id,
					take_address_id: _this.take_address_id
				}).then(res => {
					if (res.errno === 0) {
						_this.distance = res.data.distance;
						_this.amountTotle = res.data.amountTotle;
					}
				})
			}

		},
		methods: {
			takechooseAddr() {
				uni.navigateTo({
					url: "/pagesA/my/address/address?atype=take"
				})
			},
			curchooseAddr() {
				uni.navigateTo({
					url: "/pagesA/my/address/address"
				})
			},
			btnPay() {
				var _this = this;
				if (!_this.takeAddressData.id) {
					uni.hideLoading();
					uni.showModal({
						title: '错误',
						content: '请添加取件地址！',
						showCancel: false
					})
					return;
				}
				if (!_this.curAddressData.id) {
					uni.hideLoading();
					uni.showModal({
						title: '错误',
						content: '请添加收件地址！',
						showCancel: false
					})
					return;
				}

				this.show = true
			},
			popupClose() {
				this.show = false
			},
			goPay(e) {
				if (e.paymentType) {
					this.paymentType = e.paymentType;
				}
				this.createOrder();
			},
			createOrder: function() {
				var _this = this;
				if (_this.remark == undefined) {
					_this.remark = '';
				}
				let postData = {
					otype: 2,
					remark: _this.remark,
					distance: _this.distance,
					amountTotle: _this.amountTotle,
					paymentType: _this.paymentType
				};

				if (_this.paymentType == 0) {
					uni.hideLoading();
					uni.showModal({
						title: '错误',
						content: '请选择支付方式！',
						showCancel: false
					})
					return;
				}

				postData.address_id = _this.curAddressData.id;
				postData.take_address_id = _this.takeAddressData.id;

				_this.ptype = 2;

				_this.$request.post('order.errandscreate', postData).then(res => {
					if (res.errno != 0) {
						uni.showModal({
							title: '错误',
							content: res.msg,
							showCancel: false
						})
						return;
					}

					const redirectUrl = "/pagesA/submitOrder/success?ptype=" + _this.ptype;
					if (res.data.payment_code == 'wx_pay') {
						pay.wxpay('order', res.data.pay_total, res.data.id, redirectUrl);
					} else if (res.data.payment_code == 'balance_pay') {
						_this.$request.post('order.pay', {
							orderid: res.data.id
						}).then(res => {
							if (res.errno == 0) {
								wx.showModal({
									title: '成功',
									content: '使用余额支付成功',
									showCancel: false,
									success: function(res) {
										wx.redirectTo({
											url: redirectUrl
										});
									}
								})
							} else {
								wx.showModal({
									title: '失败',
									content: res.msg,
									showCancel: false
								})
							}
						})
					} else if (res.data.payment_code == 'offline_pay') {
						_this.sam.navigateTo('/pagesA/submitOrder/offlinepayment?id=' + res.data.id);
					} else if (res.data.payment_code == 'delivery_pay') {
						_this.sam.navigateTo(redirectUrl);
					} else {
						_this.sam.navigateTo(redirectUrl);
					}

				})
			},
			remarkInput: function(e) {
				this.remark = e.target.value;
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
		padding-bottom: 98rpx;
	}

	.tui-item-box {
		width: 100%;
		display: flex;
		align-items: center;
	}

	.tui-list-cell_name {
		padding-left: 5rpx;
		display: flex;
		align-items: center;
		justify-content: center;
	}

	.tui-right {
		margin-left: auto;
		margin-right: 34rpx;
		font-size: 26rpx;
		color: #999;
	}

	.tui-box {
		padding: 20rpx 0 118rpx;
		box-sizing: border-box;
	}

	.tui-address {
		min-height: 80rpx;
		padding: 10rpx 0;
		box-sizing: border-box;
		position: relative;
	}

	.serviceaddr {
		color: #999999;
	}

	.tui-userinfo {
		font-size: 30rpx;
		font-weight: 500;
		line-height: 30rpx;
		padding-top: 10rpx;
		padding-bottom: 12rpx;
	}

	.tui-name {
		padding-right: 40rpx;
	}

	.tui-addr {
		font-size: 24rpx;
		word-break: break-all;
		padding-right: 25rpx;
	}

	.tui-addr-tag {
		padding: 5rpx 8rpx;
		flex-shrink: 0;
		background: #EB0909;
		color: #fff;
		display: inline-flex;
		align-items: center;
		justify-content: center;
		font-size: 25rpx;
		line-height: 25rpx;
		transform: scale(0.8);
		transform-origin: 0 center;
		border-radius: 6rpx;
	}

	.tui-top {
		margin-top: 20rpx;
		overflow: hidden;
	}

	.tui-goods-title {
		font-size: 28rpx;
		display: flex;
		align-items: center;
	}

	.tui-padding {
		box-sizing: border-box;
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

	.tui-flex {
		width: 100%;
		display: flex;
		align-items: center;
		justify-content: space-between;
		font-size: 26rpx;
	}

	.tui-total-flex {
		justify-content: flex-end;
	}

	.tui-color-red {
		color: #E41F19;
		padding-right: 30rpx;
	}

	.tui-balance {
		font-size: 28rpx;
		font-weight: 500;
	}

	.tui-black {
		color: #222;
		line-height: 30rpx;
	}

	.tui-gray {
		color: #888888;
		font-weight: 400;
	}

	.tui-light-dark {
		color: #666;
	}

	.tui-goods-price {
		display: flex;
		align-items: center;
		padding-top: 20rpx;
	}

	.tui-size-26 {
		font-size: 26rpx;
		line-height: 26rpx;
	}

	.tui-price-large {
		font-size: 34rpx;
		line-height: 32rpx;
		font-weight: 600;
	}

	.tui-flex-end {
		display: flex;
		align-items: flex-end;
		padding-right: 0;
	}

	.tui-phcolor {
		color: #B3B3B3;
		font-size: 26rpx;
	}

	/* #ifndef H5 */
	.tui-remark-box {
		padding: 22rpx 30rpx;
	}

	/* #endif */
	/* #ifdef H5 */
	.tui-remark-box {
		padding: 26rpx 30rpx;
	}

	/* #endif */

	.tui-remark {
		flex: 1;
		font-size: 26rpx;
		padding-left: 64rpx;
	}

	.tui-scale-small {
		transform: scale(0.8);
		transform-origin: 100% center;
	}

	.tui-scale-small .wx-switch-input {
		margin: 0 !important;
	}

	/* #ifdef H5 */
	>>>uni-switch .uni-switch-input {
		margin-right: 0 !important;
	}

	/* #endif */
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

	.tui-pr-30 {
		padding-right: 30rpx;
	}

	.tui-pr-20 {
		padding-right: 20rpx;
	}

	.tui-none-addr {
		height: 80rpx;
		padding-left: 5rpx;
		display: flex;
		align-items: center;
	}

	.tui-addr-img {
		width: 36rpx;
		height: 46rpx;
		display: block;
		margin-right: 15rpx;
	}


	.tui-pr25 {
		padding-right: 25rpx;
	}
	.paybutt {
		width: 380rpx;
		height: 70rpx;
		line-height: 70rpx;
		font-size: 28rpx;
		border-radius: 50rpx;
		color: #ffffff;
		align-items: center;
	}

	.tui-safe-area {
		height: 1rpx;
		padding-bottom: env(safe-area-inset-bottom);
	}

	.tui-pay-item__title {
		width: 100%;
		height: 90rpx;
		display: flex;
		align-items: center;
		justify-content: space-between;
		padding: 0 20rpx;
		box-sizing: border-box;
		font-size: 28rpx;
	}

	.tui-pay-amuont {
		color: #eb0909;
		font-weight: 500;
		font-size: 34rpx;
	}

	.tui-pay-item {
		width: 100%;
		height: 80rpx;
		display: flex;
		align-items: center;
		padding: 0 20rpx;
		box-sizing: border-box;
		font-size: 28rpx;
	}

	.tui-pay-logo {
		width: 48rpx;
		height: 48rpx;
		margin-right: 15rpx;
	}

	.tui-radio {
		margin-left: auto;
		transform: scale(0.8);
		transform-origin: 100% center;
	}

	.tui-btn-pay {
		width: 100%;
		padding: 68rpx 60rpx 50rpx;
		box-sizing: border-box;
	}

	.tui-recharge {
		color: #fc872d;
		margin-left: auto;
		padding: 12rpx 0;
	}

	.acea-row {
		font-size: 28rpx;
		padding-top: 30rpx;
		padding-left: 20rpx;
		height: 60rpx;
		background-color: #fff;
	}

	.acea-row label {
		padding: 10rpx;
	}
</style>
