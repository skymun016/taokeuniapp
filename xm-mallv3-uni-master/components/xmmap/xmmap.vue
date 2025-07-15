<template>
	<!-- 地图找人 -->
	<map :style="{height:winHeight+'px'}" class="map" :markers="markers" scale='12' enable-building='true' :latitude="latitude" :longitude="longitude" id="map" ref='map'
		:show-location='true' @callouttap='callouttap'>
		<cover-view slot="callout">
			<block v-for="(item, index) in markers" :key="index">
				<cover-view :marker-id="index" style='height:188rpx'>
					<cover-image style='width:99rpx;height:110rpx;'
						src='/static/images/onlineBg.png' />
					<cover-image style='width:88rpx;height:88rpx;border-radius:50%;margin-top:-105rpx;margin-left:6rpx;box-shadow:0 26rpx 8rpx 0 rgba(136, 136, 136, 0.5);'
						:src="markers[index].customCallout.avatar || '/static/images/my/mine_def_touxiang_3x.png'" />
				</cover-view>
			</block>
		</cover-view>
	</map>
	<!-- 地图找人end -->
</template>
<script>
	export default {
		props: {
			//中心点纬度
			latitude: {
				type: String,
				default: ''
			},
			//中心点经度
			longitude: {
				type: String,
				default: ''
			},
			//标记点数据
			markers: {
				type: Array,
				default () {
					return []
				}
			},
			//地图高度
			winHeight: {
				type: Number,
				default: 600
			},
		},
		data() {
			return {}
		},
		onReady() {

		},
		onShow: function() {

		},
		methods: {
			callouttap(e) {
				this.$emit("callouttap", e)
			},
		},
	}
</script>
<style>
	.map {
		flex: 1;
		width: 100%;
	}
</style>