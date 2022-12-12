<template>
<div>
  <div class="row">
    <div class="col-12">
      <div class="alert alert-primary" role="alert">
        (<span class="text-danger">*</span>) İşareti olan alanların doldurulması zorunludur.
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-4">
      <div class="form-group">
        <label for="tc-no">{{$t('messages.thr_tc_no')}} <span class="text-danger">*</span></label>
        <input type="text" 
          class="form-control" 
          id="tc-no2" 
          :placeholder="$t('messages.thr_tc_no')"
          name="thr_tc_no" 
          value=""
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
          value: oldValue('thr_name'),
          necessity: true
        }"
      >
      </form-form-component>
    </div>
    <div class="col-4">
      <form-form-component
        :ppsettings="{
          type: 'text', 
          fieldName: 'thr_surname', 
          value: oldValue('thr_surname'),
          necessity: true
        }"
      >
      </form-form-component>
    </div>
  </div>

  <div class="row">
    <div class="col-4">
      <div class="form-group">
        <label for="career-ladder">
          {{$t('messages.careerLadder')}} <span class="text-danger">*</span> :
        </label>

        <select class="form-control" id="career-ladder"
          name="thr_career_ladder"
        >
          <option selected disabled value="">{{$t('messages.selectCareerLadder')}}.</option>
          <option value="-1">Bilinmiyor</option>
          <option value="0">Öğretmen</option>
          <option value="1">Uzman Öğretmen</option>
          <option value="2">Başöğretmen</option>
        </select>
      </div>
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
    <div class="col-3">
      <div class="form-group">
        <label for="exampleFormControlSelect1">
          Cinsiyet <span class="text-danger">*</span> :
        </label>
        <select class="form-control" id="exampleFormControlSelect1"
          name="thr_gender"
        >
          <option selected disabled value="">Cinsiyet Seçiniz.</option>
          <option value="0">Erkek</option>
          <option value="1">Bayan</option>
        </select>
      </div>
    </div>

    <div class="col-3">
      <div class="form-group">
        <label for="date-of-birth2">
          {{$t('messages.dateOfBirth')}} <span class="text-danger">*</span> :
        </label>
        <input type="text" 
          class="form-control" 
          id="date-of-birth2" 
          :placeholder="$t('messages.dateOfBirth')"
          name="thr_birth_day" 
          value=""
          data-inputmask='"mask": "99/99/9999"' 
          data-mask
        >
      </div>
    </div>

    <div class="col-3">
      <div class="form-group">
        <label for="add-province">İller </label>
        <treeselect
          :id="'add-province'"
          :multiple="false"
          :async="true"
          :load-options="loadProvinces"
          v-model="provinceArr"
          loadingText="Yükleniyor..."
          clearAllText="Hepsini sil."
          clearValueText="Değeri sil."
          noOptionsText="Hiçbir seçenek yok."
          noResultsText="Mevcut seçenek yok."
          searchPromptText="Aramak için yazınız."
          placeholder="Seçiniz..."
          name="prv_id"
          @select="getTownsList"
          @input="clearTownsList"
        />
      </div>
    </div>
    <div class="col-3">
      <div class="form-group">
        <label for="add-town">İlçeler </label>
        <select class="form-control" name="twn_id">
          <option value="" selected>Seçiniz</option>
          <option :value="town.id" :key="key" v-for="(town, key) in townsArr">
            {{town.label}}
          </option>
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
            id="mobile-no2" 
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
        <label for="exampleFormControlSelect1">
          {{$t('messages.thr_institution')}} <span class="text-danger">*</span> :
        </label>
        <select class="form-control" id="exampleFormControlSelect1"
          name="inst_id"
          value=""
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
import Treeselect from '@riophae/vue-treeselect'
import { ASYNC_SEARCH } from '@riophae/vue-treeselect';

const simulateAsyncOperation = fn => {
  setTimeout(fn, 2000)
}

// import the styles
import '@riophae/vue-treeselect/dist/vue-treeselect.css'

import { mapState } from 'vuex';

export default {
  name: 'CreateFormComponent',
  data () {
    return {
      institutionList: [],
      ajaxErrorCount: -1,
      townsArr: [],
      provinceArr: null,
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
    },
    loadProvinces({ action, searchQuery, callback }) {
      if (action === ASYNC_SEARCH) {
        simulateAsyncOperation(() => {

          if(searchQuery.length > 2) {
            this.getProvincesList(searchQuery, callback);
          }else {
            callback(null, [])    
          }
        })
      }
    },
    getProvincesList: function(searchWords, callback) {
      $.ajax({
        url: this.routes.getProvincesList,
        type: 'GET',
        dataType: 'JSON',
				data: {'searchWords': searchWords}
      })
      .done((res) => {
				callback(null, res)
        this.ajaxErrorCount = -1;
      })
      .fail((error) => {
        setTimeout(() => {
          this.ajaxErrorCount++

          if(this.ajaxErrorCount < 3)
            this.getProvincesList(searchWords, callback);
          else
            this.ajaxErrorCount = -1;

        }, 100);
      })
      .then((res) => {})
		},

    getTownsList: function(node) {
      $.ajax({
        url: this.routes.getTownsList,
        type: 'GET',
        dataType: 'JSON',
				data: {'prv_id': node.id}
      })
      .done((res) => {
				this.townsArr = res;
        this.ajaxErrorCount = -1;
      })
      .fail((error) => {
        setTimeout(() => {
          this.ajaxErrorCount++

          if(this.ajaxErrorCount < 3)
            this.getTownsList(node);
          else
            this.ajaxErrorCount = -1;

        }, 100);
      })
      .then((res) => {})
		},

    clearTownsList: function() {
      this.townsArr = [];
    },

    resetTreeselect: function() {
      this.provinceArr = null;
      this.townsArr = null;
      this.townsArrOpt = [];
    },
  },
  created() {
    this.getInstitutions();
  },
  mounted() {
    let mobileNoEl = document.getElementById("mobile-no2");
    let tcNo = document.getElementById("tc-no2");
    let dateOfBirth = document.getElementById("date-of-birth2");

    let im = new Inputmask();
    im.mask(mobileNoEl);
    im.mask(tcNo);
    im.mask(dateOfBirth);
  },
  components: {
    Treeselect
  }
  
}
</script>