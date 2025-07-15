<template>
	<view>
		<!--header-->
		<!-- #ifndef H5-->
		<tui-navigation-bar dropDownHide splitLine @init="initNavigation" @change="opacityChange" :scrollTop="scrollTop"
			title="我的" backgroundColor="#fff" color="#333">
			<view class="tui-header-box" :style="{ marginTop: top + 'px' }"></view>
		</tui-navigation-bar>
		<!-- #endif -->
		<!--header-->
		<view :style="'background:'+pagestyleconfig.userstylebgcolor" class="tui-mybg-box">
			<image v-if="pagestyleconfig.userstylebgimg" :src="pagestyleconfig.userstylebgimg" class="tui-my-bg"
				mode="widthFix"></image>
			<image v-if="!pagestyleconfig.userstylebgimg && !pagestyleconfig.userstylebgcolor"
				src="https://daopic.samcms.com/xm_static/images/img_bg_3x.png" class="tui-my-bg" mode="widthFix"></image>
			<view class="tui-header-center">
				<image v-if="memberinfo.is_online"
					:src="memberinfo.userpic || '/static/images/my/mine_def_touxiang_3x.png'" class="tui-avatar"
					@tap="navigateTo('/pagesA/my/userInfo/userInfo')">
				</image>
				<image v-else src="/static/images/my/mine_def_touxiang_3x.png" class="tui-avatar"
					@tap="navigateTo('/pagesA/my/userInfo/userInfo')">
				</image>
				<view class="tui-info" v-if="memberinfo.is_online">
					<view class="tui-nickname">
						{{memberinfo.nickname}}
					</view>
					<view v-if="memberinfo.gname" class="tui-explain">{{memberinfo.gname}}</view>
				</view>
				<view class="tui-login" v-if="!memberinfo.is_online" @click="login">
					<text>登录/注册</text>
					<tui-icon name="arrowright" color="#fff" :size="36" unit="rpx"></tui-icon>
				</view>
				<view class="tui-btn-edit" v-if="memberinfo.is_online">
					<tui-button type="white" :plain="true" shape="circle" width="92rpx" height="40rpx" :size="22"
						@click="navigateTo('/pagesA/my/userInfo/userInfo?gotopage=/pagesA/my/userInfo/index')">编辑
					</tui-button>
				</view>
				<view class="tui-set-box" v-if="memberinfo.is_online">
					<view class="tui-icon-box tui-icon-message">
						<!--<tui-icon @tap="navigateTo('/pagesA/my/message/message')" name="message" color="#fff" :size="26"></tui-icon>
						<view v-if="memberinfo.is_online" class="tui-badge tui-badge-white">1</view>-->
					</view>
					<view class="tui-icon-box tui-icon-setup" @tap="navigateTo('/pagesA/my/set/set')">
						<tui-icon name="setup" color="#fff" :size="26"></tui-icon>
					</view>
				</view>

			</view>
			<view class="tui-header-btm">
				<view @click="recharge" class="tui-btm-item">
					<view class="tui-btm-num">{{memberinfo.balance}}</view>
					<view class="tui-btm-text">余额</view>
				</view>
				<view @tap="navigateTo('/pagesA/my/myCoupon/myCoupon')" class="tui-btm-item">
					<view class="tui-btm-num">
						<text>{{memberinfo.coupon}}</text>
						<view class="tui-badgewhite-dot"></view>
					</view>
					<view class="tui-btm-text">优惠券</view>
				</view>
				<view @tap="navigateTo('/pagesA/my/points/points')" class="tui-btm-item">
					<view class="tui-btm-num">
						<text>{{memberinfo.points}}</text>
						<view class="tui-badgewhite-dot"></view>
					</view>
					<view class="tui-btm-text">{{lang.points}}</view>
				</view>
				<view @tap="navigateTo('/pagesA/my/fx/index')" class="tui-btm-item">
					<view class="tui-btm-num">{{memberinfo.agent ? memberinfo.agent.income : ''}}</view>
					<view v-if="module==version3" class="tui-btm-text">佣金</view>
					<view v-if="module==version2" class="tui-btm-text">创业收入</view>
				</view>
			</view>
		</view>
		<view class="tui-content-box">
			<view v-if="is_submitaudit!=1 && memberconfig.is_vip_paid==1" class="vip-box" style="margin-bottom: 20rpx;">
				<tui-list-cell backgroundColor="#5c5c5c" :arrow="false" :hover="false" padding="0" unlined
					:lineLeft="false" @click="navigateTo('/pagesA/my/vip_paid/index')">
					<view class="vip-box-header">
						<view class="vip-box-title">{{memberinfo.gname ? memberinfo.gname : 'VIP会员卡'}}</view>
						<view class="tui-ml-auto">
							<button class="vip-box-but">{{memberinfo.gid ? '升级会员' : '立即开通'}}</button>
						</view>
					</view>
				</tui-list-cell>
			</view>
			<view v-if="module==version3" class="tui-box tui-order-box">
				<tui-list-cell :arrow="true" padding="0" unlined :lineLeft="false"
					@click="navigateTo('/pagesA/my/myOrder/myOrder?ptype=2')">
					<view class="tui-cell-header">
						<view class="tui-cell-title">我的订单</view>
						<view class="tui-cell-sub">全部订单</view>
					</view>
				</tui-list-cell>
				<view class="tui-order-list">
					<view class="tui-order-item" @tap="navigateTo('/pagesA/my/myOrder/myOrder?currentTab=1&ptype=2')">
						<view class="tui-icon-box">
							<image src="/static/images/mall/my/icon_daifukuan_3x.png" class="tui-order-icon"></image>
							<view v-if="serviceorder[1]>0" class="tui-badge tui-badge-red">{{serviceorder[1]}}</view>
						</view>
						<view class="tui-order-text">待付款</view>
					</view>
					<view class="tui-order-item" @tap="navigateTo('/pagesA/my/myOrder/myOrder?currentTab=2&ptype=2')">
						<view class="tui-icon-box">
							<image src="/static/images/mall/my/icon_daifahuo_3x.png" class="tui-order-icon"></image>
							<view v-if="serviceorder[3]>0" class="tui-badge tui-badge-red">{{serviceorder[3]}}</view>
						</view>
						<view class="tui-order-text">待服务</view>
					</view>
					<view class="tui-order-item" @tap="navigateTo('/pagesA/my/myOrder/myOrder?currentTab=3&ptype=2')">
						<view class="tui-icon-box">
							<image src="/static/images/mall/my/icon_daishouhuo_3x.png" class="tui-order-icon"></image>
							<view v-if="serviceorder[5]>0" class="tui-badge tui-badge-red">{{serviceorder[5]}}</view>
						</view>
						<view class="tui-order-text">已服务</view>
					</view>
					<view class="tui-order-item" @tap="navigateTo('/pagesA/my/myOrder/myOrder?currentTab=5&ptype=2')">
						<view class="tui-icon-box">
							<image src="/static/images/mall/my/icon_pingjia_3x.png" class="tui-order-icon"></image>
							<view v-if="serviceorder[7]>0" class="tui-badge tui-badge-red">{{serviceorder[7]}}</view>
						</view>
						<view class="tui-order-text">评价</view>
					</view>
					<view class="tui-order-item" @tap="navigateTo('/pagesA/my/myOrder/myOrder?currentTab=4&ptype=2')">
						<view class="tui-icon-box">
							<image src="/static/images/mall/my/icon_tuikuan_3x.png" class="tui-order-icon"></image>
							<view v-if="serviceorder[6]>0" class="tui-badge tui-badge-red">{{serviceorder[6]}}</view>
						</view>
						<view class="tui-order-text">退款/售后</view>
					</view>
				</view>
			</view>
			<view v-if="module==version2" class="tui-box tui-order-box">
				<tui-list-cell :arrow="true" padding="0" unlined :lineLeft="false"
					@click="navigateTo('/pagesA/my/myOrder/myOrder?ptype=1')">
					<view class="tui-cell-header">
						<view class="tui-cell-title">我的订单</view>
						<view class="tui-cell-sub">全部订单</view>
					</view>
				</tui-list-cell>
				<view class="tui-order-list">
					<view class="tui-order-item" @tap="navigateTo('/pagesA/my/myOrder/myOrder?currentTab=1&ptype=1')">
						<view class="tui-icon-box">
							<image src="/static/images/mall/my/icon_daifukuan_3x.png" class="tui-order-icon"></image>
							<view v-if="goodsorder[1]>0" class="tui-badge tui-badge-red">{{goodsorder[1]}}</view>
						</view>
						<view class="tui-order-text">待付款</view>
					</view>
					<view class="tui-order-item" @tap="navigateTo('/pagesA/my/myOrder/myOrder?currentTab=2&ptype=1')">
						<view class="tui-icon-box">
							<image src="/static/images/mall/my/icon_daifahuo_3x.png" class="tui-order-icon"></image>
							<view v-if="goodsorder[2]>0" class="tui-badge tui-badge-red">{{goodsorder[2]}}</view>
						</view>
						<view class="tui-order-text">待发货</view>
					</view>
					<view class="tui-order-item" @tap="navigateTo('/pagesA/my/myOrder/myOrder?currentTab=3&ptype=1')">
						<view class="tui-icon-box">
							<image src="/static/images/mall/my/icon_daishouhuo_3x.png" class="tui-order-icon"></image>
							<view v-if="goodsorder[3]>0" class="tui-badge tui-badge-red">{{goodsorder[3]}}</view>
						</view>
						<view class="tui-order-text">已发货</view>
					</view>
					<view class="tui-order-item" @tap="navigateTo('/pagesA/my/myOrder/myOrder?currentTab=4&ptype=1')">
						<view class="tui-icon-box">
							<image src="/static/images/mall/my/icon_pingjia_3x.png" class="tui-order-icon"></image>
							<view v-if="goodsorder[7]>0" class="tui-badge tui-badge-red">{{goodsorder[7]}}</view>
						</view>
						<view class="tui-order-text">评价</view>
					</view>
					<view class="tui-order-item" @tap="navigateTo('/pagesA/my/myOrder/myOrder?currentTab=5&ptype=1')">
						<view class="tui-icon-box">
							<image src="/static/images/mall/my/icon_tuikuan_3x.png" class="tui-order-icon"></image>
							<view v-if="goodsorder[6]>0" class="tui-badge tui-badge-red">{{goodsorder[6]}}</view>
						</view>
						<view class="tui-order-text">退款/售后</view>
					</view>
				</view>
			</view>

			<view class="tui-box tui-tool-box">
				<view class="tui-tool-list tui-flex-wrap">
					<view class="tui-tool-item" @tap="navigateTo('/pagesA/my/records/records')">
						<view class="tui-icon-box">
							<image src="/static/images/mall/my/icon_zhihuan_3x.png" class="tui-tool-icon"></image>
						</view>
						<view class="tui-tool-text">资金明细</view>
					</view>
					<view class="tui-tool-item" @tap="navigateTo('/pagesA/my/points/points')">
						<view class="tui-icon-box">
							<image src="/static/images/mall/my/jifen.png" class="tui-tool-icon"></image>
						</view>
						<view class="tui-tool-text">{{lang.points}}明细</view>
					</view>
					<view class="tui-tool-item" @tap="navigateTo('/pagesA/my/myCoupon/myCoupon')">
						<view class="tui-icon-box">
							<image src="/static/images/mall/my/myCoupon.png" class="tui-tool-icon"></image>
						</view>
						<view class="tui-tool-text">我的优惠券</view>
					</view>
					<!-- #ifdef MP-WEIXIN -->					
					<button v-if="configkefu.minionline==1 || configkefu.minionline==3" @click="toim" class="tui-tool-item">
						<view class="tui-icon-box">
							<image src="/static/images/mall/my/icon_kefu_3x.png" class="tui-tool-icon"></image>
						</view>
						<view class="tui-tool-text">客服服务</view>
					</button>
					<button v-else-if="configkefu.minionline==2" @click="toimwebview(configkefu.kefuurl)" class="tui-tool-item">
						<view class="tui-icon-box">
							<image src="/static/images/mall/my/icon_kefu_3x.png" class="tui-tool-icon"></image>
						</view>
						<view class="tui-tool-text">客服服务</view>
					</button>
					<button v-else open-type="contact" class="tui-tool-item">
						<view class="tui-icon-box">
							<image src="/static/images/mall/my/icon_kefu_3x.png" class="tui-tool-icon"></image>
						</view>
						<view class="tui-tool-text">客服服务</view>
					</button>
					<!-- #endif -->
					<!-- #ifndef MP-WEIXIN -->
					<view class="tui-tool-item" @tap="toim">
						<view class="tui-icon-box">
							<image src="/static/images/mall/my/icon_kefu_3x.png" class="tui-tool-icon"></image>
						</view>
						<view class="tui-tool-text">客服服务</view>
					</view>
					<!-- #endif -->
				</view>
			</view>
			<view v-if="pagestyleconfig.usersmenustyle==1" class="tui-box tui-tool-box">
				<tui-list-view>
					<tui-list-cell v-for="(menuitem,index) in menulist" @tap="navigateTo(menuitem.pagePath)"
						:arrow="true">
						<view class="tui-item-box">
							<view class="tui-icon-box">
								<image :src="menuitem.iconPath" class="tui-tool-icon"></image>
							</view>
							<view class="tui-list-cell_name">{{menuitem.text}}</view>
						</view>
					</tui-list-cell>
				</tui-list-view>
			</view>
			<view v-else class="tui-box tui-tool-box">
				<view class="tui-menu-list tui-flex-wrap">
					<view class="tui-tool-item" v-for="(menuitem,index) in menulist" >
						<view class="tui-icon-box"  @tap="navigateTo(menuitem.pagePath)">
							<image :src="menuitem.iconPath" class="tui-tool-icon"></image>
						</view>
						<view class="tui-tool-text">{{menuitem.text}}</view>
					</view>
			
				</view>
			</view>
			<!--猜你喜欢-->
			<block v-if="recommendgoods.length>0">
				<tui-divider :size="28" :bold="true" color="#333" width="50%">猜你喜欢</tui-divider>
				<view class="tui-product-list">
					<view class="tui-product-container">
						<block v-for="(item, index) in recommendgoods" :key="index">
							<!--商品列表-->
							<view class="tui-pro-item" hover-class="hover" :hover-start-time="150"
								@tap="navigateTo('/pages/goodsDetail/goodsDetail?id='+item.id)">
								<image :src="item.pic" class="tui-pro-img" mode="widthFix" />
								<view class="tui-pro-content">
									<view class="tui-pro-tit">{{ item.name }}</view>
									<view>
										<view class="tui-pro-price">
											<text v-if="item.is_points_goods==1"
												class="tui-sale-price">{{lang.points}}:{{ item.pay_points }}</text>
											<text v-if="item.is_points_goods!=1"
												class="tui-sale-price">￥{{ item.price }}</text>
											<text v-if="item.costs>0" class="tui-factory-price">￥{{ item.costs }}</text>
										</view>
										<view class="tui-pro-pay">{{ item.sale_count }}人付款</view>
									</view>
								</view>
							</view>
						</block>
					</view>
				</view>
			</block>
		</view>
		<tui-tabbar :current="current">
		</tui-tabbar>
	</view>
