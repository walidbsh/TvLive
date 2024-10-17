<script type="text/javascript">
import AppTemplate from '../components/AppTemplate.vue'

import {IonButton, IonInput, IonIcon, IonList} from '@ionic/vue'
import {clipboardOutline } from 'ionicons/icons' 
import AppTitle from '../components/AppTitle.vue'
import RoomOptions from '../components/RoomOptions.vue'

import { mapActions, mapGetters } from 'vuex'
export default{
	components:{
		IonIcon,
		AppTemplate,
		IonInput,
		IonButton,  
		AppTitle,
		RoomOptions,
		IonList
	},
	data(){
		return {
			room_link:'',
			clipboardOutline,
		}
	},
	methods:{
		...mapActions(['set_urls']),
 
		join(){   
			this.$router.push(`/room/${this.room_link}`)
		},
		async passt(){    
			try {
		        var text = await navigator.clipboard.readText();
		        
		        this.room_link = text;

	        } catch (err) {
	            console.error('Failed to read clipboard contents: ', err);
	        }
		}
	},
	computed:{...mapGetters(['urls', 'room_options'])}
}
</script>

<template>

	<app-template header_title="Join a Room" :back_button="true">



	<div>
		<div class="m-0 d-flex flex-column
		align-items-center center w-100 p-3">
			

  			<ion-list class="p-3 w-100">
  				
				<app-title title="Join Link" class="me-auto mb-3"  />
			 	<div class="d-flex align-items-center mb-3">
			        <ion-input 
			        	v-model="room_link"
			        	class="align-text-start rounded-pill pe-2" 
			        	label="Room Link" 
			        	label-placement="floating" 
			        	fill="outline"  />	
					<ion-button @click="passt" color="light"> 
						<ion-icon :icon="clipboardOutline"
						slot="icon-only" ></ion-icon>
					</ion-button>	
			 	</div>
  	 

  			</ion-list>

  			<room-options class="w-100"></room-options>

  			<div class="col-12">
				<ion-button @click="join" class="w-100">Join</ion-button>	
  			</div>

		</div>
	</div>




	</app-template>

</template>

<style type="text/css">
	
.fixed-buttom{
	width: 100%;
	position: absolute;
	bottom: 0;
	left: 50%;
	transform: translateX(-50%);
}

</style>