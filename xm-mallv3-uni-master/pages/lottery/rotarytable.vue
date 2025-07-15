<template>
	<view class="almost-lottery">
		<view class="almost-lottery__wheel">
			<almost-lottery :lottery-size="lotteryConfig.lotterySize" :action-size="lotteryConfig.actionSize"
				:ring-count="10" :duration="3" :img-circled="true" :canvasCached="true" :prize-list="prizeList"
				:prize-index="prizeIndex" @reset-index="prizeIndex = -1" @draw-start="handleDrawStart"
				@draw-end="handleDrawEnd" @finish="handleDrawFinish" v-if="prizeList.length" />
			<view class="almost-lottery__count">
				<!--<text class="text">剩余免费抽奖 3次</text>-->
			</view>
		</view>
		<!-- rule -->
		<view class="almost-lottery__rule">
			<view class="rule-head">
				<view class="line"></view>
				<text class="title">活动规则</text>
				<view class="line"></view>
			</view>
			<view class="rule-body">
				<view class="item">
					<view class="text">
						<text>{{config.content}}</text>
					</view>
				</view>
				<template v-if="isApple">
					<view class="item">
						<view class="number">1</view>
						<view class="text">本次活动由发起，与Apple.Inc无关。</view>
					</view>
					<view class="item">
						<view class="number">2</view>
						<view class="text">苹果公司不是本活动的赞助商且没有以任何形式参与活动。</view>
					</view>
					<view class="item">
						<view class="number">3</view>
						<view class="text">本活动仅限17岁以上用户参加。</view>
					</view>
				</template>
				<template v-else>
					<view class="item">
						<view class="text">本活动仅限17岁以上用户参加。</view>
					</view>
				</template>
			</view>
		</view>
	</view>
</template>

<script>
	import AlmostLottery from '@/uni_modules/almost-lottery/components/almost-lottery/almost-lottery.vue'
	import {
		clearCacheFile,
		clearStore
	} from '@/uni_modules/almost-lottery/utils/almost-utils.js'
	export default {
		name: 'Home',
		components: {
			AlmostLottery
		},
		data() {
			return {
				// 开启调试模式
				isDev: true,

				// 以下是转盘配置相关数据
				lotteryConfig: {
					// 抽奖转盘的整体尺寸，单位rpx
					lotterySize: 600,
					// 抽奖按钮的尺寸，单位rpx
					actionSize: 200
				},
				config: [],

				// 以下是奖品配置数据
				// 奖品数据
				prizeList: [],
				// 中奖下标
				prizeIndex: -1,

				// 是否正在抽奖中，避免重复触发
				prizeing: false,
			}
		},
		computed: {
			isApple() {
				return uni.getSystemInfoSync().platform === 'ios'
			}
		},
		methods: {
			// 获取奖品列表
			async getPrizeList() {
				uni.showLoading({
					title: '奖品准备中...'
				})

				// 等待接口返回的数据进一步处理
				let res = await this.requestApiGetPrizeList()
				console.log('获取奖品列表', res)

				if (res.ok) {
					let data = res.data
					if (data.length) {
						this.config = res.config;
						this.prizeList = data;
						console.log('已获取到奖品列表数据，开始绘制抽奖转盘')

						// 计算开始绘制的时间
						if (console.time) {
							console.time('绘制转盘用时')
						}
					}
				} else {
					uni.hideLoading()
					uni.showToast({
						title: '获取奖品失败',
						mask: true,
						icon: 'none'
					})
				}
			},
			// 模拟请求 获取奖品列表 接口，
			// 注意这里返回的是一个 Promise
			requestApiGetPrizeList() {
				return new Promise((resolve, reject) => {
					let requestTimer = setTimeout(() => {
						clearTimeout(requestTimer)
						requestTimer = null
						let _this = this;
						_this.$request.get('Rotarytableprize.index', {
							samkey: (new Date()).valueOf()
						}).then(res => {
							if (res.errno == 0) {
								// prizeWeight 中奖概率，数值越大中奖概率越高，权重一样时随机中奖
								resolve({
									ok: true,
									config: res.data.config,
									data: res.data.prizelist
								})
							}
						});
					}, 200)
				})
			},
			// 本次抽奖开始
			handleDrawStart() {
				console.log('触发抽奖按钮')
				if (this.prizeing) return
				this.prizeing = true

				let _this = this;
				_this.$request.get('Rotarytableprize.drawstart', {
					samkey: (new Date()).valueOf()
				}).then(res => {
					if (res.errno == 0) {
						this.tryLotteryDraw();
					}else{
						this.prizeing = false;
					}
				});
			},
			// 尝试发起抽奖
			tryLotteryDraw() {
				console.log('旋转开始，获取中奖下标......')
				this.remoteGetPrizeIndex()
			},
			// 远程请求接口获取中奖下标
			// 大哥，这里只是模拟，别告诉我你不会对接自己的接口
			remoteGetPrizeIndex() {
				console.warn('###当前处于模拟的请求接口，并返回了中奖信息###')
				// 模拟请求接口获取中奖信息
				let stoTimer = setTimeout(() => {
					stoTimer = null

					let list = [...this.prizeList]

					let _this = this;
					_this.$request.get('Rotarytableprize.getprizeid', {
						samkey: (new Date()).valueOf()
					}).then(res => {
						if (res.errno == 0) {
							// 这里随机产生的 prizeId 是模拟后端返回的 prizeId
							let prizeId = res.data.prizeId;

							// 拿到后端返回的 prizeId 后，开始循环比对得出那个中奖的数据
							for (let i = 0; i < list.length; i++) {
								let item = list[i]
								if (item.prizeId === prizeId) {
									// 中奖下标
									this.prizeIndex = i
									break
								}
							}
							//console.log(this.prizeIndex)
							//console.log(this.prizeList)

							console.log('本次抽中奖品 =>', this.prizeList[this.prizeIndex].prizeName)
						}
					});

				}, 200)
			},
			// 本次抽奖结束
			handleDrawEnd() {
				console.log('旋转结束，执行拿到结果后到逻辑')

				// 旋转结束后，开始处理拿到结果后的逻辑
				let prizeName = this.prizeList[this.prizeIndex].prizeName
				let tipContent = ''

				if (prizeName === '谢谢参与') {
					tipContent = '很遗憾，没有中奖，请再接再厉！'
				} else {
					tipContent = `恭喜您，获得 ${prizeName} ！`
				}

				uni.showModal({
					content: tipContent,
					showCancel: false,
					complete: () => {
						this.prizeing = false
					}
				})
			},
			// 抽奖转盘绘制完成
			handleDrawFinish(res) {
				console.log('抽奖转盘绘制完成', res)

				if (res.ok) {
					// 计算结束绘制的时间
					if (console.timeEnd) {
						console.timeEnd('绘制转盘用时')
					}
				}

				let stoTimer = setTimeout(() => {
					stoTimer = null

					uni.hideLoading()
					uni.showToast({
						title: res.msg,
						mask: true,
						icon: 'none'
					})
				}, 50)
			}
		},
		onLoad() {
			this.prizeList = []
			this.getPrizeList()
		},
		/**
		 * 页面相关事件处理函数--监听用户下拉动作
		 */
		onPullDownRefresh: function() {
			setTimeout(() => {
				uni.stopPullDownRefresh()
			}, 200);
		},
		onUnload() {
			uni.hideLoading()
		}
	}
