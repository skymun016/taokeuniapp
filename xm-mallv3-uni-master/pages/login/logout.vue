<template>

</template>

<script>
	export default {
		data() {
			return {
				ptype: ""
			};
		},
		onLoad(e) {
			var _this = this;
			if (e.ptype) {
				this.ptype = e.ptype;
			}

			_this.$request.post('login.logout', {
				ptype: _this.ptype
			}).then(res => {
				if (res.errno != 0) {
					uni.showToast({
						title: res.msg,
						icon: 'none'
					});
					return;
				} else {
					let url = "/pages/login/userlogin?ptype=" + _this.ptype;
					if (_this.ptype == 'member') {
						url = "/pages/login/login?ptype=" + _this.ptype;
					}
					uni.reLaunch({
						url: url
					});
				}
			});



		},
		methods: {},
		/**
		 * 页面相关事件处理函数--监听用户下拉动作
		 */
		onPullDownRefresh: function() {
			setTimeout(() => {
				uni.stopPullDownRefresh()
			}, 200);
		},
	};
</script>