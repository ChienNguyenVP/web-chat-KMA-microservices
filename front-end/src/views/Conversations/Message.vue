<template>
  <!-- .page -->
  <div class="page has-sidebar has-sidebar-expand-xl">
    <!-- .page-inner -->
    <div class="page-inner page-inner-fill">
      <!-- .message -->
      <div class="message">
        <!-- message header -->
        <div v-if="conversation.contact_name" class="message-header">
          <div class="d-flex">
            <a class="btn btn-light btn-icon" href="page-messages.html"><i class="fa fa-flip-horizontal fa-share"></i></a>
          </div>
          <div class="user-avatar">
              <img v-if="conversation.avatar" :src="conversation.avatar" alt="">
              <img v-if="!conversation.avatar" src="assets/images/avatars/user.png" alt="">
          </div>
          <h4 class="message-title"> {{ conversation.contact_name }} </h4>
          <div class="message-header-actions">
            <Encrypt/>
            <Decrypt/>
            <GetKey/>
            <!-- invite members -->
            <div class="dropdown d-inline-block">
              <button v-if="encrypt === 0" @click="showEncrypt" type="button" class="btn btn-light btn-icon" title="Mở khóa cuộc trò truyện"><i class="fas fa-lock"></i></button> <!-- .dropdown-menu -->
              <button v-if="encrypt === 1" @click="showDecrypt" type="button" class="btn btn-light btn-icon" title="Khóa cuộc trò truyện"><i class="fas fa-lock-open"></i></button> <!-- .dropdown-menu -->
              <!-- <button v-if="encrypt === null" @click="showGetKey" type="button" class="btn btn-light btn-icon" title="Lấy khóa cuộc trò truyện"><i class="fas fa-key"></i></button> .dropdown-menu -->
              <button type="button" class="btn btn-light btn-icon" title="Invite members" data-toggle="dropdown" data-display="static" aria-haspopup="true" aria-expanded="false"><i class="fas fa-user-plus"></i></button> <!-- .dropdown-menu -->
              <button @click="call" type="button" class="btn btn-light btn-icon" title="Invite members" data-toggle="" data-display="static" aria-haspopup="true" aria-expanded="false"><i class="fas fa-phone-square-alt"></i></button> <!-- .dropdown-menu -->
              <div class="dropdown-menu dropdown-menu-right dropdown-menu-rich stop-propagation">
                <div class="dropdown-arrow"></div>
                <div class="dropdown-header"> Add members </div>
                <div class="form-group px-3 py-2 m-0">
                  <input type="text" class="form-control" placeholder="e.g. @bent10" data-toggle="tribute" data-remote="assets/data/tribute.json" data-menu-container="#people-list" data-item-template="true" data-autofocus="true"> <small class="form-text text-muted">Search people by username or email address to invite them.</small>
                </div>
                <div id="people-list" class="tribute-inline stop-propagation"></div><a href="#" class="dropdown-footer">Invite member by link <i class="far fa-clone"></i></a>
              </div><!-- /.dropdown-menu -->
            </div><!-- /invite members -->
            <button type="button" class="btn btn-light btn-icon d-xl-none" data-toggle="sidebar"><i class="fa fa-angle-double-left"></i></button>
          </div>
        </div><!-- /message header -->
        <!-- message body -->
        <div class="message-body" ref="feed">
          <!-- .card -->
          <div v-if="messages.length" class="card card-fluid mb-0">
            <!-- .conversations -->
            <div role="log" class="conversations">
              <!-- .conversation-list -->
              <ul class="conversation-list">
                <!-- .conversation-inbound -->
                <li v-for="message in messages" v-bind:class="`conversation-${message.receiver_id == conversation.contact_id ? 'outbound' : 'inbound'}`" v-bind:key="message.id">
                  <div v-if="message.receiver_id !== conversation.contact_id" class="conversation-avatar">
                    <a href="#" class="user-avatar">
                      <img v-if="conversation.avatar" :src="conversation.avatar" alt="">
                      <img v-if="!conversation.avatar" src="assets/images/avatars/user.png" alt="">
                      <span class="avatar-badge online"></span></a>
                  </div>
                  <div class="conversation-message">
                    <!-- <div v-if="!message.file_id && message.encrypt === 1" class="conversation-message-text encrypt-text">Tin nhắn đã được mã hóa</div> -->
                    <div v-if="!message.file_id" class="conversation-message-text">{{ message.content }}</div>
                    <div v-if="message.file_id && message.file_type === 1" class="conversation-message-text"><img class="message-img" :src="message.file_path" alt=""></div>
                    <div v-if="message.file_id && !message.file_type" class="conversation-message-text"><span>
                  <a :href="message.file_path" class="tile tile-circle bg-teal"><span class="fa fa-file-alt"></span></a>
                  <a class="content-file" :href="message.file_path">{{ message.file_name }}</a>
                  </span>
                        <!-- <i class="far fa-file-alt"></i><a class="" :href="message.file_path">{{ message.file_name }}</a> -->
                  </div>
                  </div>
                  <div class="conversation-actions dropdown">
                    <button class="btn btn-sm btn-icon btn-light" data-toggle="dropdown"><i class="fa fa-fw fa-ellipsis-h"></i></button>
                    <div class="dropdown-menu">
                      <div class="dropdown-arrow ml-n1"></div><button type="button" class="dropdown-item">Copy text</button> <button type="button" class="dropdown-item">Edit</button> <button type="button" class="dropdown-item">Reply</button> <button type="button" class="dropdown-item">Remove</button>
                    </div>
                  </div>
                </li><!-- /.conversation-inbound -->
              </ul><!-- /.conversation-list -->
            </div><!-- /.conversations -->
            <!-- PhotoSwipe (.pswp) element -->
            <div id="gallery" class="pswp" tabindex="-1" role="dialog" aria-hidden="true">
              <!-- .pswp__bg -->
              <div class="pswp__bg"></div><!-- .pswp__scroll-wrap -->
              <div class="pswp__scroll-wrap">
                <!-- .pswp__container -->
                <div class="pswp__container">
                  <div class="pswp__item"></div>
                  <div class="pswp__item"></div>
                  <div class="pswp__item"></div>
                </div><!-- /.pswp__container -->
                <!-- .pswp__ui pswp__ui--hidden -->
                <div class="pswp__ui pswp__ui--hidden">
                  <!-- .pswp__top-bar -->
                  <div class="pswp__top-bar">
                    <div class="pswp__counter"></div><button class="pswp__button pswp__button--close" title="Close (Esc)"></button> <button class="pswp__button pswp__button--share" title="Share"></button> <button class="pswp__button pswp__button--fs" title="Toggle fullscreen"></button> <button class="pswp__button pswp__button--zoom" title="Zoom in/out"></button>
                    <div class="pswp__preloader">
                      <div class="pswp__preloader__icn">
                        <div class="pswp__preloader__cut">
                          <div class="pswp__preloader__donut"></div>
                        </div>
                      </div>
                    </div>
                  </div><!-- /.pswp__top-bar -->
                  <!-- .pswp__share-modal -->
                  <div class="pswp__share-modal pswp__share-modal--hidden pswp__single-tap">
                    <div class="pswp__share-tooltip"></div>
                  </div><!-- /.pswp__share-modal -->
                  <button class="pswp__button pswp__button--arrow--left" title="Previous (arrow left)"></button> <button class="pswp__button pswp__button--arrow--right" title="Next (arrow right)"></button>
                  <div class="pswp__caption">
                    <div class="pswp__caption__center"></div>
                  </div>
                </div><!-- /.pswp__ui pswp__ui--hidden -->
              </div><!-- /.pswp__scroll-wrap -->
            </div><!-- /PhotoSwipe (.pswp) element -->
          </div><!-- /.card -->
        </div><!-- /message body -->
        <VideoCall />
        <!-- message publisher -->
        <SendMessage />
      </div><!-- /.message -->
    </div><!-- /.page-inner -->
    <!-- .page-sidebar -->
    <div v-if="files.length" class="page-sidebar">
      <!-- .sidebar-header -->
      <header class="sidebar-header d-sm-none">
        <ol class="breadcrumb mb-0">
          <li class="breadcrumb-item">
            <a class="prevent-default" href="#" onclick="Looper.toggleSidebar()"><i class="breadcrumb-icon fa fa-angle-left mr-2"></i>Back</a>
          </li>
        </ol>
      </header><!-- /.sidebar-header -->
      <!-- .nav-tabs -->
      <ul class="nav nav-tabs nav-fill" role="tablist">
        <li class="nav-item">
          <a class="nav-link active" data-toggle="tab" href="#team-profile" role="tab" aria-controls="team-profile" aria-selected="true">Hình ảnh</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" data-toggle="tab" href="#message-files" role="tab" aria-controls="message-files" aria-selected="false">Tệp</a>
        </li>
      </ul><!-- /.nav-tabs -->
      <!-- .sidebar-section-fill -->
      <div class="sidebar-section-fill">
        <!-- Tab panes -->
        <div class="tab-content">
          <!-- #team-profile -->
          <div id="team-profile" class="tab-pane fade show active" role="tabpanel" aria-labelledby="team-profile">
            <!-- .list-group -->
            <div class="list-group list-group-reflow list-group-flush list-group-divider">
              <!-- .list-group-header -->
              <div class="list-group-header"> Ảnh </div><!-- /.list-group-header -->
              <!-- .list-group-item -->
              <div class="list-group-item" v-for="file in files" v-bind:key="file.id">
                <img class="list-image" v-if="file.type === 1" :src="file.path" alt="">
              </div><!-- /.list-group-item -->
            </div><!-- /.list-group -->
          </div><!-- /#team-profile -->
          <!-- #message-files -->
          <div id="message-files" class="tab-pane fade" role="tabpanel" aria-labelledby="message-files">
            <!-- .list-group -->
            <div class="list-group list-group-reflow list-group-flush list-group-divider">
              <!-- .list-group-header -->
              <div class="list-group-header"> Tệp </div><!-- /.list-group-header -->
              <!-- .list-group-item -->
              <div  v-for="file in files" v-bind:key="file.id" class="list-group-item align-items-start">
                  <div class="list-group-item-figure" v-if="file.type !== 1">
                    <a :href="file.path" class="tile tile-circle bg-teal"><span class="fa fa-file-alt"></span></a>
                  </div>
                  <div class="list-group-item-body" v-if="file.type !== 1">
                    <h4 class="list-group-item-title text-truncate">
                      <a :href="file.path">{{ file.name }}</a>
                    </h4>
                    <p class="list-group-item-text small"> {{ file.created_at | moment("dddd, MMMM Do YYYY, h:mm:ss a") }}</p>
                  </div>
              </div><!-- /.list-group-item -->
              <!-- .list-group-item -->
            </div><!-- /.list-group -->
          </div><!-- /#message-files -->
        </div><!-- /Tab panes -->
      </div><!-- /.sidebar-section-fill -->
    </div><!-- /.page-sidebar -->
  </div><!-- /.page -->
