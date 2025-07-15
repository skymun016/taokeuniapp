<template>
	<view class="tui-product-box">
		<view class="tui-block__box" :style="diyitem.base.bgstyle">
			<view v-if="diyitem.title.title.show" class="group-name-box">
				<view class="tui-group-name">
					<view>
						<text>{{ diyitem.title.title.txt }}</text>
						<text :style="'color: '+ pagestyleconfig.appstylecolor+';'"
							class="tui-sub__desc">拼着买更便宜</text>
					</view>
					<view v-if="diyitem.title.more.show" class="tui-more__box" @tap="group">
						<text>{{ diyitem.title.more.txt }}</text>
						<tui-icon name="arrowright" :size="36" unit="rpx" color="#999"></tui-icon>
					</view>
				</view>
			</view>
			<view v-if="diyitem.list.length > 0" class="tui-group-list">
				<view class="diy-goods">
					<view
						:class="'goods-list display__' + diyitem.base.display + ' column__' + diyitem.base.column">
						<scroll-view :scroll-x="diyitem.base.display === 'slide' ? true : false">
							<block v-for="(dataItem, index) in diyitem.list" :key="index">
								<view v-if="dataItem.goods_id" class="goods-item">
									<navigator hover-class="none"
										:url="dataItem.id > 0 ? '/pages/goodsDetail/goodsDetail?tuanid=' + dataItem.id + '&id=' + dataItem.goods_id : ''">
										<view class="goods-image">
											<image
												:style="diyitem.base.widthheight ? diyitem.base.widthheight : ''"
												:src="dataItem.image"></image>
										</view>
										<view class="groupdetail">
											<view v-if="diyitem.base.text.show > 0"
												class="goods-name twolist-hidden f-28">
												{{ dataItem.title }}
											</view>
											<view v-if="diyitem.base.sjg" class="goods-price col-m">
												<text v-if="dataItem.is_points_goods != 1"><text
														class="f-24">￥</text>{{
													dataItem.price }}
													<block v-if="dataItem.ptype==2 && dataItem.quantity_unit">
														<text v-if="dataItem.time_amount > 0"
															class="f-24">/{{dataItem.time_amount}}{{dataItem.quantity_unit}}</text>
														<text v-else class="f-24"><text
																v-if="dataItem.is_times && dataItem.timesmum">/{{dataItem.timesmum}}</text>{{dataItem.quantity_unit}}</text>
													</block>
												</text>
											</view>
											<view
												:style="'background:'+ pagestyleconfig.appstylecolor"
												class="tui-group-btn">
												<view
													:style="'color: '+ pagestyleconfig.appstylecolor+';'"
													class="tui-flex-btn">
													{{ dataItem.people_num }}人团
												</view>
												<view class="tui-flex-btn">去拼团</view>
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
		name: 'tuan',
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
			return {};
		},
		methods: {
			navigateTo: function(e) {
				this.sam.diynavigateTo(e)
			},
			group() {
				let url = '/pages/groupList/groupList';
				this.tui.href(url);
			},
		}
	};
</script>
<style>
	@import './diyapge.css';
</style>