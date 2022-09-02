<template>
<div 
  v-if="Object.keys(errors).length !== 0"
  class="alert alert-danger alert-dismissible"
>
  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
  <h5><i class="icon fas fa-ban"></i> Hata!</h5>
  <!-- <ul v-if="typeof errors !== 'undefined' && errors.length > 0"> -->
  <ul>
    <li v-for="error in errors">{{ error[0] }}</li>
  </ul>
</div>
</template>

<script>
export default {
  name: "ErrorMsgComponent",
  data() {
    return {
    }
  },
  props: {
    pperrors: {
      type: Object,
      required: false,
    }
  },
  computed: {
    errors () {
      return this.pperrors;
    }
  }
}
</script>

<style>

</style>