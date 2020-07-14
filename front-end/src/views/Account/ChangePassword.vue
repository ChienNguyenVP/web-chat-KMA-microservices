<template>
  <!-- .app -->
  <div class="app">
    <!--[if lt IE 10]>
      <div class="page-message" role="alert">
        You are using an <strong>outdated</strong> browser. Please
        <a class="alert-link" href="http://browsehappy.com/"
          >upgrade your browser</a
        >
        to improve your experience and security.
      </div>
    <![endif]-->
    <!-- .app-header -->
    <header class="app-header app-header-dark">
      <!-- .top-bar -->
      <div class="top-bar">
        <!-- .top-bar-brand -->
        <div class="top-bar-brand">
          <!-- toggle aside menu -->
          <!-- <button
            class="hamburger hamburger-squeeze mr-2"
            type="button"
            data-toggle="aside-menu"
            aria-label="toggle aside menu"
          >
            <span class="hamburger-box"
              ><span class="hamburger-inner"></span
            ></span>
          </button> -->
          <!-- /toggle aside menu -->
          <router-link :to="{ name: 'Home' }"><img class="logo-home" src="assets/images/logo.png" alt=""></router-link>
        </div>
        <!-- /.top-bar-brand -->
        <!-- .top-bar-list -->
        <div class="top-bar-list">
          <!-- .top-bar-item -->
          <div class="top-bar-item px-2 d-md-none d-lg-none d-xl-none">
            <!-- toggle menu -->
            <button
              class="hamburger hamburger-squeeze"
              type="button"
              data-toggle="aside"
              aria-label="toggle menu"
            >
              <span class="hamburger-box"
                ><span class="hamburger-inner"></span
              ></span>
            </button>
            <!-- /toggle menu -->
          </div>
          <!-- /.top-bar-item -->
          <!-- .top-bar-item -->
          <div class="top-bar-item top-bar-item-right px-0 d-none d-sm-flex">
            <!-- .btn-account -->
            <div class="dropdown d-flex">
              <button
                class="btn-account d-none d-md-flex"
                type="button"
                data-toggle="dropdown"
                aria-haspopup="true"
                aria-expanded="false"
              >
                <span class="user-avatar user-avatar-md"
                  >                  <img v-if="user.avatar" :src="user.avatar" alt="">
                  <img v-if="!user.avatar" src="assets/images/avatars/user.png" alt=""></span>
                <span class="account-summary pr-lg-4 d-none d-lg-block"
                  ><span class="account-name">{{ user.name }}</span></span
                >
              </button>
              <!-- .dropdown-menu -->
              <div class="dropdown-menu">
                <div class="dropdown-arrow ml-3"></div>
                <h6 class="dropdown-header d-none d-md-block d-lg-none">
                  {{ user.name }}
                </h6>
                <router-link class="dropdown-item" :to="{ name: 'profile' }">
                  <span class="dropdown-icon oi oi-person"></span> Chỉnh sửa thông tin</router-link>
                <a @click="logout" class="dropdown-item" href=""><span class="dropdown-icon oi oi-account-logout"></span> Đăng xuất</a>
              </div>
              <!-- /.dropdown-menu -->
            </div>
            <!-- /.btn-account -->
          </div>
          <!-- /.top-bar-item -->
        </div>
        <!-- /.top-bar-list -->
      </div>
      <!-- /.top-bar -->
    </header>
    <!-- /.app-header -->
    <!-- .app-aside -->
    <!-- /.app-aside -->
    <!-- .app-main -->
    <main class="app-main">
      <!-- .wrapper -->
      <div class="wrapper">
        <!-- .page -->
        <div class="page">
          <!-- .page-navs -->
          <!-- /.page-navs -->
          <!-- .page-inner -->
          <div class="page-inner">
            <!-- .page-title-bar -->
            <header class="page-title-bar">
              <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                  <li class="breadcrumb-item active">
                    <a href=""
                      ><i class="breadcrumb-icon fa fa-angle-left mr-2"></i
                      >Quay lại cuộc trò truyện</a
                    >
                  </li>
                </ol>
              </nav>
            </header>
            <!-- /.page-title-bar -->
            <!-- .page-section -->
            <div class="page-section">
              <!-- grid row -->
              <div class="row">
                <!-- grid column -->
                <div class="col-lg-4">
                  <!-- .card -->
                  <div class="card card-fluid">
                    <h6 class="card-header">Thông tin</h6>
                    <!-- .nav -->
                    <nav class="nav nav-tabs flex-column border-0">
                      <router-link :to="{ name: 'profile' }" class="nav-link">Thông tin cá nhân</router-link >
                      <router-link :to="{ name: 'changepassword' }" class="nav-link active">Đổi mật khẩu</router-link >
                    </nav>
                    <!-- /.nav -->
                  </div>
                  <!-- /.card -->
                  <footer class="aside-footer border-top p-2">
                    <button class="btn btn-light btn-block text-primary" data-toggle="skin"><span class="d-compact-menu-none">Night mode</span> <i class="fas fa-moon ml-1"></i></button>
                  </footer><!-- /Skin changer -->
                </div>
                <!-- /grid column -->
                <!-- grid column -->
                <div class="col-lg-8">
                  <!-- .card -->
                  <div class="card card-fluid">
                    <h6 class="card-header">Thay đổi mật khẩu</h6>
                    <!-- .card-body -->
                    <div class="card-body">
                      <!-- form -->
                      <form method="post">
                        <!-- form row -->
                        <div class="form-row">
                          <!-- form column -->
                          <label for="input02" class="col-md-3">Mật khẩu cũ</label>
                          <!-- /form column -->
                          <!-- form column -->
                          <div class="col-md-9 mb-3">
                            <input
                              type="password"
                              class="form-control"
                              id="input02"
                              v-model="oldPassword"
                            />
                            <p v-if="errors.current_password" class="text-danger">{{ errors.current_password[0] }}</p>
                            <p v-if="errors.message" class="text-danger">{{ errors.message }}</p>
                          </div>
                          <!-- /form column -->
                        </div>
                        <!-- /form row -->
                         <!-- form row -->
                        <div class="form-row">
                          <!-- form column -->
                          <label for="input03" class="col-md-3">Mật khẩu mới</label>
                          <!-- /form column -->
                          <!-- form column -->
                          <div class="col-md-9 mb-3">
                            <input
                              type="password"
                              class="form-control"
                              id="input03"
                              v-model="newPassword"
                            />
                            <p v-if="errors.password" class="text-danger">{{ errors.password[0] }}</p>
                          </div>
                          <!-- /form column -->
                        </div>
                        <!-- /form row -->
                         <!-- form row -->
                        <div class="form-row">
                          <!-- form column -->
                          <label for="input04" class="col-md-3">Nhập lại mật khẩu</label>
                          <!-- /form column -->
                          <!-- form column -->
                          <div class="col-md-9 mb-3">
                            <input
                              type="password"
                              class="form-control"
                              id="input04"
                              v-model="confirmPassword"
                            />
                          </div>
                          <!-- /form column -->
                        </div>
                        <!-- /form row -->
                        <hr />
                        <!-- .form-actions -->
                        <div class="form-actions">
                          <button v-on:click.prevent="submit" type="submit" class="btn btn-primary ml-auto">
                            Cập nhật
                          </button>
                        </div>
                        <!-- /.form-actions -->
                      </form>
                      <!-- /form -->
                    </div>
                    <!-- /.card-body -->
                  </div>
                  <!-- /.card -->
                  <!-- .card -->
                  <!-- /.card -->
                </div>
                <!-- /grid column -->
              </div>
              <!-- /grid row -->
            </div>
            <!-- /.page-section -->
          </div>
          <!-- /.page-inner -->
        </div>
        <!-- /.page -->
      </div>
      <!-- .app-footer -->
      <footer class="app-footer">
        <ul class="list-inline">
          <li class="list-inline-item">
            <a class="text-muted" href="#">Support</a>
          </li>
          <li class="list-inline-item">
            <a class="text-muted" href="#">Help Center</a>
          </li>
          <li class="list-inline-item">
            <a class="text-muted" href="#">Privacy</a>
          </li>
          <li class="list-inline-item">
            <a class="text-muted" href="#">Terms of Service</a>
          </li>
        </ul>
        <div class="copyright">Copyright © 2018. All right reserved.</div>
      </footer>
      <!-- /.app-footer -->
      <!-- /.wrapper -->
    </main>
    <!-- /.app-main -->
  </div>
  <!-- /.app -->
</template>

<script>
import { mapState } from 'vuex'
import axios from '../../utils/axios'

export default {
  data () {
    return {
      oldPassword: '',
      newPassword: '',
      confirmPassword: '',
      errors: ''
    }
  },
  computed: {
    ...mapState({
      user: state => state.message.user
    })
  },
  created () {
    this.$store.dispatch('detailUser')
  },
  methods: {
    logout () {
      this.$store.dispatch('logout')
    },
    submit () {
      axios.post('changepassword', {
        current_password: this.oldPassword,
        password: this.newPassword,
        password_confirmation: this.confirmPassword
      }).then(data => {
        this.$toast.success(data.data.message, { position: 'top' })
        this.logout()
        setTimeout(() => { location.reload() }, 2000)
      }).catch((e) => {
        this.errors = e.response.data.errors || e.response.data
        console.log(this.errors)
      })
    }
  }
}
</script>

<style></style>
