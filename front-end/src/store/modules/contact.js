import axios from '../../utils/axios'
// import Vue from 'vue'
// import router from '../../router'

export default {
  state: {
    friends: [],
    requests: []
  },
  getters: {

  },
  mutations: {
    setfriends: (state, payload) => {
      state.friends = payload
    },
    setRequests: (state, payload) => {
      state.requests = payload
    },
    addRequest: (state, payload) => {
      state.requests.unshift(payload)
    },
    addNewFriend: (state, payload) => {
      state.friends.unshift(payload)
    },
    removeRequest: (state, payload) => {
      state.requests.filter(i => i.id !== payload.id)
    }
  },
  actions: {
    addFriend: ({ commit }, payload) => {
      axios.post('addFriend', payload).then(data => {
        console.log(data.data)
      })
    },
    getFriends: ({ commit }, payload) => {
      axios.get('getFriends').then(data => {
        commit('setfriends', data.data)
      })
    },
    getRequests: ({ commit }, payload) => {
      axios.get('getRequests').then(data => {
        commit('setRequests', data.data)
      })
    },
    accepFriend: ({ commit }, payload) => {
      axios.post('acceptFriend', payload).then(data => {
        commit('addNewFriend', data.data)
        commit('removeRequest', payload)
      })
    }
  }
}
