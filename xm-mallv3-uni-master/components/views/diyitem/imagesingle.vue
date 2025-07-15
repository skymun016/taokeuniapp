<template>
	<view class="diy-imageSingle" :style="diyitem.base.bgstyle">
		<view class="item-image">
			<view class="nav-to" @tap="navigateTo" :data-url="diyitem.link">
				<image :src="diyitem.img" mode="widthFix"></image>
			</view>
		</view>

	</view>
</template>

<script>
	export default {
		name: 'imagesingle',
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
	/* 单图组 */
	
	.diy-imageSingle .item-image image {
		display: block;
		width: 100%;
	}
</style>