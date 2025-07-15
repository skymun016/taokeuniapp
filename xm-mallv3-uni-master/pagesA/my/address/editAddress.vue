<template>
	<view class="tui-addr-box">
		<form @submit="bindSave" :report-submit="true">
			<tui-list-cell :hover="false" padding="0">
				<view class="tui-line-cell">
					<view class="tui-title">姓名</view>
					<input placeholder-class="tui-phcolor" class="tui-input" name="name" :value="addressData.name"
						placeholder="请输入姓名" maxlength="15" type="text" />
				</view>
			</tui-list-cell>
			<tui-list-cell :hover="false" padding="0">
				<view class="tui-line-cell">
					<view class="tui-title">手机号码</view>
					<input placeholder-class="tui-phcolor" class="tui-input" name="telephone"
						:value="addressData.telephone" placeholder="请输入手机号码" maxlength="11" type="text" />
				</view>
			</tui-list-cell>
			<tui-list-cell @tap="onChangePosition" :arrow="true" padding="0">
				<view class="tui-line-cell">
					<view class="tui-title"><text class="tui-title-city-text">所在位置</text></view>
					<input placeholder-class="tui-phcolor" class="tui-input tui-pr__30" disabled placeholder="请选择所在位置"
						maxlength="50" type="text" v-model="showRegionStr" />
				</view>
			</tui-list-cell>
			<tui-list-cell :hover="false" padding="0">
				<view class="tui-line-cell">
					<view class="tui-title">详细地址</view>
					<input placeholder-class="tui-phcolor" class="tui-input" name="address" :value="addressData.address"
						placeholder="请输入详细的地址" maxlength="50" type="text" />
				</view>
			</tui-list-cell>
			<!-- 默认地址 -->
			<!--<tui-list-cell :hover="false" padding="0">
				<view class="tui-swipe-cell">
					<view>设为默认地址</view>
					<switch checked color="#19be6b" class="tui-switch-small" />
				</view>
			</tui-list-cell>-->

			<!-- 保存地址 -->
			<view class="tui-addr-save">
				<button form-type="submit" class="addressbutt" :style="'background:'+ pagestyleconfig.appstylecolor">保存地址</button>
			</view>
			<view class="tui-del" v-if="id > 0">
				<tui-button shadow type="white" @click="deleteAddress" height="88rpx" shape="circle">删除地址</tui-button>
			</view>
		</form>
	</view>
</template>

