<template>
	<view class="container">
		<view class="content">
			<view class="col" v-for="(item,index) in list" :key="index" @click="play(index+1,index)">
				<view class="videoimgbox">
					<view v-if="item.videotype==1">
						<video v-if="currentId == (index+1)"
							style="width: 400rpx;height: 708rpx; border-radius: 10rpx; display: block;" autoplay="true"
							:id="'video'+(index+1)" :src="item.videourl"></video>
						<block v-else>
							<video style="width: 400rpx;height: 708rpx; border-radius: 10rpx; display: block;"
								:src="item.videourl"></video>
						</block>
					</view>
					<view class="imgbox">
						<navigator hover-class="none"
							:url="item.id > 0 ? '/pages/goodsDetail/goodsDetail?id=' + item.id : ''">
						<view style="padding-bottom: 23rpx;">
							<image v-if="item.images[0].image" :src="item.images[0].image" class="goods-image"></image>
						</view>
						<view style="padding-bottom: 23rpx;">
							<image v-if="item.images[1].image" :src="item.images[1].image" class="goods-image"></image>
						</view>
						<view>
							<image v-if="item.images[2].image" :src="item.images[2].image" class="goods-image"></image>
						</view>
						</navigator>
					</view>
				</view>
				<view style="padding-top: 30rpx;">
						<navigator hover-class="none"
							:url="item.id > 0 ? '/pages/goodsDetail/goodsDetail?id=' + item.id : ''"><button style="background-color: #ed6980;color: #ffffff;">去看看</button></navigator>
				</view>
				<tui-tabbar :current="current">
				</tui-tabbar>
			</view>
		</view>
		<!--加载loadding-->
		<tui-loadmore v-if="loadding" :index="3" type="red"></tui-loadmore>
		<tui-nomore v-if="!pullUpOn" backgroundColor="#f7f7f7"></tui-nomore>
		<!--加载loadding-->
	</view>
</template>

<script>
	export default {
		data() {
			return {
				list: [],
				where: {
					page: 1,
					limit: 10,
				},
				current: '',
				currentId: 0,
				scrollH: 0,
				scrollTop: 0,
				height: 0,
				windowHeight: 0,
				loadding: false,
				pullUpOn: true
			}
		},
		onLoad() {
			let that = this
			// #ifdef MP-WEIXIN
			this.current = "/" + this.__route__;
			// #endif
			//#ifdef H5
			this.current = this.$route.path;
			//#endif
			uni.setNavigationBarTitle({
				title: '短视频'
			});
			uni.setNavigationBarColor({
				frontColor: '#ffffff',
				backgroundColor: '#ed6980',
				animation: { //动画效果
					duration: 400,
					timingFunc: 'easeIn'
				}
			});
			this.getList()
			// 获取可视区域高度
			uni.getSystemInfo({
				success: function(res) {
					that.windowHeight = res.windowHeight
				}
			})
			this.play(1, 0);
		},
		onPageScroll(res) {
			// 获取滚动距离
			this.scrollH = res.scrollTop
			// 判断元素是否已经出了可视区
			if (this.scrollH > this.scrollTop) {
				const e = uni.createVideoContext("video" + this.currentId, this);
				e.pause();
				this.play(this.currentId + 1, this.currentId);
			}
			if (this.scrollH + this.windowHeight < this.scrollTop) {
				const e = uni.createVideoContext("video" + this.currentId, this);
				e.pause();
				this.play(this.currentId - 1, this.currentId - 2);
			}
		},
		onHide() {
			this.currentId = 0
		},
		methods: {
			play(id, i) {
				this.currentId = id
				// 获取当前播放视频 元素距离顶部的高度
				if (this.height == 0) {
					uni.createSelectorQuery().select('.col').boundingClientRect((res) => {
						this.height = res.height
						this.scrollTop = res.height * (i + .5)
					}).exec()
				} else {
					this.scrollTop = this.height * (i + .5)
				}
			},
			getList(isPage) {
				var that = this;
				if (that.loadend) return;
				if (that.loading) return;

				if (isPage === true) {
					that.list = [];
					that.where.page =1;
				}

				this.$request.post('goods.video', that.where).then(res => {
					if (res.errno == 0) {
						that.list = that.list.concat(res.data.data);
						that.loadend = that.list.length < that.where.limit;
						that.loading = false;
						that.where.page = that.where.page + 1

					}
				})
			}
		},
		/**
		 * 页面相关事件处理函数--监听用户下拉动作
		 */
		onPullDownRefresh: function() {
			this.where.page = 1;
			this.loadend = false;
			this.list = [];
			this.getList();
			uni.stopPullDownRefresh();
		},
		/**
		 * 页面上拉触底事件的处理函数
		 */
		onReachBottom: function() {
			this.getList();
		}
	}
</script>

<style lang="scss">
	page {
		background-color: #ed6980;
	}
	.container {
		padding-bottom: 168rpx;
	}

	.content {
		padding: 30rpx;
	}

	.col {
		overflow: hidden;
		position: relative;
		overflow: hidden;
		padding: 25rpx;
		background-color: #ffffff;
		border-radius: 20rpx;
		margin-bottom: 30rpx;
		.videoimgbox {
			display: flex;
			flex-flow: row;
			.imgbox {
				margin-left: 25rpx;
				display: flex;
				flex-flow: column;
				.goods-image {
					border-radius: 10rpx;
					width: 217rpx;
					height: 217rpx;
				}
			}
		}

	}
</style>
