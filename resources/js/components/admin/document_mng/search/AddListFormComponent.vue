<template>
<div>
  <div class="row">
    <div class="col-12">

      <div class="row">
        <div class="col-12">
          <ul>
            <li v-for="item in list.selected">
              <span>{{ item.dc_list_name }}</span>
              <span v-if="item.user_id === list.userId"
                class="float-right"
                @click="deleteList(item.id)"
              >
                <i class="bi bi-x-circle-fill delete-list-icon"></i>
              </span>
              <hr/>
            </li>
          </ul>
        </div>
      </div>

      <div class="row">
        <div class="col-12">
          <div class="form-group">
            <label for="exampleFormControlSelect1">Listeler</label>
            <select class="form-control" 
              id="exampleFormControlSelect1"
              name="id"
            >
              <option v-for="item in list.list" :value="item.id">
                {{item.dc_list_name}}
              </option>
            </select>
          </div>
        </div>
      </div>

    </div>
  </div>

  <input type="hidden" name="dc_id" :value="datas.id"/>
</div>
</template>

<script>
// import createLangFormComponent from './CreateLangFormComponent';
import Treeselect from '@riophae/vue-treeselect'
// import the styles
import '@riophae/vue-treeselect/dist/vue-treeselect.css'
import { mapState } from 'vuex';
export default {
  name: 'AddListFormComponent',
  data () {
    return {
      list: [],
      ajaxErrorCount: -1,
      datas: this.ppdatas
    }
  },
  props: {
    ppdatas: {
      type: Object,
      required: true,
    },
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
    getListAndSelectedList: function() {
      $.ajax({
        url: this.routes.getListAndSelected,
        type: 'GET',
        dataType: 'JSON',
        data: {'dc_id': this.datas.id}
      })
      .done((res) => {
        this.list = res;
        this.ajaxErrorCount = -1;
      })
      .fail((error) => {
        setTimeout(() => {
          this.ajaxErrorCount++
          if(this.ajaxErrorCount < 3)
            this.getListAndSelectedList();
          else
            this.ajaxErrorCount = -1;
        }, 100);
        
      })
      .then((res) => {})
      .always(() => {});
    },
    deleteList: function(id){
      $.ajax({
        url: this.routes.deleteList,
        type: 'POST',
        dataType: 'JSON',
        data: {
          'id': id,
          'dc_id': this.datas.id,
        }
      })
      .done((res) => {
        this.getListAndSelectedList();
      })
      .fail((error) => {
        setTimeout(() => {
          this.ajaxErrorCount++
          if(this.ajaxErrorCount < 3)
            this.deleteList();
          else
            this.ajaxErrorCount = -1;
        }, 100);
        
      })
      .then((res) => {})
      .always(() => {});
    }
  },
  created() {
    this.getListAndSelectedList();
  },
  components: {
    Treeselect
    // 'create-lang-form-component': createLangFormComponent,
  }
  
}
</script>