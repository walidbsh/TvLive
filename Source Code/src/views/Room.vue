<template>
  <AppTemplate>

    <video ref="mainVideo"  @click="toggleMenu" class="position-absolute" autoplay> </video> 

    <!-- bubble and video container -->
    <BubbleChatsContainer :edit="menuOpen" class="m-2" :stream="faceStream" :streams="streamLives"></BubbleChatsContainer>


    <player-controller v-if="mainVideo" @close-menu="closeMenu" :visible="menuOpen" :videoElement="mainVideo"></player-controller>


    <!-- room not available -->
    <div v-if="show_room_not_existe_panale" class="center text-warning d-flex flex-column align-items-center col-12"  >
      <h2>Room not available!</h2>
      <ion-button @click="$router.go(-1)">home</ion-button >
    </div> 

  </AppTemplate>
</template>

<script>
import { CapacitorHttp } from '@capacitor/core';

import { IonIcon, loadingController, IonButton } from '@ionic/vue';
import { playCircleOutline, videocam, logOut, videocamOff, micOff, mic, logoYoutube, people, closeOutline } from 'ionicons/icons';


 

import PlayerController from '../components/PlayerController.vue'  
import BubbleChatsContainer from '../components/BubbleChatsContainer.vue'  
import AppTemplate from '../components/AppTemplate.vue'  


import { Client, LocalStream, RemoteStream } from 'ion-sdk-js';
import { IonSFUJSONRPCSignal } from 'ion-sdk-js/lib/signal/json-rpc-impl';
let streamWatcher;
let streamIDs = []
const config = {
  iceServers: [
  /*  { urls: "stun:stun.l.google.com:19302"  },  */
    { urls: 'stun:stun1.l.google.com:19302' },
    { urls: 'stun:stun2.l.google.com:19302' }, 
  ]
}
const mainLoading = null