</template>

<script>
	export default {
		components: {},
		data() {
			return {
				config: {},
				lang:{},
				memberconfig:{},
				configkefu:{},
				memberinfo: [],
				userordercount: [],
				serviceorder:[],
				goodsorder:[],
				current: '',
				top: 0, //标题图标距离顶部距离
				opacity: 0,
				scrollTop: 0.5,
				agentconfig: [],
				pagestyleconfig: [],
				recommendgoods: [],
				module: this.$module,
				version2: this.$version2,
				version3: this.$version3,
				menulist: [],
				is_submitaudit: 1
			};
		},
		onLoad: function(e) {
			let _this = this;
			uni.setStorageSync('NewMessage', '');
			// #ifdef MP-WEIXIN
			this.current = "/" + this.__route__;
			// #endif
			//#ifdef H5
			this.current = this.$route.path;
			//#endif
			let obj = {};
			// #ifdef MP-WEIXIN
			obj = wx.getMenuButtonBoundingClientRect();
			// #endif
			// #ifdef MP-BAIDU
			obj = swan.getMenuButtonBoundingClientRect();
			// #endif
			// #ifdef MP-ALIPAY
			my.hideAddToDesktopMenu();
			// #endif
			uni.getSystemInfo({
				success: res => {
					this.width = obj.left || res.windowWidth;
					this.height = obj.top ? obj.top + obj.height + 8 : res.statusBarHeight + 44;
					this.top = obj.top ? obj.top + (obj.height - 32) / 2 : res.statusBarHeight + 6;
					this.scrollH = res.windowWidth * 0.6;
				}
			});

			_this.$request.post('bottommenu.list', {
				mo: 'member'
			}).then(res => {
				if (res.errno == 0) {
					_this.menulist = res.data.list;
				}
			});
			_this.$request.get('Goods.recommend').then(res => {
				if (res.errno == 0) {
					_this.recommendgoods = res.data;
				}
			});
			_this.$request.get('Lang.getlang').then(res => {
				if (res.errno == 0) {
					_this.lang = res.data;
				}
			});
			_this.$config.init(function() {
				_this.configkefu = _this.$config.getConf("kefu")||{};
				_this.module = _this.$config.getConf("module");
			});
			
			_this.$request.post('config', {
				mo: 'agent'
			}).then(res => {
				if (res.errno == 0) {
					_this.agentconfig = res.data
				}
			});
			_this.$request.post('config', {
				mo: 'pagestyle'
			}).then(res => {
				if (res.errno == 0) {
					_this.pagestyleconfig = res.data
				}
			});

			_this.$util.getUserInfo(function(userInfo) {
				//Console.log("adfs");
				_this.$request.get('member.detail', {
					samkey: (new Date()).valueOf()
				}).then(res => {
					if (res.errno == 0) {
						_this.memberinfo = res.data;
						_this.is_submitaudit = res.data.is_submitaudit;
					}
				});
			});
		},
		onShow: function() {
			let _this = this;
			_this.$request.post('Config.memberislogin', {
				samkey: (new Date()).valueOf()
			}).then(res => {
				if(res.data){
					console.log(res.data.userInfo_is_login);
					_this.memberconfig = res.data;
					if (res.data.userInfo_is_login == 1) {
						uni.setStorageSync('gotopage', '/pagesA/my/userInfo/index');
						_this.sam.checktelephone().then(res => {
						});
					}
				}
				
			});
			_this.$request.get('ordercount.userordercount', {
				samkey: (new Date()).valueOf()
			}).then(res => {
				if (res.errno == 0) {
					_this.userordercount = res.data;
					if(_this.userordercount.serviceorder){
						_this.serviceorder = _this.userordercount.serviceorder;
					}
					if(_this.userordercount.goodsorder){
						_this.goodsorder = _this.userordercount.goodsorder;
					}
				}
			});
		},
		methods: {
			initNavigation(e) {
				this.opacity = e.opacity;
				this.top = e.top;
			},
			opacityChange(e) {
				this.opacity = e.opacity;
			},
			navigateTo(url) {
				uni.navigateTo({
					url: url
				});
			},
			recharge() {
				if (this.is_submitaudit != 1 && this.memberconfig.is_recharge==1) {
					uni.navigateTo({
						url: '/pagesA/my/recharge/recharge?isshow=1'
					});
				}
			},
			
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
			login: function() {
				let _this = this;
				var isWeixin = 0;
				// #ifdef H5
				var ua = navigator.userAgent.toLowerCase();
				isWeixin = ua.indexOf('micromessenger') != -1;
				// #endif
				
				if(isWeixin){
					_this.$util.getmpuserinfo();
				}else{
					uni.reLaunch({
						url: "/pages/login/login?ptype=member&gotopage=/pagesA/my/userInfo/index",
					})
				}
			}
		},
		onPageScroll(e) {
			this.scrollTop = e.scrollTop;
		},
		onPullDownRefresh() {
			setTimeout(() => {
				uni.stopPullDownRefresh();
			}, 200);
		},
		onReachBottom: function() {}
	};
