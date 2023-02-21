<template>
<multi-section-template-component
	:ppTitleName="$t('messages.waiting_documents')"
>
  <succeed-msg-component></succeed-msg-component>
  <error-msg-list-component></error-msg-list-component>
  
  <div class="row mt-3">
    <div class="col-md-12">
      <div class="card">
        <div class="card-body">
          <form 
            @submit.prevent
            :id="formIDName"
          >
          <!-- <form :action="routes.saveBotDocument" method="POST" > -->
            <!-- <input type="hidden" name="_token" :value="token"> -->
            <div class="row">
              <div class="col-3">
                <!-- Date -->
                <div class="form-group">
                  <label>Tarih:</label>
                  <div class="input-group date">
                    <select name="date"  class="form-control">
                      <option v-for="date in dates" :value="date">{{ date }}</option>
                    </select>
                    <div class="input-group-append">
                      <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                    </div>
                  </div>
                </div>
              </div>
              
              <div class="col-3">
                <div class="form-group">
                  <label>Evrak Durumu:</label>
                  <div>
                    <select name="item_status" class="form-control">
                      <option value="0">Gelen</option>
                      <option value="1">Giden</option>
                    </select>
                  </div>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-2">
                <button :disabled="addBotDocLoading" type="button" class="btn btn-primary"
                  @click="addBotDocuments"
                >
                  {{ $t('messages.add') }}
                </button>
                <!-- <button type="submit" class="btn btn-primary">
                  {{ $t('messages.add') }}
                </button> -->
              </div>

              <!-- <div class="col-2" v-if="addBotDocLoading == true"> -->

            </div>

            <div class="row mt-3" v-if="addBotDocLoading">
              <div class="col-2">
                <label for="excel-file">{{$t('messages.loading')}}...</label>
                <br/>
                <div class="spinner-border text-secondary ml-3" role="status">
                  <span class="sr-only">{{$t('messages.loading')}}</span>
                </div>
              </div>
            </div>

          </form>
        </div>
      </div>
    </div>
  </div>

  <div class="row mt-3">
    <div class="col-md-12">
      <div class="card">
        <div class="card-body">
          <table class="res-dt-table table table-striped table-bordered" 
            style="width:100%">
            <thead>
              <tr>
                <th>{{ $t("messages.dc_id") }}</th>
                <th>{{ $t("messages.dc_item_status") }}</th>
                <th>{{ $t("messages.dc_date") }}</th>
                <th>{{ $t("messages.thr_name") }}</th>
                <th>{{$t('messages.processes')}}</th>
              </tr>
            </thead>
          </table>
        </div><!-- /.card-body-->
      </div><!-- /.card-->
    </div><!-- /.col-md-12-->
  </div><!-- /.row-->
  

  <!-- Modal -->
  <div class="modal fade" tabindex="-1" role="dialog" 
    aria-labelledby="formModalLongTitle" aria-hidden="true"
    data-backdrop="static" :id="modalIDName"
  >
    <div class="modal-dialog modal-xl" role="document">
      <div class="modal-body">
        <component
          v-if="formModalBody.show"
          :is="formModalBody.component"
          :ppdatas="formModalBody.datas"
        >
        </component>
      </div>
    </div>
  </div>
  
</multi-section-template-component>
</template>

<script>
import showComponent from './ShowComponent';
import addlistComponent from './AddListComponent';
import addCommentComponent from './AddCommentComponent';
import deleteComponent from './DeleteComponent';

import { mapState, mapMutations } from 'vuex';

let formTitleName = 'waitingDocuments'

