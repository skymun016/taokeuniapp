<template>
	<view>

	</view>
</template>
<script>
	export default {
		data() {
			return {};
		},
		onLoad: function(e) {
			let _this = this
			uni.showLoading({
				title: '数据加载中'
			});

			if (e && e.scene) {
				const scene = decodeURIComponent(e.scene) // 处理扫码进商品详情页面的逻辑
				if (scene) {
					if (scene.split(',')[1]) {
						e.reid = scene.split(',')[1];
						uni.setStorageSync('reid', e.reid)
					}
					if (scene.split(',')[0]) {
						_this.$request.get('tuanzhang.detail', {
							id: scene.split(',')[0],
							samkey: (new Date()).valueOf()
						}).then(res => {
							if (res.errno == 0) {
								uni.setStorageSync('tz_id', res.data.id);
								uni.setStorageSync('tz_title', res.data.title);
								uni.reLaunch({
									url: '/pages/index/index'
								});
							}

						})
					}
				}
			}
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
