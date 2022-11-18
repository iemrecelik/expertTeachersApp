<template>
<div class="row">
  <div class="col-6">
    <div class="form-group">
      <label for="excel-file">{{$t('messages.storeImages')}}</label>

      <input type="file" name="images_file" class="form-control-file" id="images-file" multiple>
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