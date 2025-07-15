<template>
	<view class="container">
		<!--screen-->
		<view class="tui-header-screen">
			<view class="tui-search__bar">
				<view class="tui-searchbox" @tap="search">
					<icon type="search" :size="13" color="#999"></icon>
					<text class="tui-search-text">请输入搜索关键词</text>
				</view>
			</view>
			<view class="tui-screen__box">
				<view class="tui-screen-item" :class="[tabIndex == -1 ? 'tui-active tui-bold' : '']" @tap="screen" data-index="-1">综合</view>
				<view class="tui-screen-item" :class="[tabIndex == 0 ? 'tui-active tui-bold' : '']" data-index="0" @tap="screen">
					<view>价格</view>
					<tui-icon :name="selectH > 0 ? 'arrowup' : 'arrowdown'" :size="14" :color="tabIndex == 0 ? '#e41f19' : '#444'"></tui-icon>
				</view>
				<view class="tui-screen-item" :class="[tabIndex == 1 ? 'tui-active tui-bold' : '']" @tap="screen" data-index="1">销量</view>
				<view class="tui-screen-item" @tap="screen" data-index="2">
					<tui-icon :name="isList ? 'manage' : 'listview'" :size="isList ? 22 : 18" :bold="isList ? false : true" color="#333"></tui-icon>
				</view>
		
				<!--下拉选择列表-综合-->
				<view class="tui-dropdownlist" :class="[selectH > 0 ? 'tui-dropdownlist-show' : '']" :style="{ height: selectH + 'rpx' }">
					<view class="tui-dropdownlist-item" :class="[item.selected ? 'tui-bold' : '']" v-for="(item, index) in dropdownList"
					 :key="index" @tap.stop="dropdownItem" :data-index="index">
						<text>{{ item.name }}</text>
						<tui-icon name="check" :size="16" color="#e41f19" :bold="true" v-if="item.selected"></tui-icon>
					</view>
				</view>
				<view class="tui-dropdownlist-mask" :class="[selectH > 0 ? 'tui-mask-show' : '']" @tap.stop="hideDropdownList"></view>
				<!--下拉选择列表--综合-->
			</view>
		</view>
		<!--screen-->
		<!--list-->
		<view class="tui-product-list" :style="{ marginTop: px(dropScreenH + 18) }">
			<view class="tui-product-container">
				<block v-for="(item, index) in goodsList" :key="index">
					<!--商品列表-->
					<view class="tui-pro-item" :class="[isList ? 'tui-flex-list' : 'tui-flex-card']"
						hover-class="tui-hover" :hover-start-time="150"
						:data-url="'/pages/goodsDetail/goodsDetail?tuanid='+item.id+'&id='+item.goods_id" @tap="navigationTo">
						<image :src="item.image" class="tui-pro-img" :class="[isList ? 'tui-proimg-list' : '']"
							mode="widthFix" />
						<view class="tui-pro-content">
							<view class="tui-pro-tit">{{ item.title }}</view>
							<view>
								<view class="tui-pro-price">
									<text v-if="item.is_points_goods!=1" class="tui-sale-price">￥{{ item.price }}</text>
									<text v-if="item.goods.price>0" class="tui-factory-price">￥{{ item.goods.price }}</text>
								</view>
								<view class="tui-group-text">已有{{ item.sale_count }}人拼团</view>
								<view class="tui-group-btn">
									<view class="tui-flex-btn tui-color-red">{{ item.people_num }}人团</view>
									<view class="tui-flex-btn">去拼团</view>
								</view>
							</view>
						</view>
					</view>
					<!--商品列表-->
				</block>
			</view>
		</view>
		<!--list end-->		
		<!--加载loadding-->
		<tui-loadmore v-if="loadding" :index="3" type="red"></tui-loadmore>
		<tui-nomore v-if="!pullUpOn && isList" backgroundColor="#f7f7f7"></tui-nomore>
		<!--加载loadding-->
	</view>
</template>

