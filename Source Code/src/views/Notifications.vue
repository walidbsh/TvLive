

<script> 
 
import axios from 'axios'
import { mapGetters } from 'vuex' 
import { mapActions } from 'vuex'

import AppTemplate   from '../components/AppTemplate.vue'
import FriendRequest from '../components/FriendRequest.vue'
import { CapacitorHttp } from '@capacitor/core'

export default {
  name: 'notifications',
  components:{
    FriendRequest, 
    AppTemplate
  },
  data(){
    return { 
    }
  },
  created(){ 
  },
  mounted(){

   // this.$refs.input.value = 'l'
    //this.find()
  },
  methods:{
    ...mapActions(['setUser', 'update_requests']),  
  },
  mounted(){

 
  },
  computed:{
    ...mapGetters(['urls', 'uid', 'requests'])
  }
}
</script>


<template>   
 
 	<app-template header_title="Notifications" :back_button="true">
     
	    <div v-if="requests.length > 0">
	      <h4 class="p-2">Friend Requests ({{requests.length}})</h4>
	      <FriendRequest v-for="request in requests" :user='request'></FriendRequest>
	    </div>
	    <div v-else class="center bg-transparent">
	      <h5 class="text-secondary text-nowrap">No Friends Requests!</h5>
	    </div>

	</app-template>
 
 
</template>