export default {
  name: 'IndexComponent',
  data () {
    return {
      modalIDName: 'formModalLong',
      formTitleName,
      dataTable: null,
      ajaxErrorCount: -1,
      datas: this.ppdatas,
      formIDName: 'waiting-documents',
      dates: [],
      addBotDocLoading: false,
      // users: this.ppdatas.users
    };
  },
  props: {
    pproutes: {
      type: Object,
      required: true,
    },
    pperrors: {
      type: Object,
      required: true,
    },
    ppsuccess: {
      type: String,
      required: false,
      default: '',
    },
    ppdatas: {
      type: Array,
      required: false,
    },
  },
  computed: {
    ...mapState([
      'formModalBody',
      'routes',
      'errors',
      'token',
    ]),
    cformTitleName: function(){
      return _.capitalize(this.formTitleName);
    },
    componentTitleName: function(){
      return _.capitalize(this.formTitleName) + 'Component';
    },
    modalSelector: function(){
      return '#' + this.modalIDName;
    },
  },
  methods: {
    ...mapMutations([
      'setRoutes',
      'setErrors',
      'setSucceed',
    ]),
    processesRow: function(id){
      let row = '';
      row += this.editBtnHtml(id);
      row += this.listBtnHtml(id);
      row += this.commentBtnHtml(id);
      return row;
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

    oldValue: function(fieldName){
      return this.$store.state.old[fieldName];
    },
    destroyTable() {
      if (typeof this.dataTable !== 'undefined') {
        this.dataTable.destroy();
        // $("#"+this.form+" tbody").empty();
      }
    },

    loadDataTable() {
      if(this.dataTable) {
        this.destroyTable();
      }
      
      this.dataTable = this.dataTableRun({
        jQDomName: '.res-dt-table',
        url: this.routes.getWaitingDocument,
        method: 'GET',
        /* data: {
          'datas': form.serializeArray(),
        }, */
        columns: [
          { "data": "dc_number" },
          { 
            "data": "dc_item_status",
            "render": (data, type, row) => {
              return data < 1 ? "Gelen" : "Giden";
            }
          },
          { 
            "data": "dc_date",
            "render": (data, type, row) => {
              return this.unixTimestamp(data);
            }
          },
          { 
            "data": "user_name",
            "render": ( data, type, row ) => {
              return data;
            },
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
        /* initComplete: (settings, json) => {
          this.recordedDocumentsCount = json.recordsFiltered;
        } */
      });
    },

    addBotDocuments() {
      let form = $('#' + this.formIDName);

      this.addBotDocLoading = true;

      $.ajax({
        url: this.routes.saveBotDocument,
        type: 'POST',
        dataType: 'JSON',
        data: form.serialize(),
      })
      .done((res) => {
        this.setErrors('');
        this.setSucceed(res.succeed);
      })
      .fail((error) => {
        if(error.responseJSON) {
          if(error.responseJSON.errors) {
            this.setErrors(error.responseJSON.errors);
          }else if(error.responseJSON.message) {
            this.setErrors(
              {'botMessage': [error.responseJSON.message]}
            );
          }
        }
        this.setSucceed('');
      })
      .then((res) => {
        this.dataTable.ajax.reload();
        
      })
      .always(() => {
        this.addBotDocLoading = false;
      });
    }
  },
  created(){
    this.setRoutes(this.pproutes);
    this.setErrors(this.pperrors);
    this.setSucceed(this.ppsuccess);
  },
  mounted(){
    this.showModalBody(this.modalSelector);
    
    this.loadDataTable();

    let today = new Date();
    let dd = String(today.getDate() - 1).padStart(2, '0');
    let nowdd = String(today.getDate()).padStart(2, '0');
    let mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
    let yyyy = today.getFullYear();

    today = nowdd + '-' + mm + '-' + yyyy;
    let yesterday = dd + '-' + mm + '-' + yyyy;

    this.dates.push(today);
    this.dates.push(yesterday);
  },
  components: {
    [formTitleName + '-show-component']: showComponent,
    [formTitleName + '-add-list-component']: addlistComponent,
    [formTitleName + '-add-comment-component']: addCommentComponent,
    [formTitleName + '-delete-component']: deleteComponent
  }
}
</script>

<style scoped>
</style>