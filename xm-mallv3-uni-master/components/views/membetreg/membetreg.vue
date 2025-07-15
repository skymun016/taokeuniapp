<template>
	<view>
		<tui-bottom-popup :show="show">
			<tui-list-cell :hover="false">
				<view class="tui-item__title">
					<view>填写您的昵称、头像</view>
					<view class="titletxt">提供具有辨识度的用户中心界面</view>
				</view>
				<view class="tui-icon-close" @tap="close">
					<tui-icon name="shut" :size="22" color="#BFBFBF"></tui-icon>
				</view>
			</tui-list-cell>
			<button class="avatar-wrapper" open-type="chooseAvatar" @chooseavatar="onChooseAvatar">
			<tui-list-cell :hover="false" :lineLeft="false">
				<view class="tui-cell-input">
					<label style="padding-right: 60rpx;">头像</label>
					<image :src="userpic || '/static/images/my/mine_def_touxiang_3x.png'" class="tui-avatar"></image>
					<label class="getuserpic">获取头像</label>
				</view>
			</tui-list-cell>
			</button>
			<tui-list-cell :hover="false" :lineLeft="false">
				<view class="tui-cell-input">
					<label style="padding-right: 60rpx;">昵称</label>
					<!-- #ifndef MP-WEIXIN -->
					<input type="text" v-model="nickname" placeholder="请输入昵称" placeholder-class="tui-phcolor" />
					<!-- #endif -->
					<!-- #ifdef MP-WEIXIN -->
					<input type="nickname" v-model="nickname" @blur="inputNickname" @input="inputNickname" placeholder="请输入昵称" placeholder-class="tui-phcolor" />
					<!-- #endif -->
				</view>
			</tui-list-cell>
			<view class="tui-btn-pay">
				<button class="gopaybutt" @click="bindSave" :style="'background:'+ stylecolor">保存</button>
			</view>
		</tui-bottom-popup>
	</view>
</template>

<script>
	export default {
		name: 'membetreg',
		props: {
			//支付方式
			paymethod: {
				type: Array,
				default() {
					return [];
				}
			},
			//控制显示
			show: {
				type: Boolean,
				default: false
			},
			amuont:{
				type: String,
				default: ''
			},
			stylecolor:{
				type: String,
				default: '#ff1e02'
			},
			page: {
				type: Number,
				default: 1
			}
		},
		data() {
			return {
				paymentType:"",
				nickname: '',
				userpic:''
			};
		},
		
		methods: {
			bindSave() {
				let _this = this;
				_this.$request.post('member.update', {
					samkey: (new Date()).valueOf(),
					nickname: _this.nickname,
					userpic: _this.userpic,
				}).then(res => {
					if (res.errno == 0) {
						this.$emit("loginnextpage")
					}
				});
			},
			inputNickname(e) {
				this.nickname = e.detail.value;
			},
			clearInput() {
				this.nickname = '';
			},
			close() {
				this.$emit("close", {})
			},
			paymentradioChange(e) {
				this.paymentType = e.target.value
			},
			onChooseAvatar(e) {
				let _this = this;
				this.userpic = e.detail.avatarUrl;
				this.$request.uploadFile(e.detail.avatarUrl).then(res => {
					//console.log(res);
					_this.userpic = res.url;
					_this.$request.post('member.update', {
						userpic: _this.userpic
					}).then(res => {});
				});
			},
			onChooseAvatar2() {
				let _this = this;
				uni.chooseImage({
					count: 1,
					sizeType: ['original', 'compressed'],
					sourceType: ['album', 'camera'],
					success: res => {
						//console.log(res);
						_this.userpic = res.tempFilePaths[0];
						this.$request.uploadFile(_this.userpic).then(res => {
							//console.log(res);
							_this.userpic = res.url;
							_this.$request.post('member.update', {
								userpic: _this.userpic
							}).then(res => {});
						});
					}
				});
			},
		}
	}
</script>

<style lang="scss">
	.tui-item__title {
		width: 100%;
		padding-bottom: 20rpx;
		align-items: center;
		justify-content: space-between;
		box-sizing: border-box;
		font-size: 34rpx;
		font-weight: bold;
	}
	.titletxt {
		color: #999999;
		padding-top: 10rpx;
		font-size: 34rpx;
		font-weight: normal;
	}
	.tui-icon-close {
		position: absolute;
		right: 30rpx;
		top: 60rpx;
		transform: translateY(-50%);
	}
	
	.getuserpic {
		color: #999;
		padding-top: 10rpx;
		padding-left: 30rpx;
	}
	.tui-cell-input {
		width: 100%;
		display: flex;
		align-items: center;
		padding-top: 18rpx;
		padding-bottom: $uni-spacing-col-base;
	
		input {
			flex: 1;
			padding-left: $uni-spacing-row-base;
		}
	}
	.avatar-wrapper {
		padding-left: 0px;
		padding-right: 0px;
		box-sizing: border-box;
		border-radius: 0px;
	}
	.tui-avatar {
		width: 100rpx;
		height: 100rpx;
		border-radius: 50%;
		display: block;
	}
	.tui-btn-pay {
		width: 100%;
		padding: 68rpx 60rpx 50rpx;
		box-sizing: border-box;
	}

	.gopaybutt {
		height: 88rpx;
		line-height: 88rpx;
		font-size: 28rpx;
		border-radius: 50rpx;
		color: #ffffff;
		align-items: center;
	}
</style>
