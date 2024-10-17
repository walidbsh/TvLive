<template>
  <div v-if="visible" class="bubble_chat d-flex flex-column align-items-center position-relative" >
      <div @click.stop="show_menu"  class="position-relative bubble mb-2"> 
 
            <video ref="video" class="video_chat" autoplay>
            </video>

            <!-- for onwer -->
            <img v-if="!state.video && owner" class="avatar" :src="urls.database + user_settings.general.avatar" />
            <!-- for other -->
            <img v-if="!state.video && !owner && user_states" class="avatar" :src="urls.database + user_states.avatar" />
            
            <ion-icon v-if="!state.audio && !owner" color="" :icon="volumeMute" class="bottom-mid bull"/>
            
      </div>

      <ion-icon @click.stop="close" v-if="edit&!owner" class="top-right" color="danger" size="large" :icon="closeCircle" />

      <div v-if="bubble_managable" ref="buttons_container" :class="['buttons-container p-2 d-flex flex-column align-items-center close']" >
        <div @click.stop="micToggle" class="d-flex justify-content-center align-items-center">
          <ion-icon v-if="state.mic" class="fs-24" :icon="mic" />
          <ion-icon v-else class="fs-24" :icon="micOff" />
        </div>


        <div v-if="!owner" @click.stop="audioToggle" class="d-flex justify-content-center align-items-center">
          <ion-icon v-if="state.audio" class="fs-24" :icon="volumeMedium" />
          <ion-icon v-else class="fs-24" :icon="volumeMute" />
        </div>


        <div @click.stop="videoToggle" class="d-flex justify-content-center align-items-center">
          <ion-icon v-if="state.video" class="" style="font-size: 20px;" :icon="videocam" />
          <ion-icon v-else class="" style="font-size: 20px;" :icon="videocamOff" />
        </div>
        <div @click.stop="close" class="d-flex justify-content-center align-items-center m-0 p-0">
          <ion-icon class="fs-24" :icon="closeCircle" /> 
        </div>
      </div>
  </div>
</template>

<script>
 
import {volumeMute, volumeMedium, closeCircle, videocamOff, videocam, micOff, mic} from 'ionicons/icons'
import { IonIcon, IonImg } from '@ionic/vue'
import { LocalStream } from 'ion-sdk-js';
import { mapGetters, mapActions } from 'vuex'

let scriptProcessor
let analyser
let microphone
let audioContext

