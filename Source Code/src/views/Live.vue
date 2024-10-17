<script type="text/javascript">

import AppTemplate from '../components/AppTemplate.vue'
import PlayerController from '../components/PlayerController.vue'
import BubbleChatsContainer from '../components/BubbleChatsContainer.vue'


import { Client, LocalStream, RemoteStream } from 'ion-sdk-js';
import { IonSFUJSONRPCSignal } from 'ion-sdk-js/lib/signal/json-rpc-impl';
 
import { CapacitorHttp } from '@capacitor/core'
import { mapGetters, mapActions } from 'vuex';   
 
const config = {
	debug: true,
	iceServers: [
		{
			urls:"stun:stun.l.google.com:19302"
		}
	]
}


let stream;
export default { 
  name: 'Live', 
  created(){
  	
  },
  data(){
  	return { 
  		selected_video_name : "",
  		selected_file : null, 
  		faceStream:null,
  		streamLives:[],

  		menuOpen:false,
  		mainLive: this.$refs.mainLive,

  		clientWatcher:null,
  		clientLive:null,
  		dataChannel:null
  	}
  },
  components: {  
		AppTemplate,
		PlayerController,
		BubbleChatsContainer,
  },  
  methods:{
  	...mapActions(['setLivesData']),
  		async clear_stream(stream){
		    stream.getTracks().forEach(track => track.stop());
  			stream.mute()
		    return null
  		},
  		send_states(){

  			var states = {
          uid:this.user_settings.id,
          username:this.user_settings.general.username,
          stream_id:this.faceStream.id
        }

        this.dataChannel.send(JSON.stringify( states ))

        console.log('sent')
  		},
	  	toggleMenu() {
	      	this.menuOpen = !this.menuOpen;
	    },
	    closeMenu() {
	      	this.menuOpen = false;
	    },
  		async resizeStream(videoStream, new_width, new_height) {
			    // Create a video element to play the original stream
			    const video = document.createElement('video');
			    video.srcObject = videoStream;
			    await video.play();

			    // Create a canvas element to draw the resized video frames
			    const canvas  = document.createElement('canvas');
			    const context = canvas.getContext('2d');

			    // Set the desired width and height for the canvas
			    canvas.width  = new_width;
			    canvas.height = new_height;

			    // Wait for the video to start playing
			    await new Promise((resolve) => {
			        video.onloadedmetadata = () => {
			            resolve();
			        };
			    });

			    // Draw the video frames onto the canvas at the desired size
			    function drawFrame() {
			        context.drawImage(video, 0, 0, canvas.width, canvas.height);
			        requestAnimationFrame(drawFrame);
			    }
			    drawFrame();

			    // Capture the stream from the canvas
			    const resizedStream = canvas.captureStream();

			    return resizedStream;
			},	
	  	async get_stream_from_video(videoElement, quality, simulcast=false){ 

	  		if (!videoElement) {
			    throw new Error('No video element provided');
			  }

			  try {
			    // Wait for the video to be ready to play
			    await videoElement.play(); 

			    let stream = null;
			    if (videoElement.captureStream) {
			      stream = videoElement.captureStream(); 
			    } else if (videoElement.mozCaptureStream) {
			      stream = videoElement.mozCaptureStream(); 
			    } else {
			      throw new Error('captureStream() not supported');
			    }

			    const videoOptions = {
			      codec: 'H.265',
			      resolution: quality,
			      audio: true,
			      video: true,
			      simulcast: simulcast
			    }; 
			    // Assuming LocalStream is a valid constructor for your needs
			    const newStream = new LocalStream(stream, videoOptions); 
			    return newStream;
			  } catch (error) {
			    console.error('Failed to get stream from video:', error);
			    // Handle the error appropriately, e.g., notify the user
			    throw error; // Rethrow the error after logging
			  }
		  },

		  async startWatcher(){ /// movie shaire

	    	const self = this;
 
				this.mainLive.src = this.getRoom.movie
				const playing = await this.mainLive.play()

 
				const stream_quality = this.user_settings.stream.quality?'hd':'vga'

	    	var liveStream = await this.get_stream_from_video(this.mainLive, stream_quality, false) 
				this.clientWatcher.publish(liveStream)  
				  
		  },
		  async updateStream(){
		  	const faceLiveOptions = this.getFaceStreamOptions()

	      this.clientLive.close()
	      this.faceStream = this.clear_stream(this.faceStream)
	      this.faceStream = null 

	      this.startLive()
		  	console.log('stream updated')
		  },
		  getFaceStreamOptions(){
		  	var faceLiveOptions = null
		  	if(this.user_settings.room_options.camera)
					faceLiveOptions = {
	            codec: 'H.265',
	            resolution: 'qvga',
	            audio:this.user_settings.room_options.mic,
	            video: {
	              facingMode: "user",
	            },
	            simulcast:true
	        }
	      else{
	      	faceLiveOptions = {
	            codec: 'H.265',
	            resolution: 'qvga',
	            audio:this.user_settings.room_options.mic,
	            video: false,
	            simulcast:true
	        }
	      }

	      return faceLiveOptions
		  },
		  async startLive(){ 

		  	const self = this


		  	/*FACE STREAM SECTION*/ 
				const signalLive    = new IonSFUJSONRPCSignal(this.urls.ionSfuServer)
				this.clientLive     = new Client(signalLive   , config)

				signalLive.onopen = async () =>{
					var uid = 'uidl_' + self.user_settings.id
					var rid = 'ridl_' + self.getRoom.id
					self.clientLive.join(rid, uid)
					self.dataChannel = self.clientLive.createDataChannel('111')

		      // Setup event handlers for the data channel
		      self.dataChannel.onopen = () => {  
		        // Send a message
		        self.send_states() 
		      };

		      self.dataChannel.onmessage = (event) => {
		       
		      	console.log('get message', event.data)
		        self.setLivesData(event.data)
		      };

	 				const faceLiveOptions = this.getFaceStreamOptions()
	 				 
					self.clientLive.ontrack = (track, stream) => {  

						track.onunmute = ()=> { 
							if(track.kind == 'video'){
								self.send_states()
								/*make sure the stream not existe*/
								for (var i = 0; i < self.streamLives.length; i++) {
									if(self.streamLives[i].id == stream.id)
										return
								}

								self.streamLives.push(stream) 

								/*remove live bubble*/
								stream.onremovetrack = () => { 
									const result = self.streamLives.filter(stream_ => stream_.id !== stream.id)
			          	self.streamLives = result; 
			          } 
							}
						}
					} 
		   		 
	        var faceStream_ = await LocalStream.getUserMedia(faceLiveOptions)
	        self.faceStream = faceStream_

			    self.clientLive.publish(faceStream_);   
					
		 
				} 


 

	    }
  	},
    computed:{ 
	   	...mapGetters(['users_invited', 'urls', 'getRoom', 'user_settings']),
		},
		mounted(){ 

    	if(this.getRoom == null){ 
    		this.$router.push('/make')
    		return
    	}


			// initil the main video tag 
			this.mainLive = this.$refs.mainLive

			const self = this
			var ionURL = this.urls.ionSfuServer

			/*WATCH SECTION*/
 
			const signalWatcher = new IonSFUJSONRPCSignal(ionURL)
			this.clientWatcher  = new Client(signalWatcher, config)
 
			signalWatcher.onopen = () =>{ 
				var uid = 'uidw_' + self.user_settings.id 
				var rid = 'ridw_' + self.getRoom.id
				self.clientWatcher.join(rid, uid)  
				self.startWatcher()
			}
 
 
			this.startLive()

		},
		async beforeUnmount(){  /*  closeing the page */
		if(this.getRoom == null) return
    /* close the loading live panel if it open*/
/*    if (this.mainLoading) {
      await this.mainLoading.dismiss();
      this.mainLoading= null;
    }
*/


		/*REMOVE FACE STREAM*/
    /*close the camera */
    if(this.faceStream)
      this.faceStream.getTracks().forEach(track => track.stop());

    /*unpublich the facelive stream*/
 
    if(this.faceStream)
      await this.faceStream.unpublish();

    /*REMOVE THE ROOM FORM SREVER*/
    var endpoint = this.urls .database + '/delete_room.php?rid=' + this.getRoom.id;
    CapacitorHttp.get({url:endpoint})
  }

}
</script>
 

<template>

	<AppTemplate>
		
    <video ref="mainLive" @click="toggleMenu" class="position-absolute video" autoplay > 
    </video> 


    <!-- bubble and video container -->
    <BubbleChatsContainer @updateStream="updateStream" :edit="menuOpen" class="m-2" :videoElement="mainLive" :stream="faceStream" :streams="streamLives"></BubbleChatsContainer>


    <player-controller v-if="mainLive" @close-menu="closeMenu" :visible="menuOpen" :videoElement="mainLive"></player-controller>



	</AppTemplate>

</template>

<style type="text/css" scoped>
.video{
	object-fit: contain;
}
</style>

 