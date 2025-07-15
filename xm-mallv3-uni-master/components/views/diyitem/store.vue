<template>
	<view class="tui-product-box">
		<view class="tui-block__box" :style="diyitem.base.bgstyle">
			<view v-if="diyitem.title.title.show" class="group-name-box">
				<view class="tui-group-name">
					<view>
						<text>{{ diyitem.title.title.txt }}</text>
					</view>
					<view v-if="diyitem.title.more.show" class="tui-more__box" @tap="gotostore">
						<text>{{ diyitem.title.more.txt }}</text>
						<tui-icon name="arrowright" :size="36" unit="rpx" color="#999"></tui-icon>
					</view>
				</view>
			</view>
			<view v-if="diyitem.list.length > 0" class="tui-product-list">
				<view class="diy-goods" :style="diyitem.base.bgstyle">
					<view :class="'goods-list display__' + diyitem.base.display + ' column__' + diyitem.base.column">
						<scroll-view :scroll-x="diyitem.base.display === 'slide' ? true : false">
							<block v-for="(dataItem, index) in diyitem.list" :key="index">
								<view v-if="dataItem.id" class="goods-item">
									<navigator hover-class="none"
										:url="dataItem.id > 0 ? '/pages/store_details/store_details?id=' + dataItem.id : ''">
										<view class="goods-image">
											<image :style="diyitem.base.widthheight ? diyitem.base.widthheight : ''"
												:src="dataItem.store_logo || '/static/images/default_img.png'">
											</image>
										</view>
										<view class="technicaldetail">
											<view class="goods-name twolist-hidden f-28">
												{{ dataItem.title }}
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
		name: 'store',
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
			gotostore() {
				let url = '/pages/store_list/store_list?from=bottom';
				this.tui.href(url);
			},
		}
	};
</script>
<style>
	@import './diyapge.css';
</style>