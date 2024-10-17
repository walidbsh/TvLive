<template> 
    <div v-if="exist" class="d-flex align-items-center m-2 p-1 shadow-sm rounded"> 
      <img class="col-2 rounded-50" :src="urls.database + user.avatar">
      <h6 class="ms-2 roboto-thin-4">{{user.username}}</h6>
      <ion-button @click="delete_()" class="ms-auto">
        <ion-icon slot="icon-only" :icon="closeOutline"></ion-icon>
      </ion-button> 
      <ion-button @click="accepte()" class="ms-1">
        <ion-icon slot="icon-only"  :icon="checkmarkOutline"></ion-icon>
      </ion-button>
    </div> 
</template>


<script type="text/javascript">
import { IonButton } from '@ionic/vue'
import { closeOutline, checkmarkOutline } from 'ionicons/icons'
import { mapActions } from 'vuex'
import { mapGetters } from 'vuex'
import { CapacitorHttp } from '@capacitor/core'

export default{
  name:'FriendRequest',
  components:{
    IonButton
  },
  data(){
    return {
      state:0,
      exist:true,
      checkmarkOutline,
      closeOutline
    }
  },
  props:{
    user:Object
  },
  mounted(){
  },
  methods:{
    ...mapActions(['update_requests', 'update_friends']),
    accepte(){
      var endpoint = `${this.urls.database}/aprove_request.php?uid=${this.user_settings.id}&friend=${this.user.id}&type=accept`;
      this.exist = false 
      let self = this
      console.log(endpoint)
      CapacitorHttp.get({url:endpoint}).then(res => {
        self.update_requests()
        self.update_friends()
      })
    },
    delete_(){
      var endpoint = `${this.urls.database}/aprove_request.php?uid=${this.user_settings.id}&friend=${this.user.id}&type=remove`;
      this.exist = false
 
      CapacitorHttp.get({url:endpoint}).then(res => {
        this.update_requests()
        self.update_friends()
      })
    }
  },
  computed:{...mapGetters(['socket', 'myFriend', 'urls', 'user_settings'])}
}
</script>


<style scoped>
.roboto-thin-4 {
  font-family: "Roboto", sans-serif;
  font-weight: 400;
  font-style: normal;
}

.roboto-thin{
  font-family: "Roboto", sans-serif;
  font-weight: 300;
  font-style: normal;
}
.border-2{
  border: 2px solid black;
}

.rounded-50{
  width: 60px;
  height: 60px;
}
</style>