</script>
<style>
	.tui-header-box {
		width: 100%;
		padding: 0 30rpx 0 20rpx;
		box-sizing: border-box;
		position: fixed;
		top: 0;
		left: 0;
		display: flex;
		align-items: center;
		justify-content: flex-end;
		height: 32px;
		-webkit-transform: translateZ(0);
		transform: translateZ(0);
		z-index: 9999
	}

	.tui-set-box {
		width: 123rpx;
		display: flex;
		align-items: center;
		justify-content: space-between
	}

	.tui-icon-box {
		position: relative
	}

	.tui-icon-setup {
		margin-left: 8rpx;
		float: right
	}

	.tui-badge {
		position: absolute;
		font-size: 24rpx;
		height: 32rpx;
		min-width: 20rpx;
		padding: 0 6rpx;
		border-radius: 40rpx;
		right: 10rpx;
		top: -5rpx;
		-webkit-transform: scale(.8) translateX(60%);
		transform: scale(.8) translateX(60%);
		-webkit-transform-origin: center center;
		transform-origin: center center;
		display: flex;
		align-items: center;
		justify-content: center;
		z-index: 10
	}

	.tui-badge-red {
		background: #f74d54;
		color: #fff
	}

	.tui-badge-white {
		background: #fff;
		color: #f74d54
	}

	.tui-badge-dot {
		position: absolute;
		height: 12rpx;
		width: 12rpx;
		border-radius: 50%;
		right: -12rpx;
		top: 0;
		background: #f74d54
	}

	.tui-badgewhite-dot {
		position: absolute;
		height: 12rpx;
		width: 12rpx;
		border-radius: 50%;
		right: -12rpx;
		top: 0;
		background: #ebeea0
	}

	.tui-mybg-box {
		width: 100%;
		height: 464rpx;
		position: relative
	}

	.tui-my-bg {
		width: 100%;
		height: 464rpx;
		display: block
	}

	.tui-header-center {
		position: absolute;
		width: 100%;
		height: 128rpx;
		left: 0;
		top: 120rpx;
		padding: 0 30rpx;
		box-sizing: border-box;
		display: flex;
		align-items: center
	}

	.tui-avatar {
		flex-shrink: 0;
		width: 128rpx;
		height: 128rpx;
		border-radius: 50%;
		display: block
	}

	.tui-info {
		width: 60%;
		padding-left: 30rpx
	}

	.tui-login {
		width: 60%;
		padding-left: 30rpx;
		font-size: 32rpx;
		line-height: 32rpx;
		color: #fff;
		display: flex;
		align-items: center
	}

	.tui-nickname {
		font-size: 30rpx;
		font-weight: 500;
		color: #fff;
		display: flex;
		align-items: center
	}

	.tui-img-vip {
		width: 56rpx;
		height: 24rpx;
		margin-left: 18rpx
	}

	.tui-explain {
		width: 80%;
		font-size: 24rpx;
		font-weight: 400;
		color: #fff;
		opacity: .75;
		padding-top: 8rpx;
		white-space: nowrap;
		overflow: hidden;
		text-overflow: ellipsis
	}

	.tui-btn-edit {
		flex-shrink: 0;
		padding-right: 22rpx
	}

	.tui-header-btm {
		width: 100%;
		padding: 0 30rpx;
		box-sizing: border-box;
		position: absolute;
		left: 0;
		top: 260rpx;
		display: flex;
		align-items: center;
		justify-content: space-between;
		color: #fff
	}

	.tui-btm-item {
		flex: 1;
		display: flex;
		flex-direction: column;
		align-items: center;
		justify-content: center
	}

	.tui-btm-num {
		font-size: 32rpx;
		font-weight: 600;
		position: relative
	}

	.tui-btm-text {
		font-size: 24rpx;
		opacity: .85;
		padding-top: 4rpx
	}

	.tui-content-box {
		width: 100%;
		padding: 0 30rpx;
		box-sizing: border-box;
		position: relative;
		top: -100rpx;
		z-index: 10;
		padding-bottom: 100rpx
	}

	.tui-box {
		width: 100%;
		background: #fff;
		box-shadow: 0 3rpx 20rpx hsla(0, 0%, 71.8%, .1);
		border-radius: 30rpx;
		overflow: hidden
	}

	.vip-box {
		width: 100%;
		box-shadow: 0 3rpx 20rpx hsla(0, 0%, 71.8%, .1);
		border-radius: 30rpx;
		overflow: hidden
	}

	.vip-box-header {
		width: 100%;
		height: 118rpx;
		padding: 0 26rpx;
		box-sizing: border-box;
		display: flex;
		align-items: center;
		justify-content: space-between
	}

	.vip-box-title {
		font-size: 30rpx;
		line-height: 30rpx;
		font-weight: 600;
		color: #f5e3b1
	}

	.vip-box-but {
		color: #503f35;
		font-size: 30rpx;
		background-color: #f8e6c0;
		height: 60rpx;
		line-height: 60rpx;
		border-radius: 30rpx
	}

	.tui-order-box {
		height: 208rpx
	}

	.tui-cell-header {
		width: 100%;
		height: 74rpx;
		padding: 0 26rpx;
		box-sizing: border-box;
		display: flex;
		align-items: center;
		justify-content: space-between
	}

	.tui-cell-title {
		font-size: 30rpx;
		line-height: 30rpx;
		font-weight: 600;
		color: #333
	}

	.tui-cell-sub {
		font-size: 26rpx;
		font-weight: 400;
		color: #999;
		padding-right: 28rpx
	}

	.tui-order-list {
		width: 100%;
		height: 134rpx;
		padding: 0 30rpx;
		box-sizing: border-box;
		display: flex;
		align-items: center;
		justify-content: space-between
	}
	.tui-menu-list {
		width: 100%;
		height: 134rpx;
		padding-top: 15rpx;
		padding-bottom: 20rpx;
		padding-left: 20rpx;
		padding-right: 20rpx;
		box-sizing: border-box;
		display: flex;
		align-items: center;
	}

	.tui-order-item {
		flex: 1;
		display: flex;
		float: left;
		flex-direction: column;
		align-items: center
	}

	.tui-order-text,
	.tui-tool-text {
		font-size: 26rpx;
		font-weight: 400;
		color: #666;
		padding-top: 4rpx
	}

	.tui-tool-text {
		font-size: 25rpx;
		line-height: 25rpx
	}

	.tui-order-icon {
		width: 70rpx;
		height: 70rpx;
		display: block
	}

	.tui-assets-box {
		height: 118rpx;
		margin-top: 20rpx;
		padding-top: 30rpx
	}

	.tui-assets-list {
		height: 84rpx
	}

	.tui-assets-num {
		font-size: 38rpx;
		font-weight: 500;
		color: #333;
		font-weight: 700;
		position: relative
	}

	.tui-assets-text {
		font-size: 24rpx;
		font-weight: 400;
		color: #666;
		padding-top: 6rpx
	}

	.tui-tool-box {
		margin-top: 20rpx
	}

	.tui-flex-wrap {
		flex-wrap: wrap;
		height: auto
	}

	.tui-tool-list {
		width: 100%;
		padding-top: 15rpx;
		padding-bottom: 20rpx;
		padding-left: 20rpx;
		padding-right: 20rpx;
		box-sizing: border-box;
		display: flex;
		align-items: center
	}

	.tui-tool-item {
		width: 25%;
		display: flex;
		align-items: center;
		justify-content: center;
		flex-direction: column;
		padding-top: 15rpx;
		padding-bottom: 15rpx;
		background-color: #fff
	}

	.tui-tool-icon {
		width: 64rpx;
		height: 64rpx;
		display: block
	}

	.tui-tool-liicon {
		width: 50rpx;
		height: 50rpx;
		display: block
	}

	.tui-badge-icon {
		width: 66rpx;
		height: 30rpx;
		position: absolute;
		right: 0;
		-webkit-transform: translateX(88%);
		transform: translateX(88%);
		top: -15rpx
	}

	.tui-product-list {
		display: flex;
		justify-content: space-between;
		flex-direction: row;
		flex-wrap: wrap;
		box-sizing: border-box
	}

	.tui-product-container {
		width: 100%;
		display: flex;
		flex-direction: row;
		flex-wrap: wrap
	}

	.tui-pro-item {
		background: #fff;
		box-sizing: border-box;
		overflow: hidden;
		border-radius: 12rpx;
		width: 48%;
		margin-left: 1%;
		margin-right: 1%;
		margin-bottom: 2%
	}

	.tui-pro-img {
		width: 100%;
		display: block
	}

	.tui-pro-content {
		display: flex;
		flex-direction: column;
		justify-content: space-between;
		box-sizing: border-box;
		padding: 20rpx
	}

	.tui-pro-tit {
		color: #2e2e2e;
		font-size: 26rpx;
		word-break: break-all;
		overflow: hidden;
		text-overflow: ellipsis;
		display: -webkit-box;
		-webkit-box-orient: vertical;
		-webkit-line-clamp: 2
	}

	.tui-pro-price {
		padding-top: 18rpx
	}

	.tui-sale-price {
		font-size: 34rpx;
		font-weight: 500;
		color: #e41f19
	}

	.tui-factory-price {
		font-size: 24rpx;
		color: #a0a0a0;
		text-decoration: line-through;
		padding-left: 12rpx
	}

	.tui-pro-pay {
		padding-top: 10rpx;
		font-size: 24rpx;
		color: #656565
	}

	.tui-item-box {
		width: 100%;
		display: flex;
		align-items: center
	}

	.tui-list-cell_name {
		padding-left: 20rpx;
		display: flex;
		align-items: center;
		justify-content: center
	}
</style>
