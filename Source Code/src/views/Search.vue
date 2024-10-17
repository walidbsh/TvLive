

<script> 


import { CapacitorHttp } from '@capacitor/core'
import { mapGetters } from 'vuex' 
import { mapActions } from 'vuex'
 


import { IonSearchbar } from '@ionic/vue';


import UserLine from '../components/UserLine.vue' 
import AppTemplate from '../components/AppTemplate.vue' 


export default {
  name: 'search',
  components:{
    UserLine, 
    AppTemplate, 
    IonSearchbar
  },
  data(){
    return {
      users:[]
    }
  },
  created(){ 
  },
  mounted(){
   },
  methods:{
    ...mapActions(['setUser']),
    
 
    find(event){ 
      var key = event.target.value.replace(/^\s+/, '');
      if(key == ''){
        this.users = []
        return
      }
      let self = this;
      var endpoint = this.urls.database + '/searchFriends.php?key='+key
 
      CapacitorHttp.get({ 
          url: endpoint,
      }).then(({ data }) => {
        self.users = data;
      }) 
    }
  },
  computed:{
    ...mapGetters(['urls'])
  }
}
</script>
 
<template>  
 
  <app-template header_title="Search" :back_button="true" :notif="true"> 
  
    <ion-searchbar class="p-2" :debounce="500" @ionInput="find($event)"></ion-searchbar>
    <UserLine v-for='user in users' :key='user.id' :user="user"></UserLine>
   
   </app-template>

</template>
