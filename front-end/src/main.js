import Vue from 'vue'
import App from './App.vue'
import router from './router'
import store from './store'
import VueCookie from 'vue-cookie'
import { setHeaders } from '../src/utils/axios'
import VuePeerJS from 'vue-peerjs'
import Peer from 'peerjs'
import VModal from 'vue-js-modal'
import ElementUI from 'element-ui'
import 'element-ui/lib/theme-chalk/index.css'
import VueToast from 'vue-toast-notification'
import 'vue-toast-notification/dist/theme-default.css'
import VueCookies from 'vue-cookies'

Vue.use(VueCookies)

Vue.use(VueToast)

Vue.use(VModal)

Vue.use(VueCookie)

Vue.use(ElementUI)

Vue.use(require('vue-moment'))

Vue.use(VuePeerJS, new Peer({}))

Vue.config.productionTip = false

new Vue({
  router,
  store,
  render: h => h(App),
  created () {
    const token = Vue.cookie.get('token')
    !!token && setHeaders({ Authorization: 'Bearer ' + token })
    this.$store.dispatch('getCurrentUser')
    console.log('user')
  }
  // computed: {
  //   ...mapState({
  //     user: state => state.user.user
  //   })
  // }
}).$mount('#app')