<script>
	export default {
		data() {
			return {
				searchKey: '', //搜索关键词
				width: 200, //header宽度
				height: 64, //header高度
				inputTop: 0, //搜索框距离顶部距离
				arrowTop: 0, //箭头距离顶部距离
				dropScreenH: 0, //下拉筛选框距顶部距离
				attrData: [],
				attrIndex: -1,
				scrollTop: 0,
				tabIndex: 0, //顶部筛选索引
				isList: false, //是否以列表展示  | 列表或大图
				drawer: false,
				drawerH: 0, //抽屉内部scrollview高度
				selectedName: '综合',
				selectH: 0,
				dropdownList: [{
						name: '综合',
						selected: true
					},
					{
						name: '价格升序',
						selected: false
					},
					{
						name: '价格降序',
						selected: false
					}
				],
				goodsList: [],
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
				price: 0,
				stock: 0,
				pageIndex: 1,
				loadding: false,
				pullUpOn: true
			};
		},
		onLoad: function(options) {
			this.where.categoryId = options.cid || ''
			this.where.keyword = options.keyword || '';
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
					this.inputTop = obj.top ? obj.top + (obj.height - 30) / 2 : res.statusBarHeight + 7;
					this.arrowTop = obj.top ? obj.top + (obj.height - 32) / 2 : res.statusBarHeight + 6;
					this.searchKey = options.keyword || options.searchKey || '';
					//略小，避免误差带来的影响
					this.dropScreenH = (this.height * 750) / res.windowWidth + 10;
					this.drawerH = res.windowHeight - uni.upx2px(100) - this.height;
				}
			});
			this.get_product_list();
		},
		methods: {
			px(num) {
				return uni.upx2px(num) + 'px';
			},
			btnCloseDrop() {
				this.scrollTop = 1;
				this.$nextTick(() => {
					this.scrollTop = 0;
				});
				this.dropScreenShow = false;
				this.attrIndex = -1;
			},
			btnSure: function() {
				let index = this.attrIndex;
				let arr = this.attrData;
				let active = false;
				let attrName = '';
				//这里只是为了展示选中效果,并非实际场景
				for (let item of arr) {
					if (item.selected) {
						active = true;
						attrName += attrName ? ';' + item.name : item.name;
					}
				}
				let obj = this.attrArr[index];
				this.btnCloseDrop();
				this.$set(obj, 'isActive', active);
				this.$set(obj, 'selectedName', attrName);
			},
			showDropdownList: function() {
				this.selectH = 246;
				this.tabIndex = 0;
			},
			hideDropdownList: function() {
				this.selectH = 0;
			},
			dropdownItem: function(e) {
				let index = Number(e.currentTarget.dataset.index);
				let arr = this.dropdownList;
				
				if(index==0){
					
				}
				for (let i = 0; i < arr.length; i++) {
					if (i === index) {
						arr[i].selected = true;
					} else {
						arr[i].selected = false;
					}
				}
				this.dropdownList = arr;
				this.selectedName = index == 0 ? '综合' : '价格';
				this.selectH = 0;
				this.price = index;
				this.where.page = 1;
				this.get_product_list(true);
			},
			screen: function(e) {
				let index = e.currentTarget.dataset.index;
				this.hideDropdownList();
				this.btnCloseDrop();
				if (index == 0) {
					this.showDropdownList();
				} else if (index == 1) {
					this.tabIndex = 1;
					if(this.stock == 1){
						this.stock = 2
					}else{
						this.stock = 1
					}
				} else if (index == 2) {
					this.isList = !this.isList;
				} else if (index == 3) {
					this.drawer = true;
				} else if (index == 4) {
					if(this.tabIndex == 4){
						this.tabIndex = 0;
					}else{
						this.tabIndex = 4;
					}

					if(this.where.news==1){
						this.where.news = 0;
					}else{
						this.where.news = 1;
					}
				}
				this.loadend = false;
				this.where.page = 1;
				this.get_product_list(true);
			},
			back: function() {
				if (this.drawer) {
					this.closeDrawer();
				} else {
					uni.navigateBack();
				}
			},
			search: function() {
				uni.navigateTo({
					url: '/pages/common/search/search'
				});
			},
			navigationTo: function(e) {
				uni.navigateTo({
					url: e.currentTarget.dataset.url
				});
			},
			//设置where条件
			setWhere: function() {
				if (this.price == 0)
					this.where.priceOrder = '';
				else if (this.price == 1)
					this.where.priceOrder = 'desc';
				else if (this.price == 2)
					this.where.priceOrder = 'asc';
				if (this.stock == 0)
					this.where.salesOrder = '';
				else if (this.stock == 1)
					this.where.salesOrder = 'desc';
				else if (this.stock == 2)
					this.where.salesOrder = 'asc';

			},
			//查找产品
			get_product_list: function(isPage) {

				var that = this;
				this.setWhere();
				console.log(this.where);
				if (that.loadend) return;
				if (that.loading) return;

				if (isPage === true) {
					that.goodsList = [];
					that.where.page =1;
				}
				this.$request.post('tuangoods', that.where).then(res => {
					if (res.errno == 0) {
						that.goodsList = that.goodsList.concat(res.data.data);
						that.loadend = that.goodsList.length < that.where.limit;
						that.loading = false;
						that.where.page = that.where.page + 1

					}
				})
			},
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
	page {
		background-color: #f7f7f7;
	}

.tui-search__bar {
		width: 100%;
		background-color: #fff;
		padding: 20rpx 25rpx;
		box-sizing: border-box;
	}

	.tui-searchbox {
		width: 100%;
		height: 64rpx;
		margin-right: 30rpx;
		border-radius: 15px;
		font-size: 24rpx;
		background: #f7f7f7;
		padding: 6rpx 20rpx;
		box-sizing: border-box;
		color: #999;
		display: flex;
		align-items: center;
		overflow: hidden;
	}

	.tui-search-text {
		padding-left: 16rpx;
	}

	/*screen*/

	.tui-header-screen {
		width: 100%;
		box-sizing: border-box;
		background: #fff;
		position: fixed;
		z-index: 996;
	}

	.tui-screen__box {
		width: 100%;
		height: 80rpx;
		display: flex;
		align-items: center;
		justify-content: space-between;
		font-size: 28rpx;
		color: #333;
		position: relative;
		background: #fff;
	}


	.tui-screen-item {
		height: 28rpx;
		line-height: 28rpx;
		flex: 1;
		display: flex;
		align-items: center;
		justify-content: center;
	}



	.tui-btmItem-active {
		background-color: #fcedea !important;
		color: #e41f19;
		font-weight: bold;
		position: relative;
	}

	.tui-btmItem-active::after {
		content: '';
		position: absolute;
		border: 1rpx solid #e41f19;
		width: 100%;
		height: 100%;
		border-radius: 40rpx;
		left: 0;
		top: 0;
	}


	.tui-addr-left {
		padding-left: 6rpx;
	}

	.tui-bold {
		font-weight: bold;
	}

	.tui-active {
		color: #e41f19;
	}


	/*screen*/

	.tui-screen-top,
	.tui-screen-bottom {
		display: flex;
		align-items: center;
		justify-content: space-between;
		font-size: 28rpx;
		color: #333;
	}

	.tui-screen-top {
		height: 88rpx;
		position: relative;
		background: #fff;
	}

	.tui-top-item {
		height: 28rpx;
		line-height: 28rpx;
		flex: 1;
		display: flex;
		align-items: center;
		justify-content: center;
	}

	.tui-topitem-active {
		color: #e41f19;
	}

	.tui-screen-bottom {
		height: 100rpx;
		padding: 0 30rpx;
		box-sizing: border-box;
		font-size: 24rpx;
		align-items: center;
		overflow: hidden;
	}

	.tui-bottom-text {
		line-height: 26rpx;
		max-width: 82%;
		white-space: nowrap;
		overflow: hidden;
		text-overflow: ellipsis;
	}

	.tui-bottom-item {
		flex: 1;
		width: 0;
		display: flex;
		align-items: center;
		justify-content: center;
		padding: 0 12rpx;
		box-sizing: border-box;
		background-color: #f7f7f7;
		margin-right: 20rpx;
		white-space: nowrap;
		height: 60rpx;
		border-radius: 40rpx;
	}

	.tui-bottom-item:last-child {
		margin-right: 0;
	}

	.tui-btmItem-active {
		background-color: #fcedea !important;
		color: #e41f19;
		font-weight: bold;
		position: relative;
	}

	.tui-btmItem-active::after {
		content: '';
		position: absolute;
		border: 1rpx solid #e41f19;
		width: 100%;
		height: 100%;
		border-radius: 40rpx;
		left: 0;
		top: 0;
	}

	.tui-btmItem-tap {
		position: relative;
		border-bottom-left-radius: 0;
		border-bottom-right-radius: 0;
	}

	.tui-btmItem-tap::after {
		content: '';
		position: absolute;
		width: 100%;
		height: 22rpx;
		background: #f7f7f7;
		left: 0;
		top: 58rpx;
	}

	.tui-bold {
		font-weight: bold;
	}

	.tui-active {
		color: #e41f19;
	}

	.tui-addr-left {
		padding-left: 6rpx;
	}

	.tui-seizeaseat-20 {
		height: 20rpx;
	}

	.tui-seizeaseat-30 {
		height: 30rpx;
	}

	/*screen*/

	/*顶部下拉选择 属性*/

	.tui-scroll-box {
		width: 100%;
		height: 480rpx;
		box-sizing: border-box;
		position: relative;
		z-index: 99;
		color: #fff;
		font-size: 30rpx;
		word-break: break-all;
	}

	.tui-drop-item {
		color: #333;
		height: 80rpx;
		font-size: 28rpx;
		padding: 20rpx 40rpx 20rpx 40rpx;
		box-sizing: border-box;
		display: inline-block;
		width: 50%;
	}

	.tui-drop-btnbox {
		width: 100%;
		height: 100rpx;
		position: absolute;
		left: 0;
		bottom: 0;
		box-sizing: border-box;
		display: flex;
	}

	.tui-drop-btn {
		width: 50%;
		font-size: 32rpx;
		text-align: center;
		height: 100rpx;
		line-height: 100rpx;
		border: 0;
	}

	.tui-btn-red {
		background: #e41f19;
		color: #fff;
	}

	.tui-red-hover {
		background: #c51a15 !important;
		color: #e5e5e5;
	}

	.tui-btn-white {
		background: #fff;
		color: #333;
	}

	.tui-white-hover {
		background: #e5e5e5;
		color: #2e2e2e;
	}

	/*顶部下拉选择 属性*/

	/*顶部下拉选择 综合*/

	.tui-dropdownlist {
		width: 100%;
		position: absolute;
		background-color: #fff;
		border-bottom-left-radius: 24rpx;
		border-bottom-right-radius: 24rpx;
		overflow: hidden;
		box-sizing: border-box;
		padding-top: 10rpx;
		padding-bottom: 26rpx;
		left: 0;
		top: 88rpx;
		visibility: hidden;
		transition: all 0.2s ease-in-out;
		z-index: 999;
	}

	.tui-dropdownlist-show {
		visibility: visible;
	}

	.tui-dropdownlist-mask {
		position: fixed;
		top: 0;
		left: 0;
		right: 0;
		bottom: 0;
		background-color: rgba(0, 0, 0, 0.6);
		z-index: -1;
		transition: all 0.2s ease-in-out;
		opacity: 0;
		visibility: hidden;
	}

	.tui-mask-show {
		opacity: 1;
		visibility: visible;
	}

	.tui-dropdownlist-item {
		color: #333;
		height: 70rpx;
		font-size: 28rpx;
		padding: 0 40rpx;
		box-sizing: border-box;
		display: flex;
		align-items: center;
		justify-content: space-between;
	}

	/*顶部下拉选择 综合*/

	.tui-drawer-box {
		width: 686rpx;
		font-size: 24rpx;
		overflow: hidden;
		position: relative;
		padding-bottom: 100rpx;
	}

	.tui-drawer-title {
		display: flex;
		align-items: center;
		justify-content: space-between;
		padding: 0 30rpx;
		box-sizing: border-box;
		height: 80rpx;
	}

	.tui-title-bold {
		font-size: 26rpx;
		font-weight: bold;
		flex-shrink: 0;
	}

	.tui-location {
		margin-right: 6rpx;
	}

	.tui-attr-right {
		width: 70%;
		white-space: nowrap;
		overflow: hidden;
		text-overflow: ellipsis;
		text-align: right;
	}

	.tui-all-box {
		width: 90%;
		white-space: nowrap;
		display: flex;
		align-items: center;
		justify-content: flex-end;
	}

	.tui-drawer-content {
		padding: 16rpx 30rpx 30rpx 30rpx;
		display: flex;
		align-items: center;
		justify-content: space-between;
		box-sizing: border-box;
	}

	.tui-input {
		border: 0;
		height: 64rpx;
		border-radius: 32rpx;
		width: 45%;
		background-color: #f7f7f7;
		text-align: center;
		font-size: 24rpx;
		color: #333;
	}

	.tui-phcolor {
		text-align: center;
		color: #b2b2b2;
		font-size: 24rpx;
	}

	.tui-flex-attr {
		flex-wrap: wrap;
		justify-content: flex-start;
	}

	.tui-attr-item {
		width: 30%;
		height: 64rpx;
		background-color: #f7f7f7;
		display: flex;
		align-items: center;
		justify-content: center;
		padding: 0 4rpx;
		box-sizing: border-box;
		border-radius: 32rpx;
		margin-right: 5%;
		margin-bottom: 5%;
	}

	.tui-attr-ellipsis {
		white-space: nowrap;
		text-overflow: ellipsis;
		overflow: hidden;
		width: 96%;
		text-align: center;
	}

	.tui-attr-item:nth-of-type(3n) {
		margin-right: 0%;
	}

	.tui-attr-btnbox {
		width: 100%;
		position: absolute;
		left: 0;
		bottom: 0;
		box-sizing: border-box;
		padding: 0 30rpx;
		background: #fff;
	}

	.tui-attr-safearea {
		height: 100rpx;
		display: flex;
		align-items: center;
		justify-content: space-between;
		padding-bottom: env(safe-area-inset-bottom);
	}

	.tui-safearea-bottom {
		width: 100%;
		height: env(safe-area-inset-bottom);
	}

	.tui-attr-btnbox::before {
		content: '';
		position: absolute;
		top: 0;
		right: 0;
		left: 0;
		border-top: 1px solid #eaeef1;
		transform: scaleY(0.5) translateZ(0);
		transform-origin: 0 0;
	}

	.tui-drawer-btn {
		width: 47%;
		text-align: center;
		height: 60rpx;
		border-radius: 30rpx;
		flex-shrink: 0;
		display: flex;
		align-items: center;
		justify-content: center;
		box-sizing: border-box;
	}

	.tui-drawerbtn-black {
		border: 1rpx solid #555;
	}

	.tui-drawerbtn-primary {
		background: #e41f19;
		color: #fff;
	}

	/* 商品列表*/

	.tui-product-list {
		display: flex;
		justify-content: space-between;
		flex-direction: row;
		flex-wrap: wrap;
		box-sizing: border-box;
	}

	.tui-product-container {
		width: 100%;
		margin-left: 10rpx;
		margin-right: 10rpx;
		display: flex;
		flex-direction: row;
		flex-wrap: wrap;
	}

	.tui-pro-item {
		background: #fff;
		box-sizing: border-box;
		overflow: hidden;
		transition: all 0.15s ease-in-out;
	}

	.tui-flex-card {
		border-radius: 12rpx;
		width: 48%;
		margin-left: 1%;
		margin-right: 1%;
		margin-bottom: 2%;
	}

	.tui-flex-list {
		width: 100%;
		padding-top: 10rpx;
		padding-bottom: 10rpx;
		display: flex;
		margin-bottom: 1rpx !important;
	}

	.tui-pro-img {
		width: 100%;
		display: block;
	}

	.tui-proimg-list {
		width: 260rpx;
		height: 260rpx !important;
		flex-shrink: 0;
		border-radius: 12rpx;
	}

	.tui-pro-content {
		display: flex;
		flex-direction: column;
		justify-content: space-between;
		box-sizing: border-box;
		padding: 20rpx;
	}

	.tui-pro-tit {
		color: #2e2e2e;
		font-size: 26rpx;
		word-break: break-all;
		overflow: hidden;
		text-overflow: ellipsis;
		display: -webkit-box;
		-webkit-box-orient: vertical;
		-webkit-line-clamp: 2;
	}

	.tui-pro-price {
		padding-top: 18rpx;
	}

	.tui-sale-price {
		font-size: 34rpx;
		font-weight: 500;
		color: #e41f19;
	}

	.tui-factory-price {
		font-size: 24rpx;
		color: #a0a0a0;
		text-decoration: line-through;
		padding-left: 12rpx;
	}

	.tui-pro-pay {
		padding-top: 10rpx;
		font-size: 24rpx;
		color: #656565;
	}
	.tui-group-btn {
		max-width: 312rpx;
		height: 48rpx;
		border-radius: 6rpx;
		background: #EB0909;
		display: flex;
		align-items: center;
		padding: 4rpx;
		margin-top: 10rpx;
		box-sizing: border-box;
	}
	
	.tui-flex-btn {
		height: 100%;
		flex: 1;
		text-align: center;
		font-size: 26rpx;
		line-height: 26rpx;
		font-weight: 400;
		color: #fff;
		display: flex;
		align-items: center;
		justify-content: center;
	}
	
	.tui-flex-btn:first-child {
		background: #fff;
	}
	
	.tui-group-text {
		font-size: 25rpx;
		line-height: 25rpx;
		transform: scale(0.8);
		transform-origin: 0 center;
		padding-top: 30rpx;
		color: #999;
	}
	
	.tui-color-red {
		color: #EB0909;
	}

	/* 列表*/
</style>
