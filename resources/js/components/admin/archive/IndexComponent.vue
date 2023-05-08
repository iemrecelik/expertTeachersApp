<template>
  <multi-section-template-component
    :ppTitleName="$t('messages.archive_manage')"
  >
    <div class="row mt-3">
      <div class="col-md-12">
        <div class="card">
          <div class="card-body">
            <form
              @submit.prevent
              :id="formIDName"
            >
              <div class="row">
                <table class="res-dt-table table table-striped table-bordered dt-responsive" 
                  style="width:100%">
                  <thead>
                    <tr>
                      <th data-priority="1">{{ $t("messages.archive_name") }}</th>
                      <th data-priority="3">{{ $t("messages.processes") }}</th>
                    </tr>
                  </thead>
                  <tfoot>
                    <tr>
                      <th colspan="2">
                        <button type="button" class="btn btn-primary"
                          @click="recordArchive"
                        >
                          {{ $t('messages.record_archive') }}
                        </button>
                        <div class="loading-btn">
                          <br/>
                          <label for="excel-file">{{$t('messages.loading')}}...</label>
                          <br/>
                          <div class="spinner-border text-secondary ml-3" role="status">
                            <span class="sr-only">{{$t('messages.loading')}}</span>
                          </div>
                        </div>
                      </th>
                    </tr>
                  </tfoot>
                </table>
              </div><!-- /.row -->
            </form>
  
          </div><!-- /.card-body -->
        </div><!-- /.card -->
      </div><!-- /.col-md-12 -->
    </div><!-- /.row mt-3 -->

    <!-- Modal -->
    <div class="modal fade" tabindex="-1" role="dialog" 
      aria-labelledby="formModalLongTitle" aria-hidden="true"
      data-backdrop="static" :id="modalIDName"
    >
      <div class="modal-dialog" role="document">
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
import deleteComponent from './DeleteComponent';

import { mapState, mapMutations } from 'vuex';

let formTitleName = 'archive';

export default {
  name: 'IndexComponent',
  data () {
    return {
      modalIDName: 'formModalLong',
      formTitleName,
      datas: this.ppdatas,
      logs: [],
      formIDName: 'logs-form',
      Toast: Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
          toast.addEventListener('mouseenter', Swal.stopTimer)
          toast.addEventListener('mouseleave', Swal.resumeTimer)
        }
      })
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
    ppdatas: {
      type: Object,
      required: true,
    },
  },
  computed: {
    ...mapState([
      'routes',
      'formModalBody',
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
      'setSucceed',
      'setErrors',
    ]),
    processesRow: function(data){
      let row = '';
      row += this.fileDownloadBtnHtml(data);
      row += this.deleteBtnHtml(data);
      return row;
    },

    fileDownloadBtnHtml: function(data){
      return  `
        <span 
          data-toggle="tooltip" data-placement="top" 
          title="${this.$t('messages.archive_download')}"
        >
          <a type="button" class="btn btn-sm btn-success"
            data-file-download
            href="/storage/upload/zip_archives/${data}"
            download
          >
            <i class="bi bi-file-earmark-arrow-down"></i>
          </a>
        </span>`;
    },

    deleteBtnHtml: function(data){
      return  `
        <span 
            data-toggle="tooltip" data-placement="top" 
            title="${this.$t('messages.delete')}"
          >
          <button type="button" class="btn btn-sm btn-danger"
            data-toggle="modal" data-target="${this.modalSelector}"
            data-component="${this.formTitleName}-delete-component" 
            data-datas='{
              "archive_name": "${data}",
              "formTitleName": "${this.formTitleName}"
            }'
          >
            <i class="bi bi-trash"></i>
          </button>
        </span>`;
    },
    
    loadDataTable() {
      if(this.dataTable) {
        this.dataTable.destroy();
      }

      this.dataTable = this.dataTableManuelRun({
        jQDomName: '.res-dt-table',
        data: this.datas.archive,
        initComplete: () => {},
        columns: [
          { 'data': 'archive_name' },
          {
            "orderable": false,
            "searchable": false,
            "sortable": false,
            "data": "download_href",
            "render": ( data, type, row ) => {
              return this.processesRow(data);
            },
            "defaultContent": ""
          },
        ],
      });
    },

    deleteArchiveItem(arcName) {
      let findId = this.datas.archive.findIndex((item) => {
        console.log('item: ', item.archive_name);
        console.log('arcName', arcName);
        return item.archive_name == arcName;
      });

      this.datas.archive.splice(findId, 1);

      this.loadDataTable();
    },
    
    addArchiveItem(arcName) {
      this.datas.archive.push({
        archive_name:arcName,
        download_href:arcName
      });
      this.loadDataTable();
    },

    recordArchive: function(){
      $.ajax({
        url: this.routes.recordArchive,
        type: 'POST',
        dataType: 'JSON',
        beforeSend: function () {
          $('.loading-btn').parent().find('button').prop('disabled', true);;
          $('.loading-btn').show();
        }
      })
      .done((res) => {
        this.addArchiveItem(res.zipFilePath);
        this.setErrors('');
        this.setSucceed(res.succeed);
        this.Toast.fire({
          icon: 'success',
          title: 'Arşiv kaydedildi.'
        });
      })
      .fail((error) => {
        let htmlItems = '';
        for (const key in error.responseJSON.errors) {
          if (Object.hasOwnProperty.call(error.responseJSON.errors, key)) {
            htmlItems += '<li>'+error.responseJSON.errors[key]+'</li>';
          }
        }

        let titleHtml = `
          <ol>
            ${htmlItems}
          </ol>
        `
        this.Toast.fire({
          icon: 'error',
          html: titleHtml
        })

        /* if(error.responseJSON) {
          if(error.responseJSON.errors) {
            this.setErrors(error.responseJSON.errors);
          }else if(error.responseJSON.message) {
            this.setErrors(
              {'permissionMessage': [error.responseJSON.message]}
            );
          }
        } */
        this.setSucceed('');
      })
      .then((res) => {})
      .always(() => {
        $('.loading-btn').hide();
        $('.loading-btn').parent().find('button').prop('disabled', false);;
      });
    },
  },
  created(){
    this.setRoutes(this.pproutes);
    this.setErrors(this.pperrors);
  },
  mounted(){
    this.loadDataTable();
    this.showModalBody(this.modalSelector);
  },
  components: {
    [formTitleName + '-delete-component']: deleteComponent,
  }
}
</script>

<style>
.loading-btn{
  display: none;
}
</style>