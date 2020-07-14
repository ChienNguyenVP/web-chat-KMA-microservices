<template>
  <div class="aside-menu overflow-hidden">
    <!-- .stacked-menu -->
    <nav id="stacked-menu" class="stacked-menu">
      <!-- .menu -->
      <ul class="menu conversation-list">
        <!-- /.menu-item -->
        <li class="menu-header">Danh sách trò truyện</li>
        <!-- /.menu-item -->
        <!-- .menu-item -->
        <li v-for="con in conversations" v-bind:key="con.id" class="menu-item" v-bind:class="conversation.conversation_id === con.conversation_id ? 'has-active' : ''"  @click="openConversation(con)">
          <div class="menu-link conversation">
            <div class="user-avatar avatar-conversation">
              <img v-if="con.avatar" :src="con.avatar" alt="">
              <img v-if="!con.avatar" src="assets/images/avatars/user.png" alt="">
            </div>
            <span class="menu-text contact-name">{{ con.contact_name }}</span>
            <div class="notification-conversation rounded-circle" v-if="con.unread > 0">{{ con.unread }}</div></div>
        </li>
      </ul>
      <!-- /.menu -->
    </nav>
    <!-- /.stacked-menu -->
  </div>
  <!-- /.aside-menu -->
  <!-- .app-aside -->
</template>

<script>

import { mapState } from 'vuex'
import pusher from '../../pusher'
import axios from '../../utils/axios'
// import Vue from 'vue'

export default {
  data () {
    return {
      open: false,
      active: null
    }
  },
  computed: {
    ...mapState({
      conversations: state => state.message.conversations,
      user: state => state.message.user,
      conversation: state => state.message.conversation
    })
  },
  created () {
    this.$store.dispatch('getConversation')
    this.subscribe()
    // this.$store.dispatch('contentDefault')
  },
  methods: {
    openConversation (conversation) {
      this.active = conversation.conversation_id
      this.$cookie.set('conversation', conversation.conversation_id)
      this.$store.dispatch('contentConversation', { conversation })
      this.$store.dispatch('getFile', { conversation_id: conversation.conversation_id })
      this.$store.dispatch('makeRead', conversation.conversation_id)
    },
    subscribe () {
      axios.get('user').then(data => {
        var channel = pusher.subscribe(`message.${data.data.id}`)
        channel.bind('SendMessage', (data) => {
          this.$store.dispatch('receiveMessage', data.message)
          this.$store.commit('makeUnread', data.message.conversation_id)
          if (data.message.conversation_id === this.conversation.conversation_id) {
            this.$store.dispatch('makeRead', this.conversation.conversation_id)
          }
          this.$store.commit('changeOrder', { conversationId: data.message.conversation_id })
        })
        var conChannel = pusher.subscribe(`conversation.${data.data.id}`)
        conChannel.bind('Conversation', (data) => {
          this.$store.commit('addConversation', data.conversation)
          this.$store.commit('setDecrypt')
        })
      })
    }

  },

  destroyed () {
    pusher.unsubscribe(`message.${this.user.id}`)
  }
}
</script>

<style>
  .conversation {
    float: left ;
    width: 100%;
    text-align: left;
    padding-left: 35px !important;
  }
  .contact-name {
    padding-left: 10px;
  }
  .notification-conversation {
    position: absolute;
    right: 40px;
    top: 0px;
    color: #f56c6c;
    background-color: rgba(20, 31, 25, 0.15);
  }
  .has-active .conversation {
    background-color: antiquewhite !important;
  }
  .menu-item {
    cursor: pointer;
  }
</style>
