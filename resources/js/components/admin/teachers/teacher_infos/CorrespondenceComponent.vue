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
      // row += this.listBtnHtml(id);
      // row += this.commentBtnHtml(id);
      row += this.fileDownloadBtnHtml(id);
      // this.fileDownloadScript();
      return row;
    },

    fileDownloadScript() {
      $( "[data-file-download]" ).on( "click", function(event) {
        event.attr({
          target: '_blank', 
          href  : 'public/upload/2022/09/02/08/Evin KESKİN Eksiz ilgisiz.udf'
        });
      });
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
  },
  mounted() {
    $('.res-dt-table').DataTable({
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
                'userName': 'row.user_name',
                'document': row
              });
          },
          "defaultContent": ""
        },
      ],
    });

    this.showModalBody(this.modalSelector);
  },
  components: {
    [formTitleName + '-show-component']: showComponent,
    [formTitleName + '-add-list-component']: addlistComponent,
    [formTitleName + '-add-comment-component']: addCommentComponent,
  }
}
</script>

<style>

</style>