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
          :defaultOptions="teacherArrOpt"
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
          :defaultOptions="unionArrOpt"
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
          :defaultOptions="mainDcNumberArrOpt"
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

    <div class="col-3">
      
      <div class="form-group">
        <label>Tarih </label>
        <div :class="'item-date'+0"></div>
      </div>
      
    </div>

    <div class="col-2">
      
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
        <div class="row mb-1" v-for="(item, key) in lawSubjects">
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
              v-model="lawSubjects[key]"
            >
            </textarea>
            <input type="hidden" name="sub_order[]" :value="(key+1)">
          </div>
          <div class="col-1 text-left">
            <span
              @click="editMode(key)"
            >
              <i class="bi bi-pen"></i>
            </span>
            
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
          :defaultOptions="relDcNumberArrOpt[key]"
          v-model="relDcNumberArr[key]"
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
        <label>Tarih </label>
        <div :class="'item-date'+(key+1)"></div>
      </div>
      
    </div>

    <div class="col-2">
      
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

  <!-- Modal -->
  <!-- <div class="modal fade" tabindex="-1" role="dialog" 
    aria-labelledby="formModalLongTitle" aria-hidden="true"
    data-backdrop="static" :id="modalIDName"
  >
    
    <div class="modal-dialog modal-xl" role="document">
        <component
          v-if="formModalBody.show"
          :is="formModalBody.component"
          :ppdatas="formModalBody.datas"
          :ppDcContent="dcContent"
        >
        </component>
    </div>

  </div> -->
  
</div>
</template>

<script>
// import showComponent from './ShowComponent';
import Treeselect from '@riophae/vue-treeselect'
import { ASYNC_SEARCH } from '@riophae/vue-treeselect';

const simulateAsyncOperation = fn => {
  setTimeout(fn, 2000)
}

// import the styles
import '@riophae/vue-treeselect/dist/vue-treeselect.css'

import { mapState } from 'vuex';

// let formTitleName= 'lawsuit-dc-show';

