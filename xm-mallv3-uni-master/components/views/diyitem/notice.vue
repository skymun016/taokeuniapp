<template>
	<view class="diy-notice"
		:style="{ paddingTop: diyitem.base.paddingTop + 'px',paddingBottom: diyitem.base.paddingTop + 'px', paddingLeft: 0, background: diyitem.base.bc }">
		<uni-notice-bar :showIcon="false" :scrollable="true" :single="true"
			:background-color="diyitem.base.bc" :color="diyitem.text.color"
			:fontsize="diyitem.text.fontsize" :text="diyitem.text.txt">
		</uni-notice-bar>
	</view>
</template>

<script>
	export default {
		name: 'notice',
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