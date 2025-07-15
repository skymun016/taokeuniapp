<template>
	<view v-if="registerfield.length > 0">
		<form @submit="bindSave">
			<view class="tui-form">
				<view class="tui-view-input">
					<block v-for="(item, index) in registerfield" :key="index">
						<block v-if="item.inputtype == 'text'">
							<tui-list-cell :hover="false" padding="0">
								<view class="tui-line-cell">
									<view class="tui-title">{{ item.viewmingcheng }}</view>
									<input v-model="item.fieldsvalue" placeholder-class="tui-phcolor" class="tui-input"
										name="name" :placeholder="'请输入' + item.viewmingcheng" type="text" />
								</view>
							</tui-list-cell>
						</block>
						<block v-if="item.inputtype == 'textarea'">
							<tui-list-cell :hover="false" :lineLeft="false">
								<view class="tui-cell-input">
									<textarea class="weui-textarea" v-model="item.fieldsvalue"
										:placeholder="'请输入' + item.viewmingcheng" style="height: 3.3em" />
								</view>
							</tui-list-cell>
						</block>
						<block v-if="item.inputtype == 'pic'">
							<tui-list-cell :hover="true" :lineLeft="false" :arrow="true">
								<view @click="chooseImg(index)" class="tui-list-cell">
									<view>{{ item.viewmingcheng }}</view>
									<image :src="item.fieldsvalue || '/static/images/default_img.png'"
										class="tui-avatar">
									</image>
								</view>
							</tui-list-cell>
						</block>
						<block v-if="item.inputtype == 'pics'">
							<tui-list-cell :hover="false" :lineLeft="false">
								<view class="tui-img__title">{{ item.viewmingcheng }}</view>
								<view>
									<tui-upload :value="item.fieldsvalue" :limit="5" :params="index"
										@complete="uploadresult" @remove="remove">
									</tui-upload>
								</view>
							</tui-list-cell>
						</block>
						<block v-if="item.inputtype == 'lbs'">
							<tui-list-cell @tap="onChangePosition(index)" :arrow="true" padding="0">
								<view class="tui-line-cell">
									<view class="tui-title"><text
											class="tui-title-city-text">{{ item.viewmingcheng }}</text>
									</view>
									<input placeholder-class="tui-phcolor" class="tui-input tui-pr__30" disabled
										:placeholder="'请选择' + item.viewmingcheng" maxlength="50" type="text"
										v-model="item.fieldsvalue.region_name" />
								</view>
							</tui-list-cell>
						</block>
						<block v-if="item.inputtype == 'checkbox'">
							<tui-list-cell :hover="false" :lineLeft="false">
								<view class="uni-list">
									<button class="ptypebut" @click="selcate" :data-index="index"
										:data-val="checkitem.val"
										:class="item.fieldsvalue.includes(checkitem.val.toString()) ? 'selcss' : ''"
										type="default" v-for="checkitem in item.selectvaluearray"
										:key="checkitem.val">{{ checkitem.key
									}}</button>
								</view>
							</tui-list-cell>
						</block>
						<block v-if="item.inputtype === 'select'">
							<tui-list-cell :padding="0" :hover="true" :lineLeft="false" :arrow="true">
								<view class="tui-list-cell" style="padding: 0rpx;">
									<picker style="width: 100%;padding: 40rpx 30rpx 40rpx 30rpx;"
										@change="bindSelectChange" range-key="key"
										:data-selectval="item.selectvaluearray" :data-index="index" :data-sid="item.id"
										:range="item.selectvaluearray">
										<view v-if="item.fieldsvalue_name" class="weui-select">
											{{ item.fieldsvalue_name }}
										</view>
										<view v-else style="color: #888" class="weui-select">请选择{{ item.viewmingcheng }}
										</view>
									</picker>
								</view>
							</tui-list-cell>
						</block>
					</block>
				</view>
				<view class="tui-btn-box">
					<button form-type="submit" class="btn86"
						:style="'background:'+ pagestyleconfig.appstylecolor">{{submittxt}}</button>
				</view>
			</view>
		</form>
	</view>
</template>

