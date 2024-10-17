<template>
	<div>
	  <li class="col-12 d-flex align-items-center pb-1"> 
	  	<img class="user-img rounded-50 border-2" :src="urls.database + user.avatar">
	  	<h6 class="ms-2 text-capitalize">{{user.username}}</h6>
	  	<ion-button v-if="!invited" @click="invite_user" class="ms-auto text-capitalize">invite</ion-button>
	  	<ion-button disabled v-else @click="invite_user" class="ms-auto text-capitalize">invited</ion-button>
	  </li>
	</div>
</template>
<script type="text/javascript">
import {IonIcon, IonButton} from '@ionic/vue'
import { mapActions } from 'vuex'
import { mapGetters } from 'vuex'
export default{
	name:'InviteUser',
	components:{
		IonIcon, IonButton
	},
	data(){
		return {
			invited:false
		}
	},
	props:{
		user:Object
	},
	methods:{
		...mapActions(['setUserToRoom']),
		invite_user(){
			this.setUserToRoom(this.user)
			this.invited = true;
		}
	},
	computed:{...mapGetters(['socket', 'urls', 'user_settings'])}
}
</script>


<style>
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

.user-img{
	height: 55px;
	width: 55px;
}
</style>