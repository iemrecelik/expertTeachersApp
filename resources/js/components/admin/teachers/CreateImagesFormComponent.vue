<template>
<div class="row">
  <div class="col-6">
    <div class="form-group">
      <label for="excel-file">{{$t('messages.storeImages')}}</label>

      <input type="file" name="images_file" class="form-control-file" id="images-file" multiple/>

      <div class="images-loading">
        <br/>
        <label for="excel-file">{{$t('messages.loading')}}</label>
        <br/>
        <div class="spinner-border text-secondary" role="status">
          <span class="sr-only">{{$t('messages.loading')}}</span>
        </div>
      </div>
      
    </div>
  </div>
</div>
</template>

<script>


import { mapState } from 'vuex';

export default {
  name: 'CreateImagesFormComponent',
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