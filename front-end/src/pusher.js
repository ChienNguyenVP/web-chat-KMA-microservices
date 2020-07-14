import Pusher from 'pusher-js'

Pusher.logToConsole = true

const pusher = new Pusher('9baadb173fdccc127371', {
  cluster: 'ap1'
})

export default pusher
