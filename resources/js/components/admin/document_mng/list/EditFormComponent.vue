<template>
<div>
  <div class="row mb-3">
    <div class="col-12">
      <input type="hidden" name="common_status" :value="commonStatus">
      <div class="icheck-primary d-inline">
        <input type="checkbox" 
          id="checkboxPrimary2" 
          @change="setCommonStatus"
          :checked="commonStatus"
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
          value: value('dc_list_name')
        }"
      >
      </form-form-component>
    </div>
  </div>
</div>
</template>

<script>
// import editLangFormComponent from './EditLangFormComponent';
import Treeselect from '@riophae/vue-treeselect'
// import the styles
import '@riophae/vue-treeselect/dist/vue-treeselect.css'

import { mapState } from 'vuex';

export default {
  name: 'EditFormComponent',
  data () {
    return {
      categoryList: [],
      ajaxErrorCount: -1,
      commonStatus: 0,
    }
  },
  props: {
    ppitem: {
      type: Object,
      required: true,
    }
  },
  computed: {
    item: function(){
      return this.ppitem;
    },
    ...mapState([
      'routes',
    ]),
  },
  methods: {
    value: function(fieldName){
      return this.$store.state.old[fieldName] || this.item[fieldName];
    },
    langFieldName: function(fieldName){
      return `langs[${this.$store.state.lang}][${fieldName}]`;
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
    this.commonStatus = this.value('user_id') == 0 ? 1 : 0;
  },
  components: {
    Treeselect,
  }
  
}
</script>