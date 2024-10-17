<script type="text/javascript">
import {mapGetters} from 'vuex'
import {IonButton} from '@ionic/vue'
import {CapacitorHttp} from '@capacitor/core'
export default{
	name:'PublicChannelContainer',
	props:['movie'],
	components:{
		IonButton
	},
	mounted(){
 
	},
	methods:{
		timeAgo(dateString) {
	        const now = new Date();
	        const date = new Date(dateString);

	        const diffInSeconds = Math.floor((now - date) / 1000);

	        const intervals = {
	            y: 31536000,
	            month: 2592000,
	            week: 604800,
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
	    addFriend(){
				let uid = this.user_settings.id
				var endpoint = `${this.urls.database}/follow.php?follower=${uid}&followed=${this.movie.creator}`
				console.log(endpoint)
				CapacitorHttp.get({url:endpoint})
				this.socket.emit('send_add', this.user.id , 'new friend request check it!')
	    },
	    open(id){
	    	this.$router.push(`/room/${this.movie.link}`)
	    },
	},
	computed:{...mapGetters(['urls', 'user_settings', 'socket'])}
}

</script>



<template>
	<div @click="open" class="position-relative mb-2 col-12"> 
        <div class="d-flex to-top  w-100 align-items-center p-2"> 
          <img :src="urls.database + movie.avatar">
          <h6 class="ms-2 text-capitalize my-auto">{{movie.username}}</h6>

          <ion-button v-if="!movie.isFollowed" @click="addFriend" size="small" class="ms-auto">follow</ion-button>
        </div>
        <!-- provider avatar -->
        <img class="movie-tmb shadow-sm w-100" :src="urls.database + movie.thumbnail"/>
        <div class="linear_black movie-footer p-1 d-flex justify-content-between align-items-end w-100">
          <p class="movie-title towLineLim">{{movie.name}}</p>
          <p class="duration">{{timeAgo(movie.date)}}</p>
        </div>
    </div>	
</template>


<style>

.to-top{
	position: absolute;
	top: 0;
	left: 0;
}
.to-top > img{
	width: 35px;
	height: 35px;
	border-radius: 50%;
	object-fit: cover;

}

.towLineLim{
    overflow: hidden;
    text-overflow: ellipsis;
    display: -webkit-box; /* For WebKit */
    display: -moz-box; /* For Firefox */
    -webkit-line-clamp: 1; /* Limit number of lines */
    -moz-box-line-clamp: 1; /* Limit number of lines for Firefox */
    -webkit-box-orient: vertical;
    -moz-box-orient: vertical;
    /* Approximate max-height for two lines */
   
    /*border: 1px solid red;*/
}
.linear_black{  
background: linear-gradient(0deg, rgba(0,0,0,1) 0%, rgba(0,0,0,0.3) 64%, rgba(255,255,255,0) 100%); 
  border-bottom-left-radius: 4px;
  border-bottom-right-radius: 4px;
}
.footer{
  width: max-width;
  position: absolute;
  left: 50%;
  bottom: 0;
  transform: translate(-50%, 0);
}


.movie-footer{
  position: absolute;
  bottom: 0;
  left: 0;
}
.duration{  
  min-width:max-content; 
}
.movie-title, .duration{
  color: white;
  margin: 0;
  opacity: 75%;
  width: max-content;
}
.movie-title{ 
  width: max-content;
}
.movie-tmb{
  width: 75vw;
  height: 200px;
  border-radius: 4px;
  object-fit: cover;

}
.movie-container{
  width: 100vw; 
}

</style>
