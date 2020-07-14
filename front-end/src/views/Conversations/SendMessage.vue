<template>
  <div v-if="conversation" class="message-publisher">
    <!-- form -->
    <form method="post" enctype="multipart/form-data">
      <!-- .media -->
      <div class="media mb-1">
        <div class="btn btn-light btn-icon fileinput-button">
          <i class="fa fa-paperclip"></i>
          <input
            type="file"
            id="pm-attachment1"
            v-on:change="onFileChange"
            name="pmAttachment[]"
          />
        </div>
        <div class="btn btn-light btn-icon fileinput-button">
          <i class="far fa-image"  accept="image/*"></i>
          <input
            type="file"
            id="pm-attachment2"
            v-on:change="onImageChange"
            name="pmAttachment[]"
          />
        </div>
        <img class="prev-img" v-if="image !== ''" :src="image"/>
        <div class="prev-file" v-if="file !==''" :src="file"><span class="fa fa-file-alt"></span> {{ file.name }} </div>
        <div class="media-body">
          <input v-model="message"
            type="text"
            class="form-control border-0 shadow-none"
            name="messageText"
            placeholder="Nhập tin nhắn"
          />
        </div>
        <div>
          <button type="button" class="btn btn-light btn-icon">
            <i class="far fa-smile"></i>
          </button>
          <button v-on:click.prevent="send" type="submit" class="btn btn-light btn-icon">
            <i class="far fa-paper-plane"></i>
          </button>
        </div>
      </div>
      <!-- /.media -->
    </form>
    <!-- /form -->
  </div>
  <!-- /message publisher -->
</template>

<script>

import { mapState } from 'vuex'
import VueCryptojs from 'vue-cryptojs'
import Vue from 'vue'
import axios from '../../utils/axios'

Vue.use(VueCryptojs)

export default {
  data () {
    return {
      message: '',
      file: '',
      image: '',
      img: ''
    }
  },
  computed: mapState({
    conversation: state => state.message.conversation,
    encrypt: state => state.message.encrypt
  }),
  methods: {
    onFileChange (e) {
      var files = e.target.files || e.dataTransfer.files
      if (!files.length) {
        return
      }
      this.file = files[0]
    },
    onImageChange (e) {
      var files = e.target.files || e.dataTransfer.files
      if (!files.length) {
        return
      }
      this.createImage(files[0])
      this.img = files[0]
    },
    createImage (file) {
      var reader = new FileReader()
      var vm = this
      reader.onload = (e) => {
        vm.image = e.target.result
      }
      reader.readAsDataURL(file)
    },

    send () {
      var currentObj = this

      const config = {
        headers: { 'content-type': 'multipart/form-data' }
      }

      var formData = new FormData()
      formData.append('file', this.file || this.img)
      formData.append('conversation_id', this.conversation.conversation_id)
      if (this.file || this.img) {
        formData.append('type', this.img ? 'image' : 'file')
        axios.post('/upload ', formData, config)
          .then(data => {
            this.$store.dispatch(
              'sendMessage',
              {
                message: data.data.name,
                conversationId: this.conversation.conversation_id,
                receiverId: this.conversation.contact_id,
                file_id: data.data.id,
                file_type: data.data.type,
                file_path: data.data.path,
                file_name: data.data.name,
                isEncrypt: 0
              })
            this.$store.commit('changeOrder', { conversationId: this.conversation.conversation_id })
          })
          .catch(function (error) {
            currentObj.output = error
          })
        this.file = ''
        this.image = ''
      }

      if (this.message === '') {
        return
      }

      if (!this.$cookie.get('messageKey' + this.conversation.conversation_id)) {
        this.$store.dispatch(
          'sendMessage',
          { message: this.message, conversationId: this.conversation.conversation_id, receiverId: this.conversation.contact_id, isEncrypt: 0 })
        this.$store.commit('changeOrder', { conversationId: this.conversation.conversation_id })
      } else {
        const message = this.CryptoJS.AES.encrypt(this.message, Vue.cookie.get('messageKey' + this.conversation.conversation_id)).toString()
        this.$store.dispatch(
          'sendMessage',
          { originMessage: this.message, message: message, conversationId: this.conversation.conversation_id, receiverId: this.conversation.contact_id, isEncrypt: 1 })
        this.$store.commit('changeOrder', { conversationId: this.conversation.conversation_id })
      }
      this.message = ''
    }
  }
}
</script>

<style>
  .prev-img {
    height: 40px;
    width: auto;
  }
  .prev-file {
  background: aquamarine;
    border-radius: 30px;
    margin-top: 5px;
    padding: 3px;
  }
</style>
