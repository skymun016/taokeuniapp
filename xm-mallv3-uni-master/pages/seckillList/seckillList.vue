<template>
	<view class="container">
		<view class="tui-bg__box">
			<image src="../../static/images/bg_seckill.png" class="tui-bg__img" mode="widthFix" :style="{ opacity: opacity }"></image>
		</view>
		<view class="tui-header__bg">
			<image src="../../static/images/bg_seckill.png" class="tui-bg__img" mode="widthFix"></image>
			<scroll-view class="tui-time-slot" scroll-x>
				<view class="tui-time__list" :class="{ 'tui-flex__between': timeSlot.length < 6 }">
					<view class="tui-time__item" :class="[timeSlot.length < 6 ? 'tui-flex__1' : 'tui-width__min', index == currentTab ? 'tui-time__active' : '']"
					 v-for="(item, index) in timeSlot" :key="index" @tap="changeTabs(index)">
						<view class="tui-time">{{ item.time }}</view>
						<view class="tui-status">{{ item.statusText }}</view>
					</view>
				</view>
			</scroll-view>
		</view>
		<view class="tui-body">
			<view class="tui-status__box">
				<view class="tui-full__width">
					<tui-divider gradual width="80%" backgroundColor="#fff" :height="34">
						<view class="tui-divider__status">
							<image src="../../static/images/img_seckill.png" mode="widthFix"></image>
							<text class="tui-color__red">{{ time }}</text>
							<text>{{ status | getStatusText }}</text>
						</view>
					</tui-divider>
				</view>
				<view class="tui-countdown__box" v-if="status > 1">
					<text>距离{{ status == 2 ? '结束还剩' : '开始还有' }}</text>
					<tui-countdown :time="3880" backgroundColor="#4D4D4D" color="#fff"></tui-countdown>
				</view>
			</view>

			<view class="tui-list__goods">
				<view class="tui-goods__left" :class="{ 'tui-full__width': status == 3 }">
					<block v-for="(item, index) in goodsList" :key="index">
						<t-goods-item v-if="index % 2 == 0 || status == 3" :item="item" :status="status" :isList="status == 3"></t-goods-item>
					</block>
				</view>
				<view class="tui-goods__right" v-if="status !== 3">
					<block v-for="(item, index) in goodsList" :key="index">
						<t-goods-item v-if="index % 2 !== 0" :item="item" :status="status" :isList="status == 3"></t-goods-item>
					</block>
				</view>
			</view>
		</view>
	</view>
</template>

<script>
	import tGoodsItem from '@/components/views/t-goods-item/t-goods-item';
	export default {
		components: {
			tGoodsItem
		},
		data() {
			return {
				//status：1-已结束，2-正在进行，3-即将开抢
				timeSlot: [{
						time: '08:00',
						status: 1,
						statusText: '已结束'
					},
					{
						time: '10:00',
						status: 2,
						statusText: '正在疯抢'
					},
					{
						time: '12:00',
						status: 3,
						statusText: '即将开抢'
					},
					{
						time: '14:00',
						status: 3,
						statusText: '即将开抢'
					},
					{
						time: '16:00',
						status: 3,
						statusText: '即将开抢'
					}
				],
				goodsList:[],
				where: {
					sid: 0,
					keyword: '',
					priceOrder: '',
					salesOrder: '',
					news: 0,
					page: 1,
					limit: 10,
					categoryId: 0,
				},
				currentTab: 0,
				//status：1-已结束，2-正在进行，3-即将开抢
				status: 2,
				time: '08:00',
				opacity: 1
			};
		},
		onLoad(options) {
			//展示优先：正在进行，即将开抢，已结束
			for (let i = 0, len = this.timeSlot.length; i < len; i++) {
				let item = this.timeSlot[i];
				if (item.status !== 1) {
					this.currentTab = i;
					break;
				}
			}
			this.get_product_list();
		},
		filters: {
			getStatusText(status) {
				let text = ['活动已结束', '正在疯抢', '即将开抢'][status - 1];
				return text;
			}
		},
		methods: {
			get_product_list: function(isPage) {
			
				var that = this;
				console.log(this.where);
				if (that.loadend) return;
				if (that.loading) return;
			
				if (isPage === true) {
					that.goodsList = [];
					that.where.page =1;
				}
			
				this.$request.post('miaoshagoods', that.where).then(res => {
					if (res.errno == 0) {
						that.goodsList = that.goodsList.concat(res.data.data);
						that.loadend = that.goodsList.length < that.where.limit;
						that.loading = false;
						that.where.page = that.where.page + 1
			
					}
				})
			},
			changeTabs(index) {
				this.currentTab = index;
				let item = this.timeSlot[index];
				this.status = item.status;
				this.time = item.time;
			}
		},
		onPageScroll(e) {
			let scrollTop = e.scrollTop;
			if (scrollTop <= 2) {
				this.opacity = 1;
			} else {
				if (this.opacity <= 0) return;
				this.opacity = 1 - scrollTop / 40;
			}
		},
		/**
		 * 页面相关事件处理函数--监听用户下拉动作
		 */
		onPullDownRefresh: function() {			
			this.where.page = 1;
			this.loadend = false;
			this.goodsList = [];
			this.get_product_list();
			uni.stopPullDownRefresh();
		},
		/**
		 * 页面上拉触底事件的处理函数
		 */
		onReachBottom: function() {
			this.get_product_list();
		}
	};
