<template>
	<view class="diy-richText"
		:style="{ paddingTop: diyitem.base.paddingTop + 'px', paddingLeft: diyitem.base.paddingLeft + 'px', background: diyitem.base.bc }">
		<view class="">
			<rich-text :nodes="diyitem.params.content"></rich-text>
		</view>
	</view>
</template>

<script>
	export default {
		name: 'richtext',
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