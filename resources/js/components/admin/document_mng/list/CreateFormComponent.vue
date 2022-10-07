<template>
<div>
  <div class="row mb-3">
    <div class="col-12">
      <input type="hidden" name="common_status" :value="commonStatus">
      <div class="icheck-primary d-inline">
        <input type="checkbox" 
          id="checkboxPrimary2" 
          @change="setCommonStatus"
        >
        <label for="checkboxPrimary2">
          Ortak Liste
        </label>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-12">
      <form-form-component
        :ppsettings="{
          type: 'text', 
          fieldName: 'dc_list_name', 
          value: oldValue('dc_list_name')
        }"
      >
      </form-form-component>
    </div>
  </div>
</div>
</template>

<script>
// import createLangFormComponent from './CreateLangFormComponent';

import Treeselect from '@riophae/vue-treeselect'
// import the styles
import '@riophae/vue-treeselect/dist/vue-treeselect.css'

import { mapState } from 'vuex';

export default {
  name: 'CreateFormComponent',
  data () {
    return {
      categoryList: [],
      ajaxErrorCount: -1,
      commonStatus: 0,
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
    setCommonStatus: function(event) {
      if(event.target.checked) {
        this.commonStatus = 1
      }else {
        this.commonStatus = 0
      }
    },
  },
  created() {
  },
  components: {
    Treeselect
  }
  
}
</script>