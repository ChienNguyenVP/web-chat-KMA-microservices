<template>
  <div>
    <modal name="encrypt" :width="400" :height="200">
      <el-container>
      <el-header><h5>Nhập mật khẩu</h5></el-header>
      <el-container>
        <el-container>
          <el-main>
            <el-input type="password" v-model="password"></el-input>
          </el-main>
          <el-footer>
            <el-button @click="encrypt" type="primary">Lấy khóa</el-button>
            <el-button @click="$modal.hide('encrypt')" type="info">Bỏ qua</el-button>
          </el-footer>
        </el-container>
      </el-container>
    </el-container>
    </modal>

  </div>
</template>
<script>
import { mapState } from 'vuex'
export default {
  data () {
    return {
      password: ''
    }
  },
  computed: mapState({
    conversation: state => state.message.conversation
  }),
  methods: {
    encrypt () {
      this.$store.dispatch('encrypt', { password: this.password, conversation_id: this.conversation.conversation_id })
      this.$modal.hide('encrypt')
    }
  }
}
</script>

<style>

</style>
