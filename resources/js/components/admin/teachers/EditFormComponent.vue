<template>
  <div>
    <div class="row">
      <div class="col-4">
        <div class="form-group">
          <label for="tc-no2">TcNO</label>
          <input type="text" 
            class="form-control" 
            id="tc-no2" 
            placeholder="Example input placeholder"
            name="thr_tc_no" 
            :value="value('thr_tc_no')"
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
            value: value('thr_name')
          }"
        >
        </form-form-component>
      </div>
      <div class="col-4">
        <form-form-component
          :ppsettings="{
            type: 'text', 
            fieldName: 'thr_surname', 
            value: value('thr_surname')
          }"
        >
        </form-form-component>
      </div>
    </div>
  
    <div class="row">

      <div class="col-4">
        <div class="form-group">
          <label for="career-ladder">{{$t('messages.careerLadder')}} :</label>
          <select class="form-control" id="career-ladder"
            name="thr_career_ladder"
          >
            <option selected disabled value="">{{$t('messages.selectCareerLadder')}}.</option>
            <option  :selected="value('thr_career_ladder') == -1" value="-1">Bilinmiyor</option>
            <option  :selected="value('thr_career_ladder') == 0" value="0">Öğretmen</option>
            <option  :selected="value('thr_career_ladder') == 1" value="1">Uzman Öğretmen</option>
            <option  :selected="value('thr_career_ladder') == 2" value="2">Başöğretmen</option>
          </select>
        </div>
      </div>

      <div class="col-4">
        <form-form-component
          :ppsettings="{
            type: 'text', 
            fieldName: 'thr_degree', 
            value: value('thr_degree')
          }"
        >
        </form-form-component>
      </div>
      <div class="col-4">
        <form-form-component
          :ppsettings="{
            type: 'text', 
            fieldName: 'thr_task', 
            value: value('thr_task')
          }"
        >
        </form-form-component>
      </div>
    </div>
  
    <div class="row">
      <div class="col-3">
        <div class="form-group">
          <label for="exampleFormControlSelect1">Cinsiyet :</label>
          <select class="form-control" id="exampleFormControlSelect1"
            name="thr_gender"
          >
            <option selected disabled value="">Cinsiyet Seçiniz.</option>
            <option :selected="value('thr_gender') == 0" value="0">Erkek</option>
            <option :selected="value('thr_gender') == 1" value="1">Bayan</option>
          </select>
        </div>
      </div>

      <div class="col-3">
        <div class="form-group">
          <label for="date-of-birth2">{{$t('messages.dateOfBirth')}}</label>
          <input type="text" 
            class="form-control" 
            id="date-of-birth2" 
            :placeholder="$t('messages.dateOfBirth')"
            name="thr_birth_day" 
            :value="value('thr_birth_day')"
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
            :defaultOptions="provinceArrOpt"
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
            <option :selected="townSelected(town.id)" :value="town.id" :key="key" v-for="(town, key) in townsArr">
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
            value: value('thr_education_st')
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
              :value="value('thr_mobile_no')"
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
            value: value('thr_place_of_task')
          }"
        >
        </form-form-component>
      </div>
    </div>
  
    <div class="row">
      <div class="col-12">
        <div class="form-group">
          <label for="exampleFormControlSelect1">{{$t('messages.thr_institution')}} :</label>
          <select class="form-control" id="exampleFormControlSelect1"
            name="inst_id"
          >
            <option selected disabled value="">Kurum Seçiniz</option>
            <option :key="index" v-for="(item, index) in institutionList" 
              :value="item.id" 
              :selected="item.id === value('inst_id')"
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
  name: 'EditFormComponent',
  data () {
    return {
      institutionList: [],
      ajaxErrorCount: -1,
      townsArr: null,
      provinceArr: null,
      provinceArrOpt: [],
      townArrOpt: []
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

    townSelected: function(id) {
      let selected = false;
      if(this.item.town) {
        selected = this.item.town.id == id;
      }

      return selected;
    }
  },
  created() {
    this.getInstitutions();

    /* il ve ilçeyi ekle başla */
    if(this.item.province) {
      this.provinceArrOpt = [
        {
          id: this.item.province.id,
          label: this.item.province.prv_name
        }
      ];

      this.provinceArr = this.item.province.id;
      
      this.getTownsList({id: this.item.province.id});
    }
    /* il ve ilçeyi ekle bitir */
  },
  mounted() {
    var mobileNoEl = document.getElementById("mobile-no2");
    var tcNo = document.getElementById("tc-no2");
    var dateOfBirth = document.getElementById("date-of-birth2");

    var im = new Inputmask();
    im.mask(mobileNoEl);
    im.mask(dateOfBirth);
    im.mask(tcNo);
  },
  components: {
    Treeselect
  }
}
</script>