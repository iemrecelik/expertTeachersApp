<template>
<div class="row">
  <div class="col-12">

    <div class="alert alert-warning" role="alert">
      Dosyayı silmek istediğinizden emin misiniz?
    </div>

  </div>
</div>
</template>

<script>


import { mapState } from 'vuex';

export default {
  name: 'DeleteLawFilesFormComponent',
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