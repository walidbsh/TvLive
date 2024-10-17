<template> 
	<div class="mx-2 p-0 mb-1 rounded d-flex align-items-center bg-dark pe-2"> 
	  	<img class="avatar-img rounded-50 p-2" :src="urls.database + user.avatar"/>
	  	<h6 class="m-0 text-capitalize" style="font-size:18px">{{user.username}}</h6>
	  	<ion-button v-if="state==0" @click="send_req" class="me-1 ms-auto text-capitalize">Add Friend</ion-button>
	  	<ion-button v-else-if="state==1" class="me-1 ms-auto text-capitalize" disabled>Friend</ion-button>
	  	<ion-button v-else-if="state==2" class="me-1 ms-auto text-capitalize" disabled>Me</ion-button>
	  	<ion-button v-else-if="state==3" class="me-1 ms-auto text-capitalize" disabled>Sent</ion-button>
	</div> 
</template>


<script type="text/javascript">

import {IonButton, IonTitle, IonItem} from '@ionic/vue'

import { mapActions } from 'vuex'
import { mapGetters } from 'vuex'
import { CapacitorHttp } from '@capacitor/core'


export default{
	components:{
		IonTitle,
		IonItem,
		IonButton
	},
	name:'UserLine',
	data(){
		return {
			state:0
		}
	},
	props:{
		user:Object
	},
	mounted(){

		for (var i = 0; i < this.myFriend.length; i++) {
			if(this.myFriend[i].id == this.user.id) // already friend
				this.state = 1 
		}

		if(this.user.id == this.user_settings.id){  // thats me 
			this.state = 2 
		}

	},
	methods:{
		...mapActions(['setUserToRoom']),
		send_req(){
 			
 			let uid = this.user_settings.id
			var endpoint = `${this.urls.database}/send_request.php?uid=${uid}&friend=${this.user.id}`
			console.log(endpoint)
			CapacitorHttp.get({url:endpoint})
			this.socket.emit('send_add', this.user.id , 'new friend request check it!')

			this.state = 3
		}
	},
	computed:{...mapGetters(['socket', 'myFriend', 'urls', 'user_settings'])}
}
</script>

<style type="text/css" scoped>
.avatar-img{
	width: 75px;
	height: 75px;
}
.rounded{
	border-radius: 8px !important
}
.unread-indicator {
	display: none !important;
    background: black;

    width: 10px;
    height: 10px;

    border-radius: 100%;

    position: absolute;

    inset-inline-start: 12px;
    top: 12px;
  }
</style>
 