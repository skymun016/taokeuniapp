<template>
	<view>
		<web-view :src="webscr"></web-view>
	</view>
</template>

<script>
	const app = getApp();
	export default {
		data() {
			return {
				user: [],
				webscr:'',

			};
		},
		onLoad: function(e) {

		},
		onShow: function() {
			let _this = this;
			_this.$request.get('member.getsetmpurl').then(res => {
				if (res.errno == 0) {
					_this.webscr = res.data.url;
				}
			});
		},
		methods: {
		},
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
