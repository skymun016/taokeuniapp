import Vue from 'vue'
import App from './App'
import store from './store'
import tui from './common/httpRequest'
import util from './common/we7_js/util.js'
import request from './common/request.js'
import config from './common/config.js'
import sam from './common/sam.js'
import siteInfo from './siteinfo.js'
const xm = 'xm_m';
App.module = 'xm_mallv3';
App.version2 = 'all';
App.version3 = 'allv3';

Vue.config.productionTip = false
Vue.prototype.tui = tui
Vue.prototype.$eventHub = Vue.prototype.$eventHub || new Vue()
Vue.prototype.$store = store
Vue.prototype.$request = request
Vue.prototype.$config = config
Vue.prototype.$siteInfo = siteInfo
Vue.prototype.sam = sam
Vue.prototype.$util = util
Vue.prototype.$module = App.module
Vue.prototype.$version2 = xm + App.version2
Vue.prototype.$version3 = xm + App.version3

App.mpType = 'app'
App.util = util
App.siteInfo = siteInfo
const app = new Vue({
	store,
	...App
})
app.$mount()
