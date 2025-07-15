<template>
	<view class="diy-search" :style="diyitem.base.bgstyle">
		<view :style="{ opacity: opacity }">
			<view class="tui-rolling-search">
				<block v-if="diyitem.base.is_location == '1'">
					<tui-icon @tap="onChangePosition" name="position-fill" color="#333" :size="30" unit="rpx">
					</tui-icon>
					<view @tap="onChangePosition" class="tui-city-name">{{ cityName }}</view>
					<tui-icon @tap="onChangePosition" name="turningdown" :size="32" unit="rpx">
					</tui-icon>
					<view class="tui-city-line">|</view>
				</block>
				<tui-icon name="search-2" :size="32" unit="rpx"></tui-icon>
				<swiper @tap="search" vertical autoplay circular interval="8000" class="tui-swiper">
					<block v-if="diyitem.params.placeholder">
						<swiper-item class="tui-swiper-item">
							<view class="tui-hot-item">{{diyitem.params.placeholder}}</view>
						</swiper-item>
					</block>
					<block v-else>
						<swiper-item v-for="(item, index) in diyitem.params.hotkey" :key="index"
							class="tui-swiper-item">
							<view class="tui-hot-item">{{ item }}</view>
						</swiper-item>
					</block>
				</swiper>
			</view>
			<view class="hot-keywords">
				<view v-if="diyitem.base.is_hotkey == '1'" class="tui-hot-search"
					:style="{ color: diyitem.base.hotkeytxtColor }">
					<view>热搜</view>
					<block v-for="(item, index) in diyitem.params.hotkey" :key="index">
						<view class="tui-hot-tag" :data-key="item" @tap="more">{{ item }}</view>
					</block>
				</view>
				<view v-else class="tui-not-hot-search">
				</view>
			</view>
		</view>
	</view>
</template>

<script>
	export default {
		name: 'search',
		props: {
			diyitem: {
				type: Object,
				default () {
					return {};
				}
			},
			cityName: {
				type: String,
				default: ''
			},
			opacity: {
				type: Number,
				default: 1
			}
		},
		computed: {

		},
		data() {
			return {};
		},
		methods: {
			onChangePosition() {
				this.$emit("onChangePosition", {})
			},
			search: function() {
				this.sam.navigateTo('/pages/common/search/search');
			},
			more: function(e) {
				let key = e.currentTarget.dataset.key || '';
				this.sam.navigateTo('/pages/goodsList/goodsList?keyword=' + key);
			},
		}
	};
</script>
<style>
	@import './diyapge.css';
	.diy-search {
		width: 100%;
		padding-top: 10rpx;
		padding-left: 20rpx;
		padding-right: 20rpx;
		box-sizing: border-box;
		align-items: center;
	}
	
	.tui-rolling-search {
		width: 100%;
		height: 60rpx;
		border-radius: 35rpx;
		padding: 0 40rpx 0 30rpx;
		box-sizing: border-box;
		background-color: #fff;
		display: flex;
		align-items: center;
		flex-wrap: nowrap;
		color: #999;
	}
	
	.tui-category {
		font-size: 24rpx;
		color: #fff;
		display: flex;
		align-items: center;
		justify-content: center;
		flex-direction: column;
		margin: 0;
		margin-right: 22rpx;
		flex-shrink: 0;
	}
	
	.tui-category-scale {
		transform: scale(0.7);
		line-height: 24rpx;
	}
	
	.tui-icon-category {
		line-height: 20px !important;
		margin-bottom: 0 !important;
	}
	
	.tui-swiper {
		font-size: 26rpx;
		height: 60rpx;
		flex: 1;
		padding-left: 12rpx;
	}
	
	.tui-swiper-item {
		display: flex;
		align-items: center;
	}
	
	.tui-hot-item {
		line-height: 26rpx;
		white-space: nowrap;
		overflow: hidden;
		text-overflow: ellipsis;
	}
	
	.tui-city-name {
		padding-left: 6rpx;
		padding-right: 0rpx;
		color: #333;
		font-size: 24rpx;
		line-height: 24rpx;
	}
	
	.tui-city-line {
		color: #d3d3d3;
		padding-left: 16rpx;
		padding-right: 20rpx;
		font-size: 24rpx;
		line-height: 24rpx;
	}
	
	.hot-keywords {
		z-index: 10;
	}
	
	.tui-hot-search {
		color: #fff;
		font-size: 26rpx;
		display: flex;
		align-items: center;
		justify-content: space-between;
		padding-top: 12rpx;
		padding-left: 8rpx;
		padding-right: 8rpx;
		padding-bottom: 12rpx;
		box-sizing: border-box;
		position: relative;
		z-index: 2;
	}
	
	.tui-not-hot-search {
		height: 20rpx;
	}
	
	.tui-hot-tag {
		display: flex;
		align-items: center;
		justify-content: center;
		line-height: 22rpx;
	}
	.tui-group-name {
		width: 100%;
		font-size: 34rpx;
		line-height: 34rpx;
		font-weight: bold;
		text-align: center;
		padding: 30rpx 0 20rpx;
		display: flex;
		align-items: center;
		justify-content: space-between;
		color: #333;
	}
	
</style>