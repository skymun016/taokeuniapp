<template>
	<view v-if="is_login==1" class="container">
		<view class="storebox">
			<view class="tui-mybg-box">
				<view class="tui-header-center">
					<image :src="detail.store_logo" class="tui-avatar"></image>
					<view class="tui-info">
						<view class="tui-nickname">
							{{detail.title}}
						</view>
						<view class="tui-explain">企业店</view>
					</view>
				</view>
			</view>
			<view class="tui-content-box">
				<view class="tui-box tui-assets-box">
					<view class="tui-order-list tui-assets-list">
						<view class="tui-order-item">
							<view class="tui-assets-num"><text>{{detail.total_income}}</text></view>
							<view class="tui-assets-text">总收入</view>
						</view>
						<view class="tui-order-item">
							<view class="tui-assets-num">
								<text>{{detail.income}}</text>
							</view>
							<view class="tui-assets-text">本月收入</view>
						</view>
						<view class="tui-order-item">
							<view class="tui-assets-num">
								<text>{{detail.income}}</text>
							</view>
							<view class="tui-assets-text">可提现</view>
						</view>
					</view>
				</view>
				<view v-if="module==version3" class="tui-box tui-order-box" style="margin-top: 20rpx;">
					<tui-list-cell :arrow="true" padding="0" unlined :lineLeft="false"
						@click="navigateTo('/pagesA/my/adminstore/yuyueOrder?ptype=2')">
						<view class="tui-cell-header">
							<view class="tui-cell-title">服务订单管理</view>
							<view class="tui-cell-sub">全部订单</view>
						</view>
					</tui-list-cell>
					<view class="tui-order-list">
						<view class="tui-order-item" @tap="navigateTo('/pagesA/my/publicOrder/order?identity=store')">
							<view class="tui-icon-box">
								<tui-icon name="clock" :size="30" color="#ff1e02"></tui-icon>
							</view>
							<view class="tui-order-text">接单广场</view>
						</view>
						<view class="tui-order-item"
							@tap="navigateTo('/pagesA/my/adminstore/yuyueOrder?currentTab=2&ptype=2')">
							<view class="tui-icon-box">
								<tui-icon name="signin" :size="30" color="#666666"></tui-icon>
							</view>
							<view class="tui-order-text">已接单</view>
						</view>
						<view class="tui-order-item"
							@tap="navigateTo('/pagesA/my/adminstore/yuyueOrder?currentTab=3&ptype=2')">
							<view class="tui-icon-box">
								<tui-icon name="feedback" :size="30" color="#666666"></tui-icon>
							</view>
							<view class="tui-order-text">服务中</view>
						</view>
						<view class="tui-order-item"
							@tap="navigateTo('/pagesA/my/adminstore/yuyueOrder?currentTab=5&ptype=2')">
							<view class="tui-icon-box">
								<tui-icon name="square-fill" :size="30" color="#666666"></tui-icon>
							</view>
							<view class="tui-order-text">已完成</view>
						</view>
					</view>
				</view>
				<view class="tui-box tui-order-box" style="margin-top: 20rpx;">
					<tui-list-cell :arrow="true" padding="0" unlined :lineLeft="false"
						@click="navigateTo('/pagesA/my/adminstore/order?ptype=1')">
						<view class="tui-cell-header">
							<view class="tui-cell-title">商品订单管理</view>
							<view class="tui-cell-sub">全部订单</view>
						</view>
					</tui-list-cell>
					<view class="tui-order-list">
						<view class="tui-order-item"
							@tap="navigateTo('/pagesA/my/adminstore/order?currentTab=1&ptype=1')">
							<view class="tui-icon-box">
								<tui-icon name="signin" :size="30" color="#666666"></tui-icon>
							</view>
							<view class="tui-order-text">待付款</view>
						</view>
						<view class="tui-order-item"
							@tap="navigateTo('/pagesA/my/adminstore/order?currentTab=2&ptype=1')">
							<view class="tui-icon-box">
								<tui-icon name="signin" :size="30" color="#666666"></tui-icon>
							</view>
							<view class="tui-order-text">待发货</view>
						</view>
						<view class="tui-order-item"
							@tap="navigateTo('/pagesA/my/adminstore/order?currentTab=3&ptype=1')">
							<view class="tui-icon-box">
								<tui-icon name="feedback" :size="30" color="#666666"></tui-icon>
							</view>
							<view class="tui-order-text">已发货</view>
						</view>
						<view class="tui-order-item"
							@tap="navigateTo('/pagesA/my/adminstore/order?currentTab=5&ptype=1')">
							<view class="tui-icon-box">
								<tui-icon name="square-fill" :size="30" color="#666666"></tui-icon>
							</view>
							<view class="tui-order-text">已完成</view>
						</view>
					</view>
				</view>
				<view class="tui-box tui-tool-box">
					<tui-list-view>
						<tui-list-cell v-if="module==version3"
							@tap="navigateTo('/pagesA/my/publicOrder/timescard?identity=store')" :arrow="true">
							<view class="tui-item-box">
								<tui-icon name="bankcard" :size="24" color="#5677fc"></tui-icon>
								<view class="tui-list-cell_name">次卡订单</view>
							</view>
						</tui-list-cell>
						<tui-list-cell @tap="qrcode" :arrow="true">
							<view class="tui-item-box">
								<tui-icon name="qrcode" :size="24" color="#5677fc"></tui-icon>
								<view class="tui-list-cell_name">店铺二维码</view>
							</view>
						</tui-list-cell>
						<tui-list-cell v-if="detail.is_submitaudit!=1"
							@tap="navigateTo('/pagesA/my/withdraw/withdraw?mo=store')" :arrow="true">
							<view class="tui-item-box">
								<tui-icon name="wealth-fill" :size="24" color="#5677fc"></tui-icon>
								<text class="tui-list-cell_name">申请提现</text>
							</view>
						</tui-list-cell>
						<tui-list-cell @tap="navigateTo('/pagesA/my/withdraw/withdrawlog?mo=store')" :arrow="true">
							<view class="tui-item-box">
								<tui-icon name="listview" :size="24" color="#5677fc"></tui-icon>
								<view class="tui-list-cell_name">提现记录</view>
							</view>
						</tui-list-cell>
						<tui-list-cell v-if="detail.is_submitaudit!=1"
							@tap="navigateTo('/pagesA/my/memberbankcard/memberbankcard')" :arrow="true">
							<view class="tui-item-box">
								<tui-icon name="bankcard-fill" :size="24" color="#5677fc"></tui-icon>
								<view class="tui-list-cell_name">提现帐号</view>
							</view>
						</tui-list-cell>
						<!-- #ifdef MP-WEIXIN -->
						<tui-list-cell @tap="scancode" :arrow="true">
							<view class="tui-item-box">
								<tui-icon name="sweep" :size="24" color="#5677fc"></tui-icon>
								<view class="tui-list-cell_name">扫码核销</view>
							</view>
						</tui-list-cell>
						<tui-list-cell @tap="navigateTo('/pagesA/my/userInfo/setmpopenid')" :arrow="true">
							<view class="tui-item-box">
								<tui-icon name="setup" :size="24" color="#5677fc"></tui-icon>
								<view class="tui-list-cell_name">绑定公众号通知</view>
							</view>
						</tui-list-cell>
						<!-- #endif -->
						<tui-list-cell @tap="navigateTo('/pagesA/my/adminstore/update')" :arrow="true">
							<view class="tui-item-box">
								<tui-icon name="setup" :size="24" color="#5677fc"></tui-icon>
								<view class="tui-list-cell_name">修改资料</view>
							</view>
						</tui-list-cell>
						<tui-list-cell @tap="navigateTo('/pages/login/logout?ptype=store')" :arrow="true">
							<view class="tui-item-box">
								<tui-icon name="setup" :size="24" color="#5677fc"></tui-icon>
								<view class="tui-list-cell_name">安全退出</view>
							</view>
						</tui-list-cell>
					</tui-list-view>
				</view>
			</view>
		</view>
		<!--=======二维码 start=======-->
		<tui-modal custom :show="modalShow" backgroundColor="transparent" padding="0" @cancel="hideModal">
			<view class="tui-poster__box" :style="{marginTop:height+'px'}">
				<image src="/static/images/mall/icon_popup_closed.png" class="tui-close__img" @tap.stop="hideModal">
				</image>
				<image :src="qrcodeImg" v-if="qrcodeImg" class="tui-poster__img"></image>
				<tui-button type="danger" width="460rpx" height="80rpx" shape="circle" @click="savePic">保存图片
				</tui-button>
				<view class="tui-share__tips">保存二维图片到手机相册后，分享到您的圈子</view>
			</view>
		</tui-modal>
		<!--=======二维码 end=======-->
		<tui-tabbar mo='store' :current="current">
		</tui-tabbar>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				module: this.$module,
				version2: this.$version2,
				version3: this.$version3,
				is_login: 0,
				modalShow: false,
				qrcodeImg: '',
				isLogin: false,
				top: 0, //标题图标距离顶部距离
				opacity: 0,
				scrollTop: 0.5,
				detail: []
			};
		},
		onLoad: function(options) {
			let _this = this;
			let obj = {};
			// #ifdef MP-WEIXIN
			obj = wx.getMenuButtonBoundingClientRect();
			// #endif
			// #ifdef MP-BAIDU
			obj = swan.getMenuButtonBoundingClientRect();
			// #endif
			// #ifdef MP-ALIPAY
			my.hideAddToDesktopMenu();
			// #endif
			uni.getSystemInfo({
				success: res => {
					this.width = obj.left || res.windowWidth;
					this.height = obj.top ? obj.top + obj.height + 8 : res.statusBarHeight + 44;
					this.top = obj.top ? obj.top + (obj.height - 32) / 2 : res.statusBarHeight + 6;
					this.scrollH = res.windowWidth * 0.6;
				}
			});
		},
		onShow() {
			let _this = this;
			_this.$request.get('adminstore.detail', {
				samkey: (new Date()).valueOf()
			}).then(res => {
				if (res.errno == 0 && res.is_login == 1) {
					_this.is_login = res.is_login;
					_this.detail = res.data;
				} else {
					uni.showModal({
						title: '提示',
						content: res.message || '请先登录！',
						showCancel: false,
						//是否显示取消按钮 
						success: function(res) {
							if (res.cancel) { //点击取消,默认隐藏弹框
							} else {
								uni.reLaunch({
									url: "/pages/login/userlogin?ptype=store",
								})
							}
						}
					});
				}

			});
		},
		methods: {
			navigateTo(url) {
				uni.navigateTo({
					url: url
				});

			},
			initNavigation(e) {
				this.opacity = e.opacity;
				this.top = e.top;
			},
			opacityChange(e) {
				this.opacity = e.opacity;
			},
			scancode: function() {
				var _this = this;
				// 允许从相机和相册扫码
				uni.scanCode({
					success: function(res) {
						if (res.path) {
							uni.navigateTo({
								url: '/' + res.path
							});
						}
						//console.log('条码类型：' + res.scanType);
						//console.log('条码内容：' + res.path);
					}
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
					sid: _this.detail.id,
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
		},
		onPageScroll(e) {
			this.scrollTop = e.scrollTop;
		},
		onPullDownRefresh() {
			setTimeout(() => {
				uni.stopPullDownRefresh();
			}, 200);
		},
		onReachBottom: function() {

		}
	};
</script>

<style>
	.storebox {
		padding-bottom: 80rpx;
	}

	.tui-set-box {
		display: flex;
		align-items: center;
		justify-content: space-between;
	}

	.tui-icon-box {
		position: relative;
	}

	.tui-icon-setup {
		margin-left: 8rpx;
	}

	.tui-badge {
		position: absolute;
		font-size: 24rpx;
		height: 32rpx;
		min-width: 20rpx;
		padding: 0 6rpx;
		border-radius: 40rpx;
		right: 10rpx;
		top: -5rpx;
		transform: scale(0.8) translateX(60%);
		transform-origin: center center;
		display: flex;
		align-items: center;
		justify-content: center;
		z-index: 10;
	}

	.tui-badge-red {
		background: #f74d54;
		color: #fff;
	}

	.tui-badge-white {
		background: #fff;
		color: #f74d54;
	}

	.tui-badge-dot {
		position: absolute;
		height: 12rpx;
		width: 12rpx;
		border-radius: 50%;
		right: -12rpx;
		top: 0;
		background: #f74d54;
	}

	.tui-mybg-box {
		width: 100%;
		height: 264rpx;
		position: relative;
		background-color: #e41f19;
	}

	.tui-my-bg {
		width: 100%;
		height: 464rpx;
		display: block;
	}

	.tui-header-center {
		position: absolute;
		width: 100%;
		height: 128rpx;
		left: 0;
		top: 18rpx;
		padding: 0 30rpx;
		box-sizing: border-box;
		display: flex;
		align-items: center;
	}

	.tui-avatar {
		flex-shrink: 0;
		width: 128rpx;
		height: 128rpx;
		border-radius: 50%;
		display: block;
	}

	.tui-info {
		width: 60%;
		padding-left: 30rpx;
	}

	.tui-nickname {
		font-size: 30rpx;
		font-weight: 500;
		color: #fff;
		display: flex;
		align-items: center;
	}

	.tui-img-vip {
		width: 56rpx;
		height: 24rpx;
		margin-left: 18rpx;
	}

	.tui-explain {
		width: 80%;
		font-size: 24rpx;
		font-weight: 400;
		color: #fff;
		opacity: 0.75;
		padding-top: 8rpx;
		white-space: nowrap;
		overflow: hidden;
		text-overflow: ellipsis;
	}

	.tui-btn-edit {
		flex-shrink: 0;
		padding-right: 22rpx;
	}

	.tui-header-btm {
		width: 100%;
		padding: 0 30rpx;
		box-sizing: border-box;
		position: absolute;
		left: 0;
		top: 280rpx;
		display: flex;
		align-items: center;
		justify-content: space-between;
		color: #fff;
	}

	.tui-btm-item {
		flex: 1;
		display: flex;
		flex-direction: column;
		align-items: center;
		justify-content: center;
	}

	.tui-btm-num {
		font-size: 32rpx;
		font-weight: 600;
		position: relative;
	}

	.tui-btm-text {
		font-size: 24rpx;
		opacity: 0.85;
		padding-top: 4rpx;
	}

	.tui-content-box {
		width: 100%;
		padding: 0 30rpx;
		box-sizing: border-box;
		position: relative;
		top: -100rpx;
		z-index: 10;
	}

	.tui-box {
		width: 100%;
		background: #fff;
		box-shadow: 0 3rpx 20rpx rgba(183, 183, 183, 0.1);
		border-radius: 30rpx;
		overflow: hidden;
	}

	.tui-order-box {
		height: 208rpx;
	}

	.tui-cell-header {
		width: 100%;
		height: 74rpx;
		padding: 0 26rpx;
		box-sizing: border-box;
		display: flex;
		align-items: center;
		justify-content: space-between;
	}

	.tui-cell-title {
		font-size: 30rpx;
		line-height: 30rpx;
		font-weight: 600;
		color: #333;
	}

	.tui-cell-sub {
		font-size: 26rpx;
		font-weight: 400;
		color: #999;
		padding-right: 28rpx;
	}

	.tui-order-list {
		width: 100%;
		height: 134rpx;
		padding: 0 30rpx;
		box-sizing: border-box;
		display: flex;
		align-items: center;
		justify-content: space-between;
	}

	.tui-order-item {
		flex: 1;
		display: flex;
		flex-direction: column;
		align-items: center;
	}

	.tui-order-text,
	.tui-tool-text {
		font-size: 26rpx;
		font-weight: 400;
		color: #666;
		padding-top: 4rpx;
	}

	.tui-tool-text {
		font-size: 24rpx;
	}

	.tui-order-icon {
		width: 56rpx;
		height: 56rpx;
		display: block;
	}

	.tui-assets-box {
		height: 118rpx;
		margin-top: 20rpx;
		padding-top: 30rpx;
	}

	.tui-assets-list {
		height: 84rpx;
	}

	.tui-assets-num {
		font-size: 38rpx;
		font-weight: 500;
		color: #333;
		font-weight: bold;
		position: relative;
	}

	.tui-assets-text {
		font-size: 24rpx;
		font-weight: 400;
		color: #666;
		padding-top: 6rpx;
	}

	.tui-tool-box {
		margin-top: 20rpx;
	}

	.tui-flex-wrap {
		flex-wrap: wrap;
		height: auto;
		padding-bottom: 30rpx;
	}

	.tui-tool-item {
		width: 25%;
		display: flex;
		align-items: center;
		justify-content: center;
		flex-direction: column;
		padding-top: 30rpx;
	}

	.tui-tool-icon {
		width: 64rpx;
		height: 64rpx;
		display: block;
	}

	.tui-badge-icon {
		width: 66rpx;
		height: 30rpx;
		position: absolute;
		right: 0;
		transform: translateX(88%);
		top: -15rpx;
	}

	/*为你推荐*/
	.tui-product-list {
		display: flex;
		justify-content: space-between;
		flex-direction: row;
		flex-wrap: wrap;
		box-sizing: border-box;
	}

	.tui-product-container {
		width: 100%;
		display: flex;
		flex-direction: row;
		flex-wrap: wrap;
	}

	.tui-pro-item {
		background: #fff;
		box-sizing: border-box;
		overflow: hidden;
		border-radius: 12rpx;
		width: 48%;
		margin-left: 1%;
		margin-right: 1%;
		margin-bottom: 2%;
	}

	.tui-pro-img {
		width: 100%;
		display: block;
	}

	.tui-pro-content {
		display: flex;
		flex-direction: column;
		justify-content: space-between;
		box-sizing: border-box;
		padding: 20rpx;
	}

	.tui-pro-tit {
		color: #2e2e2e;
		font-size: 26rpx;
		word-break: break-all;
		overflow: hidden;
		text-overflow: ellipsis;
		display: -webkit-box;
		-webkit-box-orient: vertical;
		-webkit-line-clamp: 2;
	}

	.tui-pro-price {
		padding-top: 18rpx;
	}

	.tui-sale-price {
		font-size: 34rpx;
		font-weight: 500;
		color: #e41f19;
	}

	.tui-factory-price {
		font-size: 24rpx;
		color: #a0a0a0;
		text-decoration: line-through;
		padding-left: 12rpx;
	}

	.tui-pro-pay {
		padding-top: 10rpx;
		font-size: 24rpx;
		color: #656565;
	}

	.tui-item-box {
		width: 100%;
		display: flex;
		align-items: center;
	}

	.tui-list-cell_name {
		padding-left: 20rpx;
		display: flex;
		align-items: center;
		justify-content: center;
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
</style>