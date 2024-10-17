<template>	 
	<div class="fixed-top d-flex w-min"> 
		<bubble @update_stream="updateStream" :owner="true" :edit="true" v-if="stream" class="livebubble" :stream='stream'></bubble> 
		<bubble :edit="edit" v-for="s in streams" :key="s.id" class="ms-1 livebubble" :stream='s'></bubble>  
	</div> 

</template>

<script>
import Bubble from './Bubble.vue'  
export default{
	name:"BubbleChatsContainer",
	components:{
		Bubble,
	},
	emits:['updateStream'],
	props:['stream','streams', 'edit'],
	methods:{
		updateStream(){
			console.log('updating from bubble container')
			this.$emit('updateStream')
		}
	}
}

</script>