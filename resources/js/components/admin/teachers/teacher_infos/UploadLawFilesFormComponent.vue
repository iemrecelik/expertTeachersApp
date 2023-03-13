<template>
<div class="row">
  <div class="col-6">

      <form-form-component
        :ppsettings="{
          type: 'text', 
          fieldName: 'lawf_file_name', 
          value: oldValue('lawf_file_name')
        }"
      >
      </form-form-component>

  </div>
</div>
</template>

<script>


import { mapState } from 'vuex';

export default {
  name: 'UploadLawFilesFormComponent',
  data () {
    return {
      ajaxErrorCount: -1,
    }
  },
  computed: {
    ...mapState([
      'routes',
    ]),
  },
  methods: {
    oldValue: function(fieldName){
      return this.$store.state.old[fieldName];
    },
  },
  created() {
  },
  mounted() {
  },
}
</script>
<style>
.images-loading{
  display: none;
}
</style>