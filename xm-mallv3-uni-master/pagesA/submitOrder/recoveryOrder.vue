<template>
	<view v-if="is_submitaudit!=1" class="container">
		<form>
			<view class="card-box">
				<view class="card-title">回收品类</view>
				<scroll-view class="navscroll" scroll-x="true">
					<view class="cate-content">
						<view class="cate-content-item" v-for="(item,index) in Recoverycategory" :key="index"
							:class="[category_id==item.id?'select':'']" @click="changeCategory(item.id)">
							<view class="item-image">
								<image :src="item.icon"></image>
							</view>
							<view class="item-text f-26 onelist-hidden">
								{{ item.title }}
							</view>
						</view>
					</view>
				</scroll-view>
			</view>

			<view class="card-box">
				<tui-list-cell :arrow="true" unlined :radius="true" padding="0" @click="chooseAddr">
					<view class="tui-address">
						<view v-if="curAddressData.address">
							<view class="tui-userinfo">
								<text class="tui-name">{{curAddressData.name}}</text>{{curAddressData.telephone}}
							</view>
							<view class="tui-addr">
								<image src="/static/images/location_fill.png" class="tui-addr-img" mode="widthFix">
								</image><text>{{curAddressData.address_default}}{{curAddressData.address}}</text>
							</view>
						</view>
						<view class="tui-none-addr" v-else>
							<image src="/static/images/location_fill.png" class="tui-addr-img" mode="widthFix"></image>
							<text>请选择上门地址</text>
						</view>
					</view>
				</tui-list-cell>
				<tui-list-cell :arrow="true" unlined :radius="true" @click="chooseTime">
					<view class="tui-address">
						<view v-if="servicetime">
							<view class="tui-addr">
								<text>期望上门时间:{{servicetime}}</text>
							</view>
						</view>
						<view class="tui-none-addr" v-else>
							<text>请选择期望服务时间</text>
						</view>
					</view>
				</tui-list-cell>
			</view>

			<view class="card-box">
				<view class="card-title">选择预估重量(结算价格以实际为准)</view>
				<view class="weight-content">
					<view class="weight-list">
						<view class="weight" v-for="(item,index) in Recoveryweight" :key="index"
							:class="[weight_id==item.id?'select':'']" @click="changeRecoveryweight(item.id)">
							{{ item.title }}
						</view>
					</view>
					<view class="weightcontent-bg">
						<view class="weightcontent">
							<view>预计收益</view>
							<view class="income">20元-30元</view>
							<view class="income">结算金额以实际为准</view>
						</view>
					</view>
				</view>
			</view>
			<view class="card-box">
				<tui-list-cell :hover="false" :lineLeft="false" padding="0">
					<view class="tui-remark-box tui-padding tui-flex">
						<view>备注</view>
						<input type="text" @input="remarkInput" name="remark" class="tui-remark" placeholder="请输入您的要求"
							placeholder-class="tui-phcolor"></input>
					</view>
				</tui-list-cell>
				<tui-list-cell :hover="false">
					<view class="tui-img__title">上传图片 (100公斤以上必填)</view>
					<view>
						<tui-upload :value="imgvalue" :limit="5" @complete="uploadresult" @remove="remove"></tui-upload>
					</view>
				</tui-list-cell>
			</view>
			<view class="card-box">
				<view class="card-title">注意事项</view>
				<view class="card-content">
					<view class="explain">
						<view class="explain-content">1、因回收成本原因，社区、写字楼单元楼价格面议</view>
						<view class="explain-content">2、小于10公斤暂不保证上门回收</view>
						<view class="explain-content">3、重量超过100公斤，需提供照片供回收员参考</view>
					</view>
				</view>
			</view>
			<view class="tui-safe-area"></view>
			<view class="tui-tabbar">
				<view class="totalPrice tui-pr-20">
					<view class="agreement">
						<radio style="transform:scale(0.7)" @click="setagreementagree" :checked="agreementagree == 1" />
						<label @click="showagreementAlert">确认下单将自动默认《上门服务条款》</label>
						<tui-modal :show="showagreement" custom>
							<view class="tui-modal-custom">
								<scroll-view :style="'height:'+(height-160)+'px'" scroll-y="true">
									<view class="tui-modal-custom-text">
										<text>{{agreement.content}}</text>
									</view>
								</scroll-view>
								<tui-button height="72rpx" :size="28" type="danger" shape="circle"
									@click="hideagreementAlert">同意</tui-button>
							</view>
						</tui-modal>
					</view>
				</view>
				<view class="paybuttbox tui-flex-end">
					<button class="paybutt" @click="createOrder"
						:style="'background:'+ pagestyleconfig.appstylecolor">确认提交</button>
				</view>
			</view>
		</form>
	</view>
