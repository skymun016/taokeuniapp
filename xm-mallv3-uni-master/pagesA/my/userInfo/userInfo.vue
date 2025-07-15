<template>
	<view class="tui-userinfo-box">
		<button class="avatar-wrapper" open-type="chooseAvatar" @chooseavatar="onChooseAvatar">
			<tui-list-cell padding="0" :arrow="true">
				<view class="tui-list-cell">
					<view>头像</view>
					<image :src="memberinfo.userpic" class="tui-avatar"></image>
				</view>
			</tui-list-cell>
		</button>
		<tui-list-cell padding="0" :arrow="true" @click="nickname">
			<view class="tui-list-cell">
				<view>昵称</view>
				<view class="tui-content">{{memberinfo.nickname}}</view>
			</view>
		</tui-list-cell>
		<tui-list-cell padding="0" :arrow="true" @click="changeSex">
			<view class="tui-list-cell">
				<view>性别</view>
				<view class="tui-content">{{sex}}</view>
			</view>
		</tui-list-cell>
		<!--<picker mode="date" @change="changeDate">
			<tui-list-cell padding="0" :arrow="true" unlined>
				<view class="tui-list-cell">
					<view>出生日期</view>
					<view class="tui-content">{{date}}</view>
				</view>
			</tui-list-cell>
		</picker>-->
		<view v-if="gotopage" class="tui-butt">
			<button @click="ongotopage" class="btn86" :style="'background:'+ pagestyleconfig.appstylecolor">下一步</button>
		</view>

		<!-- #ifdef H5 -->
		<tui-modal :show="wxmpautomodal" @cancel="wxmpautomodalcancel" :custom="true" fadeIn>
			<view class="tui-modal-custom">
				<view class="tui-prompt-title">你可以自动获取微信头像昵称</view>
				<view class="tui-modal__btn">
					<view class="tui-box">
						<button :style="'background:'+ pagestyleconfig.appstylecolor" class="tui-btn-danger" @click="getmpuserinfo">自动获取微信头像昵称</button>
					</view>
					<view class="tui-box">
						<tui-button type="white" shape="circle" height="80rpx" :size="26"
							@click="wxmpautomodalcancel">手动填写</tui-button>
					</view>
				</view>
			</view>
		</tui-modal>
		<!-- #endif -->
	</view>

</template>

<script>
	export default {
		data() {
			return {
				sex: '男',
				date: '请选择',
				pagestyleconfig: [],
				memberinfo: [],
				gotopage: '',
				wxmpautomodal: false,
			}
		},
		onLoad(e) {
			var _this = this;
			if (e.gotopage) {
				this.gotopage = e.gotopage;
				uni.setStorageSync("gotopage", this.gotopage);
			}else{
				this.gotopage = uni.getStorageSync('gotopage')
			}
			_this.$request.post('config', {
				mo: 'pagestyle'
			}).then(res => {
				if (res.errno == 0) {
					_this.pagestyleconfig = res.data
				}
			});
		},
		onShow: function() {
			let _this = this;
			var isWeixin = 0;
			// #ifdef H5
			var ua = navigator.userAgent.toLowerCase();
			isWeixin = ua.indexOf('micromessenger') != -1;
			// #endif

			_this.$util.getUserInfo(function(userInfo) {
				//Console.log("adfs");
				_this.$request.get('member.detail', {
					samkey: (new Date()).valueOf()
				}).then(res => {
					if (res.errno == 0) {
						_this.memberinfo = res.data;
						_this.sex = res.data.sex;
						if (isWeixin) {
							if (_this.memberinfo.nickname == '微信用户' || _this.memberinfo.nickname ==
								'匿名用户') {
								_this.wxmpautomodal = true
							}
						}

					}
				});
			})

		},
       /*
		onUnload: function() {
			uni.navigateTo({
				url: '/pagesA/my/userInfo/index'
			});
		},
		*/
		methods: {
			getmpuserinfo: function() {
				let _this = this;
				_this.$util.getmpuserinfo().then(res => {
					uni.navigateTo({
						url: '/pagesA/my/userInfo/index'
					});
				});
			},
			wxmpautomodalcancel() {
				this.wxmpautomodal = false
			},
			ongotopage() {
				console.log(this.gotopage);
				this.sam.navigateTo(this.gotopage);
			},
			onChooseAvatar(e) {
				this.tui.href('/pagesA/my/cropper/cropper?src=' + e.detail.avatarUrl);
				console.log(e);
			},
			onChooseAvatar2() {
				uni.chooseImage({
					count: 1,
					sizeType: ['original', 'compressed'],
					sourceType: ['album', 'camera'],
					success: res => {
						console.log(res);
						const tempFilePaths = res.tempFilePaths[0];
						this.tui.href('/pagesA/my/cropper/cropper?src=' + tempFilePaths);
					}
				});
			},
			nickname() {
				this.tui.href("/pagesA/my/nickname/nickname")
			},
			changeSex() {
				let _this = this
				uni.showActionSheet({
					itemList: ['男', '女'],
					success(e) {
						_this.sex = ['男', '女'][e.tapIndex];
						_this.$request.post('member.update', {
							sex: _this.sex
						}).then(res => {
							if (res.errno == 0) {

							}
						});
					}
				})

			},
			changeDate(e) {
				console.log(e)
				this.date = e.detail.value
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
	.tui-userinfo-box {
		margin: 20rpx 0;
		color: #333;
	}

	.avatar-wrapper {
		padding-left: 0px;
		padding-right: 0px;
		box-sizing: border-box;
		border-radius: 0px;
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
		width: 100rpx;
		height: 100rpx;
		border-radius: 50%;
		display: block;
	}

	.tui-content {
		font-size: 26rpx;
		color: #666;
	}

	.tui-butt {
		padding: 100rpx 24rpx;
	}

	.tui-modal-custom {
		padding-top: 60rpx;
		padding-bottom: 50rpx;
		text-align: center;
	}

	.tui-prompt-title {
		padding-bottom: 20rpx;
		font-size: 34rpx;
	}

	.tui-modal__btn {
		align-items: center;
		justify-content: space-between;
	}

	.tui-box {
		padding: 15rpx 20rpx;
		box-sizing: border-box;
	}

	.tui-btn-danger {
		height: 80rpx;
		line-height: 80rpx;
		border-radius: 98rpx;
		color: #fff;
	}
	.btn86{
		height: 86rpx;
		line-height: 86rpx;
		border-radius: 98rpx;
		color: #fff;
	}
</style>
