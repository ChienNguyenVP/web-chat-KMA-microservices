<template>
  <div>
  </div>
</template>

<script>

import pusher from '../../pusher'
import axios from '../../utils/axios'
import Peer from 'peerjs'
import MediaHandler from '../../utils/MediaHandler'

export default {
  data () {
    const mediaHandler = new MediaHandler()
    return {
      hasMedia: false,
      otherUserId: null,
      mediaHandler,
      stream: null
    }
  },
  created () {
    this.$store.dispatch('getConversation')
    const peer = new Peer()
    peer.on('open', (id) => {
      console.log(id)
      axios.get('user').then(data => {
        var videoChannel = pusher.subscribe(`video-call.${data.data.id}`)
        videoChannel.bind('VideoCall', (data) => {
          this.$store.dispatch('acceptCall', { idCall: id, inviter: data.sender_id })
        })
        var acceptCall = pusher.subscribe(`accept-call.${data.data.id}`)
        acceptCall.bind('AcceptCall', (data) => {
          console.log(data)
          this.mediaHandler.getPermissions().then(stream => {
            this.playStream(stream)
            const call = peer.call(id, stream)
            call.on('stream', remoteStream => this.RequestStream(remoteStream))
          })
          peer.on('call', call => {
            this.mediaHandler.getPermissions().then(stream => {
              call.answer(stream)
              this.playStream(stream)
              call.on('stream', remoteStream => this.RequestStream(remoteStream))
            })
          })
        })
      })
    })
  },
  methods: {
    playStream (stream) {
      try {
        this.$refs.myVideo.srcObject = stream
      } catch (e) {
        this.$refs.myVideo.src = URL.createObjectURL(stream)
      }
      this.$refs.myVideo.play()
    },
    RequestStream (stream) {
      try {
        this.$refs.userVideo.srcObject = stream
      } catch (e) {
        this.$refs.userVideo.src = URL.createObjectURL(stream)
      }
      this.$refs.userVideo.play()
    }
    //   })
    // }
    //     const call = peer.call(id, stream)
    //     call.on('stream', remoteStream => {
    //       try {
    //         this.$refs.userVideo.srcObject = remoteStream
    //       } catch (e) {
    //         this.$refs.userVideo.src = URL.createObjectURL(remoteStream)
    //       }
    //       this.$refs.userVideo.play()
    //       })
    //   })
    //   peer.on('call', call => {
    //     this.mediaHandler.getPermissions().then((stream) => {
    //       call.answer(stream)
    //       try {
    //         this.$refs.myVideo.srcObject = stream
    //       } catch (e) {
    //         this.$refs.myVideo.src = URL.createObjectURL(stream)
    //       }
    //       this.$refs.myVideo.play()
    //       call.on('stream', remoteStream => {
    //         try {
    //           this.$refs.userVideo.srcObject = remoteStream
    //         } catch (e) {
    //           this.$refs.userVideo.src = URL.createObjectURL(remoteStream)
    //         }
    //         this.$refs.userVideo.play()
    //       })
    //     })
    //   })
    // }
  }
}
</script>

<style>

</style>
