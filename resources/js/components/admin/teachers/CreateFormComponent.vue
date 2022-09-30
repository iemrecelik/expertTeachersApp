<template>
<div>
  <div class="row">
    <div class="col-4">
      <div class="form-group">
        <label for="tc-no">TcNO</label>
        <input type="text" 
          class="form-control" 
          id="tc-no" 
          placeholder="Example input placeholder"
          name="thr_tc_no" 
          :value="oldValue('thr_tc_no')"
          data-inputmask='"mask": "99999999999"' 
          data-mask
        >
      </div>
    </div>
    <div class="col-4">
      <form-form-component
        :ppsettings="{
          type: 'text', 
          fieldName: 'thr_name', 
          value: oldValue('thr_name')
        }"
      >
      </form-form-component>
    </div>
    <div class="col-4">
      <form-form-component
        :ppsettings="{
          type: 'text', 
          fieldName: 'thr_surname', 
          value: oldValue('thr_surname')
        }"
      >
      </form-form-component>
    </div>
  </div>

  <div class="row">
    <div class="col-4">
      <form-form-component
        :ppsettings="{
          type: 'text', 
          fieldName: 'thr_career_ladder', 
          value: oldValue('thr_career_ladder')
        }"
      >
      </form-form-component>
    </div>
    <div class="col-4">
      <form-form-component
        :ppsettings="{
          type: 'text', 
          fieldName: 'thr_degree', 
          value: oldValue('thr_degree')
        }"
      >
      </form-form-component>
    </div>
    <div class="col-4">
      <form-form-component
        :ppsettings="{
          type: 'text', 
          fieldName: 'thr_task', 
          value: oldValue('thr_task')
        }"
      >
      </form-form-component>
    </div>
  </div>

  <div class="row">
    <div class="col-4">
      <div class="form-group">
        <label for="exampleFormControlSelect1">Cinsiyet :</label>
        <select class="form-control" id="exampleFormControlSelect1"
          name="thr_gender"
        >
          <option selected disabled value="">Cinsiyet Seçiniz.</option>
          <option value="0">Erkek</option>
          <option value="1">Bayan</option>
        </select>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-12">
      <form-form-component
        :ppsettings="{
          type: 'text', 
          fieldName: 'thr_education_st', 
          value: oldValue('thr_education_st')
        }"
      >
      </form-form-component>
    </div>
  </div>

  <div class="row">
    <div class="col-6">
      <div class="form-group">
        <label>Telefon no</label>

        <div class="input-group">
          <div class="input-group-prepend">
            <span class="input-group-text"><i class="fas fa-phone"></i></span>
          </div>
          <input type="text" 
            id="mobile-no" 
            class="form-control" 
            name="thr_mobile_no"
            data-inputmask='"mask": "0(999) 999 99 99"' 
            data-mask
          >
        </div>
        <!-- /.input group -->
      </div>
    </div>
    <div class="col-6">
      <form-form-component
        :ppsettings="{
          type: 'text', 
          fieldName: 'thr_place_of_task', 
          value: oldValue('thr_place_of_task')
        }"
      >
      </form-form-component>
    </div>
  </div>

  <div class="row">
    <div class="col-12">
      <div class="form-group">
        <label for="exampleFormControlSelect1">thr_institution :</label>
        <select class="form-control" id="exampleFormControlSelect1"
          name="inst_id"
          :value="oldValue('inst_id')"
        >
          <option selected disabled value="">Kurum Seçiniz</option>
          <option v-for="item in institutionList" 
            :value="item.id" 
            :selected="item.id === oldValue('inst_id')"
          >
            {{item.inst_name}}
          </option>
        </select>
      </div>
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
      institutionList: [],
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
    getInstitutions: function() {
      $.ajax({
        url: this.routes.getInstitutions,
        type: 'GET',
        dataType: 'JSON',
      })
      .done((res) => {
        this.institutionList = res;
        this.ajaxErrorCount = -1;
      })
      .fail((error) => {

        setTimeout(() => {
          this.ajaxErrorCount++

          if(this.ajaxErrorCount < 3)
            this.getInstitutions();
          else
            this.ajaxErrorCount = -1;

        }, 100);
        
      })
      .then((res) => {})
      .always(() => {});
    }
  },
  created() {
    this.getInstitutions();
  },
  mounted() {
    var mobileNoEl = document.getElementById("mobile-no");
    var tcNo = document.getElementById("tc-no");

    var im = new Inputmask();
    im.mask(mobileNoEl);
    im.mask(tcNo);
  },
  components: {
    Treeselect
    // 'create-lang-form-component': createLangFormComponent,
  }
  
}
</script>