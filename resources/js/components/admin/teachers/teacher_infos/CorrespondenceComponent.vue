<template>
<div>
  <table class="res-dt-table table table-striped table-bordered" 
    style="width:100%">
    <thead>
      <tr>
        <th>{{ $t("messages.dc_item_status") }}</th>
        <th>{{ $t("messages.dc_number") }}</th>
        <th>{{ $t("messages.dc_subject") }}</th>
        <th>{{ $t("messages.dc_date") }}</th>
        <th>{{ $t("messages.processes") }}</th>
      </tr>
    </thead>
  </table>

  <div class="row mt-3">
    <div class="row">
      <div class="col-12">
        <label for="addDocumentToTeacher">Evrak Ekle </label>
      </div>
    </div>

    <div class="row">
      <div class="col-2">
        <div class="form-group">
          <treeselect
            :id="'addDocumentToTeacher'"
            :multiple="false"
            :async="true"
            :load-options="loadDcNumbers"
            loadingText="Yükleniyor..."
            clearAllText="Hepsini sil."
            clearValueText="Değeri sil."
            noOptionsText="Hiçbir seçenek yok."
            noResultsText="Mevcut seçenek yok."
            searchPromptText="Aramak için yazınız."
            placeholder="Seçiniz..."
            name="dc_id"
          />
        </div>
      </div>

      <div class="col-2">
        <button type="button" 
          id="add-document" 
          class="btn btn-primary"
          @click="addDocumentToTeacher"
        >
          {{ $t('messages.add') }}
        </button>
      </div>
    </div>
    
  </div>

  <!-- Modal -->
  <div class="modal fade" tabindex="-1" role="dialog" 
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

  </div>
</div>
</template>

<script>
import showComponent from './ShowComponent';
import addlistComponent from './AddListComponent';
import addCommentComponent from './AddCommentComponent';
import deleteComponent from './DeleteComponent';

import Treeselect from '@riophae/vue-treeselect'
import { ASYNC_SEARCH } from '@riophae/vue-treeselect';

const simulateAsyncOperation = fn => {
  setTimeout(fn, 2000)
}

// import the styles
import '@riophae/vue-treeselect/dist/vue-treeselect.css'

let formTitleName= 'teacher-dc-search-list';

import { mapState, mapMutations } from 'vuex';

