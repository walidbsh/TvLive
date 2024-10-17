<script type="text/javascript">

import AppTemplate from "../components/AppTemplate.vue"
import AppTitle from    "../components/AppTitle.vue"
import { camera } from    'ionicons/icons'

import { IonIcon, IonButton, IonInput, IonList } from '@ionic/vue'
import { VueToggles } from 'vue-toggles'

import { mapGetters, mapActions } from 'vuex'
import { CapacitorHttp } from '@capacitor/core'

import { Camera, CameraResultType } from '@capacitor/camera';

export default{
	components:{
		AppTemplate,
		IonInput,
		IonIcon,
		IonButton,
		IonList,
		AppTitle,
		VueToggles
	},
	data(){
		return {
			user_settings_:null,
			camera, 
		}
	},

	methods:{
		...mapActions(['set_user_settings']),
		uuid(length){
	  		var uuid = ''
	  		var keys = ['1','2','3','4','5','6','7','8','9','a','A','W','Z','z','Y','y','X','x']
	  		for (var i = 0; i < length; i++) {
	  			var r = Math.floor( Math.random() * (keys.length-1)) 
	  			uuid = uuid + keys[r] 
	  		} 
	  		return uuid;
	  	},

		async uploadImage(){ 

			const image = await Camera.getPhoto({
				quality: 60, 
				resultType: CameraResultType.Uri
			});

			var url = image.webPath; 

	    	const self = this
	    	const file_name = this.uuid(8) + ".jpeg"
	    	fetch(url)
	        .then(res => res.blob())
	        .then(blob => { 
	            const formData = new FormData();
	            formData.append('file', blob, file_name);
	            formData.append('id', self.user_settings.id);
	            var endpoint = self.urls.database + '/upload_avatar.php';
	            fetch(endpoint, {
	                method: 'POST',
	                body: formData
	            })
	            .then(response => response.text())
	            .then(result => {
	            	self.user_settings_.general.avatar = "/avatar/" + file_name;
	            	self.set_user_settings(self.user_settings_)
	            	this.$notify({
				        type: "success",
				        text: "image uploaded!",
				        duration:1000 
				      }); 
	                console.log('Success:', result);
	            })
	            .catch(error => {
	                console.error('Error:', error);
	            });
	        }); 
 
			  
		},
	  
		async save(){
			
			this.set_user_settings(this.user_settings_)



			let self = this;
 			const id = this.user_settings.id
 			const username = encodeURIComponent(this.user_settings_.general.username)
 			const password = encodeURIComponent(this.user_settings_.general.password)


			var endpoint = this.urls.database + `/update_user_info.php?id=${id}&username=${username}&password=${password}`;
			console.log(endpoint)
 
		   	CapacitorHttp.get({ 
		        url: endpoint,
			}).then(({ data }) => {
				console.log(data)
				if(data == "200"){

			      this.$notify({
			        type: "success",
			        text: "saved!",
			        duration:1000
			      }); 
					//self.router.push('/home')
				}else{
					this.$notify({
			        type: "error",
			        text: "data not updated!",
			        duration:1000 
			      }); 
				}
			}) 
		
		}
	},
	mounted(){ 
		this.user_settings_ = this.user_settings  
	},	
	computed:{...mapGetters(['urls', 'user_settings'])}
}
</script>

