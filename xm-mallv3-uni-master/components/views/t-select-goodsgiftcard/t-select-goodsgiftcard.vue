<template>
	<view>
		<tui-bottom-popup :show="show" @close="close">
			<view class="tui-goodsgiftcard__box">
				<view class="tui-goodsgiftcard__title">
					<text>购物卡</text>
					<view class="tui-icon-close" @tap="close">
						<tui-icon name="shut" :size="22" color="#BFBFBF"></tui-icon>
					</view>
				</view>
				<scroll-view scroll-y class="tui-goodsgiftcard__list">
					<radio-group @change="radioChange">
						<label class="tui-not-used  tui-top20">
							<text>不使用购物卡</text>
							<radio value="-1" class="tui-goodsgiftcard-radio" color="#e41f19" />
						</label>
						<view class="tui-bankcard__list tui-top20">
							<view class="tui-bankcard__item"  @click="Change(index)" :class="'cardbg'+item.styleno" v-for="(item,index) in dataList"
								:key="index">
								<view class="tui-card__info">
										<view class="tui-logo__box">
											<radio :value="index" class="tui-goodsgiftcard-radio" color="#e41f19" :checked="selIndex==index" />
										</view>
										<view class="tui-name__box">
											<view class="tui-name">{{item.name}}</view>
											<view class="tui-desc">余额：{{item.balance}}</view>
											</view>
										</view>
								</view>
							</view>
					</radio-group>
					<view class="tui-seat__box tui-top20"></view>
				</scroll-view>
				<view class="tui-btn-pay">
					<tui-button height="88rpx" type="danger" shape="circle" shadow @click="btnConfirm">确定</tui-button>
				</view>
			</view>
		</tui-bottom-popup>
	</view>
</template>

<script>
	export default {
		name: 'tSelectgoodsgiftcard',
		props: {
			dataList: {
				type: Array,
				default () {
					return [{}, {}, {}, {}, {}];
				}
			},
			//控制显示
			show: {
				type: Boolean,
				default: false
			},
			page: {
				type: Number,
				default: 1
			}
		},
		data() {
			return {
				selIndex:'-1'
			};
		},
		methods: {
			btnConfirm() {
				this.$emit("ChangeGoodsgiftcard", {selIndex:this.selIndex})
			},
			radioChange(e) {				
				this.selIndex = e.target.value					  
			},
			Change(e) {
				this.selIndex = e				  
			},
			close() {
				this.$emit("close", {})
			},
			
		}
	};
</script>

