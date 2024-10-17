<template>
			<ion-list v-if="user_settings_" :inset="true" class="p-3 m-3"> 
				<app-title class="mb-3" title="Room Options"></app-title>
				<div class="d-flex justify-content-between">
					<h6 class="roboto-thin-4 pb-1">Camera</h6>
					<VueToggles
					  :value="user_settings_.room_options.camera"
					  :height="25"
					  :width="50" 
					  checkedBg="blueviolet"
					  uncheckedBg="#4e1e7a"
					  @click="update('camera')"
					/>
				</div> 
				<div class="d-flex justify-content-between">
					<h6 class="roboto-thin-4 mb-0">Mic</h6>
					<VueToggles
					  :value="user_settings_.room_options.mic"
					  :height="25"
					  :width="50" 
					  checkedBg="blueviolet"
					  uncheckedBg="#4e1e7a"
					  @click="update('mic')"
					/>
				</div>  
			</ion-list>

</template>

<script type="text/javascript">

import {IonButton, IonInput, IonList, IonIcon} from '@ionic/vue'
import { VueToggles } from "vue-toggles";
import AppTitle from '../components/AppTitle.vue'

import { mapGetters, mapActions } from 'vuex'

export default{
	components:{
		VueToggles, 
		IonList,
		AppTitle
	},
	data(){
		return {
			user_settings_:null
		}
	},
	mounted(){
		this.user_settings_ = this.user_settings;
	},
	methods:{
		...mapActions(['set_user_settings']),
		update(state ){

			if(state == 'camera')
				 this.user_settings_.room_options.camera = !this.user_settings_.room_options.camera
			
			if(state == 'mic')
				 this.user_settings_.room_options.mic    = !this.user_settings_.room_options.mic
			
			this.set_user_settings(this.user_settings_)
		},

	},
	computed:{...mapGetters(['user_settings'])}
}


</script>