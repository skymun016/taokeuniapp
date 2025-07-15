<template>
	<view>
		<view class="tui-form">
			<view class="tui-view-input">
				<block v-for="(item,index) in registerfield" :key="index">
					<block v-if="item.inputtype == 'text' || item.inputtype == 'textarea'">
						<tui-list-cell :hover="false" padding="0">
							<view class="tui-line-cell">
								<view class="tui-title">{{item.viewmingcheng}}</view>
								<view v-if="item.fieldsvalue" class="tui-input">{{item.fieldsvalue}}</view>
							</view>
						</tui-list-cell>
					</block>
					<block v-if="item.inputtype == 'pics'">						
						<tui-list-cell :hover="false" :lineLeft="false">
							<view class="tui-list-cell-img">
								<view class="img-title">{{item.viewmingcheng}}</view>
								<image :src="item.fieldsvalue || '/static/images/default_img.png'" class="tui-avatar">
								</image>
							</view>
						</tui-list-cell>
					</block>
					<block v-if="item.inputtype == 'lbs'">
						<tui-list-cell padding="0">
							<view class="tui-line-cell">
								<view class="tui-title"><text class="tui-title-city-text">{{item.viewmingcheng}}</text>
								</view>
								<view class="tui-input tui-pr__30">{{item.fieldsvalue.region_name}}</view>
							</view>
						</tui-list-cell>
					</block>
					<block v-if="item.inputtype == 'checkbox'">
						<tui-list-cell :hover="false" :lineLeft="false">
							<view class="uni-list">
								<block v-for="checkitem in item.selectvaluearray" :key="checkitem.val">
									<tui-tag v-if="item.fieldsvalue.includes(checkitem.val.toString())" plain
										size="24rpx" type="red" margin="8rpx 12rpx" padding="8rpx 12rpx">
										{{checkitem.key}}</tui-tag>
								</block>
							</view>
						</tui-list-cell>
					</block>
					<block v-if="item.inputtype === 'select'">
						<tui-list-cell :padding="0" :hover="true" :lineLeft="false" :arrow="true">
							<view class="tui-list-cell" style="padding: 0rpx;">
								<view v-if="item.fieldsvalue_name" class="weui-select">
									{{item.fieldsvalue_name}}
								</view>
							</view>
						</tui-list-cell>
					</block>
				</block>
			</view>
		</view>
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
			sid: {
				type: String,
				default: ''
			},
			technicalid: {
				type: String,
				default: ''
			},
			update: {
				type: String,
				default: ''
			},
			geturl: {
				type: String,
				default: 'registerfield.list'
			}
		},
		data() {
			return {
				registerfield: {},
				latitude: '',
				longitude: '',
				is_submitaudit: 1
			};
		},
		mounted() {
			let _this = this;
			_this.$request.get(_this.geturl, {
				ptype: _this.ptype,
				update: _this.update,
				sid: _this.sid,
				technicalid: _this.technicalid,
				orderid: _this.orderid,
				samkey: (new Date()).valueOf()
			}).then(res => {
				console.log(res);
				if (res.errno == 0) {
					_this.registerfield = res.data;
				}
			});
		},
		methods: {}
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
	.tui-list-cell-img {
		width: 100%;
		color: $uni-text-color;
		box-sizing: border-box;
		font-size: 30rpx;
	}
	.img-title {
		padding-bottom: 20rpx;
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