<script>
	const util = require("@/utils/util.js")
	export default {
		name: 'diyfields',
		props: {
			ptype: {
				type: String,
				default: ''
			},
			orderid: {
				type: String,
				default: ''
			},
			update: {
				type: String,
				default: ''
			},
			submittxt: {
				type: String,
				default: '提交'
			},
			posturl: {
				type: String,
				default: 'registerfield.update'
			},
			gourl: {
				type: String,
				default: '/pages/login/success'
			},
			gotype: {
				type: String,
				default: 'reLaunch'
			}
		},
		data() {
			return {
				registerfield: {},
				pagestyleconfig: [],
				latitude: '',
				longitude: ''
			};
		},
		mounted() {
			let _this = this;
			_this.$request.get('registerfield.list', {
				update: _this.update,
				ptype: _this.ptype,
				orderid: _this.orderid,
				samkey: (new Date()).valueOf()
			}).then(res => {
				console.log(res);
				if (res.errno == 0) {
					if (res.is_submit == 1) {
						uni.reLaunch({
							url: "/pages/login/success?ptype=" + _this.ptype
						});
					} else {
						_this.registerfield = res.data;
						console.log(_this.registerfield);
					}

				}
			});
			_this.$request.post('config', {
				mo: 'pagestyle'
			}).then(res => {
				if (res.errno == 0) {
					_this.pagestyleconfig = res.data
				}
			});
		},
		watch: {},
		methods: {
			bindSave: function(e) {
				var _this = this;
				_this.$request.post(_this.posturl, {
					update: _this.update,
					orderid: _this.orderid,
					registerfield: JSON.stringify(_this.registerfield)
				}).then(res => {
					if (res.errno != 0) {
						uni.showToast({
							title: res.msg,
							icon: 'none'
						});
						return;
					} else {
						//console.log(res.errno);
						uni.showModal({
							title: '提示',
							content: res.msg,
							showCancel: false,
							//是否显示取消按钮 
							success: function(res) {
								if (res.cancel) { //点击取消,默认隐藏弹框
								} else {
									if (_this.gourl != 'no') {
										if (_this.gotype == 'reLaunch') {
											uni.reLaunch({
												url: _this.gourl + "?ptype=" + _this.ptype
											});
										} else {
											_this.sam.navigateTo(_this.gourl + "?ptype=" + _this
												.ptype);
												//console.log(_this.gourl);
										}
									}
								}
							}
						});
					}
				});
			},
			selcate: function(e) {
				var item = e.currentTarget.dataset.val.toString();
				var index = e.currentTarget.dataset.index;
				if (this.registerfield[index].fieldsvalue.includes(item)) {
					this.delcateids(index, item);
				} else {
					this.registerfield[index].fieldsvalue.push(item);
				}
			},
			bindSelectChange: function(e) {
				if (e.detail.value) {
					this.registerfield[e.currentTarget.dataset.index].fieldsvalue = this.registerfield[e.currentTarget
						.dataset.index].selectvaluearray[e.detail.value].val;
					this.registerfield[e.currentTarget.dataset.index].fieldsvalue_name = this.registerfield[e
						.currentTarget
						.dataset.index].selectvaluearray[e.detail.value].key;
				}

			},
			delcateids: function(index, val) {
				var sel = this.registerfield[index].fieldsvalue.findIndex(item => {
					if (item == val) {
						return true;
					}
				})
				// console.log(index)
				this.registerfield[index].fieldsvalue.splice(sel, 1)
			},
			chooseImg: function(key) {
				var _this = this;
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
						_this.Imgupload(key, tempFilePaths[0]);
					}
				});
			},
			Imgupload: function(key, path) {
				var _this = this;
				_this.$request.uploadFile(path).then(res => {
					_this.registerfield[key].fieldsvalue = res.url;
				});
			},
			onChangePosition: function(key) {
				const _this = this;
				console.log('sdfasdf');
				uni.chooseLocation({
					success(res) {
						_this.$request.post('geocoder.address2area', {
							address: res.address,
							latitude: res.latitude,
							longitude: res.longitude
						}).then(apires => {
							console.log(res);
							_this.registerfield[key].fieldsvalue = {
								region_name: res.name,
								address: res.address,
								province_name: apires.data.province_name,
								city_name: apires.data.city_name,
								district_name: apires.data.district_name || '',
								latitude: res.latitude,
								longitude: res.longitude,
							};
						});
					}
				});
			},
			uploadresult: function(e) {
				console.log(e)
				this.registerfield[e.params].fieldsvalue = e.imgArr;
			},
			remove: function(e) {
				//移除图片
				//console.log(e)
				let index = e.index
			}
		}
	}
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

	.tui-line-cell {
		width: 100%;
		padding: 24rpx 30rpx;
		box-sizing: border-box;
		display: flex;
		align-items: center;
	}

	.tui-title {
		color: #888888;
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

	.tui-avatar {
		width: 130rpx;
		height: 130rpx;
		display: block;
	}

	.tui-img__title {
		padding-bottom: 24rpx;
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
		padding-top: 5rpx;
		padding-bottom: 5rpx;
		margin-bottom: 10rpx;
	}

	.selcss {
		color: #fff;
		background: #007aff !important;
	}

	.checkboxbox {
		padding: 20rpx;
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


		}
	}

	.tui-btn-box {
		width: 100%;
		padding: 0 $uni-spacing-row-lg;
		box-sizing: border-box;
		margin-top: 20rpx;
	}

	.btn72 {
		width: 100%;
		height: 72rpx;
		line-height: 72rpx;
		border-radius: 98rpx;
		color: #fff;
	}

	.btn86 {
		width: 100%;
		height: 86rpx;
		line-height: 86rpx;
		border-radius: 98rpx;
		color: #fff;
	}
</style>