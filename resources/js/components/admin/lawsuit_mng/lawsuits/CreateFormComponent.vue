<template>
<div>
  <div class="row">
    <div class="col-12">
      <div class="form-group">
        
        <label>Davacıyı Seçiniz: </label><br/>
        <div class="form-check form-check-inline">
          <input class="form-check-input" type="radio" name="law" id="law-teacher" value="1" v-model="selectedLaw" @change="resetTreeselect()">
          <label class="form-check-label" for="law-teacher">
            Öğretmen
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
      <form-form-component
        :ppsettings="{
          type: 'text', 
          fieldName: 'law_brief', 
          value: oldValue('law_brief')
        }"
      >
      </form-form-component>
    </div>
  </div>

  <div class="row">
    <div class="col-4">
      
      <div class="form-group">
        <label for="addMainDocumentList">Evrak Numarası </label>
        <treeselect
          :id="'addMainDocumentList'"
          :multiple="false"
          :async="true"
          :load-options="loadDcNumbers"
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
    
    <div class="col-4">
      
      <div class="form-group">
        <label>Durumu </label>
        <div :class="'item-status'+0"></div>
      </div>
      
    </div>

    <div class="col-4">
      
      <div class="form-group">
        <label>Tarih </label>
        <div :class="'item-date'+0"></div>
      </div>
      
    </div>
  </div>

  <div class="row">
    <div class="col-12">
      <div class="form-group">
        
        <label>Dava Konularını Giriniz: </label>
        <div class="row mb-1" v-for="(item, key) in lawSubjects">
          <div class="col-1 text-right">
            <button type="button" class="btn btn-md btn-primary">
              {{key + 1}}
            </button>
          </div>
          <div class="col-10">
            <textarea class="form-control" 
              rows="1"
              name="sub_description[]" 
              placeholder="Dava konusu..."
              v-model="lawSubjects[key]"
            >
            </textarea>
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

  <div class="row" v-for="(item, key) in dcNumber">
    <div class="col-4">
      
      <div class="form-group">
        <label for="addTeacherList">Evrak Numarası </label>
        <treeselect
          :id="'addTeacherList'"
          :multiple="false"
          :async="true"
          :load-options="loadDcNumbers"
          :instanceId="key+1" 
          v-model="dcNumber[key]"
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
    
    <div class="col-4">
      
      <div class="form-group">
        <label>Durumu </label>
        <div :class="'item-status'+(key+1)"></div>
      </div>
      
    </div>

    <div class="col-3">
      
      <div class="form-group">
        <label>Tarih </label>
        <div :class="'item-date'+(key+1)"></div>
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
      dcNumber: [],
      teacherArr: null,
      unionArr: null,
    }
  },
  computed: {
    ...mapState([
      'routes',
    ]),
  },
  methods: {
    open: function() {
      console.log('open');
    },
    close: function() {
      console.log('close');
    },
    input: function() {
      console.log('input');
    },
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
      this.lawSubjects.push('');
    },
    delSubject: function(index) {
      this.lawSubjects.splice(index, 1);
    },
    addDcNumber: function() {
      // this.dcNumber.push(this.uniqueID());
      this.dcNumber.push(null);
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
            callback(null, [])    
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
    getDocumentInfos(datas, instanceId) {
      console.log('select');
      document.getElementsByClassName('item-date'+instanceId)[0].innerHTML = datas.date;
      document.getElementsByClassName('item-status'+instanceId)[0].innerHTML = datas.itemStatus;
    },
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