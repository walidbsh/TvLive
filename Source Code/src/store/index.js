import { createStore } from 'vuex'
import axios from 'axios'
import { CapacitorHttp } from '@capacitor/core'

export default createStore({
  state: { 
    urls:{
      websocketURL:  'ws://localhost:1111',
      ionSfuServer:  'ws://localhost:7000/ws',  
      database:      'http://localhost',
      http:          'http://localhost',
      https:         'https://localhost'
    },
    users:[], 
    users_invited:[],
    room:null,
    socket:null,
    user_settings:{
        id:null,
        general:{
          avatar:'/avatar/default.png',
          username:'',
          email:'',
          password:'',
        },
        notifications:{
          friend_requeste:true,
          movie_start:true,
        },
        privacy:{
          profile_visibility:true
        },
        stream:{
          quality:true
        },
        room_options:{
          camera:false,
          mic:true
        }
    }, 
    audio_manager:{
        movie:0.50,
        chat:0.75
    },
    requests:[],
    bubbles:[],
    lives_data:[] /// {'trakID', 'UID'}
  },

  getters: { 

    lives_data: state=>{return state.lives_data},
    bubbles: state=>{return state.bubbles},
    requests: state => {return state.requests},
    audio_manager: state => {return state.audio_manager},
    socket: state => { return state.socket },
    getRoom: state => { return state.room },
    users: state => { return state.users },
    user_settings: state => {
      if (state.user_settings.id == null && localStorage.getItem('user_settings')) {
        state.user_settings = JSON.parse(localStorage.getItem('user_settings'));
      }
      return state.user_settings;
    },
    urls: state => { return state.urls },
    uid: state => { return localStorage.getItem('uid') },
    myFriend: state => { return state.users },
    users_invited: state => { return state.users_invited }
  },

  mutations: {
    setLivesData(state, data){
      state.lives_data.push(data);
      console.log(data)
    },
    setUserToRoom(state, user){
      state.users_invited.push(user);
    },
    setUser(state, user){
      for (var i = 0; i < state.users.length; i++) {
        if(state.users[i].id == user.id)
          return
      }
      state.users.push(user); 
    },
    setRoom(state, room){
      state.room = room; 
    },
    setSocket(state, socket){
      state.socket = socket; 
    },
    setUserSettings(state, settings){ 
      localStorage.setItem('user_settings', JSON.stringify(settings))
      state.user_settings = settings 
    },
    clear_invited_users(state){
      state.users_invited = [] 
    },
    add_bubble(state, bubble){
      state.bubbles.push(bubble) 
    },
    update_requests(state){
      var endpoint = `${state.urls.database}/getRequests.php?uid=${state.user_settings.id}`
      let self = this
      
      CapacitorHttp.get({url: endpoint}).then(res => {
        self.state.requests = res.data
      })
    },
    send_notification(state, payload){ // playload { to, message, } 
      const username = state.user_settings.general.username
      state.socket.emit('send_notification', state.user_settings.id, payload.to, `${username} ${payload.message}`)
    }
  },
  actions: {
    send_notification({commit}, payload){ 
      commit('send_notification', payload)
    },
    add_bubble({commit}, bubble){
      commit('add_bubble', bubble)
    },
    remove_bubble({commit}, bubble){
      commit('remove_bubble', bubble)
    },
    update_requests({commit}){
      commit('update_requests')
    },
    clear_invited_users({commit}){
      commit('clear_invited_users')
    },
    set_user_settings({commit}, settings){
      commit('setUserSettings', settings)
    },
    async update_friends({commit, state}){ 
      var uid = this.getters.user_settings.id
 
      var endpoint = state.urls.database+'/get_friends.php?uid='+uid; 
      CapacitorHttp.get({url: endpoint}).then(res => {
        //console.log(res.data)
        var users = res.data
        for (var i = 0; i < users.length; i++) { 
          commit('setUser',users[i])
        }
      })

/*      await axios.get(state.urls.database+'/get_friends.php?uid='+uid+'&me').then((res=>{
        console.log(res.data)
        commit('setUserProfile', res.data)
      }))*/
    }, 
    setUserToRoom({commit}, user){
      commit('setUserToRoom',user)
    },
    setSocket({commit}, socket){
      commit('setSocket',socket)
    },
    setUser({commit}, user){
      commit('setUser',user)
    },
    setRoom({commit}, room){
      commit('setRoom', room)
    },
    setLivesData({commit}, data){
      commit('setLivesData', data)
    } 
  },
  modules: {
  }
})

