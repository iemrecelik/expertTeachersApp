<template>
<div>
  <div class="row">
    <div class="col-12">
      <form-form-component
        :ppsettings="{
          type: 'text', 
          fieldName: 'uns_name', 
          value: oldValue('uns_name')
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
  },
  components: {
    Treeselect
  }
  
}
</script>