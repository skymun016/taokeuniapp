<template>
	<view class="diy-window" :style="diyitem.base.bgstyle">
		<!-- display -->
		<view class="display">
			<!-- 1 -->
			<view class="display-left">
				<image @tap="navigateTo" :data-url="diyitem.list[0].link"
					:src="diyitem.list[0].img">
				</image>
			</view>
			<!-- 2 -->
			<view v-if="diyitem.base.style == 'style3'" class="display-right">
				<image @tap="navigateTo" :data-url="diyitem.list[1].link"
					:src="diyitem.list[1].img">
				</image>
			</view>
			<!-- 3 -->
			<view v-if="diyitem.base.style == 'style2'" class="display-right">
				<view class="display-right1">
					<image @tap="navigateTo" :data-url="diyitem.list[1].link"
						:src="diyitem.list[1].img">
					</image>
				</view>
				<view class="display-right2">
					<image @tap="navigateTo" :data-url="diyitem.list[2].link"
						:src="diyitem.list[2].img">
					</image>
				</view>
			</view>
			<!-- 4 -->
			<view v-if="diyitem.base.style == 'style1'" class="display-right">
				<view class="display-right1">
					<image @tap="navigateTo" :data-url="diyitem.list[1].link"
						:src="diyitem.list[1].img">
					</image>
				</view>
				<view class="display-right2">
					<view class="left">
						<image @tap="navigateTo" :data-url="diyitem.list[2].link"
							:src="diyitem.list[2].img"></image>
					</view>
					<view class="right">
						<image @tap="navigateTo" :data-url="diyitem.list[3].link"
							:src="diyitem.list[3].img"></image>
					</view>
				</view>
			</view>
		</view>
	</view>
</template>

<script>
	export default {
		name: 'window',
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