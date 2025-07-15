<template>
	<view class="tui-userinfo-box">
		<form @submit="bindSave" report-submit="true">
			<tui-list-cell padding="0" :arrow="true" @click="chooseImg">
				<view class="tui-list-cell">
					<view>上传支付凭证</view>
					<image :src="imageurl || '/static/images/default_img.png'" class="tui-avatar"></image>
				</view>
			</tui-list-cell>
			<textarea name="remark" class="tui-textarea" placeholder="备注" maxlength="200" auto-focus></textarea>
			<view class="tui-btn__box">
				<tui-button formType="submit" type="danger" height="88rpx" shape="circle">提交</tui-button>
			</view>
		</form>
		<view v-if="settings.payqrcode" class="qrcodebox">
			<image :show-menu-by-longpress="true" :src="settings.payqrcode" mode="widthFix" style="width: 360rpx;"></image>
			<view>请长按保存付款码到相册</view>
			<view>用微信扫码支付，并上传付款截图</view>
		</view>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				orderid: '',
				imageurl: '',
				paymethodinfo:{},
				settings:{}
			}
		},
		onLoad: function(e) {
			this.orderid = e.id;
			let _this = this
			_this.$request.post('Paymethod.getInfo',{code:'offline_pay'}).then(res => {
				if (res.errno == 0) {
					_this.paymethodinfo = res.data;
				if(_this.paymethodinfo.settings){
					_this.settings = _this.paymethodinfo.settings;
				}
					
				}
			})
		},
		onShow: function() {
			
		},
		methods: {
			chooseImg: function() {
				var that = this;
				uni.chooseImage({
					count: 1,
					// 默认9
					sizeType: ['original', 'compressed'],
					// 可以指定是原图还是压缩图，默认二者都有
					sourceType: ['album', 'camera'],
					// 可以指定来源是相册还是相机，默认二者都有
					success: function(res) {
						// 返回选定照片的本地文件路径列表，tempFilePath可以作为img标签的src属性显示图片
						var tempFilePaths = res.tempFilePaths;
						that.imageurl = tempFilePaths
						that.offlineupload(tempFilePaths[0]);
					}
				});
			},
			offlineupload: function(path) {
				var _this = this;
				_this.$request.uploadFile(path).then(res => {
					_this.imageurl = res.url;
				});
			},
			bindSave: function(e) {
				var _this = this;
				//上传线下付款信息,
				var imageurl = this.imageurl;
				var remark = e.detail.value.remark;
				_this.$request.post('Order.offlinepayment', {
					orderid: this.orderid,
					image: imageurl,
					remark: remark
				}).then(res => {
					if (res.errno != 0) {
						uni.showToast({
							title: res.message,
							icon: 'none'
						});
						return;
					} else {
						uni.showModal({
							title: '提示',
							content: res.message,
							showCancel: false,
							//是否显示取消按钮 
							success: function(res) {
								if (res.cancel) { //点击取消,默认隐藏弹框
								} else {
									uni.navigateTo({
										url: "/pagesA/submitOrder/success"
									});
								}
							}
						});
					}
				});
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
		padding: 20rpx 0;
	}

	.tui-userinfo-box {
		margin: 20rpx 0;
		color: #333;
	}

	.tui-list-cell {
		width: 100%;
		display: flex;
		align-items: center;
		justify-content: space-between;
		padding: 24rpx 60rpx 24rpx 30rpx;
		box-sizing: border-box;
		font-size: 30rpx;
	}

	.tui-pr30 {
		padding-right: 30rpx;
	}

	.tui-avatar {
		width: 200rpx;
		height: 200rpx;
		display: block;
	}

	.tui-content {
		font-size: 26rpx;
		color: #666;
	}

	.tui-textarea {
		width: 100%;
		height: 300rpx;
		font-size: 28rpx;
		padding: 20rpx 30rpx;
		box-sizing: border-box;
		background-color: #fff;
	}

	.tui-btn__box {
		width: 100%;
		padding: 60rpx;
		box-sizing: border-box;
	}
	.qrcodebox {
		padding: 20rpx;
		text-align: center;
	}
</style>