</script>

<style>
	.tui-bg__box {
		width: 100%;
		height: 210rpx;
		position: fixed;
		left: 0;
		top: 0;
		z-index: 1;
	}

	.tui-header__bg {
		width: 100%;
		height: 120rpx;
		position: fixed;
		left: 0;
		top: 0;
		z-index: 3;
		overflow: hidden;
	}

	.tui-bg__img {
		width: 100%;
		height: 210rpx;
		display: block;
		transition: opacity 0.1s linear;
	}

	.tui-body {
		width: 100%;
		position: relative;
		margin-top: 120rpx;
		z-index: 2;
		padding: 0 25rpx;
		box-sizing: border-box;
	}

	.tui-time-slot {
		width: 100%;
		height: 120rpx;
		position: absolute;
		left: 0;
		top: 0;
	}

	.tui-time__list {
		min-width: 100%;
		height: 120rpx;
		display: flex;
		align-items: center;
	}

	.tui-flex__between {
		justify-content: space-between;
	}

	.tui-time__item {
		flex-shrink: 0;
		display: flex;
		align-items: center;
		flex-direction: column;
		justify-content: center;
		color: #ffb2b2;
	}

	.tui-flex__1 {
		flex: 1 !important;
	}

	.tui-width__min {
		min-width: 150rpx;
	}

	.tui-time {
		font-size: 32rpx;
		line-height: 32rpx;
		font-weight: bold;
	}

	.tui-status {
		font-size: 24rpx;
		line-height: 24rpx;
		font-weight: 500;
		padding-top: 16rpx;
	}

	.tui-time__active .tui-time {
		color: #fff;
		font-size: 36rpx;
		line-height: 36rpx;
	}

	.tui-time__active .tui-status {
		color: #fff;
		font-size: 28rpx;
		line-height: 28rpx;
		font-weight: bold;
	}

	.tui-status__box {
		width: 100%;
		height: 146rpx;
		background: #fff;
		border-radius: 20rpx;
		box-shadow: 0 3rpx 20rpx rgba(183, 183, 183, 0.1);
		display: flex;
		align-items: center;
		justify-content: center;
		flex-direction: column;
	}

	.tui-full__width {
		width: 100%;
	}

	.tui-divider__status {
		display: flex;
		align-items: center;
		font-size: 28rpx;
		color: #333;
		font-weight: bold;
	}

	.tui-divider__status image {
		width: 30rpx;
		height: 30rpx;
		margin-right: 10rpx;
		flex-shrink: 0;
	}

	.tui-color__red {
		color: #eb0909;
		padding-right: 6rpx;
		font-size: 32rpx;
		font-weight: 500;
	}

	.tui-countdown__box {
		display: flex;
		align-items: center;
		justify-content: center;
		color: #333;
		font-size: 24rpx;
		font-weight: 400;
		margin-top: 16rpx;
	}

	.tui-countdown__box text {
		padding-right: 12rpx;
	}

	/*======商品列表 start=======*/
	.tui-list__goods {
		width: 100%;
		display: flex;
		justify-content: space-between;
		flex-wrap: wrap;
		margin-top: 20rpx;
		padding-bottom: 30rpx;
	}

	.tui-goods__left,
	.tui-goods__right {
		width: 49%;
	}

	.tui-full__width {
		width: 100% !important;
	}

	/*======商品列表 end=======*/
</style>
