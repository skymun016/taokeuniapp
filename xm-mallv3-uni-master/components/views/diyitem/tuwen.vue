<template>
	<view class="tui-tuwen-box">
		<view v-if="diyitem.base.style == 'style1'" class="item-tuwen-style1">
			<view v-if="diyitem.title.show" class="tui-tuwen-title">
				{{ diyitem.title.txt }}
			</view>
			<view class="diy-tuwen" :style="diyitem.base.bgstyle">
				<view class="item-image">
					<view class="nav-to" @tap="navigateTo" :data-url="diyitem.link">
						<image :src="diyitem.img" mode="aspectFill"></image>
					</view>
					<view v-if="diyitem.note.show" class="note">{{ diyitem.note.txt }}</view>
				</view>
			</view>
		</view>
		<view v-if="diyitem.base.style == 'style2'" class="item-tuwen-style2">
			<view class="diy-tuwen" :style="diyitem.base.bgstyle">
				<view class="item-image">
					<view class="nav-to" @tap="navigateTo" :data-url="diyitem.link">
						<image :src="diyitem.img" mode="aspectFill"></image>
					</view>
				</view>
				<view v-if="diyitem.title.show" class="tui-tuwen-title">
					{{ diyitem.title.txt }}
				</view>
				<view v-if="diyitem.note.show" class="note">{{ diyitem.note.txt }}</view>
			</view>
		</view>
	</view>
</template>

<script>
	export default {
		name: 'tuwen',
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