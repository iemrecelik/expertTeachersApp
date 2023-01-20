<template>
<div>
  <div class="row">
    <div class="col-12">
      <div class="form-group">
        
        <label>Davacıyı Seçiniz: </label><br/>
        <div class="form-check form-check-inline">
          <input class="form-check-input" type="radio" name="law" id="law-teacher" value="1" v-model="selectedLaw" @change="resetTreeselect()">
          <label class="form-check-label" for="law-teacher">
            Bireysel
          </label>
        </div>
        <div class="form-check form-check-inline">
          <input class="form-check-input" type="radio" name="law" id="law-union" value="2" v-model="selectedLaw" @change="resetTreeselect()">
          <label class="form-check-label" for="law-union">
            Sendika
          </label>
        </div>

      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-12">
      
      <div v-if="selectedLaw == 1" class="form-group">
        <label for="addTeacherList">İlgili Öğretmeni Ekle: </label>
        <treeselect
          :id="'addTeacherList'"
          :multiple="false"
          :async="true"
          :load-options="loadTeachers"
          v-model="teacherArr"
          loadingText="Yükleniyor..."
          clearAllText="Hepsini sil."
          clearValueText="Değeri sil."
          noOptionsText="Hiçbir seçenek yok."
          noResultsText="Mevcut seçenek yok."
          searchPromptText="Aramak için yazınız."
          placeholder="Seçiniz..."
          name="thr_id"
        />
      </div>
      
      <div v-if="selectedLaw == 2" class="form-group">
        <label for="addUnionList">İlgili Sendikayı Ekle: </label>
        <treeselect
          :id="'addUnionList'"
          :multiple="false"
          :async="true"
          :load-options="loadUnions"
          v-model="unionArr"
          loadingText="Yükleniyor..."
          clearAllText="Hepsini sil."
          clearValueText="Değeri sil."
          noOptionsText="Hiçbir seçenek yok."
          noResultsText="Mevcut seçenek yok."
          searchPromptText="Aramak için yazınız."
          placeholder="Seçiniz..."
          name="uns_id"
        />
      </div>
      
    </div>
  </div>

  <div class="row">
    <div class="col-12">
      <div class="form-group">
        <label for="addLawBrief">{{$t('messages.law_brief')}}: </label>
        <treeselect
          :id="'addLawBrief'"
          :multiple="false"
          :async="true"
          :load-options="loadLawBrief"
          v-model="lawBriefArr"
          loadingText="Yükleniyor..."
          clearAllText="Hepsini sil."
          clearValueText="Değeri sil."
          noOptionsText="Hiçbir seçenek yok."
          noResultsText="Mevcut seçenek yok."
          searchPromptText="Aramak için yazınız."
          placeholder="Seçiniz..."
          name="law_brief"
        />
      </div>
      <!-- <form-form-component
        :ppsettings="{
          type: 'text', 
          fieldName: 'law_brief', 
          value: oldValue('law_brief')
        }"
      >
      </form-form-component> -->
    </div>
  </div>

  <div class="row">
    <div class="col-3">
      
      <div class="form-group">
        <label for="addMainDocumentList">Evrak Numarası </label>
        <treeselect
          :id="'addMainDocumentList'"
          :multiple="false"
          :async="true"
          :load-options="loadDcNumbers"
          v-model="mainDcNumberArr"
          :cacheOptions="false"
          :instanceId="0" 
          loadingText="Yükleniyor..."
          clearAllText="Hepsini sil."
          clearValueText="Değeri sil."
          noOptionsText="Hiçbir seçenek yok."
          noResultsText="Mevcut seçenek yok."
          searchPromptText="Aramak için yazınız."
          placeholder="Seçiniz..."
          name="dc_id"
          @select="getDocumentInfos"
        />
      </div>
      
    </div>
    
    <div class="col-3">
      
      <div class="form-group">
        <label>Durumu </label>
        <div :class="'item-status'+0"></div>
      </div>
      
    </div>

    <div class="col-2">
      
      <div class="form-group">
        <label>Esas No </label>
        <div :class="'item-base-number'+0"></div>
      </div>
      
    </div>

    <div class="col-2">
      
      <div class="form-group">
        <label>Tarih </label>
        <div :class="'item-date'+0"></div>
      </div>
      
    </div>

    <div class="col-1">
      
      <div class="form-group">
        <label>İçeriği </label>
        <div :class="'item-info'+0"></div>
      </div>
      
    </div>
  </div>

  <div class="row">
    <div class="col-12">
      <div class="form-group">
        
        <label>Dava Konularını Giriniz: </label>
        <div class="row mb-1" :key="item" v-for="(item, key) in lawSubjectsUnique">
          <div class="col-1 text-right">
            <button type="button" class="btn btn-md btn-primary">
              {{key + 1}}
            </button>
          </div>
          <div class="col-10">
            <textarea class="form-control click2edit" 
              rows="1"
              name="sub_description[]" 
              placeholder="Dava konusu..."
            >
            {{lawSubjects[key]}}
            </textarea>
            <input type="hidden" name="sub_order[]" :value="(key+1)">
          </div>
          <div class="col-1 text-left">
            <span
              @click="delSubject(key)"
            >
              <i class="bi bi-x-circle-fill delete-list-icon"></i>
            </span>
          </div>
        </div>

        <div class="row mt-3">
          <div class="col-1"></div>
          <div class="col-11">
            <button type="button" 
              class="btn btn-sm btn-success"
              @click="addSubject"
            >
              <i class="bi bi-plus-circle"></i> Madde Ekle
            </button>
          </div>
        </div>
        
      </div>
    </div>
  </div>

  <div class="row" :key="item" v-for="(item, key) in dcNumber">
    <div class="col-3">
      
      <div class="form-group">
        <label for="addTeacherList">Evrak Numarası </label>
        <treeselect
          :id="'addTeacherList'"
          :multiple="false"
          :async="true"
          :load-options="loadDcNumbers"
          :instanceId="key+1" 
          loadingText="Yükleniyor..."
          clearAllText="Hepsini sil."
          clearValueText="Değeri sil."
          noOptionsText="Hiçbir seçenek yok."
          noResultsText="Mevcut seçenek yok."
          searchPromptText="Aramak için yazınız."
          placeholder="Seçiniz..."
          name="dc_down_id[]"
          @select="getDocumentInfos"
        />
      </div>
      
    </div>
    
    <div class="col-3">
      
      <div class="form-group">
        <label>Durumu </label>
        <div :class="'item-status'+(key+1)"></div>
      </div>
      
    </div>

    <div class="col-2">
      
      <div class="form-group">
        <label>Esas No </label>
        <div :class="'item-base-number'+(key+1)"></div>
      </div>
      
    </div>

    <div class="col-2">
      
      <div class="form-group">
        <label>Tarih </label>
        <div :class="'item-date'+(key+1)"></div>
      </div>
      
    </div>

    <div class="col-1">
      
      <div class="form-group">
        <label>İçeriği </label>
        <div :class="'item-info'+(key+1)"></div>
      </div>
      
    </div>

    <div class="col-1 text-left">
      <span
        @click="delDcNumber(key)"
      >
        <i class="bi bi-x-circle-fill delete-list-icon"></i>
      </span>
    </div>
  </div>

  <hr/>

  <div class="row">
    <div class="col-12">
      <button type="button" 
        class="btn btn-md btn-primary"
        @click="addDcNumber"
      >
        <i class="bi bi-plus-circle"></i> Evrak Ekle
      </button>
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
import '@riophae/vue-treeselect/dist/vue-treeselect.css';

