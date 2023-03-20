<template>
<div>
  <div class="row">
    <div class="col-12">
      <form-form-component
        :ppsettings="{
          type: 'text', 
          fieldName: 'name', 
          value: value('name')
        }"
      >
      </form-form-component>
    </div>
  </div>

  <div class="row">
    <div class="col-12">
      <form-form-component
        :ppsettings="{
          type: 'text', 
          fieldName: 'email', 
          value: value('email')
        }"
      >
      </form-form-component>
    </div>
  </div>

  <div class="row">
    <div class="col-3">
      <div class="form-group">
        <label for="career-ladder">
          {{$t('messages.role')}} :
        </label>

        <select class="form-control" id="role"
          name="role_name"
        >
          <option selected value="">{{$t('messages.select_role')}}.</option>
          <!-- <option :selected="item.roles[0].name == 'admin'" value="admin">Admin</option>
          <option :selected="item.roles[0].name == 'auth_admin'" value="auth_admin">Yetkili Admin</option> -->
          <option :selected="hasRole(item, 'staff')" value="staff">Personel</option>
          <option :selected="hasRole(item, 'admin')" value="admin">Admin</option>
          <option :selected="hasRole(item, 'auth_admin')" value="auth_admin">Yetkili Admin</option>
        </select>
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

    hasRole: function(item, role) {
      let bool = false;
      
      if(item.roles[0]) {
        bool = item.roles[0].name == role;
      }else {
        bool = false;
      }
      return bool;
    }
  },
  created() {
  },
  components: {
    Treeselect,
    // 'edit-lang-form-component': editLangFormComponent,
  }
  
}
</script>