export default {
  name: 'CorrespondenceComponent',
  data() {
    return {
      teacher: this.ppteacher,
      modalIDName: 'formModalLong',
      formTitleName,
      dcContent: '',
      dataTable: null,
    }
  },
  props: {
    ppteacher: {
      type: Object,
      required: true,
    }
  },
  computed: {
    ...mapState([
      'formModalBody',
      'routes',
    ]),
    modalSelector: function(){
      return '#' + this.modalIDName;
    },
  },
  methods: {
    ...mapMutations([
      'setErrors',
      'setSucceed',
    ]),
    processesRow: function(id){
      let row = '';
      row += this.showBtnHtml(id);
      row += this.fileDownloadBtnHtml(id);
      row += this.deleteBtnHtml(id);
      return row;
    },

    loadDcNumbers({ action, searchQuery, callback }) {
      if (action === ASYNC_SEARCH) {
        simulateAsyncOperation(() => {

          if(searchQuery.length > 2) {
            this.getDocumentSearchList(searchQuery, callback);
          }else {
            callback(null, [])    
          }
        })
      }
    },

    getDocumentSearchList: function(dcNumber, callback) {
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
    },

    deleteBtnHtml: function(datas){
      return  `
        <span 
            data-toggle="tooltip" data-placement="top" 
            title="${this.$t('messages.delete')}"
          >
          <button type="button" class="btn btn-sm btn-danger"
            data-toggle="modal" data-target="${this.modalSelector}"
            data-component="${this.formTitleName}-delete-component" 
            data-datas='{
              "dcId": ${datas.id},
              "thrId": ${this.teacher.id},
              "formTitleName": "${this.formTitleName}"
            }'
          >
            <i class="bi bi-trash"></i>
          </button>
        </span>`;
    },

    listBtnHtml: function(datas){
      return  `
        <span 
          data-toggle="tooltip" data-placement="top" 
          title="${this.$t('messages.addList')}"
        >
          <button type="button" class="btn btn-sm btn-primary"
            data-toggle="modal" data-target="${this.modalSelector}"
            data-component="${this.formTitleName}-add-list-component" 
            data-datas='{
              "id": ${datas.id},
              "formTitleName": "${this.formTitleName}"
            }'
          >
            <i class="bi bi-card-list"></i>
          </button>
        </span>`;
    },

    commentBtnHtml: function(datas){
      return  `
        <span 
          data-toggle="tooltip" data-placement="top" 
          title="${this.$t('messages.addcomment')}"
        >
          <button type="button" class="btn btn-sm btn-primary"
            data-toggle="modal" data-target="${this.modalSelector}"
            data-component="${this.formTitleName}-add-comment-component" 
            data-datas='{
              "id": ${datas.id},
              "formTitleName": "${this.formTitleName}"
            }'
          >
            <i class="bi bi-card-text"></i>
          </button>
        </span>`;
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

    addDocumentToTeacher: function() {
      let val = document.getElementsByName('dc_id')[0].value;

      $.ajax({
        url: this.routes.addDocumentToTeacher,
        type: 'POST',
        dataType: 'JSON',
        data: {id: this.teacher.id, dc_id: val},
      })
      .done((res) => {
        this.teacher.dc_documents.push(res);
        /* this.setErrors('');
        this.setSucceed(res.succeed);
        
        document.getElementById(this.formIDName).reset();
        this.$refs.createFormComponent.resetTreeselect(); */
      })
      .fail((error) => {
        /* this.setSucceed('');
        this.setErrors(error.responseJSON.errors); */
      })
      .then((res) => {
        // this.dataTable.reload();
        if(res) {
          this.loadDataTable();
        }
      })
      .always(() => {
        // this.formElement.scrollTo(0, 0);
      });
    },

    loadDataTable() {
      if(this.dataTable) {
        this.dataTable.destroy();
      }

      this.dataTable = this.dataTableManuelRun({
        jQDomName: '.res-dt-table',
        // url: this.routes.dataList,
        data: this.teacher.dc_documents,
        initComplete: () => {
          // this.exportExcelDatas = this.dataTable.rows().data();
        },
        columns: [
          { 
            'data': 'dc_item_status' ,
            'render': (data, type, row) => {
              return data < 1 ? "Gelen" : "Giden";
            }
          },
          { 'data': 'dc_number' },
          { 'data': 'dc_subject' },
          { 
            "data": "dc_date",
            "render": (data, type, row) => {
              return this.unixTimestamp(data);
            }
          },
          {
            "orderable": false,
            "searchable": false,
            "sortable": false,
            "data": "id",
            "render": ( data, type, row ) => {
                return this.processesRow({
                  'id': data,
                  'url': row.dc_files.dc_file_path,
                  'userName': row.user_name,
                  'document': row
                });
            },
            "defaultContent": ""
          },
        ],
      });

      /* this.dataTable = $('.res-dt-table').DataTable({
        data: this.teacher.dc_documents,
        columns: [
          { 
            'data': 'dc_item_status' ,
            'render': (data, type, row) => {
              return data < 1 ? "Gelen" : "Giden";
            }
          },
          { 'data': 'dc_number' },
          { 'data': 'dc_subject' },
          { 
            "data": "dc_date",
            "render": (data, type, row) => {
              return this.unixTimestamp(data);
            }
          },
          {
            "orderable": false,
            "searchable": false,
            "sortable": false,
            "data": "id",
            "render": ( data, type, row ) => {
                return this.processesRow({
                  'id': data,
                  'url': row.dc_files.dc_file_path,
                  'userName': row.user_name,
                  'document': row
                });
            },
            "defaultContent": ""
          },
        ],
      }); */
    }
  },
  mounted() {
    this.loadDataTable();

    this.showModalBody(this.modalSelector);
  },
  components: {
    [formTitleName + '-show-component']: showComponent,
    [formTitleName + '-add-list-component']: addlistComponent,
    [formTitleName + '-add-comment-component']: addCommentComponent,
    [formTitleName + '-delete-component']: deleteComponent,
    Treeselect,
  }
}
</script>

<style>

</style>