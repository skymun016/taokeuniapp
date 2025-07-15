<template>
	<view class="tui-safe-area">
		<view class="tui-address">
			<block v-for="(item,index) in addressList" :key="index">
				<tui-list-cell padding="0">
					<view class="tui-address-flex">
						<view @tap="selectTap" :data-id="item.id" class="tui-address-left">
							<view class="tui-address-main">
								<view class="tui-address-name tui-ellipsis">{{item.name}}</view>
								<view class="tui-address-tel">{{item.telephone}}</view>
							</view>
							<view class="tui-address-detail">
								<view :style="'background:'+ pagestyleconfig.appstylecolor" class="tui-address-label" v-if="item.isDefault===1">默认</view>
								<!--<view class="tui-address-label">{{["公司","住宅","其它"][index]}}</view>-->
								<text>{{item.address_detail}}{{item.address}}</text>
							</view>
						</view>
						<view v-if="item.is_bindingaddress!=1" class="tui-address-imgbox" @tap="editAddess" :data-id="item.id">
							<image class="tui-address-img" src="/static/images/mall/my/icon_addr_edit.png" />
						</view>
					</view>
				</tui-list-cell>
			</block>
		</view>
		<!-- 新增地址 -->
		<view class="tui-address-new" v-if="is_bindingaddress==0 || addressList.length==0">
			<button class="addressbutt" @click="addAddess" :style="'background:'+ pagestyleconfig.appstylecolor">+ 新增地址</button>
		</view>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				addressList: [],
				pagestyleconfig: [],
				is_bindingaddress: 0,
				atype: '',
			}
		},
		onLoad: function(options) {
			let _this = this;
			this.atype = options.atype;
			_this.$request.post('config', {
				mo: 'pagestyle'
			}).then(res => {
				if (res.errno == 0) {
					_this.pagestyleconfig = res.data
				}
			});
		},
		onShow: function() {
			this.initShippingAddress();
		},
		methods: {
			selectTap: function(e) {
				var id = e.currentTarget.dataset.id;
				if (this.atype == 'take') {
					uni.setStorageSync('take_address_id', id);
					uni.navigateBack({});
				} else {
					uni.setStorageSync('address_id', id);
					this.$request.post('address.setdefault', {
						id: id
					}).then(res => {
						uni.navigateBack({});
					})
				}


			},
			addAddess: function() {
				wx.navigateTo({
					url: "/pagesA/my/address/editAddress?is_bindingaddress=" + this.is_bindingaddress
				})
			},
			editAddess: function(e) {
				wx.navigateTo({
					url: "/pagesA/my/address/editAddress?id=" + e.currentTarget.dataset.id
				})
			},
			initShippingAddress: function() {
				var _this = this;
				_this.$request.get('address.list', {
					samkey: (new Date()).valueOf()
				}).then(res => {
					if (res.errno == 0) {
						_this.addressList = res.data;
						_this.is_bindingaddress = res.is_bindingaddress;
					} else if (res.errno == 700) {
						_this.addressList = null;
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
	}
</script>

<style>
	.tui-address {
		width: 100%;
		padding-top: 20rpx;
		padding-bottom: 160rpx;
	}

	.tui-address-flex {
		display: flex;
		justify-content: space-between;
		align-items: center;
	}

	.tui-address-main {
		width: 600rpx;
		height: 70rpx;
		display: flex;
		font-size: 30rpx;
		line-height: 86rpx;
		padding-left: 30rpx;
	}

	.tui-address-name {
		width: 120rpx;
		height: 60rpx;
	}

	.tui-address-tel {
		margin-left: 12rpx;
	}

	.tui-address-detail {
		font-size: 24rpx;
		word-break: break-all;
		padding-bottom: 25rpx;
		padding-left: 25rpx;
		padding-right: 120rpx;
	}

	.tui-address-label {
		padding: 5rpx 8rpx;
		flex-shrink: 0;
		border-radius: 6rpx;
		color: #fff;
		display: inline-flex;
		align-items: center;
		justify-content: center;
		font-size: 25rpx;
		line-height: 25rpx;
		transform: scale(0.8);
		transform-origin: center center;
		margin-right: 6rpx;
	}

	.tui-address-imgbox {
		width: 80rpx;
		height: 100rpx;
		position: absolute;
		display: flex;
		justify-content: center;
		align-items: center;
		right: 10rpx;
	}

	.tui-address-img {
		width: 36rpx;
		height: 36rpx;
	}

	.tui-address-new {
		width: 100%;
		position: fixed;
		left: 0;
		bottom: 0;
		z-index: 999;
		padding: 20rpx 25rpx 30rpx;
		box-sizing: border-box;
		background: #fafafa;
	}

	.tui-safe-area {
		padding-bottom: env(safe-area-inset-bottom);
	}
	.addressbutt {
		height: 88rpx;
		line-height: 88rpx;
		font-size: 28rpx;
		border-radius: 50rpx;
		color: #ffffff;
		align-items: center;
	}
</style>