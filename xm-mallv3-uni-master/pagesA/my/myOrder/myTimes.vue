<template>
	<view class="container">
		<view class="tui-bankcard__list">
			<view class="tui-bankcard__item" @click="orderDetail(item.id,item.is_times)" :class="'cardbg'+item.styleno" v-for="(item,orderIndex) in orderList"
				:key="orderIndex">
				<view class="tui-card__info">
					<block v-for="(goodsitem,index) in item.goodsMap" :key="index">
						<view class="tui-logo__box">
							<image class="tui-logo" :src="goodsitem.image"></image>
						</view>
						<view class="tui-name__box">
							<view class="tui-name">{{goodsitem.name}}</view>
							<view v-if="goodsitem.label" class="tui-desc">{{goodsitem.label}}</view>
							<view v-if="index ==0 && item.is_times==1" class="tui-desc">次卡：{{item.OrderCard.timesmum}}次,剩余：{{item.remain}}次</view>
							<view v-if="index ==0 && item.is_times==3" class="tui-desc">面值：{{item.OrderCard.facevalue}},余额：{{item.OrderCard.balance}}</view>
							<view v-if="index ==0 && item.timeslabel" class="tui-desc">{{item.timeslabel}}
							</view>
						</view>
					</block>
					<view class="tui-card__no">**** {{item.minialias}}</view>
				</view>
			</view>
		</view>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				tabBar: [],
				statusType: [],
				ptype: 2,
				currentType: 0,
				currentTab: 0,
				pageIndex: 1,
				loadding: false,
				pullUpOn: true,
				scrollTop: 0,
				orderList: [],
				where: {
					status: 0,
					ptype: 0,
					is_times: 1,
					keyword: '',
					page: 1,
					limit: 10,
				},
			}
		},
		onLoad: function(e) {
			let _this = this
		},
		onShow: function(e) {
			this.getorderlist(true);
		},
		methods: {
			getorderlist: function(isPage) {
				var _this = this
				console.log(_this.loadend);

				if (_this.loading) return;

				if (isPage === true) {
					_this.where.page = 1;
					_this.orderList = [];
				} else {
					if (_this.loadend) return;
				}

				_this.where.samkey = (new Date()).valueOf();
				_this.where.ptype = _this.ptype;
				_this.where.status = _this.currentType;
				//console.log(_this.where);
				_this.$request.post('order.myorder', _this.where).then(res => {
					if (res.errno == 0) {
						_this.orderList = _this.orderList.concat(res.data.data);
						_this.where.page = _this.where.page + 1
						_this.loadend = _this.orderList.length < _this.where.limit;
					}
					_this.loading = false;
				})
			},
			orderDetail: function(orderid,is_times) {
				if(is_times!=3){
					var url = '/pagesA/my/myOrder/myTimesDetail?id=' + orderid;
					uni.navigateTo({
						url: url
					})
				}
			}
		},
		/**
		 * 页面相关事件处理函数--监听用户下拉动作
		 */
		onPullDownRefresh: function() {
			this.where.page = 1;
			this.loadend = false;
			this.orderList = [];
			this.getorderlist();
			setTimeout(() => {
				uni.stopPullDownRefresh()
			}, 200);
		},
		/**
		 * 页面上拉触底事件的处理函数
		 */
		onReachBottom() {
			//只是测试效果，逻辑以实际数据为准
			this.loadding = true
			this.pullUpOn = true
			this.getorderlist();
			setTimeout(() => {
				this.loadding = false
				this.pullUpOn = false
			}, 1000)
		},
		onPageScroll(e) {
			this.scrollTop = e.scrollTop;
		}
	}
</script>

<style>
	.container {
		width: 100%;
		padding: 30rpx 40rpx 40rpx;
		box-sizing: border-box;
	}

	.tui-header {
		width: 100%;
		padding: 36rpx 0;
		display: flex;
		align-items: center;
		justify-content: space-between;
	}

	.tui-title {
		font-size: 30rpx;
		font-weight: bold;
	}

	.tui-total {
		font-size: 24rpx;
		color: #999;
	}

	.tui-bankcard__item {
		width: 100%;
		height: 240rpx;
		padding: 30rpx;
		box-sizing: border-box;
		border-radius: 16rpx;
		margin-bottom: 20rpx;
	}

	.tui-logo__box {
		width: 80rpx;
		height: 80rpx;
		border-radius: 50%;
		background-color: #fff;
		display: flex;
		align-items: center;
		justify-content: center;
		margin-right: 20rpx;
	}

	.tui-logo {
		width: 52rpx;
		height: 52rpx;
	}

	.tui-card__info {
		display: flex;
		align-items: center;
		color: #fff;
	}

	.tui-name {
		font-size: 30rpx;
		font-weight: 500;
	}

	.tui-desc {
		font-size: 24rpx;
		opacity: 0.7;
	}

	.tui-card__no {
		margin-left: auto;
	}

	.cardbg0 {
		background: linear-gradient(to right, #FEAD4B, #FF9225);
	}
	.cardbg1 {
		background: linear-gradient(to right, #2C85D5, #2D66D1);
	}
	.cardbg2 {
		background: linear-gradient(to right, #2C87D6, #2D69D0);
	}
	.cardbg3 {
		background: linear-gradient(to right, #01ADA3, #0291A9);
	}
	
	.cardbg4 {
		background: linear-gradient(to right, #FF6F64, #FE5762);
	}
	
	.cardbg5 {
		background: linear-gradient(to right, #FF7065, #FD4754);
	}

	.tui-ping_an {
		background: linear-gradient(to right, #FEAD4B, #FF9225);
	}

	.tui-jian_she {
		background: linear-gradient(to right, #2C85D5, #2D66D1);
	}

	.tui-min_sheng {
		background: linear-gradient(to right, #2C87D6, #2D69D0);
	}

	.tui-nong_ye {
		background: linear-gradient(to right, #01ADA3, #0291A9);
	}

	.tui-zhao_shang {
		background: linear-gradient(to right, #FF6F64, #FE5762);
	}

	.tui-zhong_xin {
		background: linear-gradient(to right, #FF7065, #FD4754);
	}
</style>