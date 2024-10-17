<template> 
	<div v-if='image' class="story position-relative me-2">
    <!-- movie tmbnile --> 
    <ion-thumbnail @click="join(`room/${story.link}`)"  class="story-cover">
      <ion-img :src="image" class="story-cover-img"></ion-img>  
    </ion-thumbnail>

    <!-- provider avatar -->
    <ion-thumbnail class="top-left m-1">
      <ion-img class="story-profile live" :src="urls.database + story.avatar"></ion-img>
    </ion-thumbnail> 
    <router-link :to="`room/${story.link}`" class="d-none footer w-100 p-1">
            <ion-button class="w-100 text-capitalize bg-disabled">join</ion-button>
    </router-link>
  </div>
</template>

<script type="text/javascript">

import {IonButton , IonImg, IonThumbnail,} from '@ionic/vue';
import { mapGetters } from 'vuex'
 
export default{
	name:'Story',
	props:{
		story:null
	},
	components:{ 
    IonButton,
    IonThumbnail,
    IonImg
	},
  data(){
    return {
      image:null
    }
  },
	methods:{
    join(link){
      this.$router.push(link)
    }
	},
  mounted(){
    this.image = this.urls.database + this.story.thumbnail 
  },
  computed:{...mapGetters(['urls'])}
	 
}

</script>

<style scoped lang="css"> 

.story-profile.live {
    position: relative;
    border-radius: 50%;
    border: 0px solid transparent;
    animation: pulse 1.5s infinite; 
}

@keyframes pulse {
    0% {
        box-shadow: 0 0 0 0 rgba(255, 0, 0, 1);
    }
    70% {
        box-shadow: 0 0 0 10px rgba(255, 0, 0, 0);
    }
    100% {
        box-shadow: 0 0 0 0 rgba(255, 0, 0, 0);
    }
}
.top-left{
  position: absolute;
  top: 0;
  left: 0;
  width: max-content;
  height: max-content;
}
.footer{
  width: max-width; 
  position: absolute;
  left: 50%;
  bottom: 0;
  transform: translate(-50%, 0);
}

.story{ 
/*  height: 200px;*/ 
}

.story-profile{
  margin: 3px;
  position: absolute;
  height: 100%;
  width: 100%;
  top: 0;
  left: 0;
  border-radius: 50%;
  border: 1px solid white;
  width: 35px;
  height: 35px;
} 
.story-cover{ 
  width: 100%;
  height: 100%;  
}
.story-cover-img{
  border-radius: 3px;
  object-fit:cover;
  transform: scale(1.0);

}
.bg-disabled{
  background: ;
}
</style>