<template>
	<div class="alert alert-info" role="alert"
		v-if="isInfoMsg"
		v-html="infoMsg"
	>
	</div>
</template>

<script>
export default {
  name: 'InfoMsgComponent',
  computed: {
  	infoMsg: function(){
  		return 	this.$store.state.infoMsg;
  	},
		isInfoMsg: function(){
			return !_.isEmpty(this.infoMsg);
		},
	},
}
</script>