<style scoped>
	.tui-goodsgiftcard__box {
		width: 100%;
	}

	.tui-goodsgiftcard__title {
		width: 100%;
		padding: 40rpx 30rpx;
		box-sizing: border-box;
		display: flex;
		align-items: center;
		justify-content: center;
		position: relative;
	}

	.tui-icon-close {
		position: absolute;
		right: 30rpx;
		top: 50%;
		transform: translateY(-50%);
	}

	.tui-goodsgiftcard__list {
		width: 100%;
		height: 640rpx;
		padding: 0 30rpx;
		box-sizing: border-box;
		background-color: #FAFAFA;
	}
	.tui-not-used{
		width: 100%;
		display: flex;
		align-items: center;
		justify-content: space-between;
		font-size: 28rpx;
		color: #333333;
		background-color: #fff;
		padding:20rpx 30rpx;
		box-sizing: border-box;
		border-radius:6rpx;
	}

	.tui-goodsgiftcard-item {
		width: 100%;
		height: 210rpx;
		position: relative;
		display: flex;
		align-items: center;
		padding-right: 30rpx;
		box-sizing: border-box;
		overflow: hidden;
	}

	.tui-goodsgiftcard-bg {
		width: 100%;
		height: 210rpx;
		position: absolute;
		left: 0;
		top: 0;
		z-index: 1;
	}

	.tui-goodsgiftcard-sign {
		height: 110rpx;
		width: 110rpx;
		position: absolute;
		z-index: 9;
		top: -30rpx;
		right: 40rpx;
	}

	.tui-goodsgiftcard-item-left {
		width: 218rpx;
		height: 210rpx;
		position: relative;
		z-index: 2;
		display: flex;
		align-items: center;
		justify-content: center;
		flex-direction: column;
		flex-shrink: 0;
	}

	.tui-goodsgiftcard-price-box {
		display: flex;
		color: #e41f19;
		align-items: flex-end;
	}

	.tui-goodsgiftcard-price-sign {
		font-size: 30rpx;
	}

	.tui-goodsgiftcard-price {
		font-size: 70rpx;
		line-height: 68rpx;
		font-weight: bold;
	}

	.tui-price-small {
		font-size: 58rpx !important;
		line-height: 56rpx !important;
	}

	.tui-goodsgiftcard-intro {
		background: #f7f7f7;
		padding: 8rpx 10rpx;
		font-size: 26rpx;
		line-height: 26rpx;
		font-weight: 400;
		color: #666;
		margin-top: 18rpx;
	}

	.tui-goodsgiftcard-item-right {
		flex: 1;
		height: 210rpx;
		position: relative;
		z-index: 2;
		display: flex;
		align-items: center;
		justify-content: space-between;
		padding-left: 24rpx;
		box-sizing: border-box;
		overflow: hidden;
	}

	.tui-goodsgiftcard-content {
		width: 82%;
		display: flex;
		flex-direction: column;
		justify-content: center;
	}

	.tui-goodsgiftcard-title-box {
		display: flex;
		align-items: center;
	}

	.tui-goodsgiftcard-btn {
		padding: 6rpx;
		background: #ffebeb;
		color: #e41f19;
		font-size: 25rpx;
		line-height: 25rpx;
		display: flex;
		align-items: center;
		justify-content: center;
		transform: scale(0.9);
		transform-origin: 0 center;
		border-radius: 4rpx;
		flex-shrink: 0;
	}

	.tui-goodsgiftcard-title {
		width: 100%;
		font-size: 26rpx;
		color: #333;
		white-space: nowrap;
		overflow: hidden;
		text-overflow: ellipsis;
	}

	.tui-goodsgiftcard-rule {
		padding-top: 52rpx;
	}

	.tui-rule-box {
		display: flex;
		align-items: center;
		transform: scale(0.8);
		transform-origin: 0 100%;
	}

	.tui-padding-btm {
		padding-bottom: 6rpx;
	}

	.tui-goodsgiftcard-circle {
		width: 8rpx;
		height: 8rpx;
		background: rgb(160, 160, 160);
		border-radius: 50%;
	}

	.tui-goodsgiftcard-text {
		font-size: 28rpx;
		line-height: 28rpx;
		font-weight: 400;
		color: #666;
		padding-left: 8rpx;
		white-space: nowrap;
	}

	.tui-top20 {
		margin-top: 20rpx;
	}

	.tui-goodsgiftcard-title {
		font-size: 28rpx;
		line-height: 28rpx;
	}

	.tui-goodsgiftcard-radio {
		transform: scale(0.7);
		transform-origin: 100% center;
	}

	/* #ifdef APP-PLUS || MP */
	.wx-radio-input {
		margin-right: 0 !important;
	}

	/* #endif */

	/* #ifdef H5 */
	>>>uni-radio .uni-radio-input {
		margin-right: 0 !important;
	}

	/* #endif */
	.tui-seat__box {
		width: 100%;
		height: 1rpx;
	}

	.tui-btn-pay {
		width: 100%;
		padding: 20rpx 60rpx 40rpx;
		box-sizing: border-box;
	}
	
	.tui-bankcard__item {
		width: 100%;
		height: 240rpx;
		padding: 30rpx;
		box-sizing: border-box;
		border-radius: 16rpx;
		margin-bottom: 20rpx;
	}
	
	.tui-logo__box {
		width: 80rpx;
		height: 80rpx;
		display: flex;
		align-items: center;
		justify-content: center;
		margin-right: 20rpx;
	}
	
	.tui-logo {
		width: 52rpx;
		height: 52rpx;
	}
	
	.tui-card__info {
		display: flex;
		align-items: center;
		color: #fff;
	}
	
	.tui-name {
		font-size: 30rpx;
		font-weight: 500;
	}
	
	.tui-desc {
		font-size: 24rpx;
		opacity: 0.7;
	}
	
	.tui-card__no {
		margin-left: auto;
	}
	
	.cardbg0 {
		background: linear-gradient(to right, #FEAD4B, #FF9225);
	}
	.cardbg1 {
		background: linear-gradient(to right, #2C85D5, #2D66D1);
	}
	.cardbg2 {
		background: linear-gradient(to right, #2C87D6, #2D69D0);
	}
	.cardbg3 {
		background: linear-gradient(to right, #01ADA3, #0291A9);
	}
	
	.cardbg4 {
		background: linear-gradient(to right, #FF6F64, #FE5762);
	}
	
	.cardbg5 {
		background: linear-gradient(to right, #FF7065, #FD4754);
	}
	
	.tui-ping_an {
		background: linear-gradient(to right, #FEAD4B, #FF9225);
	}
	
	.tui-jian_she {
		background: linear-gradient(to right, #2C85D5, #2D66D1);
	}
	
	.tui-min_sheng {
		background: linear-gradient(to right, #2C87D6, #2D69D0);
	}
	
	.tui-nong_ye {
		background: linear-gradient(to right, #01ADA3, #0291A9);
	}
	
	.tui-zhao_shang {
		background: linear-gradient(to right, #FF6F64, #FE5762);
	}
	
	.tui-zhong_xin {
		background: linear-gradient(to right, #FF7065, #FD4754);
	}
</style>
