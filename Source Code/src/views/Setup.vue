<script type="text/javascript">

import AppTemplate from "../components/AppTemplate.vue"
import { camera }  from 'ionicons/icons'

import { IonIcon, IonButton, IonInput } from '@ionic/vue'
 
import { CapacitorHttp } from '@capacitor/core'

import { Camera, CameraResultType } from '@capacitor/camera';
import { mapActions, mapGetters } from 'vuex'


export default{
	components:{
		AppTemplate,
		IonInput,
		IonIcon,
		IonButton
	},
	data(){
		return {
			user_settings_:null,
			camera, 
		}
	},
	mounted(){
		this.user_settings_ = this.user_settings
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
		async start(){
			this.set_user_settings(self.user_settings_)
			location.reload()
		}
	},
	computed:{...mapGetters(['urls', 'user_settings'])}
}
</script>

<template>
<AppTemplate>
	<div v-if="user_settings_" class="w-100 vh-100 d-flex flex-column
				align-items-center justify-content-center">
		<div class="position-relative">
			<img ref="avatar" class="user-image" 
			:src="user_settings.general.avatar">
			<ion-icon @click="uploadImage" class="avatar-camera" :icon="camera"></ion-icon>
		</div>

		<h5 class="m-0 mt-3 mb-5" >{{user_settings_.general.username}}</h5>

        <ion-input 
        	@IonInput="user_settings_.general.username=$event.target.value"
        	ref="username"
        	:value="user_settings_.general.username"
        	class="col-9 align-text-start rounded-pill" 
        	label="Username" 
        	label-placement="floating" 
        	fill="outline"  />	
		<ion-button class="mt-3 col-9 " v-if="user_settings_.general.username.length > 0" @click="start">Start</ion-button>	
		<ion-button class="mt-3 col-9 " v-else disabled @click="start">Start</ion-button>	
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