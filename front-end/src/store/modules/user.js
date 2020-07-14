import axios, { setHeaders } from '../../utils/axios'
import Vue from 'vue'
import router from '../../router'
import { userService } from '../../services/user.service'

export default {
  state: {
    user: [],
    errors: []
  },
  mutations: {
    setErrors: (state, payload) => {
      state.errors = payload
    }
  },
  actions: {
    LOGIN: async ({ commit, dispatch }, payload) => {
      try {
        const accessToken = await axios.post('login', payload)
          .then(res => res.data.access_token).catch((e) => {
            if (e.response.data.message) {
              console.log(e.response.data.message)
              Vue.$toast.warning(e.response.data.message, { position: 'top' })
            }
            commit('setErrors', e.response.data.errors)
          })
        if (accessToken) {
          Vue.cookie.set('token', accessToken, 30)
          setHeaders({ Authorization: 'Bearer ' + accessToken })
          await dispatch('GETUSER')
          router.push({ name: 'Home' })
        }
      } catch {
      }
    },
    SIGNUP ({ commit }, payload) {
      axios.post('signup', payload).then(
        router.push({ path: 'login' }),
        console.log('123')
      ).catch(err => {
        console.log(err)
      })
    },
    logout ({ commit }) {
      axios.get('logout')
      userService.logout()
      Vue.cookie.delete('token')
      commit('logout')
    },
    GETUSER: async ({ commit }, payload) => {
      const user = await axios.get('user').then(res => res.data)
      commit('setUser', user)
      router.push('Home')
    },
    detailUser: async ({ commit }, payload) => {
      const user = await axios.get('user').then(res => res.data)
      commit('setUser', user)
    },
    editUser: ({ commit }, payload) => {
      const user = axios.post('editUser', payload).then(res => res.data)
      // commit('setUser', user)
      console.log(user)
    }
  }
}
