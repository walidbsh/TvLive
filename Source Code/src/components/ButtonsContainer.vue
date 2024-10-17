<template>
	
<div class="d-flex position-relative">
	<div> 
		<ion-icon @click="video_option=!video_option" v-if="video_option" class="p50" :icon="videocamOff"/>
		<ion-icon @click="video_option=!video_option" v-else class="p50" :icon="videocam"/>
	</div>

	<div class="mx-2">
		<ion-icon @click="audio_option=!audio_option" v-if="audio_option" class="p50" :icon="micOffCircle"/>
		<ion-icon @click="audio_option=!audio_option" v-else class="p50" :icon="micCircle"/>
	</div>

	<div class="rounded_button_container">
	    <ion-icon class="p50" :icon="logOut"/>
	</div>
</div>


</template>

<script type="text/javascript">

	
export default{



}
</script>