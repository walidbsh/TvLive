<template>
  <AppTemplate header_title="Home" :settings="true" :notif="true">
   
    <ion-refresher slot="fixed" :pull-factor="0.5" :pull-min="100" :pull-max="200" @ionRefresh="handleRefresh($event)">
      <ion-refresher-content></ion-refresher-content>
    </ion-refresher>
 

    <!-- Storys Section -->
    <app-title class="m-2 mb-1" title="Friends Room"/>
    <!-- friends movies -->
    <div class="d-flex p-2 p story-container">
  
  		<div>
  			<div class="story position-relative d-flex  flex-column justify-content-center bg-dark align-items-center me-2">
  				<div class="w-100 h-100 d-flex justify-content-center align-items-center r45n m-4">
  					 
              <ion-icon size="large" style="scale: 1.5;" class="opacity-75 mb-2 ms-2" :icon="linkSharp" />
               
  				</div>
  				<router-link to="/join" class="footer text-decoration-none w-100 d-flex justify-content-center p-1">
  				    <ion-button class="w-100 text-capitalize">Join</ion-button>
  				</router-link>
  			</div>

  		</div>

  		<div v-for="story in myfriendsrooms" :key="story"> 
  			<Story :story="story"></Story>
  		</div> 

      <div>
        <div class="story position-relative d-flex  flex-column justify-content-center bg-dark align-items-center me-2">
          <div class="w-100 h-100 d-flex 
          justify-content-center align-items-center m-4">
             
              <ion-icon style="scale: 2.4;"
              class="opacity-75" :icon="personAddSharp" />
               
          </div>
          <router-link to="/search" class="footer text-decoration-none w-100 d-flex justify-content-center p-1">
              <ion-button class="w-100 text-capitalize">Add</ion-button>
          </router-link>
        </div>

      </div>
    </div>

    <!-- public rooms section -->
    <app-title class="m-2 mb-1" title="Public Rooms"/>
    <div class="d-flex flex-column p-2 col-12"> 
          <PublicChannelContainer v-for="(movie, index) in myfriendsrooms" :key="index" :movie="movie" />
    </div>
    

    <!-- auto loader -->
    <button class="d-none" @click="loadData"> load more </button>

    <!-- make button -->
    <router-link to="/make" class="batment bottom_right rounded-pill m-3 ">
      <ion-icon class="add-button" style="color: white;" :icon="addOutline"></ion-icon>
    </router-link>
 

  </AppTemplate>
</template>

<script>

import { IonIcon, IonButton, IonRefresher, IonRefresherContent } from '@ionic/vue';
import { personAddSharp, linkSharp, addOutline } from 'ionicons/icons'; 
import { mapGetters, mapActions } from 'vuex'
import { CapacitorHttp }          from '@capacitor/core'

import AppTemplate                from '../components/AppTemplate.vue'
import PublicChannelContainer     from '../components/PublicChannelContainer.vue'
import AppTitle                   from '../components/AppTitle.vue'
import Story                      from '../components/Story.vue'

export default{
  components:{
    AppTemplate,
    PublicChannelContainer,
    Story, 
    IonButton,
    AppTitle,
    IonIcon,
    IonRefresher,
    IonRefresherContent
  },
  setup() {
      return { personAddSharp, linkSharp, addOutline };
  },
  mounted(){
    this.loadData()
  },
  data(){
    return {
      myfriendsrooms:[],
      offset:0,
      limit:3
    }
  },

    
  methods:{
    ...mapActions(['setUser']),  
    async loadData(){
      let self = this;
      var endpoint = `${this.urls.database}/get_room.php?uid=${ this.user_settings.id}&offset=${this.offset}`;
   
      self.offset = self.offset + self.limit
      
      CapacitorHttp.get({ 
        url: endpoint,
      }).then(({ data }) => {

        for (var i = 0; i < data.length; i++) {
          self.myfriendsrooms.push(data[i])
        }
        //self.myfriendsrooms = data;
      }) 

    },
    async handleRefresh(event){
      this.offset = 0
      this.myfriendsrooms = []
      await this.loadData()
      event.target.complete()
    },
    timeAgo(dateString) {
        const now = new Date();
        const date = new Date(dateString);

        const diffInSeconds = Math.floor((now - date) / 1000);

        const intervals = {   
            d: 86400,
            h: 3600,
            m: 60,
            s: 1,
        };

        for (const [key, value] of Object.entries(intervals)) {
            const interval = Math.floor(diffInSeconds / value);
            if (interval >= 1) {
                return `${interval}${key} ago`;
            }
        }

        return 'just now';
    },

  },
  computed:{
    ...mapGetters(['urls', 'user_settings'])
  },
  
}

</script>

<style type="text/css">
  
.r45n{
  transform: rotate(-45deg) !important;
}


</style>

<style scoped>

.story{ 
	   width: 30vw;
  	border-radius: 4px;  
    height: 150px; 
}
.story-container{
  width: 100vw !important; 
  overflow-x: scroll !important; 
}


.logo{
  font-size: 26px;
  font-weight: bold;
}
.bottom_right{
  position: fixed;
  bottom: 0;
  right: 0;
} 
.p36{
  width: 36px;
  height: 36px;
}
.fullScreen{
  height:100vh !important;
  width:100vw !important;
}
.add-button{
  height: 40px;
  width: 40px;
  padding: 10px;
  
  border-radius: 50%;
  background: blueviolet;
}
@keyframes leaves {
    0% {
        transform: scale(1.0);
        -webkit-transform: scale(1.0);
    }
    100% {
        transform: scale(1.2);
        -webkit-transform: scale(1.2);
    }
}

.batment {
    animation: leaves 1s ease-in-out infinite alternate;
    -webkit-animation: leaves 1s ease-in-out infinite alternate;
}
</style>
