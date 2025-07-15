<template>
	<view>
		<web-view :src="url"></web-view>
	</view>
</template>

<script>
	const app = getApp();
	export default {
		data() {
			return {
				url: '',

			};
		},
		onLoad: function(e) {
			this.url = e.url;
		},
		methods: {},
		/**
		 * 页面相关事件处理函数--监听用户下拉动作
		 */
		onPullDownRefresh: function() {
			setTimeout(() => {
				uni.stopPullDownRefresh()
			}, 200);
		},
	};
</script>

<style>
	page {
		background-color: #fafafa;
		font-size: 32rpx;
	}
</style>
