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
          :value="value('dc_cat_id') == null?0:value('dc_cat_id')"
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
          value: value('dc_cat_name')
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
    getCategory: function() {
      $.ajax({
        url: this.routes.getCategory,
        type: 'GET',
        dataType: 'JSON',
        data: {'id': this.item['id']}
      })
      .done((res) => {
        this.categoryList = res;
        this.categoryList.push({
          id: 0,
          label: 'Üst Kategori Yok'
        });
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
    Treeselect,
    // 'edit-lang-form-component': editLangFormComponent,
  }
  
}
</script>