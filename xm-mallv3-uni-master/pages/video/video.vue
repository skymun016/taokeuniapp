<template>
	<view class="container">
		<video v-if="videourl" class="tui-video" :src="videourl" enable-danmu danmu-btn controls autoplay>
		</video>
	</view>
</template>

<script>

	export default {
		data() {
			return {
				videourl: ''
			}
		},
		onLoad(e) {
			if (e.videourl) {
				this.videourl = e.videourl;
			}
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
	}
</script>

<style>
	page,
	.container {
		height: 100%;
		background-color: #000000;
	}

	.container {
		display: flex;
		align-items: center;
		justify-content: center;
	}

	.tui-video {
		width: 100%;
		height: 100%;
	}
</style>
