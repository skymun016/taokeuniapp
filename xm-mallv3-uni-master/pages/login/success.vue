<template>
	<view class="container">
		<view class="tui-bg"></view>
		<view class="tui-content">
			<view class="tui-form">
				<image src="/static/images/mall/dd.png" class="tui-icon"></image>
				<view class="tui-title">资料审核中...</view>
				<view class="tui-sub-title">非常感谢您对我们的支持</view>
				<view v-if="ptype=='member'" class="tui-btn-box">
					<tui-button type="danger" height="70rpx" :size="28" shape="circle" @click="gotologin()">重新登录
					</tui-button>
				</view>
				<view v-if="ptype=='member'" class="tui-btn-box">
					<tui-button type="danger" height="70rpx" :size="28" shape="circle" @click="gotoreg()">修改资料
					</tui-button>
				</view>
				<view class="tui-btn-box">
					<!-- #ifdef MP-WEIXIN -->
					<tui-button v-if="configkefu.minionline==1 || configkefu.minionline==3" @click="toim"  type="danger" height="70rpx" :size="28" plain shape="circle">联系客服
					</tui-button>
					<tui-button v-else-if="configkefu.minionline==2" @click="toimwebview(configkefu.kefuurl)"  type="danger" height="70rpx" :size="28" plain shape="circle">联系客服
					</tui-button>					
					<tui-button v-else open-type="contact" type="danger" height="70rpx" :size="28" plain shape="circle">联系客服
					</tui-button>
					<!-- #endif -->
					<!-- #ifndef MP-WEIXIN -->
					<tui-button @click="toim"  type="danger" height="70rpx" :size="28" plain shape="circle">联系客服
					</tui-button>
					<!-- #endif -->
				</view>
				<view class="tui-btn-box">
					<tui-button type="danger" height="70rpx" :size="28" plain shape="circle" @click="go()">返回首页
					</tui-button>
				</view>
			</view>
		</view>
	</view>
</template>
<script>
	export default {
		data() {
			return {
				ptype: 'member',
				configkefu: {},
			}
		},
		onLoad: function(e) {
			let _this = this;
			this.ptype = e.ptype;
			_this.$config.init(function() {
				if(_this.$config.getConf("kefu")){
					_this.configkefu = _this.$config.getConf("kefu");
				}
			});
		},
		methods: {
			gotoreg() {
				uni.reLaunch({
					url: "/pages/login/reg?ptype=" + this.ptype
				});
			},
			gotologin() {
				uni.reLaunch({
					url: "/pages/login/userlogin?ptype=" + this.ptype
				});
			},
			go(page) {
				uni.reLaunch({
					url: "/pages/index/index"
				});
			},
			toimwebview(url) {
				this.tui.href("/pages/webview/h5?url=" + url)
			},
			toim() {
				if (this.configkefu.minionline == 2 && this.configkefu.kefuurl) {
					this.tui.href(this.configkefu.kefuurl);
				} else if (this.configkefu.minionline == 3) {
					var telstr = this.configkefu.kefutel;
					uni.makePhoneCall({
						phoneNumber: telstr
					});
				} else {
					this.tui.href("/pages/im/h5");
				}
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
	.tui-bg {
		width: 100%;
		height: 260rpx;
		background: linear-gradient(20deg, #E41F19, #F34B0B);
		border-bottom-left-radius: 42rpx;
	}

	.tui-content {
		padding: 0 35rpx;
		box-sizing: border-box;
	}

	.tui-form {
		background: #fff;
		box-shadow: 0 10rpx 14rpx 0 rgba(0, 0, 0, 0.08);
		border-radius: 10rpx;
		padding-bottom: 60rpx;
		margin-top: -160rpx;
		position: relative;
		z-index: 10;
		display: flex;
		align-items: center;
		flex-direction: column;
	}

	.tui-icon {
		width: 100rpx;
		height: 100rpx;
		display: block;
		margin-top: 60rpx;
	}

	.tui-title {
		font-size: 42rpx;
		line-height: 42rpx;
		padding-top: 28rpx;
	}

	.tui-sub-title {
		color: #666666;
		font-size: 28rpx;
		line-height: 28rpx;
		padding-top: 20rpx;
		padding-bottom: 20rpx;
	}

	.tui-btn-box {
		width: 380rpx;
		align-items: center;
		justify-content: space-between;
		padding-top: 20rpx;
	}

	.tui-tips {
		font-size: 26rpx;
		padding: 48rpx 90rpx;
		box-sizing: border-box;
		text-align: justify;
		line-height: 48rpx;
	}

	.tui-grey {
		color: #555;
		padding-bottom: 8rpx;
	}

	.tui-light-grey {
		color: #888;
		line-height: 40rpx;
	}
</style>