import { mapState } from 'vuex';

export default {
  name: 'CreateFormComponent',
  data () {
    return {
      categoryList: [],
      ajaxErrorCount: -1,
      selectedLaw: 0,
      lawSubjects: [''],
      lawSubjectsUnique: [],
      dcNumber: [],
      teacherArr: null,
      unionArr: null,
      lawBriefArr: null,
      mainDcNumberArr: null,
    }
  },
  computed: {
    ...mapState([
      'routes',
    ]),
  },
  methods: {
    resetTreeselect: function(){
      if(this.selectedLaw == 1) {
        this.teacherArr = null;
      }else {
        this.unionArr = null;
      }
    },
    oldValue: function(fieldName){
      return this.$store.state.old[fieldName];
    },
    addSubject: function() {
      let addSubjectProm = new Promise( (resolve, reject) => {
        this.lawSubjects.push('');
        this.lawSubjectsUnique.push(this.uniqueID());
        resolve();
      });

      addSubjectProm.then( resolve => {
        $('.click2edit').summernote();
      });
    },
    delSubject: function(index) {
      let delSubjectProm = new Promise( (resolve, reject) => {
        this.lawSubjects.splice(index, 1);
        this.lawSubjectsUnique.splice(index, 1);
        resolve();
      });

      delSubjectProm.then( resolve => {
        $('.click2edit').summernote();
      });
    },
    addDcNumber: function() {
      this.dcNumber.push(this.uniqueID());
    },
    delDcNumber: function(index) {
      this.dcNumber.splice(index, 1);
    },
		loadTeachers({ action, searchQuery, callback }) {
      if (action === ASYNC_SEARCH) {
        simulateAsyncOperation(() => {

          if(searchQuery.length > 2) {
            this.getTeachersSearchList(searchQuery, callback);
          }else {
            callback(null, [])    
          }
        })
      }
    },
    loadUnions({ action, searchQuery, callback }) {
      if (action === ASYNC_SEARCH) {
        simulateAsyncOperation(() => {

          if(searchQuery.length > 2) {
            this.getUnionsSearchList(searchQuery, callback);
          }else {
            callback(null, []); 
          }
        })
      }
    },
    loadDcNumbers({ action, searchQuery, callback, instanceId }) {
      if (action === ASYNC_SEARCH) {
        simulateAsyncOperation(() => {

          if(searchQuery.length > 2) {
            this.getDocumentSearchList(searchQuery, callback, instanceId);
          }else {
            callback(null, [])    
          }
        })
      }
    },
    loadLawBrief({ action, searchQuery, callback }) {
      if (action === ASYNC_SEARCH) {
        simulateAsyncOperation(() => {

          if(searchQuery.length > 2) {
            this.getLawBriefSearchList(searchQuery, callback);
          }else {
            callback(null, [])    
          }
        })
      }
    },
		getTeachersSearchList: function(searchTcNo, callback) {
      $.ajax({
        url: this.routes.getTeachersSearchList,
        type: 'GET',
        dataType: 'JSON',
				data: {'searchTcNo': searchTcNo}
      })
      .done((res) => {
				callback(null, res)
        this.ajaxErrorCount = -1;
      })
      .fail((error) => {
        setTimeout(() => {
          this.ajaxErrorCount++

          if(this.ajaxErrorCount < 3)
            this.getTeachersSearchList(searchTcNo, callback);
          else
            this.ajaxErrorCount = -1;

        }, 100);
        
      })
      .then((res) => {})
		},
    getUnionsSearchList: function(searchName, callback) {
      $.ajax({
        url: this.routes.getUnionsSearchList,
        type: 'GET',
        dataType: 'JSON',
				data: {'searchName': searchName}
      })
      .done((res) => {
				callback(null, res)
        this.ajaxErrorCount = -1;
      })
      .fail((error) => {
        setTimeout(() => {
          this.ajaxErrorCount++

          if(this.ajaxErrorCount < 3)
            this.getUnionsSearchList(searchName, callback);
          else
            this.ajaxErrorCount = -1;

        }, 100);
        
      })
      .then((res) => {})
		},
		getDocumentSearchList: function(dcNumber, callback, instanceId) {
      $.ajax({
        url: this.routes.getDocumentSearchList,
        type: 'GET',
        dataType: 'JSON',
				data: {'dcNumber': dcNumber}
      })
      .done((res) => {
				callback(null, res)
        this.ajaxErrorCount = -1;
      })
      .fail((error) => {
        setTimeout(() => {
          this.ajaxErrorCount++

          if(this.ajaxErrorCount < 3)
            this.getDocumentSearchList(dcNumber, callback, instanceId);
          else
            this.ajaxErrorCount = -1;

        }, 100);
        
      })
      .then((res) => {})
		},
    getLawBriefSearchList: function(searchName, callback) {
      $.ajax({
        url: this.routes.getLawBriefSearchList,
        type: 'GET',
        dataType: 'JSON',
				data: {'searchName': searchName}
      })
      .done((res) => {
        res.push({id: searchName, label: searchName+'(manuel girilmiş)' });

				callback(null, res)
        this.ajaxErrorCount = -1;
      })
      .fail((error) => {
        setTimeout(() => {
          this.ajaxErrorCount++

          if(this.ajaxErrorCount < 3)
            this.getLawBriefSearchList(searchName, callback);
          else
            this.ajaxErrorCount = -1;

        }, 100);
        
      })
      .then((res) => {})
		},
    getDocumentInfos(datas, instanceId) {
      let prom = new Promise((resolve, reject) => {
        document.getElementsByClassName('item-date'+instanceId)[0].innerHTML = datas.date;
        document.getElementsByClassName('item-status'+instanceId)[0].innerHTML = datas.itemStatus;
        document.getElementsByClassName('item-base-number'+instanceId)[0].innerHTML = datas.baseNumber;
        document.getElementsByClassName('item-info'+instanceId)[0].innerHTML = `
          <a tabindex="0" class="btn btn-sm btn-info" 
            id="show-document-${instanceId}"
            role="button" 
            data-toggle="popover" 
            data-trigger="focus" 
            title=""
          >
            <i class="bi bi-file-text"></i>
          </a>
        `;

        resolve();
      });

      prom.then((result) => {
        $(`#show-document-${instanceId}`).popover({
          html: true,
          content: datas.content,
          placement: 'left',
          trigger: 'focus',
          boundary: 'window',
          template: `
            <div class="popover" role="tooltip">
              <div class="arrow"></div>
              <h3 class="popover-header"></h3>
              <div class="popover-body"></div>
            </div>
          `
        });
      }).catch((err) => {
        console.log(err);
      });
    },
    resetForm() {
      this.lawSubjects = [];
      this.lawSubjectsUnique = [];
      this.dcNumber = [];
      this.teacherArr = null;
      this.unionArr = null;
      this.mainDcNumberArr = null;
      this.lawBriefArr = null;
    }
  },
  created() {
    this.lawSubjectsUnique.push(this.uniqueID());
  },
  mounted() {
    $('.click2edit').summernote();
  },
  components: {
    Treeselect
  }
  
}
</script>

<style>
span > i.delete-list-icon {
    font-size: 26px;
}
</style>