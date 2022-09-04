<template>
<div>
  <div class="row">
    <div class="col-12">
      <!-- <form-form-component
        :ppsettings="{
          type: 'text', 
          fieldName: 'dc_com_text', 
          value: value('dc_com_text')
        }"
      >
      </form-form-component> -->
      <div class="form-group">
        <label for="exampleFormControlTextarea1">Notunuz: </label>
        <textarea class="form-control" 
          id="exampleFormControlTextarea1" 
          rows="3"
          name="dc_com_text"
          :value="value('dc_com_text')"
        >
        </textarea>
      </div>
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
  },
  created() {
    // this.getCategory();
  },
  components: {
    Treeselect,
    // 'edit-lang-form-component': editLangFormComponent,
  }
  
}
</script>