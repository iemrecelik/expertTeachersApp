<template>
<div class="row">
  <div class="col-6">
    <div class="form-group">
      <label for="excel-file">{{$t('messages.thr_tc_no')}}</label>

      <input id="mebbis-tc-no" 
        class="form-control" 
        type="text" 
        name="tc_no" 
        placeholder="Tc Kimlik No"
        data-inputmask='"mask": "99999999999"' 
        data-mask
      >
      <input type="hidden" name="_method" value="PUT">

      <div class="loading-btn">
        <br/>
        <label for="excel-file">{{$t('messages.loading')}}...</label>
        <br/>
        <div class="spinner-border text-secondary ml-3" role="status">
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
  name: 'CreateTeacherWithMebbisFormComponent',
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
    var tcNo = document.getElementById("mebbis-tc-no");
    var im = new Inputmask();

    im.mask(tcNo);
  },
}
</script>

<style>
.loading-btn{
  display: none;
}
</style>