<template>
<AppTemplate v-if="user_settings_" header_title="Settings" :back_button="true">

 
		
		<ion-list class="p-3 m-3">
			<app-title title="General Settings" class="mb-3"></app-title>
			

			<div class="d-flex flex-column
				align-items-center justify-content-center">
				<div class="position-relative">
					<img ref="avatar" class="user-image" 
					:src="urls.database + user_settings_.general.avatar">
					<ion-icon @click="uploadImage" class="avatar-camera" :icon="camera"></ion-icon>
				</div>

				<h3 class="m-0 mt-3 mb-3 text-capitalize" >
					{{user_settings_.general.username}}
				</h3>

		        <ion-input  
		        	@IonInput="user_settings_.general.username=$event.target.value"
		        	ref="username"
		        	class="col-12 align-text-start rounded-pill mb-2" 
		        	label="Username" 
		        	label-placement="floating"
		        	:value='user_settings_.general.username'
		        	fill="outline"  />	
		        <ion-input 
		        	disabled
		        	@IonInput="user_settings_.general.email=$event.target.value"
		        	ref="username"
		        	class="col-12 align-text-start rounded-pill mb-2" 
		        	label="Email" 
		        	label-placement="floating"
		        	:value='user_settings_.general.email'
		        	fill="outline"  />	
		        <ion-input 
		        	@IonInput="user_settings_.general.password=$event.target.value"
		        	ref="username"
		        	class="col-12 align-text-start rounded-pill mb-2" 
		        	label="Password" 
		        	label-placement="floating"
		        	:value='user_settings_.general.password'
		        	fill="outline" 
		        	type="password" />	
 			</div>
		</ion-list>

		<ion-list class="p-3 m-3">
			<app-title title="Notifications" class="mb-3"></app-title>
		
			<div class="d-flex justify-content-between">
				<h6 class="roboto-thin-4 pb-1">Friend Requeste</h6>
				<VueToggles
				  :value="user_settings_.notifications.friend_requeste"
				  :height="25"
				  :width="50" 
				  checkedBg="blueviolet"
				  uncheckedBg="#4e1e7a"
				  @click="user_settings_.notifications.friend_requeste = !user_settings.notifications.friend_requeste"
				/>
			</div> 
			<div class="d-flex justify-content-between">
				<h6 class="roboto-thin-4 pb-1">Movie Start</h6>
				<VueToggles
				  :value="user_settings_.notifications.movie_start"
				  :height="25"
				  :width="50" 
				  checkedBg="blueviolet"
				  uncheckedBg="#4e1e7a"
				  @click="user_settings_.notifications.movie_start = !user_settings.notifications.movie_start"
				/>
			</div> 
 

		</ion-list>
		<ion-list class="m-3 p-3">

			<app-title title="Privacy Settings" class=mb-3></app-title>
			

			<div class="d-flex justify-content-between">
				<h6 class="roboto-thin-4 pb-1">Profile Visibility</h6>
				<VueToggles
				  :value="user_settings_.privacy.profile_visibility"
				  :height="25"
				  :width="50" 
				  checkedBg="blueviolet"
				  uncheckedBg="#4e1e7a"
				  @click="user_settings_.privacy.profile_visibility = !user_settings.privacy.profile_visibility"
				/>
			</div> 
 		</ion-list>

 		<ion-list  class="m-3 p-3">
			<app-title title="Stream Quality" class=mb-3></app-title>
			<div class="d-flex justify-content-between">
				<h6 class="roboto-thin-4 pb-1">Hight Quality (UHD)</h6>
				<VueToggles
				  :value="user_settings_.stream.quality"
				  :height="25"
				  :width="50" 
				  checkedBg="blueviolet"
				  uncheckedBg="#4e1e7a"
				  @click="user_settings_.stream.quality = !user_settings.stream.quality"
				/>
			</div> 
 		</ion-list>

 		<ion-list class="m-3 p-3 mb-5">
			<app-title title="Miscellaneous" class=mb-3></app-title>
			
			<h6><a href="">FAQ</a></h6>
			<h6><a href="">Report a Problem</a></h6>
			<h6>App Version 1.1</h6>
 		</ion-list>


 		<div class="fixed-bottom default-bg p-3">
 			<ion-button class="w-100" @click="save"> Save </ion-button>

 		</div>



</AppTemplate>
</template>

<style type="text/css">
.user-image{
	width: 120px;
	height: 120px;
	border-radius: 100%;
	object-fit: cover;
	border: 2px solid blueviolet;
}
.avatar-camera{
	position: absolute;
	bottom: 0;
	right: 0;
	font-size: 30px;
	border-radius: 100%;
	padding: 5px;
	background: blueviolet;
}
</style>