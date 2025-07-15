<template>
	<view class="diy-guide"
		:style="{ paddingTop: diyitem.base.paddingTop + 'px', paddingLeft: 0, background: diyitem.base.bc }">
		<view class="line"
			:style="['border-top:' + diyitem.base.lineHeight + 'px', diyitem.base.lineStyle, diyitem.base.lineColor]">
		</view>
	</view>
</template>

<script>
	export default {
		name: 'guide',
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
		}
	};
</script>
<style>
	/* 辅助线 */
	
	.diy-guide .line {
		width: 100%;
	}
</style>