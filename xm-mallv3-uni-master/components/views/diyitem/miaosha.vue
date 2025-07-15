<template>
	<view class="tui-product-box">
		<view class="tui-block__box" :style="diyitem.base.bgstyle">
			<view v-if="diyitem.title.title.show" class="group-name-box">
				<view class="tui-group-name">
					<view class="tui-seckill__box">
						<image src="/static/images/mall/img_home_seckill_3x.png" class="tui-seckill__img"
							mode="widthFix"></image>
						<text>{{ diyitem.title.title.txt }}</text>
						<view :style="'border: 1rpx solid '+ pagestyleconfig.appstylecolor +';'"
							class="tui-countdown__box">
							<view :style="'background:'+ pagestyleconfig.appstylecolor" class="tui-countdown__title">距结束
							</view>
							<view class="tui-flex__center">
								<tui-countdown :time="3800" backgroundColor="transparent" borderColor="transparent"
									:color="pagestyleconfig.appstylecolor" :colonColor="pagestyleconfig.appstylecolor">
								</tui-countdown>
							</view>
						</view>
					</view>
					<view v-if="diyitem.title.more.show" class="tui-more__box" @tap="seckill">
						<text>{{ diyitem.title.more.txt }}</text>
						<tui-icon name="arrowright" :size="36" unit="rpx" color="#999"></tui-icon>
					</view>
				</view>
			</view>
			<view v-if="diyitem.list.length > 0" class="tui-group-list">
				<view class="diy-goods">
					<view :class="'goods-list display__' + diyitem.base.display + ' column__' + diyitem.base.column">
						<scroll-view :scroll-x="diyitem.base.display === 'slide' ? true : false">
							<block v-for="(dataItem, index) in diyitem.list" :key="index">
								<view v-if="dataItem.goods_id" class="goods-item">
									<navigator hover-class="none"
										:url="dataItem.goods_id > 0 ? '/pages/goodsDetail/goodsDetail?msid=' + dataItem.id + '&id=' + dataItem.goods_id : ''">
										<view class="goods-image">
											<image :style="diyitem.base.widthheight ? diyitem.base.widthheight : ''"
												:src="dataItem.image"></image>
										</view>
										<view class="detail">
											<view v-if="diyitem.base.text.show > 0"
												class="goods-name twolist-hidden f-28">
												{{ dataItem.title }}
											</view>
											<view v-if="diyitem.base.sjg" class="goods-price col-m">
												<text v-if="dataItem.is_points_goods == 1">{{lang.points}}:{{
													dataItem.pay_points }}</text>
												<text v-if="dataItem.is_points_goods != 1"><text class="f-24">￥</text>{{
													dataItem.price }}
													<block v-if="dataItem.ptype==2 && dataItem.quantity_unit">
														<text v-if="dataItem.time_amount > 0"
															class="f-24">/{{dataItem.time_amount}}{{dataItem.quantity_unit}}</text>
														<text v-else class="f-24"><text
																v-if="dataItem.is_times && dataItem.timesmum">/{{dataItem.timesmum}}</text>{{dataItem.quantity_unit}}</text>
													</block>
												</text>
												<text v-if="dataItem.minimum > 1"
													style="color:#999;font-size:24rpx;">起售量
													{{ dataItem.minimum }}</text>
											</view>
										</view>
									</navigator>
								</view>
							</block>
						</scroll-view>
					</view>
				</view>
			</view>
		</view>
	</view>
</template>

<script>
	export default {
		name: 'miaosha',
		props: {
			diyitem: {
				type: Object,
				default () {
					return {};
				}
			},
			pagestyleconfig: {
				type: Object,
				default () {
					return {};
				}
			}
		},
		computed: {

		},
		data() {
			return {
				lang: {}
			};
		},
		mounted() {
			let _this = this
			_this.$request.get('Lang.getlang').then(res => {
				if (res.errno == 0) {
					_this.lang = res.data;
				}
			});
		},
		methods: {
			navigateTo: function(e) {
				this.sam.diynavigateTo(e)
			},
			seckill() {
				let url = '/pages/seckillList/seckillList';
				this.tui.href(url);
			},
		}
	};
</script>
<style>
	@import './diyapge.css';
	.tui-product-box {
		margin-top: 20rpx;
		padding: 0 25rpx;
		box-sizing: border-box;
	}
	.tui-block__box {
		width: 100%;
		box-sizing: border-box;
		background-color: #ffffff;
		border-radius: 20rpx;
		overflow: hidden;
	}
	.group-name-box {
		padding-left: 25rpx;
		padding-right: 25rpx;
	}
	.tui-seckill__box {
		display: flex;
		align-items: center;
	}	
	.tui-seckill__img {
		width: 40rpx;
	}
	.tui-countdown__box {
		width: 228rpx;
		display: flex;
		align-items: center;
	
		color: #fff;
		background-color: #fff;
		font-weight: 400;
		height: 40rpx;
		border-radius: 30px;
		overflow: hidden;
		margin-left: 25rpx;
	}
	
	.tui-countdown__title {
		width: 100rpx;
		height: 100%;
		display: flex;
		align-items: center;
		justify-content: center;
		flex-shrink: 0;
		font-size: 24rpx;
		line-height: 24rpx;
	}
	.tui-countdown__title {
		width: 100rpx;
		height: 100%;
		display: flex;
		align-items: center;
		justify-content: center;
		flex-shrink: 0;
		font-size: 24rpx;
		line-height: 24rpx;
	}
	
	.tui-flex__center {
		flex: 1;
		display: flex;
		align-items: center;
		justify-content: center;
	}
	.tui-more__box {
		display: flex;
		align-items: center;
		font-weight: 400;
		color: #999;
	}
	
	.tui-more__box text {
		font-size: 24rpx;
		line-height: 24rpx;
	}
	
	.tui-group-list {
		padding-left: 10rpx;
		padding-right: 10rpx;
		justify-content: space-between;
		box-sizing: border-box;
		/* padding-top: 20rpx; */
	}
</style>