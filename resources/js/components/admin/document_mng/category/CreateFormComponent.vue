<template>
<div>
	<!-- <create-lang-form-component></create-lang-form-component> -->
  <div class="row">
    <div class="col-12">
      <div class="form-group">
        <label for="exampleInputEmail1">
          {{ $t('messages.categoryName') }}
        </label>
        <treeselect
          name="dc_cat_id"
          :options="categoryList"
          :value=0
          :disable-branch-nodes="false"
          :show-count="true"
          :placeholder="$t('messages.enterCategoryName')"
        />
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-12">
      <form-form-component
        :ppsettings="{
          type: 'text', 
          fieldName: 'dc_cat_name', 
          value: oldValue('dc_cat_name')
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