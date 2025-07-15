<template>
	<view class="container">
		<!--header-->
		<!-- #ifdef MP-WEIXIN || APP-PLUS -->
		<view class="tui-header-box" :style="pagebase.base ? pagebase.base.titleBackground.bgstyle : ''">
			<view :style="{ height: height + 'px' }">
				<view class="tui-header"
					:style="{ 'color': pagebase.base ? pagebase.base.titleTextColor : '', paddingTop: top + 'px' }">
					<text v-if="page_title">{{ page_title }}</text>
				</view>
			</view>
		</view>
		<view class="topheight" :style="{ height: height + 'px' }">.</view>
		<!-- #endif -->
		<!--header-->
		<Pengpai-FadeInOut v-if="config.is_broadcast == 1" :top="198" :loop="true" :info="item"
			v-for="(item, index) in list" :key="index">
		</Pengpai-FadeInOut>
		<view>
			<view class="diybox" :style="pagebase.base ? pagebase.base.bgstyle : ''">
				<view v-if="configtuanzhang.tuanzhang_open==1 && housingName"
					:style="pagebase.base ? pagebase.base.titleBackground.bgstyle : ''">
					<view @tap="onChangehousingestate" class="housingestate"
						:style="{ 'color': pagebase.base ? pagebase.base.titleTextColor : ''}">
						{{ housingName }} 站长：{{tuanzhangName}}
						<tui-icon name="arrowright" color="#fff" :size="30" unit="rpx">
						</tui-icon>
					</view>
				</view>
				<block v-for="(diyitem, itemindex) in modulelist" :key="itemindex">
					<!-- banner -->
					<block v-if="diyitem.type === 'banner'">
						<view class="bannerbox" :style="diyitem.base.bgstyle">
							<view class="tui-banner-bg"
								:style="{ height:  bannerHeight[itemindex].swiperHeight + 'px' }">
								<!--banner-->
								<view class="tui-banner-box">
									<swiper :style="{ height:  bannerHeight[itemindex].swiperHeight + 'px' }"
										:indicator-dots="true" :autoplay="true" :interval="5000" :duration="150"
										class="tui-banner-swiper" :circular="true"
										indicator-color="rgba(255, 255, 255, 0.8)" indicator-active-color="#fff">
										<swiper-item v-for="(banner, index) in diyitem.list" :key="index">
											<image @tap="navigateTo" :data-url="banner.link" :src="banner.img"
												class="tui-slide-image" mode="widthFix" :data-itemindex="itemindex"
												@load="setswiperHeight" />
										</swiper-item>
									</swiper>
								</view>
							</view>
							<view class="bannerbottom"></view>
						</view>
					</block>
					<search v-if="diyitem.type === 'search'" :diyitem="diyitem" :cityName="cityName" :opacity="opacity"
						@onChangePosition="onSelectoperatingcity" />
					<!-- 导航组 -->
					<navbar v-if="diyitem.type === 'navBar'" :diyitem="diyitem" />
					<!-- 单图组 -->
					<imagesingle v-if="diyitem.type === 'imageSingle'" :diyitem="diyitem" />
					<!-- 多图组 -->
					<duo v-if="diyitem.type === 'duo'" :diyitem="diyitem" />
					<!-- 图片橱窗 -->
					<window v-if="diyitem.type === 'window'" :diyitem="diyitem" />
					<!-- 图文组 -->
					<tuwen v-if="diyitem.type === 'tuwen'" :diyitem="diyitem" />
					<!-- 视频组 -->
					<diyvideo v-if="diyitem.type === 'video'" :diyitem="diyitem" />
					<!-- 公告组 -->
					<notice v-if="diyitem.type === 'notice'" :diyitem="diyitem" />
					<!-- 空白组 -->
					<blank v-if="diyitem.type === 'blank'" :diyitem="diyitem" />
					<!-- 辅助线 -->
					<guide v-if="diyitem.type === 'guide'" :diyitem="diyitem" />
					<!-- 富文本 -->
					<richtext v-if="diyitem.type === 'richText'" :diyitem="diyitem" />
					<!--秒杀-->
					<miaosha v-if="diyitem.type === 'miaosha'" :diyitem="diyitem" :pagestyleconfig="pagestyleconfig" />
					<!--购物卡-->
					<goodscard v-if="diyitem.type === 'goodscard'" :diyitem="diyitem"
						:pagestyleconfig="pagestyleconfig" />
					<!--超值拼团-->
					<tuan v-if="diyitem.type === 'tuan'" :diyitem="diyitem" :pagestyleconfig="pagestyleconfig" />
					<!-- 商品组 -->
					<goods v-if="diyitem.type === 'goods'" :diyitem="diyitem" :pagestyleconfig="pagestyleconfig" />
					<!-- 师傅组 -->
					<technical v-if="diyitem.type === 'technical'" :diyitem="diyitem" />
					<!-- 商家组 -->
					<store v-if="diyitem.type === 'store'" :diyitem="diyitem" />
					<!-- 优惠券组 -->
					<coupon v-if="diyitem.type === 'coupon'" :diyitem="diyitem" :itemindex="itemindex"
						@receiveTap="receiveTap" />
					<!--排行榜-->
					<block v-if="diyitem.type === 'ranking'">
						<view class="tui-product-box">
							<view class="tui-block__box" :style="diyitem.base.bgstyle">
								<view class="tui-group-name" @tap="more">
									<view>
										<text>排行榜</text>
										<text class="tui-sub__desc">大家都在买</text>
									</view>
									<view class="tui-more__box">
										<text>更多</text>
										<tui-icon name="arrowright" :size="36" unit="rpx" color="#999"></tui-icon>
									</view>
								</view>
								<view class="tui-new-box">
									<view class="tui-new-item" :class="[index != 0 && index != 1 ? 'tui-new-mtop' : '']"
										v-for="(item, index) in newProduct" :key="index" @tap="detail">
										<image
											:src="'/static/images/mall/new/' + (item.type == 1 ? 'new' : 'discount') + '.png'"
											class="tui-new-label" v-if="item.isLabel"></image>
										<view class="tui-title-box">
											<view class="tui-new-title">{{ item.name }}</view>
											<view class="tui-new-price">
												<text class="tui-new-present">￥{{ item.present }}</text>
												<text class="tui-new-original">￥{{ item.original }}</text>
											</view>
										</view>
										<image :src="'/static/images/mall/new/' + item.pic" class="tui-new-img"></image>
									</view>
								</view>
							</view>
						</view>
					</block>
				</block>
			</view>
		</view>
		<!--加载loadding-->
		<tui-loadmore v-if="loadding" :index="3" type="red"></tui-loadmore>
		<!-- <tui-nomore v-if="!pullUpOn"></tui-nomore> -->
		<!--加载loadding-->
		<view @tap="tokefuTel">
			<view class="img-tel-style zindex100 yc"></view>
			<image class="img-tel-style" src="/static/images/kefutel.png"></image>
		</view>
		<view>
			<!-- #ifdef MP-WEIXIN -->
			<button v-if="configkefu.minionline == 1 || configkefu.minionline == 3" @click="toim"
				class="img-plus-style zindex100 yc aaa"></button>
			<button v-else-if="configkefu.minionline == 2" @click="toimwebview(configkefu.kefuurl)"
				class="img-plus-style zindex100 yc aaa"></button>
			<button v-else open-type="contact" class="img-plus-style zindex100 yc"></button>
			<!-- #endif -->
			<!-- #ifndef MP-WEIXIN -->
			<button v-if="configkefu.minionline == 1 || configkefu.minionline == 2 || configkefu.minionline == 3"
				@click="toim" class="img-plus-style zindex100 yc aaa"></button>
			<!-- #endif -->
			<image src="/static/images/kefulink.png" class="img-plus-style"></image>
		</view>
		<tui-modal custom :show="modalShow" backgroundColor="transparent" padding="0" @cancel="hideModal">
			<view class="tui-poster__box" :style="{ marginTop: height + 'px' }">
				<image src="/static/images/mall/icon_popup_closed.png" class="tui-close__img" @tap.stop="hideModal">
				</image>
				<image :show-menu-by-longpress="true" :src="config.kefuqrcode" class="tui-poster__img"></image>
			</view>
		</tui-modal>
		<tui-modal :show="selectOperatingcityShow" @cancel="hideOperatingcityModal" :custom="true" fadeIn>
			<image src="/static/images/mall/icon_popup_closed.png" class="tui-close__img"
				@tap.stop="hideOperatingcityModal">
			</image>
			<view class="tui-modal-custom">
				<view class="tui-prompt-title">您所在的城市还没开通运营</view>
				<view class="tui-modal__btn">
					<view class="tui-box">
						<button class="tui-btn-danger" @click="onSelectoperatingcity">选择附近的城市</button>
					</view>
					<view class="tui-box">
						<tui-button type="white" shape="circle" height="80rpx" :size="34"
							@click="onregoperatingcity">申请当地运营商</tui-button>
					</view>
				</view>
			</view>
		</tui-modal>
		<tui-modal :show="selecttuanzhangShow" @cancel="hidetuanzhangModal" :custom="true" fadeIn>
			<image src="/static/images/mall/icon_popup_closed.png" class="tui-close__img"
				@tap.stop="hidetuanzhangModal">
			</image>
			<view class="tui-modal-custom">
				<view class="tui-prompt-title">请选择社区服务点</view>
				<view class="tui-modal__btn">
					<view class="tui-box">
						<button class="tui-btn-danger" @click="onChangehousingestate">选择社区服务点</button>
					</view>
				</view>
			</view>
		</tui-modal>
		<view class="tui-safearea-bottom"></view>
		<tui-tabbar :current="current" :pageid="id">
		</tui-tabbar>
	</view>