</template>

<script>

import SendMessage from './SendMessage'
import VideoCall from './VideoCall'
import Encrypt from '../Modal/Encrypt'
import Decrypt from '../Modal/Decrypt'
import GetKey from '../Modal/GetKey'
import { mapState, mapGetters } from 'vuex'

export default {
  name: 'Message',
  components: {
    SendMessage,
    Encrypt,
    VideoCall,
    Decrypt,
    GetKey
  },
  computed: {
    ...mapState({
      conversation: state => state.message.conversation,
      encrypt: state => state.message.encrypt,
      files: state => state.message.files
    }),
    ...mapGetters({
      messages: 'getMessages'
    })
  },
  methods: {
    scrollToBottom () {
      setTimeout(() => {
        this.$refs.feed.scrollTop = this.$refs.feed.scrollHeight - this.$refs.feed.clientHeight
      })
    },
    call () {
      this.$store.dispatch(
        'sendMessage',
        { call: true, message: this.message, conversationId: this.conversation.conversation_id, receiverId: this.conversation.contact_id, isEncrypt: (this.encrypt === 1) ? 1 : 0 })
    },
    showEncrypt () {
      this.$modal.show('encrypt')
    },
    hideEncrypt () {
      this.$modal.hide('encrypt')
    },
    showDecrypt () {
      this.$modal.show('decrypt')
    },
    hideDecrypt () {
      this.$modal.hide('decrypt')
    },
    showGetKey () {
      this.$modal.show('getKey')
    },
    hideGetKey () {
      this.$modal.show('getKey')
    }
  },
  watch: {
    conversation (conversation) {
      this.scrollToBottom()
    },
    messages (messages) {
      this.scrollToBottom()
    }
  }
}
</script>

<style>
  .message-img {
    height: 300px;
    width: auto;
    max-width: 400px;
  }
  .content-file {
    color: #67c23a;
    margin-left: 10px;
  }
  .encrypt-text {
    color: deepskyblue !important;
  }
  .list-image {
    width: 300px;
    margin: auto;
  }
</style>
