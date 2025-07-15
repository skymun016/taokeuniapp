<template>
	<view class="container">
		<view class="title-box">
			<view>严重危害生命财产安全</view>
			<view>求助专属通道</view>
		</view>
		<view class="count">平台将为您：保留实时位置、通知平台联系人</view>
		<view class="but"><button @click="goforhelp">紧急求助</button></view>
		<view class="count">
			<view>生命财产安全受到侵害、严重威胁时使用</view>
			<view>有必要时，平台将为您报110</view>
		</view>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				latitude: "",
				longitude: ""
			}
		},
		onLoad() {

		},
		onShow: function(e) {
			var _this = this
			
			wx.authorize({
				scope: 'scope.userFuzzyLocation',
				success: res => {
					//console.log(res)
					wx.getFuzzyLocation({
					 type: 'wgs84',
					 success (res) {
					  _this.longitude = res.longitude;
					  _this.latitude = res.latitude;
					 }
					});
				},
				fail: res => {
					//console.log('失败：', res);
				}
			});

		},
		methods: {
			goforhelp: function(goforhelp) {
				var _this = this
				_this.$request.post('forhelp.add', {
					samkey: (new Date()).valueOf(),
					longitude: _this.longitude,
					latitude: _this.latitude
				}).then(res => {
					if (res.errno == 0) {
						uni.showModal({
							title: '提示',
							content: res.msg,
							showCancel: false,
							//是否显示取消按钮 
							success: function(res) {
								
							}
						});
					}
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
		onReachBottom() {}
	}
</script>

<style>
	page {
		background-color: #fab74e;
	}

	.container {
		color: #ffffff;
		text-align: center;
	}

	.title-box {
		padding-top: 100rpx;
		font-size: 50rpx;
		font-weight: bold;
	}

	.count {
		padding-top: 30rpx;
		font-size: 28rpx;
		line-height: 38rpx;
	}

	.but {
		padding-top: 100rpx;
	}

	.but button {
		background-color: #ffffff;
		color: #fab74e;
		border-radius: 150rpx;
		width: 300rpx;
		height: 300rpx;
		line-height: 300rpx;
	}
</style>
