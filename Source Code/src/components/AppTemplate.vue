<template>
  <ion-page>
    <ion-header v-if="$route.meta.navbar" :translucent="true">

      <ion-toolbar>
        <ion-buttons v-if="back_button" slot="start">
          <ion-back-button default-href="/home"></ion-back-button>
        </ion-buttons>
        <ion-buttons slot="end">
          <div class=" position-relative" v-if="notif">
            <ion-button @click.stop="go('/notifications')">
              <ion-icon slot="icon-only" :icon="earthOutline">
              </ion-icon>
            </ion-button>
            <div v-if="requests.length>0" class="badge-end">{{requests.length}}</div>
          </div>  
          <ion-button @click.stop="go('/settings')" v-if="settings">
            <ion-icon  slot="icon-only" :icon="settingsOutline"></ion-icon>
          </ion-button> 
          <ion-button @click.stop="go('/search')" v-if="people">
            <ion-icon slot="icon-only" :icon="peopleOutline"></ion-icon>
          </ion-button> 
          <ion-menu-button v-if="menu" :auto-hide="false"></ion-menu-button>
        
        </ion-buttons>
         
        <ion-title class="ms-2">{{header_title}}</ion-title>
      </ion-toolbar>
    </ion-header>

    <ion-content :fullscreen="true">
 
<!--       <div id="container"> -->

       <slot/>

<!--       </div> -->

    </ion-content>
  </ion-page>
</template>

<script>

import { IonContent, IonHeader,  IonPage, IonTitle, IonToolbar, IonIcon, IonBackButton, IonButton, IonMenuButton, IonButtons, IonRefresherContent, IonRefresher} from '@ionic/vue';

import {mapGetters} from 'vuex'
import {earthOutline, peopleOutline, settingsOutline} from 'ionicons/icons'

export default{
  name:'AppTemplate',
  props:{
   header_title:false,
   back_button:false ,
   menu:false ,
   notif:false,
   people:false,
   settings:false
  },

  components:{IonContent,
    IonHeader,
    IonPage,
    IonTitle,
    IonToolbar,
    IonIcon,
    IonBackButton,
    IonButton,
    IonMenuButton,
    IonButtons,
    IonRefresher,
    IonRefresherContent
  },
  data(){
    return {
      earthOutline,
      peopleOutline,
      settingsOutline
    }
  },
  methods:{
    go(url){
      this.$router.push(url)
    }
  },
  computed:{...mapGetters(['requests'])}

}
</script>

<style type="text/css">
  
.badge-end{
  position: absolute;
  right: 0;
  top: 0;
  background: red;
  width: 16px;
  height: 16px;
  margin: 5px;
  padding: 0;
  text-align: center;
  border-radius: 50%;
  font-size: 12px;
}

</style>
 