</template>

<script>
	export default {
		components: {

		},
		data() {
			return {
				price: 0,
				is_submitaudit: 1,
				pagestyleconfig: [],
				Recoverycategory: [],
				Recoveryweight: [],
				category_id: 0,
				weight_id: 0,
				show: false,
				islogin: 1, //是否要需要登录，需要在onLoad加截 app.page.onLoad(this,e);
				servicetime: '',
				cat_name: '',
				curAddressData: [],
				total: 0,
				remark: "",
				selIndex: 0,
				chargeset: '0', //是否开启服务费
				ptype: 2,
				service: 0,
				orderimage: [],
				imgvalue: [] //初始化图片
			}
		},
		onLoad: function(e) {
			let _this = this
			uni.setStorageSync('gotopage', '/pagesA/submitOrder/recoveryOrder');
			this.sam.checktelephone().then(res => {});
			_this.$request.post('config', {
				mo: 'pagestyle'
			}).then(res => {
				if (res.errno == 0) {
					_this.pagestyleconfig = res.data
				}
			});
			this.$request.get('Recoverycategory.all', {
				showLoading: true
			}).then(res => {
				if (res.errno == 0) {
					_this.Recoverycategory = res.data;
					_this.price = res.data.price;
				}
			})
			this.$request.get('Recoveryweight.list', {
				showLoading: true
			}).then(res => {
				if (res.errno == 0) {
					_this.Recoveryweight = res.data;
					_this.weight_id = _this.Recoveryweight[0].id;
				}
			})
		},
		onShow: function() {
			let _this = this
			_this.$request.post('Config.audit', {
				samkey: (new Date()).valueOf()
			}).then(res => {
				_this.is_submitaudit = res.data.is_submitaudit;
			});
			_this.servicetime = uni.getStorageSync('servicetime');
			_this.cat_name = uni.getStorageSync('cat_name');
			_this.initShippingAddress();
		},
		methods: {
			changeCategory(e) {
				this.category_id = e;
			},
			changeRecoveryweight(e) {
				this.weight_id = e;
			},
			uploadresult: function(e) {
				console.log(e)
				this.orderimage = e.imgArr;
			},
			remove: function(e) {
				//移除图片
				console.log(e)
				let index = e.index
			},
			chooseAddr() {
				uni.navigateTo({
					url: "/pagesA/my/address/address"
				})
			},
			chooseTime() {
				uni.navigateTo({
					url: "/pagesA/submitOrder/timelist"
				})
			},
			btnPay() {
				if (this.total == 0) {
					this.createOrder();
				} else {
					this.show = true
				}
			},
			popupClose() {
				this.show = false
			},
			goPay(e) {

				this.createOrder();
			},
			createOrder: function() {
				var _this = this;
				if (_this.remark == undefined) {
					_this.remark = '';
				}

				if (!_this.category_id) {
					uni.hideLoading();
					uni.showModal({
						title: '错误',
						content: '请选择回收分类！',
						showCancel: false
					})
					return;
				}

				let postData = {
					category_id: _this.category_id,
					remark: _this.remark,
					total: _this.total,
					orderimage: _this.orderimage
				};

				if (!_this.servicetime) {
					uni.hideLoading();
					uni.showModal({
						title: '错误',
						content: '请选择期望上门时间！',
						showCancel: false
					})
					return;
				}
				postData.servicetime = _this.servicetime;

				postData.address_id = _this.curAddressData.id;
				_this.ptype = 2;


				_this.$request.post('recoveryorder.create', postData).then(res => {
					if (res.errno != 0) {
						uni.showModal({
							title: '错误',
							content: res.msg,
							showCancel: false
						})
						return;
					}
					const redirectUrl = "/pagesA/submitOrder/success?ptype=" + _this.ptype;
					_this.sam.navigateTo(redirectUrl);

				})
			},
			initShippingAddress: function() {
				var _this = this;
				_this.$request.get('address.default', {
					samkey: (new Date()).valueOf()
				}).then(res => {
					if (res.errno == 0) {
						_this.curAddressData = res.data;
					} else {
						_this.curAddressData = null;
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
		padding-bottom: 160rpx;
	}

	.card-box {
		background-color: #FFFFFF;
		margin: 30rpx;
		padding: 30rpx;
		width: calc(100% -60upx);
		border-radius: 25rpx;
	}

	.card-title {
		font-size: 28rpx;
		font-weight: 500;
		color: #e6b200;
	}

	.card-content {
		padding-top: 10rpx;
		color: #666;
		font-size: 28rpx;
		line-height: 40rpx;
	}

	/*分类开始*/
	.navscroll {
		z-index: 99999;
		height: 290rpx;
		display: flex;
		background-color: #fff;
	}

	.cate-content {
		margin-top: 30upx;
		display: flex;
	}

	.cate-content-item {
		flex: 1;
		text-align: center;
		border: solid 1px #1dd894;
		margin: 10rpx;
		border-radius: 10upx;
	}

	.cate-content .select {
		border: solid 1px #e6b200;
		border-radius: 10upx;
	}

	.cate-content .select .item-text {
		color: #fff;
		background-color: #e6b200;
	}

	.item-image {
		padding: 20rpx;
	}

	.item-image image {
		width: 100rpx;
		height: 100rpx;
		border-radius: 10upx;
	}

	.item-text {
		padding-top: 10rpx;
		padding-left: 10rpx;
		padding-right: 10rpx;
		height: 78rpx;
		color: #1dd894;
	}

	/*分类结速*/

	.weight-list {
		display: flex;
	}

	.weight {
		color: #6b6b6b;
		font-size: 28rpx;
		width: 103rpx;
		display: flex;
		text-align: center;
		align-items: center;
		justify-content: center;
		border-top-left-radius: 8rpx;
		border-top-right-radius: 8rpx;
		margin-right: 6rpx;
		padding: 8rpx;
		flex-shrink: 0;
		background-color: #efefef;
	}

	.weight-list .select {
		color: #fff;
		background-color: #1ad787;
	}

	.weight-content {
		padding-top: 20rpx;
	}

	.weightcontent-bg {
		border-bottom-left-radius: 20rpx;
		border-bottom-right-radius: 20rpx;
		padding: 30rpx;
		background-color: #1ad787;
	}

	.weightcontent {
		padding: 30rpx;
		border-radius: 20rpx;
		background-color: #fff;
	}

	.income {
		color: #f2ac00;
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
		margin: 30rpx;
		background-color: #FFFFFF;
		border-radius: 30rpx;
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
		display: flex;
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
		height: 160rpx;
		background: #fff;
		position: fixed;
		text-align: center;
		left: 0;
		bottom: 0;
		align-items: center;
		font-size: 26rpx;
		box-shadow: 0 0 1px rgba(0, 0, 0, .3);
		padding-bottom: env(safe-area-inset-bottom);
		z-index: 996;
	}

	.totalPrice {
		padding-top: 10rpx;
	}

	.agreement {
		color: #666666;
		padding-top: 6rpx;
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


	.paybuttbox {
		padding-top: 10rpx;
	}

	.paybutt {
		width: 60%;
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

	.tui-img__title {
		padding-bottom: 24rpx;
	}
</style>