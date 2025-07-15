import util from '@/common/we7_js/util.js'
import App from '../App'
module.exports = {
	// get请求后台数据
	get: function(url, data) {
		return new Promise((resolve, reject) => {
			util.request({
				'url': 'entry/wxapp/' + url,
				'showLoading': data ? data.showLoading : false,
				'data': data,
				'method': 'get',
				'cachetime': '30',
				success: function(res) {
					resolve(res.data)
				}
			});
		})
	},
	// post请求后台数据
	post: function(url, data) {
		return new Promise((resolve, reject) => {
			util.request({
				'url': 'entry/wxapp/' + url,
				'data': data,
				'showLoading': data ? data.showLoading : false,
				'method': 'post',
				'cachetime': '30',
				success: function(res) {
					resolve(res.data)
				}
			});
		})
	},
	uploadFile: function(path) {
		var formvar = 'wxapp';
		var url = '';

		//#ifdef H5
		formvar = 'mp';
		//#endif
		url = App.siteInfo.siteroot + '?i=' + App.siteInfo.uniacid +
			'&c=utility&a=file&do=upload&thumb=0&from=' + formvar;
		//#ifdef H5
		let urlquery = getQuery(window.location.href);
		if (urlquery.length > 0) {
			var urli = '';
			for (let i = 0; i < urlquery.length; i++) {
				if (urlquery[i] && urlquery[i].name && urlquery[i].value) {
					if (urlquery[i].name == "i") {
						urli = urlquery[i].name + '=' + urlquery[i].value;
					}
				}
			}
			if (urli) {
				url = window.location.protocol + '//' + window.location.host +
					'/app/index.php?c=utility&a=file&do=upload&thumb=0&from=' + formvar + '&' + urli;
			}
		}
		//#endif

		url = url + '&m=' + App.module;

		return new Promise((resolve, reject) => {
			var FilePaths = path;
			console.log(FilePaths);
			uni.uploadFile({
				url: url,
				method: 'POST',
				// #ifdef MP-WEIXIN
				filePath: FilePaths,
				formData: {
					file: FilePaths
				},
				header: {
					"Content-Type": "multipart/form-data"
				},
				// #endif

				// #ifdef H5

				filePath: FilePaths,
				formData: {
					file: FilePaths
				},
				// #endif
				name: 'file',
				success: (res) => {
					resolve(JSON.parse(res.data));
					//resolve(res)
					console.info("服务器返回的图片路径是：" + res.data)
				}
			});
		});
	}

}

function getQuery(url) {
	var theRequest = [];
	if (url.indexOf("?") != -1) {
		var str = url.split('?')[1];
		if (str.indexOf("#") != -1) {
			str = str.split('#')[0]
		}
		var strs = str.split("&");
		for (var i = 0; i < strs.length; i++) {
			if (strs[i].split("=")[0] && unescape(strs[i].split("=")[1])) {
				theRequest[i] = {
					'name': strs[i].split("=")[0],
					'value': unescape(strs[i].split("=")[1])
				}
			}
		}
	}
	return theRequest;
}
