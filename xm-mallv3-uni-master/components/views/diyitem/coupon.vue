<template>
	<view class="diy-coupon" :style="diyitem.base.bgstyle">
		<scroll-view scroll-x>
			<view class="coupon-wrapper" v-for="(dataItem, index) in diyitem.list" :key="index">
				<view :class="['coupon-item', 'color__' + dataItem.color]">
					<i class="before" :style="{ 'background': diyitem.base.bc }"></i>
					<view class="left-content dis-flex flex-dir-column flex-x-center flex-y-center">
						<view class="content-top">
							<block v-if="dataItem.coupon_type == 10">
								<text class="f-30">￥</text>
								<text class="price">{{ dataItem.reduce_price }}</text>
							</block>
							<text class="price" v-if="dataItem.coupon_type == 20">{{dataItem.discount }}折</text>
						</view>
						<view class="content-bottom">
							<text class="f-24">满{{ dataItem.min_price }}元可用</text>
						</view>
					</view>
					<view class="right-receive dis-flex flex-x-center flex-y-center">
						<view v-if="dataItem.state.value" :data-itemindex="itemindex"
							:data-index="dataItem.coupon_id" :data-state="dataItem.state.value"
							:data-coupon-id="dataItem.coupon_id" @tap="receiveTap"
							class="dis-flex flex-dir-column">
							<text>立即</text>
							<text>领取</text>
						</view>
						<view v-else class="state">
							<text>{{ dataItem.state.text }}</text>
						</view>
					</view>
				</view>
			</view>
		</scroll-view>
	</view>
</template>

<script>
	export default {
		name: 'coupon',
		props: {
			diyitem: {
				type: Object,
				default () {
					return {};
				}
			},
			itemindex: {
				type: Number,
				default: 0
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
			},
			/**
			 * 领取优惠券
			 */
			receiveTap: function(e) {
				this.$emit("receiveTap", e)
			},
		}
	};
</script>
<style>
	@import './diyapge.css';
</style>