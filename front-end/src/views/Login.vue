<template>
  <main class="auth">
    <header
      id="auth-header"
      class="auth-header"
      style="background-image: url(assets/images/illustration/img-1.png);"
    >
      <h1>
        <img class="logo" src="assets/images/logo.png" alt="">
        <span class="sr-only">Sign In</span>
      </h1>
      <p>
        Bạn chưa có tài khoản?
        <router-link to="/signup">Đăng ký</router-link>
      </p>
    </header>
    <!-- form -->
    <form class="auth-form">
      <!-- .form-group -->
      <div class="form-group">
        <div class="form-label-group">
          <input
            v-model="userName"
            type="text"
            id="inputUser"
            class="form-control"
            placeholder="Tài khoản"
            autofocus
          />
          <label for="inputUser">Email</label>
          <p v-if="errors && errors.email" class="text-danger">{{ errors.email[0] }}</p>
        </div>
      </div>
      <!-- /.form-group -->
      <!-- .form-group -->
      <div class="form-group">
        <div class="form-label-group">
          <input v-model="password" type="password" id="inputPassword" class="form-control" placeholder="Mật khẩu" />
          <label for="inputPassword">Mật khẩu</label>
          <p v-if="errors && errors.password" class="text-danger">{{ errors.password[0] }}</p>
        </div>
      </div>
      <!-- /.form-group -->
      <!-- .form-group -->
      <div class="form-group">
        <button
          v-on:click.prevent="login"
          class="btn btn-lg btn-primary btn-block"
          type="submit"
        >Đăng nhập</button>
      </div>
      <!-- /.form-group -->
      <!-- .form-group -->
      <div class="form-group text-center">
        <div class="custom-control custom-control-inline custom-checkbox">
          <input type="checkbox" class="custom-control-input" id="remember-me" />
          <label class="custom-control-label" for="remember-me">Lưu đăng nhập</label>
        </div>
      </div>
      <!-- /.form-group -->
      <!-- recovery links -->
      <div class="text-center pt-3">
        <a href="http://api.webchat.com:8000/login/google" class="link"><i class="fab fa-google"></i>oogle</a>
        <!-- <span class="mx-2">·</span>
        <a href="auth-recovery-password.html" class="l">Forgot Password?</a> -->
      </div>
      <!-- <div class="text-center pt-3">
        <a href="auth-recovery-username.html" class="link">Forgot Username?</a>
        <span class="mx-2">·</span>
        <a href="auth-recovery-password.html" class="link">Forgot Password?</a>
      </div> -->
      <!-- /recovery links -->
    </form>
    <!-- /.auth-form -->
    <!-- copyright -->
    <footer class="auth-footer">
      © 2018 All Rights Reserved.
      <a href="#">Privacy</a> and
      <a href="#">Terms</a>
    </footer>
  </main>
  <!-- /.auth -->
</template>
<script>

import { mapState } from 'vuex'

export default {
  created () {
    console.log(this.$cookie.get('token'))
  },
  computed: mapState({
    errors: state => state.user.errors
  }),
  name: 'Login',
  data: function () {
    return {
      userName: '',
      password: ''
    }
  },
  methods: {
    login () {
      this.$store.dispatch('LOGIN', {
        email: this.userName,
        password: this.password
      })
    }
  }
}
</script>
<style>
.logo {
  width: 200px;
}
</style>