export default {
  name: 'EditFormComponent',
  data () {
    return {
      /* modalIDName: 'formModalLong',
      dcContent: '', */
      categoryList: [],
      ajaxErrorCount: -1,
      selectedLaw: 0,
      lawSubjects: [],
      dcNumber: [],
      teacherArr: null,
      unionArr: null,
      mainDcNumberArr: null,
      relDcNumberArr: [],
      teacherArrOpt: [],
      unionArrOpt: [],
      mainDcNumberArrOpt: [],
      relDcNumberArrOpt: [],
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
      // 'formModalBody',
    ]),
    /* modalSelector: function(){
      return '#' + this.modalIDName;
    }, */
  },
  methods: {
    editMode() {
      $('.click2edit').summernote();
    },
    value: function(fieldName){
      return this.$store.state.old[fieldName] || this.item[fieldName];
    },
    resetTreeselect: function(){
      if(this.selectedLaw == 1) {
        this.teacherArr = null;
      }else {
        this.unionArr = null;
      }
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
      this.relDcNumberArrOpt.splice(index, 1);
      this.relDcNumberArr.splice(index, 1);
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
      let prom = new Promise((resolve, reject) => {
        document.getElementsByClassName('item-date'+instanceId)[0].innerHTML = datas.date;
        document.getElementsByClassName('item-status'+instanceId)[0].innerHTML = datas.itemStatus;
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
    },
    setTreeselectOpt(fieldName, labelName, valName = null) {
      let optName = valName? `${valName}ArrOpt` : `${fieldName}ArrOpt`;
      let defValName = valName? `${valName}Arr` : `${fieldName}Arr`;
      
      let item = this.value(fieldName);

      switch (fieldName) {
        case 'teacher':
          this[optName] = [
            {
              id: item.id,
              label: "(T.C. No: "+item.thr_tc_no+")"+item.thr_name+" "+item.thr_surname,
            }
          ];   
          break;
      
        default:
          this[optName] = [
            {
              id: item.id,
              label: item[labelName],
            }
          ];
          break;
      }

      this[defValName] = item.id;
    },
    /* processesRow: function(id){
      let row = '';
      row += this.showBtnHtml(id);
      row += this.fileDownloadBtnHtml(id);
      return row;
    },

    fileDownloadBtnHtml: function(datas){
      return  `
        <span 
          data-toggle="tooltip" data-placement="top" 
          title="${this.$t('messages.docFileDownload')}"
        >
          <a type="button" class="btn btn-sm btn-success"
            data-file-download
            href="/storage/upload/images/raw${datas.url}"
            download
            data-datas='{
              "id": ${datas.id},
              "formTitleName": "${this.formTitleName}"
            }'
          >
            <i class="bi bi-file-earmark-arrow-down"></i>
          </a>
        </span>`;
    },

    showBtnHtml: function(datas){
      return  `
        <span 
          data-toggle="tooltip" data-placement="top" 
          title="${this.$t('messages.showDocument')}"
        >
          <button type="button" class="btn btn-sm btn-info"
            data-toggle="modal" data-target="${this.modalSelector}"
            data-component="${this.formTitleName}-show-component" 
            data-datas='{
              "id": ${datas.id},
              "formTitleName": "${this.formTitleName}",
              "userName": "${datas.userName}"
            }'
          >
            <i class="bi bi-file-text"></i>
          </button>
        </span>`;
    }, */

  },
  created() {
    let thrId = this.value('thr_id');
    let unsId = this.value('uns_id');
    let subjectsSort = this.value('subjectsSort');
    let dcDocuments = this.value('dc_documents');

    dcDocuments.forEach((item, key) => {
      this.dcNumber.push(this.uniqueID());
      this.relDcNumberArrOpt[key] = [
        {
          id: item.id,
          label: item.dc_number,
        }
      ];
      this.relDcNumberArr[key] = item.id;
    });

    this.setTreeselectOpt('dc_document', 'dc_number', 'mainDcNumber');
    
    if(thrId) {
      this.selectedLaw = 1;
      this.setTreeselectOpt('teacher', 'thr_name');
    }else if(unsId) {
      this.selectedLaw = 2;
      this.setTreeselectOpt('union', 'uns_name');
    }

    subjectsSort = subjectsSort.map(item => {
      return item.sub_description;
    });

    this.lawSubjects.push(...subjectsSort);
  },
  mounted() {
    this.getDocumentInfos(this.value('mainDcDocument'), 0);

    let relDcDocuments = this.value('relDcDocuments');

    relDcDocuments.forEach((item, key) => {
      this.getDocumentInfos(item, (key+1));
    });

    // this.showModalBody(this.modalSelector);
  },
  components: {
    Treeselect,
    // [formTitleName + '-show-component']: showComponent,
  }
}
</script>

<style>
.popover {
  position: absolute;
  top: 0;
  left: 0;
  z-index: 1060;
  display: block;
  max-width: 1000px;
  font-family: "Source Sans Pro", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol";
  font-style: normal;
  font-weight: 400;
  line-height: 1.5;
  text-align: left;
  text-align: start;
  text-decoration: none;
  text-shadow: none;
  text-transform: none;
  letter-spacing: normal;
  word-break: normal;
  word-spacing: normal;
  white-space: normal;
  line-break: auto;
  font-size: 0.875rem;
  word-wrap: break-word;
  background-color: #fff;
  background-clip: padding-box;
  border: 1px solid rgba(0, 0, 0, 0.2);
  border-radius: 0.3rem;
  box-shadow: 0 0.25rem 0.5rem rgb(0 0 0 / 20%);
}
.popover-body {
  padding: 0.5rem 0.75rem;
  color: #212529;
  overflow: auto;
  max-height: 600px !important;
}
</style>