</script>

<style lang="scss" scoped>
	.almost-lottery {
		flex: 1;
		background-color: #FF893F;
		padding-top: 100rpx;
		padding-bottom: 380rpx;
	}

	.almost-lottery__wheel {
		text-align: center;

		.almost-lottery__count {
			display: flex;
			flex-direction: column;
			justify-content: center;
			align-items: center;
			text-align: center;
			padding: 40rpx 0;
		}

		.text {
			color: #FFFFFF;
			font-size: 24rpx;
		}
	}

	.almost-lottery__rule {
		padding: 0 28rpx;
		color: #FFF8CB;

		.rule-head {
			display: flex;
			justify-content: space-around;
			align-items: center;
			margin: 40rpx 0;

			.line {
				flex: 1;
				height: 1px;
				background-color: #FFF3A5;
			}

			.title {
				width: 280rpx;
				color: #F63857;
				line-height: 70rpx;
				text-align: center;
				margin: 0 20rpx;
				border-radius: 8rpx;
				background-image: linear-gradient(0deg, rgba(255, 242, 158, 1), rgba(255, 244, 168, 1));
			}
		}

		.rule-body {
			color: #FFF8CB;
			font-size: 24rpx;
			padding: 10rpx 0 40rpx;

			.item {
				display: flex;
				margin-bottom: 10rpx;
			}

			.number {
				position: relative;
				top: 4rpx;
				width: 28rpx;
				height: 28rpx;
				line-height: 28rpx;
				text-align: center;
				color: #F63857;
				background: #FFF8CB;
				border-radius: 50%;
				margin-right: 10rpx;
			}

			.text {
				flex: 1;
			}

			.item-rule .text {
				display: flex;
				flex-direction: column;
			}
		}
	}

	.almost-lottery__action-dev {
		display: flex;
		flex-direction: row;
		justify-content: center;
		align-items: center;
		width: 400rpx;
		height: 80rpx;
		border-radius: 10rpx;
		text-align: center;
		background-color: red;
		margin: 0 auto 40rpx;

		.text {
			color: #FFFFFF;
			font-size: 28rpx;
		}
	}
</style>