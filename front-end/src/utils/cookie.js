import Vue from 'vue'
import VueCookie from 'vue-cookie'

Vue.use(VueCookie)

const cookie = Vue.cookie.get('token')

export default cookie
