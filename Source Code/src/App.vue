<template>
  <ion-app>
    <notifications position="top center" class="m-2 my-notification"/>
    <ion-router-outlet />
  </ion-app>
</template>

<script setup>
import { IonApp, IonRouterOutlet } from '@ionic/vue';
import { io} from 'socket.io-client'
import { CapacitorHttp} from '@capacitor/core'
</script>


<script type="text/javascript">

import { mapActions, mapGetters  } from 'vuex'
export default{
  methods:{...mapActions(['set_user_settings', 'update_friends', 'setSocket', 'update_requests'])},
  mounted(){ 

    if(localStorage.getItem('user_settings') == null ) /*user has no ID*/
      return

    const id   = this.user_settings.id
    const self = this

    // GET ALL FRIENDS
    this.update_friends(); 
    this.update_requests()

    let socketURL = this.urls.websocketURL 
    this.setSocket( io(socketURL) )


 
    this.socket.on('connect', ()=>{

      this.socket.emit('online', this.user_settings.id)


      var endpoint = `${self.urls.database}/setUserSocketId.php?uid=${this.user_settings.id}&sid=${this.socket.id}`; 

      CapacitorHttp.get({url:endpoint})

      this.$notify({
        type: "success",
        text: "Connected!",
        duration:500
      });
    })
 

    //this.sendNotification({uid:1, text:'test'})
     
    this.socket.on('notification',(uid, text)=>{
      this.$notify({
        type: "success",
        text: uid + ":" +text,
        duration:1000
      })
    })

    this.socket.on('disconnect',()=>{
      this.$notify({
        type: "error",
        text: "you are Offline!",
        duration:1000
      })
    })

    this.socket.on('receive_add',(sender_id, message)=>{
      this.update_requests()
      this.$notify({ 
        text: message,
        duration:2000
      })
    })

    this.socket.on('receive_invit',(sender_id, message)=>{
      this.update_requests()
      this.$notify({ 
        type: "info",
        text: message,
        duration:3000
      })
    })
  
  },
  computed:{
    ...mapGetters(['urls', 'socket', 'user_settings'])
  }
}

</script>



<style>
.notification-content{
  font-size: 16px;
}

.my-notification .success{
  
}

</style>
