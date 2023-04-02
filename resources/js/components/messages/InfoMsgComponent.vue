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
	data () {
		return {
			// infoMsg: this.ppinfomsg
		}
	},
	props: {
		ppinfomsg: {
			type: String,
			required: false,
			default: null
		},
	},
  computed: {
  	infoMsg: function(){
  		return 	 this.ppinfomsg ?? this.$store.state.infoMsg;
  	},
		isInfoMsg: function(){
			return !_.isEmpty(this.infoMsg);
		},
	}
}
</script>