<template>
<div>1234</div>
</template>

<script>
import { setHeaders } from '../../utils/axios'

export default {
  name: 'GoogleLogin',
  created () {
    this.checkLogin()
  },
  methods: {
    async checkLogin () {
      this.$cookie.set('token', this.$route.query.token, 30)
      setHeaders({ Authorization: 'Bearer ' + this.$route.query.token })
      await this.$store.dispatch('GETUSER')
      this.$router.push('Home')
    }
  }
}
</script>

<style>

</style>
