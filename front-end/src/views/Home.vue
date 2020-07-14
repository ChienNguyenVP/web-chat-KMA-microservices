<template>
<!-- .app -->
    <div class="app">
      <!--[if lt IE 10]>
      <div class="page-message" role="alert">You are using an <strong>outdated</strong> browser. Please <a class="alert-link" href="http://browsehappy.com/">upgrade your browser</a> to improve your experience and security.</div>
      <![endif]-->
      <!-- .app-header -->
      <header class="app-header app-header-dark">
        <!-- .top-bar -->
        <div class="top-bar">
          <!-- .top-bar-brand -->
          <div class="top-bar-brand">
            <!-- toggle aside menu -->
            <router-link to="/home"><img class="logo-home" src="assets/images/logo.png" alt=""></router-link>
          </div><!-- /.top-bar-brand -->
          <!-- .top-bar-list -->
          <div v-if="user.email_verified_at !== null" class="top-bar-list">
            <!-- .top-bar-item -->
            <div class="top-bar-item px-2 d-md-none d-lg-none d-xl-none">
              <!-- toggle menu -->
              <button class="hamburger hamburger-squeeze" type="button" data-toggle="aside" aria-label="toggle menu"><span class="hamburger-box"><span class="hamburger-inner"></span></span></button> <!-- /toggle menu -->
            </div><!-- /.top-bar-item -->
            <!-- .top-bar-item -->
            <div class="top-bar-item top-bar-item-full">
              <!-- .top-bar-search -->
              <form class="top-bar-search" v-on:submit.prevent="search">
                <!-- .input-group -->
                <div class="input-group input-group-search dropdown">
                  <div class="input-group-prepend">
                    <span class="input-group-text"><span class="oi oi-magnifying-glass"></span></span>
                  </div><input v-model="searchUser" type="text" class="form-control" aria-label="Search" placeholder="Tìm bạn bè"> <!-- .dropdown-menu -->
                  <div v-if="userResult !== ''" class="dropdown-menu dropdown-menu-rich dropdown-menu-xl ml-n4 mw-100 show">
                    <div class="dropdown-arrow ml-3"></div><!-- .dropdown-scroll -->
                    <div class="dropdown-scroll perfect-scrollbar h-auto" style="max-height: 360px">
                      <!-- .list-group -->
                      <div class="list-group list-group-flush list-group-reflow mb-2">
                          <h6 class="list-group-header d-flex justify-content-between">
                            <span>Kết quả</span>
                          </h6>
                        <div class="list-group-item py-2">
                          <div class="user-avatar user-avatar-sm bg-muted">
                            <img :src="userResult[0].avatar" alt="">
                          </div>
                          <div class="ml-2 user-result">
                            <div class="mb-n1 name-result"> {{ userResult[0].name }} </div>
                            <el-button v-if="checkFriend === false" v-on:click.prevent="addFriend" type="primary" round><i class="fas fa-user-plus"></i></el-button>
                            <el-button v-if="checkFriend === true" type="success" round><i class="fas fa-user-friends"></i></el-button>
                            <el-button v-if="checkFriend === null" type="warning" round><i class="fas fa-reply"></i></el-button>
                            <el-button v-on:click.prevent="removeSearch" type="primary" round><i class="fas fa-backspace"></i></el-button>
                          </div>
                        </div><!-- /.list-group-item -->
                      </div><!-- /.list-group -->
                    </div><!-- /.dropdown-scroll -->
                    <!-- <a href="#" class="dropdown-footer">Show all results</a> -->
                  </div><!-- /.dropdown-menu -->
                </div><!-- /.input-group -->
              </form><!-- /.top-bar-search -->
            </div><!-- /.top-bar-item -->
            <!-- .top-bar-item -->
            <div class="top-bar-item top-bar-item-right px-0 d-none d-sm-flex">
              <!-- .nav -->
              <ul class="header-nav nav">
                <!-- .nav-item -->
                <li class="nav-item dropdown header-nav-dropdown">
                  <a class="nav-link" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-user-friends"></i></a> <!-- .dropdown-menu -->
                  <div class="friend dropdown-menu dropdown-menu-rich dropdown-menu-right">
                    <div class="dropdown-arrow"></div>
                    <h6 class="dropdown-header stop-propagation">
                      <span>Danh sách bạn bè <span class="badge">{{ friends.length }}</span></span>
                    </h6><!-- .dropdown-scroll -->
                    <div class="dropdown-scroll perfect-scrollbar">
                      <!-- .dropdown-item -->
                      <a href="#"  class="dropdown-item unread" v-for="friend in friends" :key="friend.id" v-on:click.prevent="startConversation(friend.contact_id)">
                        <div class="user-avatar">
                          <img v-if="friend.avatar" :src="friend.avatar" alt="">
                          <img v-if="!friend.avatar" src="assets/images/avatars/user.png" alt="">
                        </div>
                        <div class="dropdown-item-body">
                          <p class="text">{{ friend.name }}</p><span class="date">Chat ngay</span>
                        </div>
                      </a> <!-- /.dropdown-item -->
                    </div><!-- /.dropdown-scroll -->
                    <a href="user-activities.html" class="dropdown-footer">All activities <i class="fas fa-fw fa-long-arrow-alt-right"></i></a>
                  </div><!-- /.dropdown-menu -->
                </li><!-- /.nav-item -->
                <!-- .nav-item -->
                <li class="nav-item dropdown header-nav-dropdown" v-bind:class="hasRequest ? 'has-notified' : ''" v-on:click="seenRequest">
                  <a class="nav-link" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-user-plus"></i></a> <!-- .dropdown-menu -->
                  <div class="friend dropdown-menu dropdown-menu-rich dropdown-menu-right">
                    <div class="dropdown-arrow"></div>
                    <h6 class="dropdown-header stop-propagation">
                      <span>Lời mời kết bạn <span class="badge">{{ requests.length }}</span></span>
                    </h6><!-- .dropdown-scroll -->
                    <div class="dropdown-scroll perfect-scrollbar">
                      <!-- .dropdown-item -->
                      <div class="dropdown-item unread" v-for="request in requests" :key="request.id">
                        <div class="user-avatar">
                          <img v-if="request.avatar" :src="request.avatar" alt="">
                          <img v-if="!request.avatar" src="assets/images/avatars/user.png" alt="">
                        </div>
                        <div class="dropdown-item-body">
                          <p class="text user-name">{{ request.name }}</p><span class="date"></span>
                          <div class="action-friend">
                            <el-button v-on:click="accept(request.id)" type="primary"><i class="fas fa-check-square"></i></el-button>
                            <el-button v-on:click="reject(request.id)" type="danger"><i class="fas fa-user-times"></i></el-button>
                          </div>
                        </div>
                      </div> <!-- /.dropdown-item -->
                    </div><!-- /.dropdown-scroll -->
                    <a href="user-activities.html" class="dropdown-footer">All activities <i class="fas fa-fw fa-long-arrow-alt-right"></i></a>
                  </div><!-- /.dropdown-menu -->
                </li><!-- /.nav-item -->
                <!-- .nav-item -->
                <li class="nav-item dropdown header-nav-dropdown">
                  <a class="nav-link" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="oi oi-grid-three-up"></span></a> <!-- .dropdown-menu -->
                  <div class="dropdown-menu dropdown-menu-rich dropdown-menu-right">
                    <div class="dropdown-arrow"></div><!-- .dropdown-sheets -->
                    <div class="dropdown-sheets">
                      <!-- .dropdown-sheet-item -->
                      <div class="dropdown-sheet-item">
                        <a href="#" class="tile-wrapper"><span class="tile tile-lg bg-indigo"><i class="oi oi-people"></i></span> <span class="tile-peek">Teams</span></a>
                      </div><!-- /.dropdown-sheet-item -->
                      <!-- .dropdown-sheet-item -->
                      <div class="dropdown-sheet-item">
                        <a href="#" class="tile-wrapper"><span class="tile tile-lg bg-teal"><i class="oi oi-fork"></i></span> <span class="tile-peek">Projects</span></a>
                      </div><!-- /.dropdown-sheet-item -->
                      <!-- .dropdown-sheet-item -->
                      <div class="dropdown-sheet-item">
                        <a href="#" class="tile-wrapper"><span class="tile tile-lg bg-pink"><i class="fa fa-tasks"></i></span> <span class="tile-peek">Tasks</span></a>
                      </div><!-- /.dropdown-sheet-item -->
                      <!-- .dropdown-sheet-item -->
                      <div class="dropdown-sheet-item">
                        <a href="#" class="tile-wrapper"><span class="tile tile-lg bg-yellow"><i class="oi oi-fire"></i></span> <span class="tile-peek">Feeds</span></a>
                      </div><!-- /.dropdown-sheet-item -->
                      <!-- .dropdown-sheet-item -->
                      <div class="dropdown-sheet-item">
                        <a href="#" class="tile-wrapper"><span class="tile tile-lg bg-cyan"><i class="oi oi-document"></i></span> <span class="tile-peek">Files</span></a>
                      </div><!-- /.dropdown-sheet-item -->
                    </div><!-- .dropdown-sheets -->
                  </div><!-- .dropdown-menu -->
                </li><!-- /.nav-item -->
              </ul><!-- /.nav -->
              <!-- .btn-account -->
              <div class="dropdown d-flex">
                <button class="btn-account d-none d-md-flex" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="user-avatar user-avatar-md"><img v-if="user.avatar" :src="user.avatar" alt=""><img v-if="!user.avatar" src="assets/images/avatars/user.png" alt=""></span> <span class="account-summary pr-lg-4 d-none d-lg-block"><span class="account-name">{{ user.name}}</span> <span class="account-description"></span></span></button> <!-- .dropdown-menu -->
                <div class="dropdown-menu profile">
                  <div class="dropdown-arrow ml-3"></div>
                  <h6 class="dropdown-header d-none d-md-block d-lg-none"> {{ user.name }} </h6><a class="dropdown-item" href="http://webchat.com:8081/profile"><span class="dropdown-icon oi oi-person"></span> Thông tin cá nhân</a> <a @click="logout" class="dropdown-item" href=""><span class="dropdown-icon oi oi-account-logout"></span> Đăng xuất</a>
                  <!-- <div class="dropdown-divider"></div><a class="dropdown-item" href="#">Help Center</a> <a class="dropdown-item" href="#">Ask Forum</a> <a class="dropdown-item" href="#">Keyboard Shortcuts</a> -->
                </div><!-- /.dropdown-menu -->
              </div><!-- /.btn-account -->
            </div><!-- /.top-bar-item -->
          </div><!-- /.top-bar-list -->
        </div><!-- /.top-bar -->
      </header><!-- /.app-header -->
      <!-- .app-aside -->
      <aside v-if="user.email_verified_at !== null" class="app-aside app-aside-expand-md app-aside-light">
        <!-- .aside-content -->
        <div class="aside-content">
          <!-- .aside-header -->
          <header class="aside-header d-block d-md-none">
            <!-- .btn-account -->
            <button class="btn-account" type="button" data-toggle="collapse" data-target="#dropdown-aside"><span class="user-avatar user-avatar-lg"><img :src="user.avatar" alt=""></span> <span class="account-icon"><span class="fa fa-caret-down fa-lg"></span></span> <span class="account-summary"><span class="account-name">Beni Arisandiaa</span> <span class="account-description">111Marketing Manager</span></span></button> <!-- /.btn-account -->
            <!-- .dropdown-aside -->
            <div id="dropdown-aside" class="dropdown-aside collapse">
              <!-- dropdown-items -->
              <div class="pb-3">
                <a class="dropdown-item"><span class="dropdown-icon oi oi-person"></span> Profile</a> <a class="dropdown-item" href="auth-signin-v1.html"><span class="dropdown-icon oi oi-account-logout"></span> Logout</a>
                <div class="dropdown-divider"></div><a class="dropdown-item" href="#">Help Center</a> <a class="dropdown-item" href="#">Ask Forum</a> <a class="dropdown-item" href="#">Keyboard Shortcuts</a>
              </div><!-- /dropdown-items -->
            </div><!-- /.dropdown-aside -->
          </header><!-- /.aside-header -->
          <!-- .aside-menu -->
          <!-- here -->
          <Conversations />
          <!-- Skin changer -->
          <footer class="aside-footer border-top p-2">
            <button class="btn btn-light btn-block text-primary" data-toggle="skin"><span class="d-compact-menu-none">Night mode</span> <i class="fas fa-moon ml-1"></i></button>
          </footer><!-- /Skin changer -->
        </div><!-- /.aside-content -->
      </aside><!-- /.app-aside -->
      <!-- .app-main -->
      <main class="app-main">
        <!-- .wrapper -->
        <div class="wrapper">
          <div v-if="user.email_verified_at === null">
            <div class="alert alert-warning" role="alert">
              <h4>Chúng tôi đã gửi yêu cầu xác thực tài khoản đến email của bạn
                <br>Vui lòng kiểm tra
              </h4>
            </div>
            <small>Sau khi xác nhận, click làm mới để truy cập hệ thống</small>
            <div><button @click="refresh" type="button" class="btn btn-info">Làm mới</button></div>
          </div>
          <Message v-if="user.email_verified_at !== null" />
        </div><!-- /.wrapper -->
      </main><!-- /.app-main -->
    </div><!-- /.app -->
