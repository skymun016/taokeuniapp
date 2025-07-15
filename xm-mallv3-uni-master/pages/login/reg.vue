<template>
	<view class="container">
		<view v-if="is_submitaudit!=1">
			<view v-if="ptype" class="tui-form">
				<diyfields :ptype="ptype" submittxt="注册"></diyfields>
				<view class="tui-cell-text">
				注册代表同意
				<view class="tui-color-primary" hover-class="tui-opcity" :hover-stay-time="150" @tap="showagreementAlert">入驻协议及隐私政策</view>
				<tui-modal :show="showagreement" custom>
					<view class="tui-modal-custom">
						<scroll-view :style="'height:'+(height-160)+'px'" scroll-y="true">
							<view class="tui-modal-custom-text">
								<text v-if="agreement">{{agreement.content}}</text>
							</view>
						</scroll-view>
						<button @click="hideagreementAlert" class="btn72" :style="'background:'+ pagestyleconfig.appstylecolor">同意</button>
					</view>
				</tui-modal>
			</view>
			</view>
		</view>
		<view v-if="is_submitaudit==1" class="tui-form">
			<view class="tui-btn-box">
				<!-- #ifdef MP-WEIXIN -->
				<tui-button v-if="configkefu.minionline==1 || configkefu.minionline==3" @click="toim"  height="88rpx" shape="circle">联系客服
				</tui-button>
				<tui-button v-else-if="configkefu.minionline==2" @click="toimwebview(configkefu.kefuurl)"  height="88rpx" shape="circle">联系客服
				</tui-button>					
				<tui-button v-else open-type="contact" height="88rpx" shape="circle">联系客服
				</tui-button>
				<!-- #endif -->
				<!-- #ifndef MP-WEIXIN -->
				<tui-button @click="toim"  height="88rpx" shape="circle">联系客服
				</tui-button>
				<!-- #endif -->
			</view>
		</view>
	</view>
</template>

<script>
	import diyfields from "@/components/views/diyfields/diyfields"
	export default {
		components: {
			diyfields
		},
		computed: {},
		data() {
			return {
				config: {},
				pagestyleconfig: [],
				configkefu:{},
				is_submitaudit: 1,
				ptype:"",
				agreement: {},
				showagreement: false,
				width: 0,
				height: 0
			};
		},
		onLoad: function(e) {
			let _this = this;
			if (e.ptype) {
				_this.ptype = e.ptype;
			}
			_this.$config.init(function() {
				if(_this.$config.getConf("kefu")){
					_this.configkefu = _this.$config.getConf("kefu");
				}
			});
			_this.$request.post('config', {
				mo: 'pagestyle'
			}).then(res => {
				if (res.errno == 0) {
					_this.pagestyleconfig = res.data
				}
			});

			_this.$request.post('Agreement.index', {
				code: _this.ptype +'join'
			}).then(res => {
				if (res.errno == 0) {
					_this.agreement = res.data;
				}
			});
			uni.getSystemInfo({
				success: res => {
					this.width = res.windowWidth;
					this.height = res.windowHeight;
				}
			});
		},
		onShow() {
			let _this = this;
			// #ifdef MP-WEIXIN
			_this.$request.post('Config.audit', {
				samkey: (new Date()).valueOf()
			}).then(res => {
				_this.is_submitaudit = res.data.is_submitaudit;
			});
			// #endif
			
			// #ifndef MP-WEIXIN
			_this.is_submitaudit = 0 ;
			// #endif
			
			
		},
		methods: {
			showagreementAlert() {
				this.showagreement = true;
			},
			hideagreementAlert() {
				this.showagreement = false;
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
	};
</script>

<style lang="scss" scoped>
	.tui-list-cell {
		width: 100%;
		color: $uni-text-color;
		display: flex;
		align-items: center;
		justify-content: space-between;
		padding: 24rpx 60rpx 24rpx 30rpx;
		box-sizing: border-box;
		font-size: 30rpx;
	}

	.tui-avatar {
		width: 130rpx;
		height: 130rpx;
		display: block;
	}

	.uni-list {
		width: 100%;
		padding-top: 15rpx;
		padding-bottom: 20rpx;
		padding-left: 20rpx;
		padding-right: 20rpx;
		box-sizing: border-box;
		display: flex;
		align-items: center;
		flex-wrap: wrap;
	}

	.ptypebut {
		width: 40%;
		display: flex;
		align-items: center;
		justify-content: center;
		flex-direction: column;
		padding-top: 15rpx;
		padding-bottom: 15rpx;
		margin-bottom: 10rpx;
	}

	.checkboxbox {
		padding: 20rpx;
	}
	.btn72{
		height: 72rpx;
		line-height: 72rpx;
		border-radius: 98rpx;
		color: #fff;
	}
	.btn86{
		height: 86rpx;
		line-height: 86rpx;
		border-radius: 98rpx;
		color: #fff;
	}
	.container {
		backgroundColor: #fff;
		padding-bottom: 80rpx;

		.tui-page-title {
			width: 100%;
			font-size: 48rpx;
			font-weight: bold;
			color: $uni-text-color;
			line-height: 42rpx;
			padding: 110rpx 40rpx 40rpx 40rpx;
			box-sizing: border-box;
		}

		.tui-form {
			.tui-view-input {
				width: 100%;
				box-sizing: border-box;
				padding: 0 0rpx;

				.tui-cell-input {
					width: 100%;
					display: flex;
					align-items: center;
					padding-top: 48rpx;
					padding-bottom: $uni-spacing-col-base;

					input {
						flex: 1;
						padding-left: $uni-spacing-row-base;
					}

					.tui-icon-close {
						margin-left: auto;
					}

					.tui-btn-send {
						width: 156rpx;
						text-align: right;
						flex-shrink: 0;
						font-size: $uni-font-size-base;
						color: $uni-color-primary;
					}

					.tui-gray {
						color: $uni-text-color-placeholder;
					}

					.tui-textarea {
						width: 100%;
						height: 300rpx;
						font-size: 28rpx;
						padding: 20rpx 30rpx;
						box-sizing: border-box;
						background-color: #fff;
					}
				}
			}

			.tui-cell-text {
				width: 100%;
				padding: 40rpx $uni-spacing-row-lg;
				box-sizing: border-box;
				font-size: $uni-font-size-sm;
				color: $uni-text-color-grey;
				display: flex;
				align-items: center;

				.tui-color-primary {
					color: $uni-color-primary;
					padding-left: $uni-spacing-row-sm;
				}
			}

			.tui-btn-box {
				width: 100%;
				padding: 0 $uni-spacing-row-lg;
				box-sizing: border-box;
				margin-top: 20rpx;
			}
		}
	}
</style>
