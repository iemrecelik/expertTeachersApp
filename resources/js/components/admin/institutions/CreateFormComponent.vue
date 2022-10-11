<template>
<div>
  <div class="row">
    <div class="col-12">
      <form-form-component
        :ppsettings="{
          type: 'text', 
          fieldName: 'inst_name', 
          value: oldValue('inst_name')
        }"
      >
      </form-form-component>
    </div>
  </div>
  
</div>
</template>

<script>
import Treeselect from '@riophae/vue-treeselect'

import { mapState } from 'vuex';

export default {
  name: 'CreateFormComponent',
  data () {
    return {
      categoryList: [],
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
    getCategory: function() {
      $.ajax({
        url: this.routes.getCategory,
        type: 'GET',
        dataType: 'JSON',
      })
      .done((res) => {
        this.categoryList = res;
        this.ajaxErrorCount = -1;
      })
      .fail((error) => {

        setTimeout(() => {
          this.ajaxErrorCount++

          if(this.ajaxErrorCount < 3)
            this.getCategory();
          else
            this.ajaxErrorCount = -1;

        }, 100);
        
      })
      .then((res) => {})
      .always(() => {});
    }
  },
  created() {
    this.getCategory();
  },
  components: {
    Treeselect
    // 'create-lang-form-component': createLangFormComponent,
  }
  
}
</script>