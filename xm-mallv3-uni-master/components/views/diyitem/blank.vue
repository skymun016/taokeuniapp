<template>
	<view class="diy-blank"
		:style="{ height: diyitem.base.height + 'px', background: diyitem.base.bc }">
	</view>
</template>

<script>
	export default {
		name: 'blank',
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
	@import './diyapge.css';
</style>