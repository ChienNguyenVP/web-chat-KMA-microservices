<template>
  <div>
    Video Call
    <div>
      <video src="" ref="myVideo"></video>
      <video src="" ref="userVideo"></video>
      <button @click="call">call</button>
    </div>
  </div>
</template>

<script>
import MediaHandler from '../utils/MediaHandler'
import Peer from 'peerjs'
// import Peer from 'simple-peer'

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
    const peer = new Peer()
    peer.on('open', id => console.log(id))
    const id = ''
    this.mediaHandler.getPermissions().then((stream) => {
      try {
        this.$refs.myVideo.srcObject = stream
      } catch (e) {
        this.$refs.myVideo.src = URL.createObjectURL(stream)
      }
      this.$refs.myVideo.play()
      const call = peer.call(id, stream)
      call.on('stream', remoteStream => {
        try {
          this.$refs.userVideo.srcObject = remoteStream
        } catch (e) {
          this.$refs.userVideo.src = URL.createObjectURL(remoteStream)
        }
        this.$refs.userVideo.play()
      })
    })
    peer.on('call', call => {
      this.mediaHandler.getPermissions().then((stream) => {
        call.answer(stream)
        try {
          this.$refs.myVideo.srcObject = stream
        } catch (e) {
          this.$refs.myVideo.src = URL.createObjectURL(stream)
        }
        this.$refs.myVideo.play()
        call.on('stream', remoteStream => {
          try {
            this.$refs.userVideo.srcObject = remoteStream
          } catch (e) {
            this.$refs.userVideo.src = URL.createObjectURL(remoteStream)
          }
          this.$refs.userVideo.play()
        })
      })
    })
    // const peer = new Peer('111')
    // peer.on('call', (call) => {
    //   navigator.mediaDevices.getUserMedia({ video: true, audio: true }, (stream) => {
    //     all.answer(stream); // Answer the call with an A/V stream.
    //     call.on('stream', (remoteStream) => {
    //     // Show stream in some <video> element.
    //     })
    //   }, (err) => {
    //     console.error('Failed to get local stream', err)
    //   })
    // })
  },
  // methods: {
  //   startPeer (userId, initiator = true) {
  //     const peer = new Peer({
  //       initiator,
  //       stream: this.stream,
  //       trickle: false
  //     })
  //     peer.on('signal', (data) => {
  //       this.channel.str
  //     })
  //   }
  // },
  methods: {
    call () {
      const peer = new Peer('1')
      this.mediaHandler.getPermissions().then((stream) => {
        // console.log(stream)
        const call = peer.call('1', stream)
        call.on('stream', (remoteStream) => {
          try {
            this.$refs.myVideo.srcObject = remoteStream
          } catch (e) {
            this.$refs.myVideo.src = URL.createObjectURL(remoteStream)
          }
          this.$refs.myVideo.play()
        })
      }, (err) => {
        console.error('Failed to get local stream', err)
      })
      peer.on('call', (call) => {
        navigator.mediaDevices.getUserMedia({ video: true, audio: true }, (stream) => {
          call.answer(stream) // Answer the call with an A/V stream.
          call.on('stream', (remoteStream) => {
            try {
              this.$refs.userVideo.srcObject = stream
            } catch (e) {
              this.$refs.userVideo.src = URL.createObjectURL(stream)
            }
            this.$refs.userVideo.play()
            console.log(remoteStream)
          })
        }, (err) => {
          console.error('Failed to get local stream', err)
        })
      })
    }
  }
}
</script>

<style></style>
