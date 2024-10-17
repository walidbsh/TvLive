<script type="text/javascript">

import {inject} from 'vue'
import { IonIcon, IonRange } from '@ionic/vue'
import { playCircleOutline, pauseCircleOutline, videocam, logOut, videocamOff, micOff, mic, logoYoutube, people, closeOutline,  } from 'ionicons/icons';

export default {
	name:"PlayerController",
	props:['videoElement' , 'visible', 'controllable'],
	components:{
		IonIcon,
		IonRange
	},  
  emits: ['close-menu'],
	data(){
		return { 
			pauseCircleOutline,
			audio_option:false,
			video_option:false,

			playCircleOutline,
			videocam,
			logOut,
			videocamOff,
			micOff,
			mic,
			logoYoutube,
			people, 
			time_bar:null,
			currentTime:'00:00',
			duration:'00:00', 
			durationSecs:0, 
			currentTimeSecs:0,
			isPlaying:true, 
		}
	},
	methods:{ 
		skip_time(amount){
			var newTime = this.currentTimeSecs + amount
			this.videoElement.currentTime = newTime;
		},
		closeMenu() {
			this.$emit('close-menu')
    },
    play(){
    	if(!this.videoElement) return
			this.isPlaying = true
			this.videoElement.play()
		},
		stop(){
			if(!this.videoElement) return
			this.isPlaying = false
			this.videoElement.pause()
		},
		pinFormatter(value) {
			if(value > 0 )
				this.videoElement.currentTime = Math.round( value )
			return this.formatTime(value)
		},
		onIonKnobMoveStart({ detail }) { 
			if(!this.videoElement) return
				this.videoElement.pause() 
		},
		onIonKnobMoveEnd({ detail }) {   
			if(!this.videoElement) return
			//this.currentTimeSecs = detail.value
			this.videoElement.play() 
		},
		formatTime(seconds) {
		    const h = Math.floor(seconds / 3600);
		    const m = Math.floor((seconds % 3600) / 60);
		    const s = seconds % 60;

		    if (h > 0) {
		        return `${h}:${m.toString().padStart(2, '0')}:${s.toString().padStart(2, '0')}`;
		    } else {
		        return `${m}:${s.toString().padStart(2, '0')}`;
		    }
		}

	},

	mounted(){
			var self = this
			this.time_bar = this.$refs.time_bar
			 
			self.videoElement.addEventListener('loadedmetadata', () => {
					var t = Math.round( self.videoElement.duration )
					self.durationSecs = t
					self.duration = self.formatTime( t );   
    	});
			
			self.videoElement.addEventListener('timeupdate', () => {
					var t = Math.round( self.videoElement.currentTime )
					self.currentTimeSecs = t 
					self.currentTime = self.formatTime(t)  
    	});
	}
}

</script>


<template>
  <div v-if="visible" @click.stop="closeMenu" class="blur_screen position-relative " style="z-index: 0 !important"  >


  		<!-- CENTER BUTTONS -->
      <div class="bg-black-75 z-index-200 d-flex justify-content-center align-items-center w-100 h-100">
        <div class="w-75 d-flex justify-content-around align-items-center  position-relative">
          
        	<!-- CHAT SOUND RANGE -->
        	<ion-range  
        		label-placement="end"
        		class="vertical-range z-index-200 d-none"
						ref="sound_range"
						:pin="true"  
						:value="90" 
						:max="100"> 
					</ion-range>


          <div class="z-index-200">
	          <img @click.stop="skip_time(-10)" class="back-time" src="../assets/back-time.png">
          </div>

          <div class="z-index-200">
	          <ion-icon v-if="isPlaying" @click.stop="stop" style="font-size: 60px; --ionicon-stroke-width: 12px;":icon="pauseCircleOutline"/>
          	<ion-icon v-else           @click.stop="play" style="font-size: 60px; --ionicon-stroke-width: 12px;":icon="playCircleOutline"/>
          </div>
          
          <div class="z-index-200">
	          <img @click.stop="skip_time(+10)" class="next-time" src="../assets/back-time.png">
          </div>
        </div> 

				

	 			

      </div>
 			<!-- END CENTER BUTTONS -->

			<!-- MAIN TIMER RANGE -->
	      <div class="w-100 px-3 mt-3 fixed-bottom mb-5 z-index-200"> 
					<ion-range  
						diabled
						ref="time_bar"
						@click="$event.stopPropagate()"
						@ionKnobMoveStart="onIonKnobMoveStart"
						@ionKnobMoveEnd="onIonKnobMoveEnd"
						:pin="true"  
						:value="currentTimeSecs"
						:pin-formatter="pinFormatter"
						:max="durationSecs">
						<span class="p-3" slot="start">{{currentTime}}</span>
						<span class="p-3" slot="end">{{duration}}</span>
					</ion-range>

	      </div> 
      <!-- END MAIN TIMER RANGE -->


      <!-- CONTROL BUTTONS  (MIC, CAMERA, LEAVE)-->
      <div class="d-flex fixed-bottom-center d-none" style="z-index : 200 !important"> 
 

        <div class="rounded_button_container"> 
          <ion-icon @click="video_option=!video_option" v-if="video_option" class="w-100 h-100" :icon="videocamOff"/>
          <ion-icon @click="video_option=!video_option" v-else class="w-100 h-100" :icon="videocam"/>
        </div>

        <div class="rounded_button_container">
          <ion-icon @click="audio_option=!audio_option" v-if="audio_option" class="w-100 h-100" :icon="micOff"/>
          <ion-icon @click="audio_option=!audio_option" v-else class="w-100 h-100" :icon="mic"/>
        </div>

        <div class="rounded_button_container">
          <ion-icon class="w-100 h-100" :icon="logOut"/>
        </div>
      </div> 
      <!-- END CONTRILLE BUTTONS (MIC, CAMERA, LEAVE) -->
        

      
    </div>


    <!-- exit icon -->  
<!--     <div @click="menu = !menu"  v-if="menu"  class="index-11 fixed-top-right d-flex justify-content-end ">
      <ion-icon size="large" class="m-2" :icon="closeOutline">

      </ion-icon>
    </div> -->
</template>

<style scoped type="text/css">
ion-icon .play-btn{ 
	width: 64px !important;
	height: 64px !important;
}
.blur_screen{
	width:  100vw;
	height: 100vh; 
}
.bg-black-75{
	background: rgba(0, 0, 0, .75) !important
}
.z-index-3{
	z-index: 3;
}


.vertical-range {
	left: 0;
	position: absolute;
  writing-mode: bt-lr; /* bottom to top, left to right */
 	width: 40vh !important;
 	height: 30px !important;
    
  transform: rotate(-90deg) translateY(-80px);
}

.vertical-range .range-knob-handle {
  transform: rotate(-90deg);
}
</style>