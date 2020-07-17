<template>
  <main class="auth">
    <header
      id="auth-header"
      class="auth-header"
      style="background-image: url(assets/images/illustration/img-1.png);"
    >
      <h1>
        <img class="logo" src="assets/images/logo.png" alt="">
        <span class="sr-only"> Đăng ký</span>
      </h1>
      <p>
        Bạn đã có tài khoản? Có thể
        <router-link :to="{ name: 'login' }"
  >Đăng nhập</router-link
>
      </p>
    </header>
    <!-- form -->
    <form class="auth-form">
      <!-- .form-group -->
      <div class="form-group">
        <div class="form-label-group">
          <input v-model="email"
            type="email"
            id="inputEmail"
            class="form-control"
            placeholder="Email"
            required=""
            autofocus=""
          />
          <label for="inputEmail">Email</label>
          <p v-if="errors.email" class="text-danger">{{ errors.email[0] }}</p>
        </div>
      </div>
      <!-- /.form-group -->
      <!-- .form-group -->
      <div class="form-group">
        <div class="form-label-group">
          <input v-model="userName"
            type="text"
            id="inputUser"
            class="form-control"
            placeholder="Username"
            required=""
          />
          <label for="inputUser">Tên</label>
          <p v-if="errors.name" class="text-danger">{{ errors.name[0] }}</p>
        </div>
      </div>
      <!-- /.form-group -->
      <!-- .form-group -->
      <div class="form-group">
        <div class="form-label-group">
          <input v-model="password"
            type="password"
            id="inputPassword"
            class="form-control"
            placeholder="Password"
            required=""
          />
          <label for="inputPassword">Mật khẩu</label>
          <p v-if="errors.password" class="text-danger">{{ errors.password[0] }}</p>
        </div>
      </div>
      <!-- /.form-group -->
            <!-- .form-group -->
      <div class="form-group">
        <div class="form-label-group">
          <input v-model="confirmPassword"
            type="password"
            id="confirmPassword"
            class="form-control"
            placeholder="Confirm password"
            required=""
          />
          <label for="confirmPassword">Nhập lại mật khẩu</label>
        </div>
      </div>
      <!-- /.form-group -->
      <!-- .form-group -->
      <div class="form-group">
        <button v-on:click.prevent="signup" class="btn btn-lg btn-primary btn-block" type="submit">
          Đăng ký
        </button>
      </div>
      <!-- /.form-group -->
      <!-- .form-group -->
      <div class="form-group text-center">
      </div>
      <!-- /.form-group -->
      <!-- recovery links -->
      <p class="text-center text-muted mb-0">
        Bằng cách tạo mật khẩu, bạn đã đồng ý <a href="#">Nguyên tắc</a> và
        <a href="#">Điều khoản chung</a>.
      </p>
      <!-- /recovery links -->
    </form>
    <!-- /.auth-form -->
    <!-- copyright -->
    <footer class="auth-footer">© 2018 All Rights Reserved.</footer>
  </main>
  <!-- /.auth -->
</template>

<script>
import axios from '../../utils/axios'

export default {
  name: 'singup',
  data: function () {
    return {
      email: '',
      userName: '',
      password: '',
      confirmPassword: '',
      errors: ''
    }
  },
  methods: {
    signup () {
      axios.post('signup', {
        email: this.email,
        name: this.userName,
        password: this.password,
        password_confirmation: this.confirmPassword
      }).then(data => {
        this.$router.push({ path: 'login' })
        this.$toast.success('Đăng ký thành công', { position: 'top' })
      }).catch((e) => {
        // console.log(e.response.data.errors)
        this.errors = e.response.data.errors
        console.log(this.errors.email[0])
      })
    }
  }
}
</script>

<style></style>
