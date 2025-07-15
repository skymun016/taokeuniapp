<template>
	<view class="tui-addr-box">
		<form @submit="bindSave" :report-submit="true">
			<tui-list-cell :hover="false" padding="0">
				<view class="tui-line-cell">
					<view class="tui-title">姓名</view>
					<input placeholder-class="tui-phcolor" class="tui-input" name="name" :value="addressData.name" placeholder="请输入姓名"
						maxlength="15" type="text" />
				</view>
			</tui-list-cell>
			<tui-list-cell :hover="false" padding="0">
				<view class="tui-line-cell">
					<view class="tui-title">手机号码</view>
					<input placeholder-class="tui-phcolor" class="tui-input" name="telephone" :value="addressData.telephone" placeholder="请输入手机号码"
						maxlength="11" type="text" />
				</view>
			</tui-list-cell>
			<picker :value="value" mode="multiSelector" @change="picker" @columnchange="columnPicker" :range="multiArray">
				<tui-list-cell :arrow="true" padding="0">
					<view class="tui-line-cell">
						<view class="tui-title"><text class="tui-title-city-text">所在位置</text></view>
						<input placeholder-class="tui-phcolor" class="tui-input tui-pr__30" disabled name="city" placeholder="请选择所在位置" maxlength="50"
						 type="text" v-model="showRegionStr"/>
						 
						 
					</view>
				</tui-list-cell>
			</picker>
			<tui-list-cell :hover="false" padding="0">
				<view class="tui-line-cell">
					<view class="tui-title">详细地址</view>
					<input placeholder-class="tui-phcolor" class="tui-input" name="address" :value="addressData.address" placeholder="请输入详细的地址"
						maxlength="50" type="text" />
				</view>
			</tui-list-cell>
			<!--
			<tui-list-cell :hover="false" padding="0">
				<view class="tui-line-cell">
					<view class="tui-cell-title">地址类型</view>
					<view class="tui-addr-label">
						<text v-for="(item,index) in lists" :key="index" class="tui-label-item"
							:class="{'tui-label-active':index==1}">{{item}}</text>
					</view>
				</view>
			</tui-list-cell>
            -->
			<!-- 默认地址 -->
			<!--<tui-list-cell :hover="false" padding="0">
				<view class="tui-swipe-cell">
					<view>设为默认地址</view>
					<switch checked color="#19be6b" class="tui-switch-small" />
				</view>
			</tui-list-cell>-->
			
			<!-- 保存地址 -->
			<view class="tui-addr-save">
				<tui-button formType="submit" shadow type="danger" height="88rpx" shape="circle">保存地址</tui-button>
			</view>
			<view class="tui-del" v-if="addressData.length > 0">
				<tui-button shadow type="gray" @click="deleteAddress" height="88rpx" shape="circle">删除地址</tui-button>
			</view>
		</form>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				id: 0,
				addressData:[],
				lists: ["公司", "家", "学校", "其他"],
				selectList: [],
				multiArray: [], //picker数据
				pickerSelect: [0, 0, 0],
				showRegionStr: "",
				province:[],
				city:[],
				district:[],
				pickerRegionRange: [],
				telnull: '',
			}
		},
		onLoad(e) {
			const _this = this;
			  _this.$request.get('area.tree').then(res => {
				_this.selectList = res.data;
				_this.multiArray = [
					_this.toArr(_this.selectList),
					_this.toArr(_this.selectList[0].children),
					_this.toArr(_this.selectList[0].children[0].children)
				]
			});
			
			_this.$config.init(function() {
				_this.telnull = _this.$config.getConf("telnull");
			});
			
			if (e.id) { // 修改初始化数据库数据
				_this.$request.get('address.detail', {
					samkey: (new Date()).valueOf(),
					id: e.id
				}).then(res => {
					if (res.errno === 0) {
						_this.id = e.id;
						_this.addressData = res.data;
						_this.showRegionStr = res.data.provinceStr + res.data.cityStr + res.data.areaStr;
						_this.initRegionDB(res.data.provinceStr, res.data.cityStr, res.data.areaStr)
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
			picker: function(e) {
				let value = e.detail.value;
				if (this.selectList.length > 0) {
					this.province = this.selectList[value[0]]
					this.city = this.selectList[value[0]].children[value[1]]
					this.district = this.selectList[value[0]].children[value[1]].children[value[2]]
					// this.selectList[value[0]].children[value[1]].children[value[2]].value
					this.showRegionStr = this.province.text + " " + this.city.text + " " + this.district.text;
				}
			},
			toArr(object) {
				let arr = [];
				for (let i in object) {
					arr.push(object[i].text);
				}
				return arr;
			},
			columnPicker: function(e) {
				//第几列 下标从0开始
				let column = e.detail.column;
				//console.log(column);
				//第几行 下标从0开始
				let value = e.detail.value;
				if (column === 0) {
					this.multiArray = [
						this.multiArray[0],
						this.toArr(this.selectList[value].children),
						this.toArr(this.selectList[value].children[0].children)
					];
					this.pickerSelect = [value, 0, 0]
				} else if (column === 1) {
					this.multiArray = [
						this.multiArray[0],
						this.multiArray[1],
						this.toArr(this.selectList[this.pickerSelect[0]].children[value].children)
					];
					this.pickerSelect = [this.pickerSelect[0], value, 0]
				}
			},
			
			async initRegionDB(pname, cname, dname) {
				var _this = this;
				_this.showRegionStr = pname + cname + dname
				let province = undefined
				let city = undefined
				let district = undefined
				if (pname) {
					const index = _this.pickerRegionRange[0].findIndex(ele => {
						return ele.name == pname
					})
					//console.log('pindex', index)
					if (index >= 0) {
						_this.pickerSelect[0] = index
						province = _this.pickerRegionRange[0][index]
					}
				}
				if (!province) {
					return
				}

				const _cRes = await _this.$request.get('area.child', {
					pid: province.id
				});

				if (_cRes.errno === 0) {
					_this.pickerRegionRange[1] = _cRes.data
					if (cname) {
						const index = _this.pickerRegionRange[1].findIndex(ele => {
							return ele.name == cname
						})
						if (index >= 0) {
							_this.pickerSelect[1] = index
							city = _this.pickerRegionRange[1][index]
						}
					}
				}
				if (!city) {
					return
				}

				const _dRes = await _this.$request.get('area.child', {
					pid: city.id
				});
				if (_dRes.errno === 0) {
					_this.pickerRegionRange[2] = _dRes.data
					if (dname) {
						const index = _this.pickerRegionRange[2].findIndex(ele => {
							return ele.name == dname
						})
						if (index >= 0) {
							_this.pickerSelect[2] = index
							district = _this.pickerRegionRange[2][index]
						}
					}
				}
				_this.province = province;
				_this.city = city;
				_this.district = district;
			},

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
					var district_id = _this.addressData.district_id
					if( _this.district){
						if(_this.district.id){
							district_id = _this.district.id;
						}
					}
					apiResult = _this.$request.post('address.update', {
						id: _this.id,
						province_id: _this.province.id || _this.addressData.province_id,
						city_id: _this.city.id || _this.addressData.city_id,
						district_id: district_id,
						street: _this.sObject ? _this.sObject.id : '',
						name: name,
						address: address,
						telephone: telephone,
						isDefault: '1'
					})
				} else {
					apiResult = _this.$request.post('address.add', {
						province_id: _this.province.id,
						city_id: _this.city.id,
						district_id: _this.district ? _this.district.id : '',
						street: _this.sObject ? _this.sObject.id : '',
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
			//获取微信地址
			readFromWx: function() {
				const _this = this
				uni.chooseAddress({
					success: function(res) {
						//console.log(res)
						_this.initRegionDB(res.provinceName, res.cityName, res.countyName);
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
</style>