<script>
	const util = require("@/utils/util.js")
	export default {
		data() {
			return {
				id: 0,
				addressData: [],
				pagestyleconfig: [],
				lists: ["公司", "家", "学校", "其他"],
				pickerSelect: [0, 0, 0],
				showRegionStr: "",
				province: [],
				city: [],
				district: [],
				pickerRegionRange: [],
				is_bindingaddress: 0,
				telnull: '',
				latitude: '',
				longitude: '',
				region_name: '',
				province_name: '',
				city_name: '',
				district_name: '',
			}
		},
		onLoad(e) {
			const _this = this;
			_this.$config.init(function() {
				_this.telnull = _this.$config.getConf("telnull");
			});
			_this.$request.post('config', {
				mo: 'pagestyle'
			}).then(res => {
				if (res.errno == 0) {
					_this.pagestyleconfig = res.data
				}
			});
			_this.is_bindingaddress = e.is_bindingaddress;

			if (e.id) { // 修改初始化数据库数据
				_this.$request.get('address.detail', {
					samkey: (new Date()).valueOf(),
					id: e.id
				}).then(res => {
					if (res.errno === 0) {
						_this.id = e.id;
						_this.addressData = res.data;
						_this.region_name = _this.addressData.region_name;
						_this.province_name = _this.addressData.province_name;
						_this.city_name = _this.addressData.city_name;
						_this.district_name = _this.addressData.district_name;
						_this.showRegionStr = _this.province_name + _this.city_name + _this.district_name + _this
							.region_name;
						return;
					} else {
						uni.showModal({
							title: '提示',
							content: '无法获取地址数据',
							showCancel: false
						})
					}
				})
			}
		},
		methods: {
			bindSave: function(e) {
				var _this = this;
				var name = e.detail.value.name;
				var address = e.detail.value.address;
				var telephone = e.detail.value.telephone;
				if (name == "") {
					uni.showModal({
						title: '提示',
						content: '请填写联系人姓名',
						showCancel: false
					})
					return
				}
				if (_this.telnull != '1') {
					if (telephone == "") {
						uni.showModal({
							title: '提示',
							content: '请输入联系电话',
							showCancel: false
						})
						return
					}
				}

				if (JSON.stringify(_this.province) == "{}" || JSON.stringify(_this.city) == "{}") {
					uni.showModal({
						title: '提示',
						content: '请选择地区',
						showCancel: false
					})
					return
				}
				if (address == "") {
					uni.showModal({
						title: '提示',
						content: '请填写详细地址',
						showCancel: false
					})
					return
				}
				let apiResult
				if (_this.id) {

					apiResult = _this.$request.post('address.update', {
						id: _this.id,
						region_name: _this.region_name || _this.addressData.region_name,
						province_name: _this.province_name || _this.addressData.province_name,
						city_name: _this.city_name || _this.addressData.city_name,
						district_name: _this.district_name || _this.addressData.district_name,
						latitude: _this.latitude || _this.addressData.latitude,
						longitude: _this.longitude || _this.addressData.longitude,
						name: name,
						address: address,
						telephone: telephone,
						isDefault: '1'
					})
				} else {
					apiResult = _this.$request.post('address.add', {
						is_bindingaddress: _this.is_bindingaddress,
						region_name: _this.region_name,
						province_name: _this.province_name,
						city_name: _this.city_name,
						district_name: _this.district_name,
						latitude: _this.latitude,
						longitude: _this.longitude,
						name: name,
						address: address,
						telephone: telephone,
						isDefault: '1'
					})
				}

				apiResult.then(function(res) {
					if (res.errno != 0) {
						// 登录错误 
						uni.hideLoading();
						uni.showModal({
							title: '失败',
							content: res.msg,
							showCancel: false
						})
						return;
					}
					// 跳转到结算页面
					uni.navigateBack({})
				})
			},

			deleteAddress: function() {
				var _this = this;
				var id = _this.addressData.id;
				uni.showModal({
					title: '提示',
					content: '确定要删除该地址吗？',
					success: function(res) {
						if (res.confirm) {
							_this.$request.post('address.delete', {
								id: id
							}).then(function() {
								uni.navigateBack({})
							})

						} else {
							console.log('user cancel')
						}
					}
				})
			},
			onChangePosition: function() {
				const _this = this;
				uni.chooseLocation({
					success(res) {
						_this.latitude = res.latitude;
						_this.longitude = res.longitude;
						_this.region_name = res.name;
						_this.$request.post('geocoder.address2area', {
							address:res.address,
							latitude: res.latitude,
							longitude: res.longitude
						}).then(apires => {
							//console.log(apires.data);
							_this.province_name = apires.data.province_name;
							_this.city_name = apires.data.city_name;
							_this.district_name = apires.data.district_name || '';
							_this.showRegionStr = _this.province_name + _this.city_name + _this.district_name +
								_this.region_name;
						});
					}
				});
			},
			//获取微信地址
			readFromWx: function() {
				const _this = this
				uni.chooseAddress({
					success: function(res) {
						//console.log(res)
						_this.showRegionStr = res.provinceName + res.cityName + res.countyName;
						_this.wxaddress = res;
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
	.tui-addr-box {
		padding: 20rpx 0;
	}

	.tui-line-cell {
		width: 100%;
		padding: 24rpx 30rpx;
		box-sizing: border-box;
		display: flex;
		align-items: center;
	}

	.tui-title {
		width: 180rpx;
		font-size: 28rpx;
		flex-shrink: 0;
	}

	.tui-title-city-text {
		width: 180rpx;
		height: 40rpx;
		display: block;
		line-height: 46rpx;
	}

	.tui-input {
		width: 500rpx;
	}

	.tui-pr__30 {
		padding-right: 30rpx;
	}

	.tui-input-city {
		flex: 1;
		height: 40rpx;
		font-size: 28rpx;
		padding-right: 30rpx;
	}

	.tui-phcolor {
		color: #ccc;
		font-size: 28rpx;
	}

	.tui-cell-title {
		font-size: 28rpx;
		flex-shrink: 0;
	}

	.tui-addr-label {
		margin-left: 70rpx;
	}

	.tui-label-item {
		width: 76rpx;
		height: 40rpx;
		border: 1rpx solid rgb(136, 136, 136);
		border-radius: 6rpx;
		font-size: 26rpx;
		text-align: center;
		line-height: 40rpx;
		margin-right: 20rpx;
		display: inline-block;
		transform: scale(0.92);
	}

	.tui-label-active {
		background: #E41F19;
		border-color: #E41F19;
		color: #fff;
	}

	.tui-swipe-cell {
		width: 100%;
		display: flex;
		justify-content: space-between;
		align-items: center;
		background: #fff;
		padding: 10rpx 24rpx;
		box-sizing: border-box;
		border-radius: 6rpx;
		overflow: hidden;
		font-size: 28rpx;
	}

	.tui-switch-small {
		transform: scale(0.8);
		transform-origin: 100% center;
	}

	/* #ifndef H5 */
	.tui-switch-small .wx-switch-input {
		margin: 0 !important;
	}

	/* #endif */

	/* #ifdef H5 */
	>>>uni-switch .uni-switch-input {
		margin-right: 0 !important;
	}

	/* #endif */

	.tui-addr-save {
		padding: 24rpx 30rpx;
		margin-top: 100rpx;
	}

	.tui-del {
		padding: 24rpx 30rpx;
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