import { mapGetters, mapActions } from 'vuex'
import axios from 'axios'
export default{
 name: 'Room', 
  data(){
    return { 
      menuOpen:false,
      selected_video_name : "",
      selected_file : null, 
      faceStream:null,
      streamLives:[],
      room_id:this.$route.params.id,
      audio_option:true,
      video_option:true,
      videocamOff,
      videocam,
      micOff, 
      mic,
      logoYoutube,
      people, 
      logOut,
      playCircleOutline,
      closeOutline,
      mainVideo: this.$refs.mainVideo,
      thumbnail:"",
      clientWatcher:null,
      clientLive:null,
      show_room_not_existe_panale:false,
      dataChannel:null
    }
  },
  components: { 
    IonIcon, 
    AppTemplate,
    BubbleChatsContainer,
    PlayerController,
    IonButton
  }, 
  beforeUnmount(){  /*  closeing the page */

    /* close the loading live panel if it open*/
    if (this.mainLoading) {
      /*const work = await*/ this.mainLoading.dismiss();
      this.mainLoading= null;
    }

    /*close the camera */
    if(this.faceStream)
      this.faceStream.getTracks().forEach(track => track.stop());
 
    if(this.clientLive){ 
      this.clientLive.close();
    }  
  },
  methods:{ 
      ...mapActions(['setLivesData']),
      send_states(){

        if(!this.faceStream.id){
          console.log('no stream id ')
          return
        }

        var states = {
          uid:this.user_settings.id,
          username:this.user_settings.general.username,
          stream_id:this.faceStream.id,
          avatar:this.user_settings.general.avatar
        }

        this.dataChannel.send(JSON.stringify( states ))

        console.log('data sent')
      },
      toggleMenu() {
          if(!this.show_room_not_existe_panale)
          this.menuOpen = !this.menuOpen;
      },
      closeMenu() {
          this.menuOpen = false;
      }, 
      closeMenu() {
          this.menuOpen = false;
      }, 
      async startLive(){
        var self = this;
        this.clientLive.ontrack = (track, stream) => {

          console.log(track, stream) 

          track.onunmute = ()=> { 

            if(track.kind == "video" || track.kind == "audio"){ 
 

              /*make sure the stream not existe*/
              var existe = false
              for (var i = 0; i < self.streamLives.length; i++) {
                if(self.streamLives[i].id == stream.id)
                  existe = true
              }


              if(!existe){
                self.streamLives.push(stream)  
                /*remove live bubble*/
                stream.onremovetrack = () => {  
                  const result = self.streamLives.filter(stream_ => stream_.id !== stream.id)
                  self.streamLives = result; 
                }  
              } 
              self.send_states()
            } 
          }
        }


        /*FACE STREAM OPTIONS*/
        var faceLiveOptions = {
            codec: 'VP8',
            resolution: 'hd',
            audio:this.user_settings.room_options.mic,
            video:this.user_settings.room_options.camera,
            simulcast:true
        }
        var mediaOptions = {
          resolution:'hd',
          audio: true,
          video: {
            facingMode: "user",
          },
        }
        
        if(navigator.mediaDevices){
          var faceStream_ = await LocalStream.getUserMedia(faceLiveOptions)  
          self.faceStream = faceStream_ 
          this.clientLive.publish(faceStream_)
          this.send_states()
        }else
          alert('no media')

      }
    },
    computed:{ 
      ...mapGetters(['users_invited', 'urls', 'user_settings']),
  },
  async mounted(){ 
 

    /*CHEK IF THE ROOM ARE EXISTE*/
 

    const self = this;
    const ionServer = this.urls.ionSfuServer
    this.mainVideo = this.$refs.mainVideo;
    if(this.mainVideo == null) {  
      return
    }
    /*get room thumbnail*/
    var endpoint_ = `${this.urls.http}/get_room.php?link=${this.room_id}`; 

    try {
      const { data } = await CapacitorHttp.get({ url: endpoint_ });
      if(data == 404){ 
        self.show_room_not_existe_panale = true 
        return
      }

      self.thumbnail = data.thumbnail; 
    } catch (error) {
      console.error('Room not available!:', error);
      // Handle error (e.g., show an error message to the user)
    }


  
    this.mainLoading = await loadingController.create({
      message: 'connecting, please wait...',
    });

    this.mainLoading.present();

 

    /*WATCH LIVE SECTION*/
    const signalWatcher = new IonSFUJSONRPCSignal(ionServer);
    this.clientWatcher = new Client(signalWatcher, config);
    signalWatcher.onopen = () => { 
      console.log('client opened')
      const uid = 'uidw_' + self.user_settings.id;
      const rid = 'ridw_' + self.room_id;

      self.clientWatcher.join(rid, uid).then(() => { 
      }).catch((error) => {
        console.error(`Failed to join room ${rid} as ${uid}:`, error);
      }); 
       
    };

 
    this.clientWatcher.ontrack = (track, stream) => {
 

      track.onunmute = ()=> { 

        if(track.kind == "video"){  
 
            self.mainVideo.srcObject = stream; 
            if (self.mainLoading) {
              self.mainLoading.dismiss();
              self.mainLoading= null;
            }
            stream.onremovetrack = () => { 
              this.$router.go(-1)
            }  
      

        } 
      }
    }

    
    this.clientWatcher.connectionstatechange = (state) => {
      console.log('Watcher connection state changed:', state);
      if (state === 'disconnected' || state === 'failed') {
        // Handle reconnection if necessary
        console.log('Reconnecting...');
        self.clientWatcher.reconnect();
      }
    };
 
   

    /* CHAT STREAMS */ 

    const signalLives = new IonSFUJSONRPCSignal(ionServer);
    this.clientLive = new Client(signalLives, config);
    signalLives.onopen = () => { 
      console.log('joining for live!');
      var uid = 'uidl_' + self.user_settings.id;
      var rid = 'ridl_' + self.room_id;

      self.clientLive.join(rid, uid).then(() => {
        console.log(`Successfully joined live room ${rid} as ${uid}`);

        // send a uid and 
      }).catch((error) => {
        console.error(`Failed to join live room ${rid} as ${uid}:`, error);
      });
      
      self.dataChannel = self.clientLive.createDataChannel("111")

      // Setup event handlers for the data channel
      self.dataChannel.onopen = () => { 
        self.send_states()
      };

      self.dataChannel.onmessage = (event) => {
        console.log('i go data' , event.data)
        self.setLivesData(event.data);
      };
  
      self.startLive();
    };
 
    this.clientLive.connectionstatechange = (state) => {
      console.log('Live connection state changed:', state);
      if (state === 'disconnected' || state === 'failed') {
        // Handle reconnection if necessary
        console.log('Reconnecting...');
        self.clientLive.reconnect();
      }
    };
    
  
  },
  
  created(){

  },
  
}

</script>

<style>
.next-time{
  transform: scaleX(-1.0);
}
.back-time, .next-time{
  height: 25px;
  width: 25px;
}
.fixed-bottom-center{
  position: absolute;
  bottom: 0;
  right: 50%;
  transform: translateX(50%);
  background: black;
  border-top-left-radius: 30px;
  border-top-right-radius: 30px;
  padding: 15px;
  padding-bottom: 0;
  padding-top: 0;
}
.rounded_button_container{ 
  opacity: 75%;
  width: 50px;
  height: 50px;
  border-radius: 50%;
  padding: 10px;
}
  
*{
  margin: 0;
  padding: 0;
}
.center{ 
    position: absolute;
    left: 50%;
    top: 50%;
    transform: translate(-50%,-50%);
}

.fullscreen{
  height: 100vh;
  width: 100vw;

  background: black;
}  

.full{
  height: 100%;
  width: 100%; 
}

video {
  width: 100%;
  height: 100%;
  object-fit: cover;
}
</style>

<style> 
.r90{
  transform: rotate(90deg);
}
.p50{
  width: 55px;
  height: 55px;
}
.fixed-top-right{
  z-index: 10;
  position: absolute;
  top: 0;
  right: 0;
}
.fixed-left-middle{
  position: absolute;
  right: 0;
  top: 0;  
}
.r90n{
  transform: rotate(-90deg);
}
.index-11{
  z-index: 11;
}
.w-min{
  width: min-content;
}

video{
  object-fit: contain;
}


</style>

 