import axios from '../../utils/axios'
import Vue from 'vue'
// import cookie from '../../utils/cookie'
import VueCryptojs from 'vue-cryptojs'
Vue.use(VueCryptojs)

export default {
  namespaced: false,
  state: {
    conversations: [],
    messages: [],
    conversation: [],
    user: [],
    encrypt: '',
    files: []
  },
  getters: {
    getMessages: state => {
      var messages = []
      for (var i = 0; i < state.messages.length; i++) {
        messages.push({
          content: (Vue.cookie.get('messageKey' + state.messages[0].conversation_id) && state.messages[i].is_encrypt === 1) ? Vue.CryptoJS.AES.decrypt(state.messages[i].content, Vue.cookie.get('messageKey' + state.messages[0].conversation_id)).toString(Vue.CryptoJS.enc.Utf8) : state.messages[i].content,
          conversation_id: state.messages[i].conversation_id,
          created_at: state.messages[i].created_at,
          deleted_at: state.messages[i].deleted_at,
          id: state.messages[i].id,
          read: state.messages[i].read,
          file_id: state.messages[i].file_id,
          file_name: state.messages[i].file_name,
          file_path: state.messages[i].file_path,
          file_type: state.messages[i].file_type,
          receiver_id: state.messages[i].receiver_id,
          sender_id: state.messages[i].sender_id,
          encrypt: (!Vue.cookie.get('messageKey' + state.messages[0].conversation_id) && state.messages[i].is_encrypt === 1) ? 1 : 0,
          updated_at: state.messages[i].updated_at
        })
      }
      return messages.filter(i => i.conversation_id === state.conversation.conversation_id)
    }
  },
  mutations: {
    setConversations: (state, payload) => {
      state.conversations = payload
    },
    setMessage: (state, payload) => {
      state.messages = payload
    },
    setConversation: (state, payload) => {
      state.conversation = payload
    },
    setConDefault: (state, payload) => {
      var conversation = Vue.cookie.get('conversation')
      state.conversation = payload.filter(i => i.conversation_id === parseInt(conversation))
    },
    changeOrder: (state, payload) => {
      const index = state.conversations.findIndex(i => i.conversation_id === payload.conversationId)
      const item = state.conversations.splice(index, 1)
      state.conversations = [...item, ...state.conversations]
    },
    addMessage: (state, payload) => {
      state.messages.push({
        conversation_id: payload.conversationId,
        receiver_id: payload.receiverId,
        content: payload.message,
        file_id: payload.file_id,
        file_path: payload.file_path,
        file_name: payload.file_name,
        file_type: payload.file_type ? 1 : null
      })
    },
    receiveMessage: (state, payload) => {
      state.messages.push({
        conversation_id: payload.conversation_id,
        receiver_id: payload.receiver_id,
        file_id: payload.file_id,
        file_path: payload.file_path,
        file_name: payload.file_name,
        file_type: payload.file_type ? 1 : null,
        content: (payload.is_encrypt === 1 && Vue.cookie.get('messageKey' + payload.conversation_id)) ? Vue.CryptoJS.AES.decrypt(payload.content, Vue.cookie.get('messageKey' + payload.conversation_id)).toString(Vue.CryptoJS.enc.Utf8) : payload.content
      })
    },
    setUser: (state, payload) => {
      state.user = payload
    },
    setRead: (state, payload) => {
      console.log('read')
      if (state.conversations.filter(i => i.conversation_id === payload)[0]) {
        state.conversations.filter(i => i.conversation_id === payload)[0].unread = 0
      }
    },
    makeUnread: (state, payload) => {
      state.conversations.filter(i => i.conversation_id === payload)[0].unread++
    },
    addConversation: (state, payload) => {
      console.log(state.conversations)
      var conversation = state.conversations.filter(i => i.conversation_id === payload.conversation_id)
      if (conversation.length) {
        console.log(conversation)
        state.conversation = conversation[0]
      } else {
        console.log('2')
        state.conversations.unshift(payload)
        state.conversation = payload
      }
    },
    setEncrypt: (state, payload) => {
      state.encrypt = 1
    },
    setDecrypt: (state, payload) => {
      state.encrypt = 0
    },
    setFiles: (state, payload) => {
      state.files = payload
    }
  },
  actions: {
    getConversation: ({ commit }, state) => {
      axios.get('conversations').then(data => {
        commit('setConversations', data.data)
        var conversation = Vue.cookie.get('conversation')
        if (conversation) {
          commit('setConDefault', data.data)
        }
      }).catch(err => {
        console.log('err.response', err.response)
      })
    },
    contentConversation: ({ commit }, payload) => {
      axios.get(`messages/${payload.conversation.conversation_id}`).then(async data => {
        console.log(data.data)
        let newMessages = []
        const messageFileid = data.data.filter(i => !!i.file_id)
        const fetchPath = await Promise.all(messageFileid.map(item => axios.get(`fileinfo/${item.file_id}`).then(data => ({
          ...item,
          content: (Vue.cookie.get('messageKey' + data.data[0].conversation_id) && item.is_encrypt === 1) ? Vue.CryptoJS.AES.decrypt(item.content, Vue.cookie.get('messageKey' + data.data[0].conversation_id)).toString(Vue.CryptoJS.enc.Utf8) : item.content,
          file_path: data.data[0].path,
          file_type: data.data[0].type ? 1 : null,
          file_name: data.data[0].name
        }))))

        data.data.forEach(element => {
          const findItem = fetchPath.find(i => i.id === element.id)
          if (findItem) {
            newMessages = [...newMessages, findItem]
          } else {
            newMessages = [...newMessages, element]
          }
        })

        console.log('newMessages', newMessages)

        commit('setMessage', newMessages)
        commit('setConversation', payload.conversation)
        if (Vue.cookie.get('messageKey' + payload.conversation.conversation_id)) {
          commit('setEncrypt')
        } else {
          commit('setDecrypt')
        }
      })
    },
    sendMessage: ({ commit }, payload) => {
      axios.post('sendMessage', payload).then(data => {
        if (payload.call !== true) {
          if (payload.originMessage) {
            payload.message = payload.originMessage
          }
          commit('addMessage', payload)
        }
      })
    },
    getCurrentUser: ({ commit }, payload) => {
      axios.get('user').then(data => {
        commit('setUser', data.data)
      })
    },
    receiveMessage: ({ commit }, payload) => {
      if (payload.file_id) {
        axios.get(`fileinfo/${payload.file_id}`).then(data => {
          payload.file_name = data.data[0].name
          payload.file_path = data.data[0].path
          payload.file_type = data.data[0].type
          console.log('receiveMessage', payload)
          commit('receiveMessage', payload)
        })
      } else {
        commit('receiveMessage', payload)
      }
    },
    makeRead: ({ commit }, payload) => {
      axios.post('makeRead', { conversation_id: payload })
      commit('setRead', payload)
    },
    acceptCall: ({ commit }, payload) => {
      axios.post('acceptCall', payload)
    },
    encrypt: ({ commit }, payload) => {
      axios.post('getKey', payload).then(data => {
        if (data.data.message) {
          Vue.$toast.warning(data.data.message, { position: 'top' })
          return
        }
        Vue.$toast.success('Đã lấy khóa tin nhắn', { position: 'top' })
        Vue.cookie.set('messageKey' + payload.conversation_id, data.data, 30)
        axios.get(`messages/${payload.conversation_id}`).then(data => {
          commit('setMessage', data.data)
          commit('setEncrypt')
        })
      })
    },
    decrypt: ({ commit }, payload) => {
      axios.post('checkPassword', payload).then(data => {
        if (data.data.message !== 'correct password') {
          console.log(data.data)
          Vue.$toast.warning(data.data.message, { position: 'top' })
          return
        }
        Vue.$toast.success('Đã gỡ khóa tin nhắn', { position: 'top' })
        Vue.cookie.delete('messageKey' + payload.conversation_id)
        axios.get(`messages/${payload.conversation_id}`).then(data => {
          commit('setMessage', data.data)
          commit('setDecrypt')
        })
      })
    },
    startConversation: ({ commit }, payload) => {
      axios.post('startConversation', payload).then(data => {
        if (Vue.cookie.get('messageKey' + data.data.conversation_id)) {
          commit('setEncrypt')
        } else {
          commit('setDecrypt')
        }
        console.log(data.data)
        commit('addConversation', data.data)
        axios.get(`messages/${data.data.conversation_id}`).then(data => {
          commit('setMessage', data.data)
        })
      })
    },

    getFile: ({ commit }, payload) => {
      axios.post('getfile', payload).then(data => {
        commit('setFiles', data.data)
      })
    }
  }
}
