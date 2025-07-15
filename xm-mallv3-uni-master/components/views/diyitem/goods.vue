<template>
	<view class="tui-product-box">
		<view class="tui-block__box" :style="diyitem.base.bgstyle">
			<view v-if="diyitem.title.title.show" class="group-name-box">
				<view class="tui-group-name">
					<view>
						<text>{{ diyitem.title.title.txt }}</text>
					</view>
					<view v-if="diyitem.title.more.show" class="tui-more__box" @tap="navigateTo"
						:data-url="diyitem.title.link">
						<text>{{ diyitem.title.more.txt }}</text>
						<tui-icon name="arrowright" :size="36" unit="rpx" color="#999"></tui-icon>
					</view>
				</view>
			</view>
			<view v-if="diyitem.list.length > 0" class="tui-product-list">
				<view v-if="diyitem.base.display === 'li'">
					<view class="tui-goodli-box">
						<block v-for="(dataItem, index) in diyitem.list" :key="index">
							<view v-if="dataItem.goods_id > 0" class="tui-goodli-item">
								<view class="goodliimgtitle" @tap="goodsDetail" :data-id="dataItem.goods_id">
									<image :src="dataItem.image" class="tui-goodli-img" mode="widthFix">
									</image>
									<view class="tui-goodli-title-box">
										<view class="tui-goodli-title">{{ dataItem.goods_name }}
										</view>
										<view v-if="dataItem.keyword" class="goodli-keyword">
											{{ dataItem.keyword }}
										</view>
										<view class="tui-goodli-price">
											<text class="tui-goodli-present">
												<text class="f-24">￥</text>{{ dataItem.price}}
												<block v-if="dataItem.ptype==2 && dataItem.quantity_unit">
													<text v-if="dataItem.time_amount > 0"
														class="f-24">/{{dataItem.time_amount}}{{dataItem.quantity_unit}}</text>
													<text v-else class="f-24"><text
															v-if="dataItem.is_times && dataItem.timesmum">/{{dataItem.timesmum}}</text>{{dataItem.quantity_unit}}</text>
												</block>
											</text>
											<text v-if="dataItem.original_price > 0" class="tui-factory-price">￥{{ dataItem.original_price
												}}</text>
										</view>
									</view>
								</view>
								<view v-if="diyitem.base.is_binding == true" class="goodli-butt-box">
									<button :data-id="dataItem.goods_id" @click="choosetechnical"
										:style="'background:'+ pagestyleconfig.appstylecolor"
										class="goodli-button">选择{{lang.technical}}</button>
								</view>
								<view v-else class="goodli-butt-box">
									<button :data-id="dataItem.goods_id" @click="goodsDetail"
										:style="'background:'+ pagestyleconfig.appstylecolor"
										class="goodli-button">详情</button>
								</view>
							</view>
						</block>
					</view>
				</view>
				<view v-else class="diy-goods">
					<view :class="'goods-list display__' + diyitem.base.display + ' column__' + diyitem.base.column">
						<scroll-view :scroll-x="diyitem.base.display === 'slide' ? true : false">
							<block v-for="(dataItem, index) in diyitem.list" :key="index">
								<view v-if="dataItem.goods_id" class="goods-item">
									<navigator hover-class="none"
										:url="dataItem.goods_id > 0 ? '/pages/goodsDetail/goodsDetail?id=' + dataItem.goods_id : ''">
										<view class="goods-image">
											<image :style="diyitem.base.widthheight ? diyitem.base.widthheight : ''"
												:src="dataItem.image"></image>
										</view>
										<view class="detail">
											<view v-if="diyitem.base.text.show > 0"
												class="goods-name twolist-hidden f-28">
												{{ dataItem.goods_name }}
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
												</text><text v-if="dataItem.original_price > 0"
													class="tui-factory-price">￥{{
														dataItem.original_price }}</text>
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
		name: 'goods',
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
			goodsDetail: function(e) {
				this.sam.navigateTo('/pages/goodsDetail/goodsDetail?id=' + e.currentTarget.dataset.id);
			},
			choosetechnical: function(e) {
				this.sam.navigateTo('/pages/technical/list?goodsid=' + e.currentTarget.dataset.id);
			},
		}
	};
</script>
<style>
	@import './diyapge.css';
</style>