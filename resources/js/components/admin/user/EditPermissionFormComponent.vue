<template>
<div>
  <div class="form-group" v-for="(permVal, permKey) in permissionList">
    <label for="addTeacherList"> {{$t('messages.'+permKey)}}: </label>
    <treeselect
      :id="'addTeacherList'"
      :multiple="true"
      :async="false"
      :options="permVal"
      :value="hasPermissions(permVal)"
      loadingText="Yükleniyor..."
      clearAllText="Hepsini sil."
      clearValueText="Değeri sil."
      noOptionsText="Hiçbir seçenek yok."
      noResultsText="Mevcut seçenek yok."
      searchPromptText="Aramak için yazınız."
      placeholder="Seçiniz..."
      name="perm_id[]"
    />
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
  name: 'EditPermissionFormComponent',
  data () {
    return {
      permissionList: [],
      ajaxErrorCount: -1,
    }
  },
  props: {
    ppitem: {
      type: Array,
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
    hasPermissions: function(perms) {
      let hasPerms = [];

      for (let i = 0; i < perms.length; i++) {
        const perm = perms[i];

        this.item.forEach(item => {
          if(item.name === perm.id) {
            hasPerms.push(perm.id);
          }
        });

        if(i === (perms.length - 1)) {
          return hasPerms;
        }
      }
    },
    getPermission: function() {
      $.ajax({
        url: this.routes.getPermission,
        type: 'GET',
        dataType: 'JSON',
        data: {'id': this.item['id']}
      })
      .done((res) => {
        this.permissionList = res;
        this.ajaxErrorCount = -1;
      })
      .fail((error) => {

        setTimeout(() => {
          this.ajaxErrorCount++

          if(this.ajaxErrorCount < 3)
            this.getPermission();
          else
            this.ajaxErrorCount = -1;

        }, 100);
        
      })
      .then((res) => {})
      .always(() => {});
    },
    value: function(fieldName){
      return this.$store.state.old[fieldName] || this.item[fieldName];
    },
    langFieldName: function(fieldName){
      return `langs[${this.$store.state.lang}][${fieldName}]`;
    },
  },
  created() {
    this.getPermission();
  },
  components: {
    Treeselect,
    // 'edit-lang-form-component': editLangFormComponent,
  }
}
</script>