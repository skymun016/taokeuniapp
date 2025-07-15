<template>
	<view class="diy-video" :style="{ paddingTop: diyitem.base.paddingTop + 'px', paddingLeft: 0 }">
		<video :style="{ height: diyitem.base.height + 'px' }" :src="diyitem.videoUrl"
			:poster="diyitem.img" controls></video>
	</view>
</template>

<script>
	export default {
		name: 'video',
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
	/* 视频 */
	.diy-video video {
		width: 100%;
		display: block;
	}
	
</style>