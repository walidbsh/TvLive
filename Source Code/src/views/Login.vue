<script type="text/javascript">
import AppTemplate from '../components/AppTemplate.vue'
import {IonIcon, IonButton, IonInput} from '@ionic/vue'
import {logoFacebook, logoTwitter, logoGoogle} from 'ionicons/icons'

//import '@capacitor-community/http';
import { CapacitorHttp } from '@capacitor/core';

import axios from 'axios'
import { mapGetters, mapActions } from 'vuex'

export default{
	components:{
		AppTemplate,
		IonInput,
		IonButton,
		IonIcon
	},
	data(){
		return {
			logoFacebook,
			logoTwitter,
			logoGoogle,
			email:'',
			password:'',
			user_settings_:null

		}
	},
	mounted(){
		console.log(this.user_settings)
		this.user_settings_ = this.user_settings
		 
	},
	methods:{
		...mapActions(['set_user_settings']),
		async login(){ 
			var endpoint = this.urls.database +'/login.php?email='+this.email+'&password='+this.password;

	 		let self = this 

			CapacitorHttp.get({ 
				url: endpoint,
				timeout: 20000
			}).then(({ data }) => {
				if(data != 404){ 
					// user sign in correctly
			 
					if(data.state == 100){  // user have a exest acount
						self.user_settings_.id               = data.id
						self.user_settings_.general.email    = self.email
						self.user_settings_.general.avatar   = data.avatar
						self.user_settings_.general.username = data.username
						self.user_settings_.general.password = self.password
						self.set_user_settings(self.user_settings_)

						location.reload()
					}

					if(data.state == 300){ // user signup
						self.user_settings_.id               =  data.id
						self.user_settings_.general.email    = self.email
						self.user_settings_.general.password = self.password
						self.set_user_settings(self.user_settings_)

						this.$router.push('/setup')
					}

				}else
					alert('password or email warring')
			}) 
			console.log('login called')
		}
	},
	computed:{ ...mapGetters(['urls', 'user_settings']) }
}
</script> 
<template>
	<AppTemplate>
		<div class="w-100 h-100 center d-flex
			justify-content-center
		 	flex-column align-items-center bg-transparent">
			
			<h1 class="m-0" style="font-size:34px">TvHub</h1>
			<span class="opacity-50">Stay near to your Friends</span>
			<span class="opacity-50 mb-5">Because it's Friends Time</span>
		
			<div class="mt-5 d-flex col-8 justify-content-around">
				<ion-icon class="font32" :icon="logoFacebook"></ion-icon>	
				<ion-icon class="font32" :icon="logoGoogle"></ion-icon>	
				<ion-icon class="font32" :icon="logoTwitter"></ion-icon>	
			</div>

			<div class="d-flex col-10 my-2 align-items-center">
				<div class="line"></div>
				<div class="or px-2">Or</div>
				<div class="line"></div>
			</div>

	        <ion-input 
	        	v-model="email"
	        	class="mb-2 col-10 align-text-start rounded-pill" 
	        	label="Email" 
	        	label-placement="floating" 
	        	fill="outline"  /> 
	        <ion-input 
	        	v-model="password"
	        	type="password"
	        	class="col-10 align-text-start rounded-pill" 
	        	label="Password" 
	        	label-placement="floating" 
	        	fill="outline"  /> 

			<ion-button 
				@click="login()"
				style="font-weight: 600;"
				class="mt-3 col-10">
				Login 
			</ion-button> 

 


		</div>
	</AppTemplate>	
</template>


<style type="text/css">
	
.border-none{
	border: none;
}
.h-100{
	height: 100vh !important;
}
.line{
	height: 2px;
	width: 100%;
	background: white;
	opacity: 0.5;
} 
.or {  
/*	width: 50px;*/
	font-size: 19px; 
	color: white;
	text-align: center; 
	opacity: 0.5;

}
.logo {
	font-size: 32px;
	font-weight: bold;
	font-family: roboto, monospace;
}
.font32{
	font-size: 30px !important;
	
}


</style>