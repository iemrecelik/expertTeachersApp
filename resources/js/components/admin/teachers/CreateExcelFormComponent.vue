<template>
<div>
  <div class="row">
    <div class="col-4">
      <!-- <div class="form-group">
        <label for="tc-no">{{$t('messages.thr_tc_no')}}</label>
        <input type="text" 
          class="form-control" 
          id="tc-no" 
          :placeholder="$t('messages.thr_tc_no')"
          name="thr_tc_no" 
          :value="oldValue('thr_tc_no')"
          data-inputmask='"mask": "99999999999"' 
          data-mask
        >
      </div> -->

      <div class="form-group">
        <label for="tc-no">{{$t('messages.thr_tc_no')}}</label>
        <treeselect
          :id="'tc-no'"
          :multiple="false"
          :options="options"
          loadingText="Yükleniyor..."
          clearAllText="Hepsini sil."
          clearValueText="Değeri sil."
          noOptionsText="Hiçbir seçenek yok."
          noResultsText="Mevcut seçenek yok."
          searchPromptText="Aramak için yazınız."
          placeholder="Seçiniz..."
          name="thr_tc_no"
        />
      </div>
    </div>
    <div class="col-4">
      <div class="form-group">
        <label for="thr-name">{{$t('messages.thr_name')}}</label>
        <treeselect
          :id="'thr-name'"
          :multiple="false"
          :options="options"
          loadingText="Yükleniyor..."
          clearAllText="Hepsini sil."
          clearValueText="Değeri sil."
          noOptionsText="Hiçbir seçenek yok."
          noResultsText="Mevcut seçenek yok."
          searchPromptText="Aramak için yazınız."
          placeholder="Seçiniz..."
          name="thr_name"
        />
      </div>
      <!-- <form-form-component
        :ppsettings="{
          type: 'text', 
          fieldName: 'thr_name', 
          value: oldValue('thr_name')
        }"
      >
      </form-form-component> -->
    </div>
    <div class="col-4">
      <div class="form-group">
        <label for="thr-surname">{{$t('messages.thr_surname')}}</label>
        <treeselect
          :id="'thr-surname'"
          :multiple="false"
          :options="options"
          loadingText="Yükleniyor..."
          clearAllText="Hepsini sil."
          clearValueText="Değeri sil."
          noOptionsText="Hiçbir seçenek yok."
          noResultsText="Mevcut seçenek yok."
          searchPromptText="Aramak için yazınız."
          placeholder="Seçiniz..."
          name="thr_surname"
        />
      </div>
      <!-- <form-form-component
        :ppsettings="{
          type: 'text', 
          fieldName: 'thr_surname', 
          value: oldValue('thr_surname')
        }"
      >
      </form-form-component> -->
    </div>
  </div>

  <div class="row">
    <!-- <div class="col-4">
      <form-form-component
        :ppsettings="{
          type: 'text', 
          fieldName: 'thr_career_ladder', 
          value: oldValue('thr_career_ladder')
        }"
      >
      </form-form-component>
    </div> -->

    <div class="col-4">
      <div class="form-group">
        <label for="thr-gender">{{$t('messages.dateOfBirth')}} (**/**/****, **.**.****)</label>
        <treeselect
          :id="'thr-gender'"
          :multiple="false"
          :options="options"
          loadingText="Yükleniyor..."
          clearAllText="Hepsini sil."
          clearValueText="Değeri sil."
          noOptionsText="Hiçbir seçenek yok."
          noResultsText="Mevcut seçenek yok."
          searchPromptText="Aramak için yazınız."
          placeholder="Seçiniz..."
          name="thr_birth_day"
        />
      </div>
    </div>

    <div class="col-4">
      <div class="form-group">
        <label for="thr-degree">{{$t('messages.thr_degree')}}</label>
        <treeselect
          :id="'thr-degree'"
          :multiple="false"
          :options="options"
          loadingText="Yükleniyor..."
          clearAllText="Hepsini sil."
          clearValueText="Değeri sil."
          noOptionsText="Hiçbir seçenek yok."
          noResultsText="Mevcut seçenek yok."
          searchPromptText="Aramak için yazınız."
          placeholder="Seçiniz..."
          name="thr_degree"
        />
      </div>
      <!-- <form-form-component
        :ppsettings="{
          type: 'text', 
          fieldName: 'thr_degree', 
          value: oldValue('thr_degree')
        }"
      >
      </form-form-component> -->
    </div>
    <div class="col-4">
      <div class="form-group">
        <label for="thr-task">{{$t('messages.thr_task')}}</label>
        <treeselect
          :id="'thr-task'"
          :multiple="false"
          :options="options"
          loadingText="Yükleniyor..."
          clearAllText="Hepsini sil."
          clearValueText="Değeri sil."
          noOptionsText="Hiçbir seçenek yok."
          noResultsText="Mevcut seçenek yok."
          searchPromptText="Aramak için yazınız."
          placeholder="Seçiniz..."
          name="thr_task"
        />
      </div>

      <!-- <form-form-component
        :ppsettings="{
          type: 'text', 
          fieldName: 'thr_task', 
          value: oldValue('thr_task')
        }"
      >
      </form-form-component> -->
    </div>
  </div>

  <div class="row">
    <div class="col-4">
      <div class="form-group">
        <label for="thr-gender">{{$t('messages.thr_gender')}} (erkek, bayan)</label>
        <treeselect
          :id="'thr-gender'"
          :multiple="false"
          :options="options"
          loadingText="Yükleniyor..."
          clearAllText="Hepsini sil."
          clearValueText="Değeri sil."
          noOptionsText="Hiçbir seçenek yok."
          noResultsText="Mevcut seçenek yok."
          searchPromptText="Aramak için yazınız."
          placeholder="Seçiniz..."
          name="thr_gender"
        />
      </div>
    </div>
    
    <div class="col-2">
      <div class="form-group">
        <label>Erkek</label>
        <input type="text" class="form-control" name="enter[gender][0]" placeholder="erkek" value="erkek">
      </div>
    </div>

    <div class="col-2">
      <div class="form-group">
        <label>Bayan</label>
        <input type="text" class="form-control" name="enter[gender][1]" placeholder="bayan" value="bayan">
      </div>
    </div>

  </div>

  <div class="row">
    <div class="col-4">
      <div class="form-group">
        <label for="career-ladder">{{$t('messages.careerLadder')}}(öğrt, uzman öğrt, başöğrt)</label>
        <treeselect
          :id="'career-ladder'"
          :multiple="false"
          :options="options"
          loadingText="Yükleniyor..."
          clearAllText="Hepsini sil."
          clearValueText="Değeri sil."
          noOptionsText="Hiçbir seçenek yok."
          noResultsText="Mevcut seçenek yok."
          searchPromptText="Aramak için yazınız."
          placeholder="Seçiniz..."
          name="thr_career_ladder"
        />
      </div>
    </div>

    <input type="hidden" name="enter[career][0]">

    <div class="col-2">
      <div class="form-group">
        <label>Öğretmen</label>
        <input type="text" class="form-control" 
          name="enter[career][1]" 
          placeholder="öğretmen" 
          value="öğretmen"
        >
      </div>
    </div>

    <div class="col-2">
      <div class="form-group">
        <label>Uzman Öğretmen</label>
        <input type="text" class="form-control" 
          name="enter[career][2]" 
          placeholder="uzman öğretmen" 
          value="uzman öğretmen"
        >
      </div>
    </div>

    <div class="col-2">
      <div class="form-group">
        <label>Başöğretmen</label>
        <input type="text" class="form-control" 
          name="enter[career][3]" 
          placeholder="başöğretmen" 
          value="başöğretmen"
        >
      </div>
    </div>

  </div>

  <div class="row">
    <div class="col-12">
      <div class="form-group">
        <label for="thr-education-st">{{$t('messages.thr_education_st')}}</label>
        <treeselect
          :id="'thr-education-st'"
          :multiple="false"
          :options="options"
          loadingText="Yükleniyor..."
          clearAllText="Hepsini sil."
          clearValueText="Değeri sil."
          noOptionsText="Hiçbir seçenek yok."
          noResultsText="Mevcut seçenek yok."
          searchPromptText="Aramak için yazınız."
          placeholder="Seçiniz..."
          name="thr_education_st"
        />
      </div>

      <!-- <form-form-component
        :ppsettings="{
          type: 'text', 
          fieldName: 'thr_education_st', 
          value: oldValue('thr_education_st')
        }"
      >
      </form-form-component> -->
    </div>
  </div>

  <div class="row">
    <div class="col-6">
      <div class="form-group">
        <label for="thr-mobile-no">{{$t('messages.thr_mobile_no')}}</label>
        <treeselect
          :id="'thr-mobile-no'"
          :multiple="false"
          :options="options"
          loadingText="Yükleniyor..."
          clearAllText="Hepsini sil."
          clearValueText="Değeri sil."
          noOptionsText="Hiçbir seçenek yok."
          noResultsText="Mevcut seçenek yok."
          searchPromptText="Aramak için yazınız."
          placeholder="Seçiniz..."
          name="thr_mobile_no"
        />
      </div>
      
      <!-- <div class="form-group">
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
      </div> -->
    </div>
    <div class="col-6">
      <div class="form-group">
        <label for="thr-place-of-task">{{$t('messages.thr_place_of_task')}}</label>
        <treeselect
          :id="'thr-place-of-task'"
          :multiple="false"
          :options="options"
          loadingText="Yükleniyor..."
          clearAllText="Hepsini sil."
          clearValueText="Değeri sil."
          noOptionsText="Hiçbir seçenek yok."
          noResultsText="Mevcut seçenek yok."
          searchPromptText="Aramak için yazınız."
          placeholder="Seçiniz..."
          name="thr_place_of_task"
        />
      </div>

      <!-- <form-form-component
        :ppsettings="{
          type: 'text', 
          fieldName: 'thr_place_of_task', 
          value: oldValue('thr_place_of_task')
        }"
      >
      </form-form-component> -->
    </div>
  </div>

  <div class="row">
    <div class="col-12">
      <div class="form-group">
        <label for="inst-id">{{$t('messages.thr_institution')}}</label>
        <treeselect
          :id="'inst-id'"
          :multiple="false"
          :options="options"
          loadingText="Yükleniyor..."
          clearAllText="Hepsini sil."
          clearValueText="Değeri sil."
          noOptionsText="Hiçbir seçenek yok."
          noResultsText="Mevcut seçenek yok."
          searchPromptText="Aramak için yazınız."
          placeholder="Seçiniz..."
          name="inst_id"
        />
      </div>

      <!-- <div class="form-group">
        <label for="exampleFormControlSelect1">{{$t('messages.thr_institution')}} :</label>
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
      </div> -->
    </div>
  </div>

  <div class="row">
    <div class="col-6">
      <div class="form-group">
        <label for="excel-file">{{$t('messages.addExcel')}}</label>
        
        <div class="custom-control custom-switch mb-3">
          <input type="checkbox" class="custom-control-input" id="update-db" name="updateDb" checked>
          <label class="custom-control-label" for="update-db">Var olan verileri güncelle</label>
        </div>

        <input type="hidden" name="previewUniqueId" v-model="previewUniqueId">

        <input type="file" name="excel_file" class="form-control-file" id="excel-file" @change="createPreviewUniqueId">
      </div>
    </div>
  </div>
  
</div>
</template>

<script>
import Treeselect from '@riophae/vue-treeselect'
// import the styles
import '@riophae/vue-treeselect/dist/vue-treeselect.css'

import { mapState } from 'vuex';

export default {
  name: 'CreateExcelFormComponent',
  data () {
    return {
      institutionList: [],
      ajaxErrorCount: -1,
      options: [],
      letters: ['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z'],
      previewUniqueId: null,
    }
  },
  computed: {
    ...mapState([
      'routes',
    ]),
  },
  methods: {
    createPreviewUniqueId: function() {
      this.previewUniqueId = this.uniqueID()+'-'+Math.floor(Math.random() * 10000);
    },
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
    createLetters: function() {
      let arr = [];

      for (let i = 1; i < 10; i++) {
        arr = this.letters.map((letter, key) => {
          return {
            id: key+'_'+i,
            label: letter+i,
          }
        })
        this.options = this.options.concat(arr);
      }
    }
  },
  created() {
    this.createLetters();
    this.getInstitutions();
  },
  mounted() {
    this.createPreviewUniqueId();
  },
  components: {
    Treeselect
  }
  
}
</script>