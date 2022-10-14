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
          :options="teacherArrOpt"
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
          :auto-load-root-options="false"
          :load-options="loadUnions"
          :options="deneme"
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
          value: value('law_brief')
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
          :options="mainDcNumberArrOpt"
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
    <div class="col-4">
      
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
// import editLangFormComponent from './EditLangFormComponent';
import Treeselect from '@riophae/vue-treeselect'
import { ASYNC_SEARCH } from '@riophae/vue-treeselect';

// import the styles
import '@riophae/vue-treeselect/dist/vue-treeselect.css'

import { mapState } from 'vuex';

export default {
  name: 'EditFormComponent',
  data () {
    return {
      deneme:[ {
      id: 'fruits',
      label: 'Fruits',
      children: [ {
        id: 'apple',
        label: 'Apple 🍎',
        isNew: true,
      }, {
        id: 'grapes',
        label: 'Grapes 🍇',
      }, {
        id: 'pear',
        label: 'Pear 🍐',
      }, {
        id: 'strawberry',
        label: 'Strawberry 🍓',
      }, {
        id: 'watermelon',
        label: 'Watermelon 🍉',
      } ],
    }, {
      id: 'vegetables',
      label: 'Vegetables',
      children: [ {
        id: 'corn',
        label: 'Corn 🌽',
      }, {
        id: 'carrot',
        label: 'Carrot 🥕',
      }, {
        id: 'eggplant',
        label: 'Eggplant 🍆',
      }, {
        id: 'tomato',
        label: 'Tomato 🍅',
      } ],
    } ],
      categoryList: [],
      ajaxErrorCount: -1,
      selectedLaw: 0,
      lawSubjects: [''],
      dcNumber: [],
      teacherArr: null,
      unionArr: null,
      mainDcNumberArr: null,
      teacherArrOpt: null,
      unionArrOpt: null,
      mainDcNumberArrOpt: null,
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
    addSubject: function() {
      this.lawSubjects.push('');
    },
    delSubject: function(index) {
      this.lawSubjects.splice(index, 1);
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
      console.log('sadasd');
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
      document.getElementsByClassName('item-date'+instanceId)[0].innerHTML = datas.date;
      document.getElementsByClassName('item-status'+instanceId)[0].innerHTML = datas.itemStatus;
    },
    resetForm() {
      this.lawSubjects = [];
      this.dcNumber = [];
      this.teacherArr = null;
      this.unionArr = null;
      this.mainDcNumberArr = null;
    },
    getTreeselectOpt(field, valName) {
      let val = this.value(field);

      return [
        {
          id: val.id,
          label: val[valName],
        }
      ];
    }
  },
  created() {
    let dcId = this.value('dc_id');
    let thrId = this.value('thr_id');
    let unsId = this.value('uns_id');

    if(thrId) {
      this.selectedLaw = 1;
    }else if(unsId) {
      this.selectedLaw = 2;

      let union = this.value('union');

      /* this.unionArrOpt = [
        {
          id: union.id,
          label: union.uns_name,
        }
      ];
 */
      // this.unionArr = [union.id]
      this.unionArr = ['fruits'];
    }
    // this.selectedLaw = dcId != null? 2 : 1;
  }, 
  components: {
    Treeselect,
  }
}
</script>