import Vue from 'vue'
import Vuex from 'vuex'
import User from './modules/user'
import Message from './modules/message'
import Contact from './modules/contact'
import axios from '../utils/axios'

Vue.use(Vuex)

export default new Vuex.Store({
  state: {
    currentUser: []
  },
  mutations: {
    setCurrentUser: (state, payload) => {
      state.currentUser = payload
    }
  },
  actions: {
    getUser: ({ commit }) => {
      axios.get('user').then(data => {
        commit('setCurrentUser', data.data)
      })
    }
  },
  modules: {
    user: User,
    message: Message,
    contact: Contact
  }
})
