<template>
	<view class="container" style="background-color: #ffffff;padding-bottom: 100rpx;">
		<view v-if="config.logo" class="login-logo">
			<image style="width: 300rpx;height: 300rpx;" :src="config.logo"></image>
		</view>
		<view class="tui-form">
			<view class="tui-btn-box">
				<tui-button :disabledGray="true" @click="gotologin('/pagesA/my/admintechnical/index')" height="86rpx"
					type="white" shape="circle">师傅登录</tui-button>
			</view>
			<view class="tui-btn-box">
				<tui-button :disabledGray="true" @click="gotologin('/pagesA/my/adminstore/index')" height="86rpx"
					type="white" shape="circle">商家登录</tui-button>
			</view>
			<view class="tui-btn-box">
				<tui-button :disabledGray="true" @click="gotologin('/pagesA/my/admintuanzhang/index')" height="86rpx"
					type="white" shape="circle">团长登录</tui-button>
			</view>
			<view class="tui-btn-box">
				<tui-button :disabledGray="true" @click="gotologin('/pagesA/my/adminoperatingcity/index')"
					height="86rpx" type="white" shape="circle">城市代理登录</tui-button>
			</view>
		</view>
	</view>
</template>

<script>
	export default {
		components: {},
		data() {
			return {
				ptype: "",
				pagestyleconfig: [],
				gotopage: '',
				config: {},
				disabled: false,
			};
		},
		onLoad(e) {
			var _this = this;
			_this.$request.post('config', {
				mo: 'pagestyle'
			}).then(res => {
				if (res.errno == 0) {
					_this.pagestyleconfig = res.data
				}
			});
			_this.$util.getUserInfo(function(userInfo) {
				console.log(userInfo);
			})

			_this.$request.post('config', {
				samkey: (new Date()).valueOf()
			}).then(res => {
				if (res.errno == 0) {
					if (res.data) {
						_this.config = res.data
					}
				}
			});
		},
		methods: {
			gotologin(url) {
				this.tui.href(url);
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
	};
</script>

<style lang="scss">
	page {
		background-color: #ffffff;
	}

	.container {
		.login-logo {
			margin-left: auto;
			margin-right: auto;
			text-align: center;
		}

		.bottom {
			height: 86rpx;
			border-radius: 43rpx;
		}

		.tui-form {
			padding-top: 50rpx;

			.tui-btn-box {
				width: 100%;
				padding: 0 $uni-spacing-row-lg;
				box-sizing: border-box;
				margin-top: 50rpx;
			}
		}

		.btn86 {
			height: 86rpx;
			line-height: 86rpx;
			border-radius: 98rpx;
			color: #fff;
		}
	}
</style>