export default {
  name: 'Bubble',
  components : {
    IonIcon,
    IonImg
  },
  emits: ['update_stream'],
  data(){
    return {
      mute:false,
      video_w:"60px",
      video_h:"60px",
      closeCircle,
      volumeMute,
      volumeMedium,
      visible:true,
      videocamOff,
      videocam,
      micOff,
      mic,
      isOpen:false,
      state:{
        mic:true,
        camera:true,
        video:true,
        audio:true
      }, 
      bubble_managable:true,
      user_states:null
    }
  },
  props: { 
    stream:null,
    owner: false,
    edit:false 
  },
  watch: {
    audio_manager(newVolume) {
      this.$refs.myVideo.volume = newVolume.chat
    },
  },  


  methods:{ 
    ...mapActions(['add_bubble', 'set_user_settings']),
    show_menu(){
      // Close all other chat bubbles
      document.querySelectorAll('.buttons-container.open').forEach(element => {
          if(element != this.$refs.buttons_container){
            element.classList.remove('open');
            element.classList.add('close'); 
          }
      }); 
 
      if(this.$refs.buttons_container.classList.contains('close')){ // if it close open it
        this.$refs.buttons_container.classList.remove('close')
        this.$refs.buttons_container.classList.add('open') 
      }else{ // close it 
        this.$refs.buttons_container.classList.remove('open')
        this.$refs.buttons_container.classList.add('close')
      }
  
    },
    async close(){ 
      const wait = await this.stopAnalyser()
      this.stream.getTracks().forEach(track => track.stop());
      this.visible = false 
      this.$refs.video.srcObject = null;
    },
    audioToggle(){
      this.$refs.video.muted = !this.$refs.video.muted
      this.state.audio = this.$refs.video.muted
    },
    async videoToggle(){ 
      if(this.stream == null){
        alert('no stream')
        return
      }
      this.state.video = !this.state.video
      let videoTracks = this.stream.getVideoTracks();
      

      if(videoTracks.length == 0){ // user start stream without camera so recreate new stream
        /*try to creat new stream!*/
        console.log('updating stream from bubble')
        var newOptions = this.user_settings
        newOptions.room_options.camera = this.state.video

        this.set_user_settings(newOptions);

        this.$emit('update_stream')
      }

      videoTracks = this.stream.getVideoTracks();
      if(videoTracks.length == 0 ) console.log('failed')
      let video_state = this.state.video
      videoTracks.forEach(track => {
        track.enabled = video_state;
      });

    },
    micToggle(){ // only for streamer
      this.state.mic = !this.state.mic
      let mic = this.state.mic
      var audioTracks = this.stream.getAudioTracks();
 
      audioTracks.forEach(track => {
        track.enabled = mic;
      }); 
    },
    async stopAnalyser(){
      scriptProcessor.disconnect(audioContext.destination);
      analyser.disconnect(scriptProcessor);
      microphone.disconnect(analyser);
      audioContext.close();
      console.log('audio analyser resources cleaned up');

    },
    async detectSpeaking(stream) {

      audioContext = new (window.AudioContext || window.webkitAudioContext)();
      analyser = audioContext.createAnalyser();
      microphone = audioContext.createMediaStreamSource(stream);
      scriptProcessor = audioContext.createScriptProcessor(256, 1, 1);
 
      analyser.fftSize = 256;
      microphone.connect(analyser);
      analyser.connect(scriptProcessor);
      scriptProcessor.connect(audioContext.destination);

      const dataArray = new Uint8Array(analyser.frequencyBinCount);

      scriptProcessor.onaudioprocess = () => {
        analyser.getByteFrequencyData(dataArray);
        const sum = dataArray.reduce((a, b) => a + b, 0);
        const average = sum / dataArray.length;

        if (average > 20) { // Adjust this threshold as necessary
          this.$refs.video?.classList.add('speaking');
        } else {
          this.$refs.video?.classList.remove('speaking');
        }
      };
 
    }

  },
  async mounted(){  
       
       
      const options = this.user_settings.room_options
      this.bubble_managable = !(!options.camera && !options.mic && this.owner)

      /// i must have the user video id


      // use the difault options only for the owner
      if(this.owner){
        this.state.video = this.user_settings.room_options.camera
        this.state.mic   = this.user_settings.room_options.mic
      }
 
      //this.$refs.video.muted = true



      this.$refs.video.srcObject = this.stream;
      this.$refs.video.muted     = this.state.audio

      let buttonsContainer = this.$refs.buttons_container
      buttonsContainer.addEventListener('transitionend', () => {
        if (buttonsContainer.classList.contains('close')) 
          buttonsContainer.style.pointerEvents = 'none'; // Disables interaction 
        if(buttonsContainer.classList.contains('open'))
          buttonsContainer.style.pointerEvents = 'auto'; // Re-enable interaction
      });

      if(!this.owner){
        console.log(this.stream.id)
        var data = this.lives_data
        var got  = false
        console.log(this.lives_data.length)
        for (var i = 0; i < this.lives_data.length; i++) {
          var data_ = JSON.parse(this.lives_data[i])
          if(data_.stream_id == this.stream.id){
            got = true
            this.user_states = data_;
            console.log('im ', this.user_states.username)
          }
        }
            
      }

  },
  computed:{...mapGetters(['audio_manager', 'urls', 'user_settings', 'lives_data', ])}
}
</script scopped>

<style>
 

.buttons-container.open {
  opacity: 1;
  transform: scale(1);
}

.buttons-container.close {
  opacity: 0;
  transform: scale(0.8);
  transition: all 0.3s ease;
}
.buttons-container{
  opacity: 0;
  transform: scale(0.8);
  transition: all 0.3s ease;
  background: blueviolet;
 
  border-radius: 15px;
}
.fs-24{
  font-size: 24px
}
.bottom-mid{
  position: absolute;
/*  border: 1px solid red;*/
  bottom: 0;
  left: 50%;
  transform: translate(-50%, 50%);
  font-size: 23px;
}
.top-right{
  position: absolute;
  right: 0;
  top: 0;
  transform: translate(20%, -20%);
}
.bottom-right{
  position: absolute;
  bottom: 0;
  transform: translateX(-100%);
  color: white;
}
.bull{ 
  font-size: 15px;
  border-radius: 50%;
  padding: 4px;
  background: gray;
}
</style>

<style scoped> 
.bubble{
/*  object-fit: cover; */
  height: 60px !important;
  width: 60px !important;
/*  border-radius: 50%;
  border: 1px solid blueviolet;
*/} 

.video_chat{
  object-fit: cover;
  object-position: center;
  border-radius: 50%;
  border: 1px solid blueviolet;
  transition: border-color 0.3s, box-shadow 0.3s;
}

.video_chat.speaking {
  border-color: #00ff00; /* Green border when speaking */
  box-shadow: 0 0 15px rgba(0, 255, 0, 1); /* Green glow effect */
}

.avatar{
  position: absolute;
  left: 0;
  top: 0;
  width: 60px;
  height: 60px;
  object-fit: cover;
  border-radius: 50%;
  padding: 1px;
/*  border: 1px solid blueviolet;
  transition: border-color 0.3s, box-shadow 0.3s;*/
}
.avatar.speaking {
  border-color: #00ff00;  
  box-shadow: 0 0 15px rgba(0, 255, 0, 1);  
}
</style>