<template>
  <div>
    <div class="row">
      <div class="col-4">
        <div class="form-group">
          <label for="tc-no">TcNO</label>
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
      <!-- <div class="col-4">
        <form-form-component
          :ppsettings="{
            type: 'text', 
            fieldName: 'thr_career_ladder', 
            value: value('thr_career_ladder')
          }"
        >
        </form-form-component>
      </div> -->
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
      <div class="col-4">
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
import { mapState } from 'vuex';

export default {
  name: 'EditFormComponent',
  data () {
    return {
      institutionList: [],
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
}
</script>