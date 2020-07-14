import Vue from 'vue'
import VueRouter from 'vue-router'
import Home from '../views/Home.vue'
import Login from '../views/Login.vue'
import SignUp from '../views/Auth/SignUp.vue'
import GoogleLogin from '../views/Auth/GoogleLogin.vue'
import Pusher from '../views/Pusher.vue'
import axios from 'axios'
import VideoCall from '../views/VideoCall.vue'
import Profile from '../views/Account/Profile'
import ChangePassword from '../views/Account/ChangePassword.vue'

Vue.use(VueRouter)

axios.defaults.baseURL = 'http://http://webchat.com:8000'

const routes = [
  {
    path: '/home',
    name: 'Home',
    component: Home
  },
  {
    path: '/pusher',
    name: 'Pusher',
    component: Pusher
  },
  { path: '/', redirect: '/login' },
  {
    path: '/about',
    name: 'About',
    // route level code-splitting
    // this generates a separate chunk (about.[hash].js) for this route
    // which is lazy-loaded when the route is visited.
    component: () => import(/* webpackChunkName: "about" */ '../views/About.vue')
  },
  {
    path: '/login',
    name: 'login',
    component: Login,
    meta: {
      guest: true
    }
  },
  {
    path: '/signup',
    name: 'signup',
    component: SignUp
  },
  {
    path: '/token',
    name: 'googleLogin',
    component: GoogleLogin
  },
  {
    path: '/video',
    name: 'video',
    component: VideoCall
  },
  {
    path: '/profile',
    name: 'profile',
    component: Profile
  },
  {
    path: '/changepassword',
    name: 'changepassword',
    component: ChangePassword
  }
]

const router = new VueRouter({
  mode: 'history',
  base: process.env.BASE_URL,
  routes
})

router.beforeEach((to, from, next) => {
  const publicPages = ['/login', '/signup', '/token']
  const authRequired = !publicPages.includes(to.path)
  const loggedIn = Vue.cookie.get('token')

  if (authRequired && !loggedIn) {
    console.log(!loggedIn)
    return next('/login')
  } else if (to.matched.some(record => record.meta.guest)) {
    if (!loggedIn) {
      next()
    } else {
      next({ name: 'Home' })
    }
  } else {
    next()
  }
})

export default router
