<template>
<div class="list-group">
  <div class="row">
    <div class="col-12">
      <error-msg-list-component></error-msg-list-component>
    </div>
  </div>
  <!-- /.row -->
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">Evrak Listesi</h3>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
          
          <table class="res-dt-table table table-striped table-bordered" 
            style="width:100%">
            <thead>
              <tr>
                <th>{{$t('messages.dc_main_status')}}</th>
                <th>{{$t('messages.dc_cat_name')}}</th>
                <th>{{$t('messages.dc_number')}}</th>
                <th>{{$t('messages.dc_item_status')}}</th>
                <th>{{$t('messages.dc_subject')}}</th>
                <th>{{$t('messages.dc_date')}}</th>
                <th>{{$t('messages.processes')}}</th>
              </tr>
            </thead>
            <tfoot>
              <tr>
                <th colspan="7">
                  <!-- <button type="button" class="btn btn-primary" >
                    asdasdasd
                  </button> -->
                  <!-- <button type="button" class="btn btn-primary"
                    data-toggle="modal" 
                    :data-target="modalSelector"
                    :data-datas='`{"formTitleName": "\${formTitleName}"}`'
                    :data-component="`${formTitleName}-create-component`"
                  >
                    {{ $t('messages.add') }}
                  </button> -->
                </th>
              </tr>
            </tfoot>
          </table>

        </div>
        <!-- /.card-body -->
      </div>
      <!-- /.card -->
    </div>
  </div>
  <!-- /.row -->

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
/* import createComponent from './CreateComponent';
import editComponent from './EditComponent'; */
import showComponent from './ShowComponent';
import addlistComponent from './AddListComponent';
import addCommentComponent from './AddCommentComponent';
import deleteComponent from './DeleteComponent';
// import imagesComponent from './ImagesComponent';

let formTitleName= 'dc-search-list';

import { mapState, mapMutations } from 'vuex';

export default {
  name: "SearchListComponent",
  data() {
    return {
      form: this.ppformId,
      modalIDName: 'formModalLong',
      formTitleName,
      dataTable: null,
      dcContent: '',
    }
  },
  props: {
    /* ppdatas: {
      type: Object,
      required: true,
    }, */
    ppformId: {
      type: String,
      required: true,
    },
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
      row += this.listBtnHtml(id);
      row += this.commentBtnHtml(id);
      row += this.fileDownloadBtnHtml(id);
      row += this.editBtnHtml(id);
      row += this.deleteBtnHtml(id);
      return row;
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
              "id": ${datas.id},
              "formTitleName": "${this.formTitleName}"
            }'
          >
            <i class="bi bi-trash"></i>
          </button>
        </span>`;
    },
    
    editBtnHtml: function(datas){
      return  `
      <span 
        data-toggle="tooltip" data-placement="top" 
        title="${this.$t('messages.edit')}"
      >
        <a tabindex="0" class="btn btn-sm btn-warning" 
          role="button" 
          href="/admin/document-management/document/edit/${datas.id}"
          target="_blank"
        >
          <i class="bi bi-pencil"></i>
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

    destroyTable() {
      if (typeof this.dataTable !== 'undefined') {
        this.dataTable.destroy();
        $("#"+this.form+" tbody").empty();
      }
    },
    loadDataTable() {
      this.dcContent = document.getElementsByName('dc_content')[0].value;

      let form = $("#"+this.form);

      this.dataTable = this.dataTableRun({
        jQDomName: '.res-dt-table',
        url: this.routes.getSearchDocuments,
        data: {
          'datas': form.serializeArray(),
        },
        columns: [
          { 
            "data": "dc_main_status",
            "render": (data, type, row) => {
              return data < 1 ? "İlgi" : "Asıl";
            }
          },
          { 
            "data": "dc_cat_id",
            "render": (data, type, row) => {
              let catNames = [];

              row.dcCatNames.forEach(cat => {
                catNames.push(cat.dc_cat_name);
              });
              
              return catNames.join(', ');
            }
          },
          { "data": "dc_number" },
          { 
            "data": "dc_item_status",
            "render": (data, type, row) => {
              return data < 1 ? "Gelen" : "Giden";
            }
          },
          { "data": "dc_subject" },
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
                  'url': row.dc_file_path,
                  'userName': row.user_name
                });
            },
            "defaultContent": ""
          },
        ],
        "initComplete": ((settings, json) => {
          this.setErrors('');
        })
      });
    },
    showTable(vals) {
      if(this.dataTable)
        this.destroyTable();

      this.loadDataTable(vals);
    }
  },
  mounted() {
    this.showModalBody(this.modalSelector);

    $.fn.dataTable.ext.errMode =  (( settings, helpPage, message ) => { 
      this.setSucceed('');
      this.setErrors(settings.jqXHR.responseJSON.errors);
    });
  },
  components: {
    /* [formTitleName + '-create-component']: createComponent,
    [formTitleName + '-edit-component']: editComponent, */
    [formTitleName + '-show-component']: showComponent,
    [formTitleName + '-add-list-component']: addlistComponent,
    [formTitleName + '-add-comment-component']: addCommentComponent,
    [formTitleName + '-delete-component']: deleteComponent,
    // [formTitleName + '-images-component']: imagesComponent,
  }
}
</script>

<style>

</style>