</template>
<script>
import Conversations from './Conversations/Conversations'
import Message from './Conversations/Message'
import axios from '../utils/axios'
import { mapState } from 'vuex'
import pusher from '../pusher'

export default {
  data () {
    return {
      searchUser: '',
      userResult: '',
      checkFriend: false,
      hasRequest: false
    }
  },
  components: {
    Conversations,
    Message
  },
  name: 'Home',
  computed: {
    ...mapState({
      user: state => state.message.user,
      friends: state => state.contact.friends,
      requests: state => state.contact.requests
    })
  },
  created () {
    this.$store.dispatch('getFriends')
    this.$store.dispatch('getRequests')
    this.subscribe()
  },
  methods: {
    refresh () {
      location.reload()
    },
    logout () {
      this.$store.dispatch('logout')
    },
    search () {
      if (this.searchUser === '') {
        return
      }
      this.checkFriend = false
      axios.post('searchUser', {
        email: this.searchUser
      }).then((response) => {
        this.userResult = response.data
        axios.post('checkFriend', {
          contact_id: response.data[0].id
        }).then((response) => {
          if (response.data.message === true) {
            this.checkFriend = true
          }
          if (response.data.message === 'pending') {
            this.checkFriend = null
          }
        })
      })
    },
    removeSearch () {
      this.userResult = ''
    },
    addFriend () {
      this.$store.dispatch('addFriend', { contact_id: this.userResult[0].id }).then(
        this.userResult = ''
      )
    },
    accept (contactId) {
      this.$store.dispatch('accepFriend', { id: contactId })
    },
    startConversation (contactId) {
      this.$store.dispatch('startConversation', { contact_id: contactId })
    },

    subscribe () {
      axios.get('user').then(data => {
        var channel = pusher.subscribe(`requestFriend.${data.data.id}`)
        channel.bind('RequestFriend', (data) => {
          console.log(data)
          this.hasRequest = true
          this.$store.commit('addRequest', data.request)
        })
      })
    },

    seenRequest () {
      this.hasRequest = false
    }
  }
}
</script>
<style>
  .name-result {
    padding-top: 3px;
    width: 230px;
  }
  .user-result {
    display: flex;
  }
  .user-name {
    padding-bottom: 5px;
  }
  .action-friend {
    float:right;
  }
  .friend {
    position: absolute !important;
    top: 36px !important;
    left: -276px !important;
    will-change: top, left !important;
  }
  .profile {
    position: absolute !important;
    top: 36px !important;
    left: -27px !important;
    will-change: top, left !important;
    /* -27px, 56px */
  }
  .logo-home {
    width: 70px;
    margin-left: 50px;
  }
</style>
