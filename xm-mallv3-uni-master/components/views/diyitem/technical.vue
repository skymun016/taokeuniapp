<template>
	<view v-if="diyitem.list.length > 0" class="tui-product-list">
		<view class="diy-goods" :style="diyitem.base.bgstyle">
			<view
				:class="'goods-list display__' + diyitem.base.display + ' column__' + diyitem.base.column">
				<scroll-view :scroll-x="diyitem.base.display === 'slide' ? true : false">
					<block v-for="(dataItem, index) in diyitem.list" :key="index">
						<view v-if="dataItem.id" class="goods-item">
							<navigator hover-class="none"
								:url="dataItem.id > 0 ? '/pages/technical/details?id=' + dataItem.id : ''">
								<view class="goods-image">
									<image
										:style="diyitem.base.widthheight ? diyitem.base.widthheight : ''"
										:src="dataItem.touxiang || '/static/images/my/mine_def_touxiang_3x.png'">
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
</template>

<script>
	export default {
		name: 'technical',
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
		}
	};
</script>
<style>
	@import './diyapge.css';
</style>