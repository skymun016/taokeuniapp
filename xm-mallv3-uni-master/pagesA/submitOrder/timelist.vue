<template>
	<view>
		<times @change="getTime" :stylecolor="pagestyleconfig.appstylecolor" :selectedTabColor="pagestyleconfig.appstylecolor" :selectedItemColor="pagestyleconfig.appstylecolor" :buynowinfoid="buynowinfoid" :technicalId="technicalId"></times>
	</view>
</template>

<script>
	import times from '@/components/pretty-times/pretty-times.vue'
	export default {
		components: {
			times
		},
		data() {
			return {
				pagestyleconfig: [],
				dataList: [],
				technicalId: '',
				buynowinfoid:'',
			}
		},
		onLoad: function(options) {
			let _this = this
			this.technicalId = options.technicalId;
			this.buynowinfoid = options.buynowinfoid;
			_this.$request.post('config', {
				mo: 'pagestyle'
			}).then(res => {
				if (res.errno == 0) {
					_this.pagestyleconfig = res.data
				}
			});
		},
		onShow: function() {

		},
		methods: {
			getTime(e) {
				console.log(e)
				uni.setStorageSync('servicetime', e);
				uni.navigateBack({});
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
	}
</script>

<style>

</style>
