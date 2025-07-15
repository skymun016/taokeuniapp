<template>
	<view class="diy-duo-box">
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
			<view v-if="diyitem.list.length > 0" class="diy-duo-list">
				<view class="diy-duo">
					<view
						:class="'duo-list display__' + diyitem.base.display + ' column__' + diyitem.base.column">
						<scroll-view :scroll-x="diyitem.base.display === 'slide' ? true : false">
							<block v-for="(dataItem, index) in diyitem.list" :key="index">
								<view class="duo-item" @tap="navigateTo" :data-url="dataItem.link">
									<view v-if="dataItem.img" class="duo-image">
										<image mode="widthFix"
											:style="'border-radius:' + diyitem.base.borderradius + '%'"
											:src="dataItem.img">
										</image>
									</view>
									<view v-if="dataItem.text.show" class="duo-name twolist-hidden f-28"
										:style="{ color: dataItem.text.color }">
										{{ dataItem.text.txt }}
									</view>
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
		name: 'duo',
		props: {
			diyitem: {
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
			}
		}
	};
</script>
<style>
	@import './diyapge.css';
</style>