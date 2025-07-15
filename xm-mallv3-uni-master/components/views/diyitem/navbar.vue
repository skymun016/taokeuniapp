<template>
	<view :class="diyitem.base.bg==1 ? 'diy-navBar-box' : ''">
		<view :class="diyitem.base.bg==1 ? 'tui-block__box' : ''">
			<view class="diy-navBar" :style="diyitem.base.bgstyle">
				<view :class="['data-list', 'avg-sm-' + diyitem.base.column]">
					<view class="item-nav" v-for="(dataItem, index) in diyitem.list" :key="index">
						<view class="nav-to" @tap="navigateTo" :data-url="dataItem.link">
							<view class="item-image">
								<image :src="dataItem.icon.custompic"></image>
							</view>
							<view class="item-text f-26 onelist-hidden"
								:style="{ color: dataItem.text.color }">
								{{ dataItem.text.txt }}
							</view>
						</view>
					</view>
				</view>
			</view>
		</view>
	</view>
</template>

<script>
	export default {
		name: 'navbar',
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
	/* 导航组 */
	
	.diy-navBar-box {
		padding: 0 25rpx;
		box-sizing: border-box;
	}
	
	.diy-navBar {
		padding-bottom: 20rpx;
	}
	
	.diy-navBar .data-list::after {
		clear: both;
		content: " ";
		display: table;
	}
	
	.diy-navBar .item-nav {
		float: left;
		margin: 7px 0;
		/*height: 62px;*/
		text-align: center;
	}
	
	.diy-navBar .item-nav .item-image {
		margin-bottom: 4px;
		font-size: 0;
	}
	
	.diy-navBar .item-nav image {
		width: 88rpx;
		height: 88rpx;
	}
	
	/* 分列布局 */
	
	.diy-navBar .avg-sm-3>.item-nav {
		width: 33.33333333%;
	}
	
	.diy-navBar .avg-sm-4>.item-nav {
		width: 25%;
	}
	
	.diy-navBar .avg-sm-5>.item-nav {
		width: 20%;
	}
</style>