</template>
<script>
	import uniNoticeBar from "@/components/uni-notice-bar/uni-notice-bar.vue"
	import PengpaiFadeInOut from "@/components/Pengpai-FadeInOut/Pengpai-FadeInOut.vue"
	import search from '@/components/views/diyitem/search.vue';
	import navbar from '@/components/views/diyitem/navbar.vue';
	import imagesingle from '@/components/views/diyitem/imagesingle.vue';
	import duo from '@/components/views/diyitem/duo.vue';
	import window from '@/components/views/diyitem/window.vue';
	import tuwen from '@/components/views/diyitem/tuwen.vue';
	import diyvideo from '@/components/views/diyitem/diyvideo.vue';
	import miaosha from '@/components/views/diyitem/miaosha.vue';
	import goodscard from '@/components/views/diyitem/goodscard.vue';
	import tuan from '@/components/views/diyitem/tuan.vue';
	import goods from '@/components/views/diyitem/goods.vue';
	import notice from '@/components/views/diyitem/notice.vue';
	import blank from '@/components/views/diyitem/blank.vue';
	import guide from '@/components/views/diyitem/guide.vue';
	import richtext from '@/components/views/diyitem/richtext.vue';
	import technical from '@/components/views/diyitem/technical.vue';
	import store from '@/components/views/diyitem/store.vue';
	import coupon from '@/components/views/diyitem/coupon.vue';

	export default {
		components: {
			uniNoticeBar,
			PengpaiFadeInOut,
			search,
			navbar,
			imagesingle,
			duo,
			window,
			tuwen,
			diyvideo,
			miaosha,
			goodscard,
			tuan,
			goods,
			notice,
			blank,
			guide,
			richtext,
			technical,
			store,
			coupon
		},
		data() {
			return {
				page_title: '',
				list: [],
				uid: '',
				config: {},
				configkefu: {},
				configtuanzhang: {},
				pagestyleconfig: {},
				is_submitaudit: 1,
				selecttuanzhangShow: false,
				selectOperatingcityShow: false,
				modalShow: false,
				height: 64, //header高度
				top: 26, //标题图标距离顶部距离
				scrollH: 0, //滚动总高度
				bannerHeight: [],
				windowWidth: 0,
				current: '',
				cityName: '',
				housingName: '',
				community_title: '',
				tuanzhangName: '',
				tuanzhangtouxiang: '',
				latitude: "",
				longitude: "",
				id: "0",
				//diy数据
				modulelist: [],
				pagebase: [],
				newProduct: [{
					name: '时尚舒适公主裙高街修身长裙',
					present: 198,
					original: 298,
					pic: '1.jpg',
					type: 1,
					isLabel: true
				}],
				pageIndex: 1,
				loadding: false,
				pullUpOn: true,
				opacity: 1,
				timer: null,
				num: 0,
				currentid: 0,
				cumulative: 0
			};
		},
		onLoad: function(e) {
			let _this = this
			let obj = {};
			// #ifdef MP-WEIXIN
			this.current = "/" + this.__route__;
			// #endif
			//#ifdef H5
			this.current = '/pages/index/index';
			//#endif
			uni.showLoading({
				title: '数据加载中'
			});

			if (uni.getStorageInfoSync().currentSize > 8000) {
				uni.clearStorageSync();
				console.log('清理本地数据缓存');
			}

			setTimeout(() => {
				uni.getSystemInfo({
					success: res => {
						//console.log(res);
						this.width = obj.left || res.windowWidth;
						this.height = obj.top ? obj.top + obj.height + 8 : res.statusBarHeight +
							44;
						this.top = obj.top ? obj.top + (obj.height - 32) / 2 : res
							.statusBarHeight + 6;
						this.scrollH = res.windowWidth;
						this.windowWidth = res.windowWidth;
					}
				});
			}, 0);

			if (e.jump) {
				uni.reLaunch({
					url: e.jump
				});
			}
			_this.$request.post('config', {
				mo: 'pagestyle'
			}).then(res => {
				if (res.errno == 0) {
					_this.pagestyleconfig = res.data
				}
			});

			//console.log(e);
			setTimeout(function() {
				uni.hideLoading();
			}, 2000);

			if (e.id) {
				_this.id = e.id;
			}
			if (!e.reid) {
				if (e && e.scene) {
					e.reid = e.scene;
				}
			}

			if (e.reid) {
				uni.setStorageSync('reid', e.reid)
			}
			_this.getdiypage();
			_this.sam.login().then(res => {
				//console.log(res);
				//console.log(e);
				if (res.uid > 0) {
					_this.uid = res.uid;
					if (uni.getStorageSync('reid')) {
						_this.$request.get('member.bindpid', {
							samkey: (new Date()).valueOf(),
							pid: uni.getStorageSync('reid')
						}).then(res => {
							if (res.errno == 0) {
								console.log('pid绑定成功');
							}
						})
					}
				}
			});
			setTimeout(() => {
				uni.getSystemInfo({
					success: res => {
						//console.log(res);
						this.hieghtS = res.windowHeight;
						this.windowWidth = res.windowWidth;
					}
				});
			}, 0);

			if (_this.$siteInfo.design_method == 3) {
				_this.getNewMessage();
				_this.timer = setInterval(() => {
					setTimeout(() => {
						_this.getNewMessage()
					}, 0)
				}, 5000)
			}

			if (_this.id > 0) {
				this.current = this.current + '?id=' + _this.id;
			}
			uni.setStorageSync('gotopage', this.current);
		},
		onShow() {
			let _this = this;
			//#ifdef H5  || APP-PLUS
			uni.getLocation({
				type: 'wgs84',
				success: function(res) {
					console.log(res);
				},
			})
			//#endif

			//只在app实时定位
			// #ifdef APP-PLUS
			uni.startLocationUpdate({
				success: res => console.log('开启小程序接收位置消息成功', res),
				fail: err => console.error('开启小程序接收位置消息失败：', err),
				complete: msg => console.log('调用开启小程序接收位置消息 API 完成')
			});
			// #endif
			_this.$request.post('Config.audit', {
				samkey: (new Date()).valueOf()
			}).then(res => {
				if (res.data.is_submitaudit != 1) {
					if (_this.$siteInfo.multiid == 1) {
						uni.reLaunch({
							url: '/pages/login/selectlogin'
						});
					}
				}
			});
			uni.setStorageSync('NewMessage', '1')
			this.sam.onShowlogin();

			//获取地置位置
			_this.sam.getCityPosition({
				is_index: 1
			}).then(res => {
				_this.getdiypage();
				_this.cityName = res.cityName;
				_this.housingName = res.housingName;
				_this.community_title = res.community_title;
				_this.tuanzhangName = res.tuanzhangName;
				_this.tuanzhangtouxiang = res.tuanzhangtouxiang;
				_this.tuanzhangtouxiang = res.tuanzhangtouxiang;
				console.log(res);
				if (!res.ocid && res.operatingcityis_limit == 1) {
					_this.selectOperatingcityShow = true;
				} else {
					_this.selectOperatingcityShow = false;
					if (_this.configtuanzhang.tuanzhang_open == 1) {
						if (res.he_id) {
							_this.selecttuanzhangShow = false;
						} else {
							_this.selecttuanzhangShow = true;
						}
					}
				}
			});

		},
		destroyed() {
			clearInterval(this.timer)
		},
		methods: {
			toimwebview(url) {
				this.tui.href("/pages/webview/h5?url=" + url)
			},
			toim() {
				if (this.configkefu.minionline == 2 && this.configkefu.kefuurl) {
					this.tui.href(this.configkefu.kefuurl);
				} else if (this.configkefu.minionline == 3) {
					var telstr = this.configkefu.kefutel;
					uni.makePhoneCall({
						phoneNumber: telstr
					});
				} else {
					this.tui.href("/pages/im/h5");
				}
			},
			getNewMessage: function() {
				let _this = this;
				if (uni.getStorageSync('NewMessage') == '1') {
					_this.$request.post('broadcast.index', {
						cumulative: _this.cumulative,
						currentid: _this.currentid,
					}).then(res => {
						if (res.errno == 0) {
							if (res.data) {
								if (_this.num > 3) {
									_this.list = [];
									_this.num = 0;
								}
								_this.currentid = res.data.id;
								//追加一条数据
								_this.list = _this.list.concat(res.data);
								_this.num++;
								_this.cumulative++;
							} else {
								_this.currentid = 0;
							}
						}
					})
				}
			},
			setswiperHeight(e) {
				console.log(e);
				this.bannerHeight = this.modulelist;
				this.bannerHeight[e.currentTarget.dataset.itemindex].swiperHeight = e.detail.height * ((this.windowWidth -
					20) / e.detail.width);
			},
			//获取后台数据
			getdiypage: function() {
				let _this = this;
				_this.$request.get('diypage.indexv2', {
					samkey: (new Date()).valueOf(),
					id: _this.id
				}).then(res => {
					if (res.errno == 0) {
						_this.config = res.data.config;
						if (res.data.config) {
							_this.configkefu = res.data.config.kefu || {};
						}
						if (res.data.config) {
							_this.configtuanzhang = res.data.config.tuanzhang || {};
						}
						if (_this.config.kefuqrcode && _this.is_submitaudit != 1) {
							_this.modalShow = true;
						}
						if (res.data.modulelist) {
							_this.modulelist = res.data.modulelist;
							_this.pagebase = res.data.pagebase;
							if (_this.bannerHeight.length == 0) {
								//console.log(_this.bannerHeight.length);
								_this.bannerHeight = _this.modulelist;
							}
							_this.page_title = res.data.pagebase.params.title;
							if (!_this.page_title) {
								_this.page_title = res.data.config.sys_title
							}

							uni.setNavigationBarTitle({
								title: _this.page_title
							});
							/*
							uni.setNavigationBarColor({
								frontColor: res.data.pagebase.base.titleTextColor,
								backgroundColor: res.data.pagebase.base.titleBackgroundColor,
								animation: { //动画效果
									duration: 400,
									timingFunc: 'easeIn'
								}
							});*/
						}
					}
				})
			},
			/**
			 * 领取优惠券
			 */
			receiveTap: function(e) {
				let _this = this,
					dataset = e.currentTarget.dataset;
				if (!dataset.state) {
					return false;
				}
				_this.$request.post('Couponreceive.fetch', {
					id: dataset.couponId
				}).then(res => {
					wx.showToast({
						title: res.message,
						icon: 'none'
					})
					_this.modulelist[dataset.itemindex].list[dataset.index].state = {
						value: 0,
						text: '已领取'
					};
				})
			},
			navigateTo: function(e) {
				this.sam.diynavigateTo(e);
			},
			coupon() {
				this.sam.navigateTo('/pages/coupon/coupon');
			},
			category: function() {
				this.sam.navigateTo('../category/category');
			},

			selectCity: function() {
				this.sam.navigateTo('/pages/selectCity/selectCity');
			},
			onChangehousingestate: function(e) {
				this.sam.navigateTo("/pages/housingestate/list");
			},
			onSelectoperatingcity: function(e) {
				this.sam.navigateTo("/pages/operatingcity/list");
			},
			onregoperatingcity: function(e) {
				this.sam.navigateTo("/pages/login/reg?ptype=operatingcity");
			},
			onChangePosition: function() {
				const _this = this;
				uni.chooseLocation({
					success(res) {
						_this.$request.post('operatingcity.getcity', {
							samkey: (new Date()).valueOf(),
							address: res.address,
							latitude: res.latitude,
							longitude: res.longitude
						}).then(res => {
							if (res.data) {
								_this.cityName = res.data.cityName;
								_this.community_title = res.data.community_title;
								_this.housingName = res.data.housingName;
								_this.tuanzhangName = res.data.tuanzhangName;
								_this.tuanzhangtouxiang = res.data.tuanzhangtouxiang;
							}
						});
					}
				});
			},

			tokefuTel: function(e) {
				//客服电话
				const _this = this;

				var telstr = _this.config.TELEPHONE;
				uni.makePhoneCall({
					phoneNumber: telstr
				});
			},

			hideModal() {
				this.modalShow = false;
			},
			hidetuanzhangModal() {
				this.selecttuanzhangShow = false;
			},
			hideOperatingcityModal() {
				this.selectOperatingcityShow = false;
			}


		},
		onPullDownRefresh: function() {
			this.getdiypage()
			uni.stopPullDownRefresh();
		},
		onReachBottom: function() {

		},
		onPageScroll(e) {
			// #ifdef APP-PLUS
			let scrollTop = e.scrollTop;
			if (scrollTop < 0) {
				if (this.opacity > 0)
					this.opacity = 1 - Math.abs(scrollTop) / 30;
			} else {
				this.opacity = 1
			}
			// #endif
		},
		//发送给朋友
		onShareAppMessage: function() {
			let _this = this;
			//console.log(_this.uid);
			let path = "/pages/index/index?reid=" + _this.uid;
			if (_this.id > 0) {
				path = path + '&id=' + _this.id;
			}
			return {
				title: _this.pagebase.params.share_title || "",
				path: path,
			};
		},

		onShareTimeline(res) { //分享到朋友圈
			return {}
		},
	};
</script>

<style>
	@import '@/components/views/diyitem/diyapge.css';
</style>