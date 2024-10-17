

<script type="text/javascript">
	 
import { mapActions, mapGetters } from 'vuex'
import { IonButton, IonIcon, IonContent, IonList, IonRefresher, IonRefresherContent } from '@ionic/vue'
import { linkSharp } from 'ionicons/icons'
import RoomOptions from '../components/RoomOptions.vue'

import InviteUser from '../components/MakePageComponents/InviteUser.vue'
import AppTemplate from '../components/AppTemplate.vue' 
import AppTitle from '../components/AppTitle.vue' 


import { CapacitorHttp } from '@capacitor/core'
import axios from 'axios'

let thumbnailBlob


export default{
	name:'Make',
	components:{
		AppTemplate,
		RoomOptions,
		InviteUser,
		IonButton,
		AppTitle,
		IonContent,
		IonList,
		IonRefresher, 
		IonRefresherContent,
		IonIcon
	},
	data(){
		return {
			thumbnailDataURL:'',
			linkSharp,
			selected_video_name:'',  
	  		selected_file : null,  
	  		room_uid:null,
	  		is_public_option:true,
	  		room:{
		   		name: '',
		   		id: '', 
		   		invited_friends:'',
		   		movie:'',
		   		thumbnail:'',
		   		public:0
		   	},
		   	user_settings_:null,
		   	video_loading:false
		}
	},
	  computed: {
    ...mapGetters([
    				'myFriend',
    				'users_invited',
    				'urls',
    				'user_settings'
    				])
  },
  async mounted(){  
	this.user_settings_ = this.user_settings
  	this.room_uid = this.uuid()
  	
  },
  methods:{ 
  	...mapActions(['setRoom', 'set_user_settings', 'clear_invited_users', 'send_notification']),
 
  	async copy() {

  		var copyed = true
		   // Copy the text inside the text field 
		  try {
		    await navigator.clipboard.writeText(this.room_uid);
		  } catch (error) {
		  	copyed = false 
		  	alert("text dosen't copyed!! please copy it manually: " + this.room_uid);
		  } 
		  if(copyed)
		  	alert(`Room ID Copyed '${this.room_uid}'`)

		  // Alert the copied text
		},
  	go_back(){
	    this.$router.go(-1)
	},
	go_notifications(){
	    this.$router.push('/notifications')
	},
  	uuid(length=4, prefix=""){
  		var uuid = ''
  		var keys = ['1','2','3','4','5','6','7','8','9','a','A','W','Z','z','Y','y','X','x']
  		for (var i = 0; i < length; i++) {
  			var r = Math.floor( Math.random() * (keys.length-1)) 
  			uuid = uuid + keys[r] 
  		} 
  		return uuid + prefix;
  	},

  	loadfile(){
  		var loader = document.getElementById("fileloader")
  		loader.click()
  	},
  	previewFiles(event) {

  		const self = this;

      	const file = (event.target.files[0]);
      	if(file == null) {
      		alert('pleas select a movie firs') 
      		return
      	}

      	this.video_loading = true
 
      	this.selected_file = file 
      	this.selected_video_name = file.name


		const video = document.createElement('video'); 
		const video_url  = URL.createObjectURL(file);
        video.src        = video_url
		this.room.movie  = video_url
        video.addEventListener('loadeddata', function() {
 
            video.currentTime = Math.floor( video.duration /2);
        });

        video.addEventListener('seeked', function() {
 
	        const canvas = document.createElement('canvas');
	        canvas.width = video.videoWidth /2;
	        canvas.height = video.videoHeight /2;
	        const ctx = canvas.getContext('2d');
	        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);


	        self.thumbnailDataURL = canvas.toDataURL('image/jpeg');
	        document.getElementById('image_tamplate').src = self.thumbnailDataURL;

	        self.video_loading = false
	        //URL.revokeObjectURL(video.src);
	    })
    },
    async uploadVideoThumbnail(file_name) { 
    	let self = this
    	fetch(this.thumbnailDataURL)
        .then(res => res.blob())
        .then(blob => {
            const formData = new FormData();
            formData.append('file', blob, file_name);
            var endpoint = self.urls.database + '/image_uploader.php';
            fetch(endpoint, {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(result => {
                console.log('Success:', result);
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }); 

	},

  	async startRoom(){ 
 
	   	var uid  = this.user_settings.id
	    var image_file_name = this.uuid(10, "image_") + '.jpeg'; 

	    this.room.id = this.room_uid
	    this.room.name = this.selected_video_name
	    this.room.uid = uid;
	    this.room.thumbnail = image_file_name
	    this.room.public = this.is_public_option?1:0


	   	this.setRoom(this.room);

	   	/*save room to database*/
	   	var room_name =  encodeURIComponent(this.room.name)
 
	   	var endpoint = this.urls.database + `/create_room.php?name=${room_name}&creator=${uid}&thumbnail=/thumbnail/${this.room.thumbnail}&room=${this.room.id}&state=${this.room.public}`
 
  

	   	CapacitorHttp.get({ 
	        url: endpoint
		}).then(({ data }) => {
			console.log('room uploaded :' , data);
		}).catch((error)=> {
			console.log('error' , error);
		})

 
		/*invite invited friends*/
		for (var i = 0; i < this.users_invited.length; i++) {
 
			var payload = {
				to: this.users_invited[i].id,
				message:'Invite you to watch movie!'
			} 

			this.send_notification(payload)
		}

	    var state = await  this.uploadVideoThumbnail(this.room.thumbnail)
 
		this.$router.push('/live')
	   
    },
    handleRefresh(event){ 
    	this.clear_invited_users()
    	this.selected_video_name = ''
  		this.selected_file = null,  
  		this.room.thumbnail ='' 
  		this.room.invited_friends = ''
  		this.$refs.image_thumbnail.src = this.urls.database + '/img/black.webp'
  		this.room_uid = this.uuid()
  		this.clear_invited_users()
  		if(event != null)
    		event.target.complete()
    }
  },
  beforeUnmount(){  /*  when quite the page */
    this.handleRefresh(null)
  },
}

</script>



<template>
	<AppTemplate header_title="Make room" :back_button="true" :people="true">

	<ion-refresher slot="fixed" :pull-factor="0.5" :pull-min="100" :pull-max="200" @ionRefresh="handleRefresh($event)">
      <ion-refresher-content></ion-refresher-content>
    </ion-refresher>


	<div>
		<div class="position-relative col-12">

			<!-- aspect-ratio: 1 / 1; -->
			<img ref="image_thumbnail" id="image_tamplate" :src="this.urls.database + '/img/black.webp'">


			<div @click="loadfile()" class="center bg-transparent" v-if="!selected_file">
				<div class="rounded-50 add-movie-button">
					<img src="../assets/assets/UI/add-post.png" class=" w-100 p-3">
				</div> 
				<input class="bg-transparent" id="fileloader" @change="previewFiles" type="file" name=""  hidden>
			
			</div>
			
			 
			<div class="top_relative m-2 d-flex">
				<img v-for="user in users_invited" :key="user.id" :src="urls.database + user.avatar" class="shift50 userbubble"></img> 
			</div>

			<div v-if="selected_video_name.length > 0" class="linear_black w-100 bottom-left m-0 p-0 px-1">
				
				<h5 class="roboto-thin-4 video_title taxt-ellipsis"> {{selected_video_name}} </h5>
			</div>

			<div v-if="video_loading" class="loader-container">
				<div class="loader" v-if="true"></div>
			</div>
		</div>



		<!-- video title --> 
		<room-options></room-options>


		<!-- user invit system -->
		<ion-list v-if="myFriend.length > 0" :inset="true"
			class="m-3 p-3 scrollabel">
			<AppTitle title="Invite Friends" class="mb-3"/>
			<!-- list of all frineds -->
			<div class=" shadow-sm rounded">
				<invite-user v-for="user in myFriend" :key='user.id' :user='user'></invite-user> 
			</div>
		</ion-list>

 
		
		<div class="default-bg fixed-bottom d-flex py-2 justify-content-center" color="danger"> 
			<ion-button v-if="selected_file" @click="startRoom" class="text-capitalize pe-2 col-10">Start Room</ion-button>
			<ion-button v-else @click="startRoom" class="text-capitalize pe-2 col-10 disabled" disabled>Start Room</ion-button>
			<ion-button fill="clear" @click="copy">
				<ion-icon slot="icon-only" :icon="linkSharp"></ion-icon>
			</ion-button>
		</div> 

	</div>
	</AppTemplate>
</template> 


<style> 
.default-bg{
    background-color: #121212 ; 
}

.loader-container {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
}

.loader {
  width: 60px; /* Adjust as needed */
  height: 60px; /* Adjust as needed */
  border: 5px solid white; /* Light grey */
  border-top: 5px solid blueviolet; /* Blue */
  border-radius: 50%;
  animation: spin 2s linear infinite;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

ion-list{
	border-radius: 8px !important;
}
.p-25{
	padding: 25px;
}
.p64{
	width: 100px;
	height: 100px; 
}


#image_tamplate{
	height: 50vw; 
	width: 100%;
}

ion-button{ 
    --background: blueviolet;
    --background-hover: #9ce0be;
    --background-activated: #88f4be;
    --background-focused: #88f4be;

    --color: white;
 
    --border-color: #000;  
 

    --ripple-color: deeppink; 
}
.border-rounded-white{
	border:1px solid white;
	border-radius: 50%;
	padding: 8px;
	opacity: 50%;
}
.r45{
	transform: rotate(45deg);
}
.p24{
	height: 24px;
	width: 24px;
}
.center{
    position: absolute;
    left: 50%;
    top: 50%;
    transform: translate(-50%,-50%);
}
.rounded-50{
	border-radius: 50%;/*
	width: 50px;
	height: 50px;*/
	object-fit: cover;
}
.top_relative{
	position: absolute;
	top: 0;
	left: 0;
	transform: translate(0, 0);
}
.white_border{
	border-radius: 50%;
	border:3px solid white;
}
.shift50:nth-child(n+2){ 
	margin-left: -23px;
}
</style>

<style scoped> 
.text_ecl{ 

}
.bottom-left{
	position: absolute;
	bottom: 0;
	left: 0;
	margin: 1rem;
}
.userbubble{
	height: 35px;
	width: 35px; 
	border: 1px solid white;
	border-radius: 50%;
}
.video_title{ 
	font-family: serif;
    /*-webkit-box-orient: vertical;*/
    /*-moz-box-orient: vertical;*/  
	width: 80vw;
 
	text-decoration: none;

 
}
.taxt-ellipsis{ 
    overflow: hidden;
    text-overflow: ellipsis;  
	white-space: nowrap; 
} 

.mb-sp{
	padding-bottom: 47px;
}
.scrollabel{
	max-height: 300px;
	overflow-y: auto;
	/*padding-bottom: 50px;*/
}
.add-movie-button{
	height: 65px;
	width: 65px;
	background: white;
}
 
</style>