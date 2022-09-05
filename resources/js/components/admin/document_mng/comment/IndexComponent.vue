<template>
<template-component>
  
  <div class="row">
    <div class="col-3">

      <div class="form-group">
        <label for="usersSelectBox">Kullanıcılar</label>
      
        <div class="input-group input-group-md">
          <select class="form-control" 
            id="usersSelectBox"
            name="user_id"
          >
            <option value="0">Tüm Kullanıclar</option>
            <option :selected="user.auth" v-for="user in users" :value="user.id">
              {{user.name}}
            </option>
          </select>

          <!-- <input type="text" class="form-control form-control-lg" name="surname" placeholder="Buraya soyad giriniz" value=""> -->

          <div class="input-group-append">
            <button
              type="button" 
              class="btn btn-primary"
              @click="getDataList"
            >
              <i class="fa fa-search"></i>
              Ara
            </button>
          </div>
        </div>

      </div><!-- /.form-group -->

    </div>
  </div>

  <table class="res-dt-table table table-striped table-bordered" 
  style="width:100%">
    <thead>
      <tr>
        <th>{{ $t("messages.dc_number") }}</th>
        <th>{{ $t("messages.dc_subject") }}</th>
        <th>{{ $t("messages.dc_com_text") }}</th>
        <th>{{ $t("messages.user_name") }}</th>
        <th>{{ $t("messages.processes") }}</th>
      </tr>
    </thead>
    <!-- <tfoot>
      <tr>
        <th colspan="5">
          <button type="button" class="btn btn-primary"
            data-toggle="modal" 
            :data-target="modalSelector"
            :data-datas='`{"formTitleName": "\${formTitleName}"}`'
            :data-component="`${formTitleName}-create-component`"
          >
            {{ $t('messages.add') }}
          </button>
        </th>
      </tr>
    </tfoot> -->
  </table>

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
  
</template-component>
</template>

<script>
import createComponent from './CreateComponent';
import editComponent from './EditComponent';
import showComponent from './ShowComponent';
import deleteComponent from './DeleteComponent';

import { mapState, mapMutations } from 'vuex';

let formTitleName = 'dc-comment'

export default {
  name: 'IndexComponent',

  data () {
    return {
      modalIDName: 'formModalLong',
      formTitleName,
      dataTable: undefined,
      users: this.ppusers,
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
    ppusers: {
      type: Array,
      required: true,
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
      'setEditItem',
    ]),
    processesRow: function(datas){
      let row = '';
      row += this.editBtnHtml(datas.id);
      row += this.deleteBtnHtml(datas.id);
      row += this.showBtnHtml(datas.dc_id);
      row += this.fileDownloadBtnHtml(datas.url);
      return row;
    },
    
    editBtnHtml: function(id){
      return  `
        <span 
          data-toggle="tooltip" data-placement="top" 
          title="${this.$t('messages.edit')}"
        >
          <button type="button" class="btn btn-sm btn-success"
            data-toggle="modal" data-target="${this.modalSelector}"
            data-component="${this.formTitleName}-edit-component" 
            data-datas='{
              "id": ${id},
              "formTitleName": "${this.formTitleName}"
            }'
          >
            <i class="bi bi-pencil-square"></i>
          </button>
        </span>`;
    },

    deleteBtnHtml: function(id){
      return  `
        <span 
            data-toggle="tooltip" data-placement="top" 
            title="${this.$t('messages.delete')}"
          >
          <button type="button" class="btn btn-sm btn-danger"
            data-toggle="modal" data-target="${this.modalSelector}"
            data-component="${this.formTitleName}-delete-component" 
            data-datas='{
              "id": ${id},
              "formTitleName": "${this.formTitleName}"
            }'
          >
            <i class="bi bi-trash"></i>
          </button>
        </span>`;
    },

    showBtnHtml: function(id){
      return  `
        <span 
          data-toggle="tooltip" data-placement="top" 
          title="${this.$t('messages.showDocument')}"
        >
          <button type="button" class="btn btn-sm btn-info"
            data-toggle="modal" data-target="${this.modalSelector}"
            data-component="${this.formTitleName}-show-component" 
            data-datas='{
              "id": ${id},
              "formTitleName": "${this.formTitleName}"
            }'
          >
            <i class="bi bi-file-text"></i>
          </button>
        </span>`;
    },

    fileDownloadBtnHtml: function(url){
      return  `
        <span 
          data-toggle="tooltip" data-placement="top" 
          title="${this.$t('messages.docFileDownload')}"
        >
          <a type="button" class="btn btn-sm btn-success"
            data-file-download
            href="/storage/upload/images/raw${url}"
            download
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

    getDataList: function(){
      this.destroyTable();
      
      let userId = document.getElementById('usersSelectBox').value;

      this.dataTable = this.dataTableRun({
        jQDomName: '.res-dt-table',
        url: this.routes.dataList,
        data: {'user_id': userId},
        columns: [
          { "data": "dc_number" },
          { "data": "dc_subject" },
          { "data": "dc_com_text" },
          { "data": "user_name" },
          {
            "orderable": false,
            "searchable": false,
            "sortable": false,
            "data": "id",
            "render": ( data, type, row ) => {
              if(data) {
                return this.processesRow({
                  'id': data, 
                  'dc_id': row.dc_id,
                  'url': row.dc_file_path
                });
              }
            },
            "defaultContent": ""
          },
        ],
      });
    }

  },
  created(){
    this.setRoutes(this.pproutes);
    this.setErrors(this.pperrors);
  },
  mounted(){
    this.showModalBody(this.modalSelector);

    this.getDataList();
  },
  components: {
    [formTitleName + '-create-component']: createComponent,
    [formTitleName + '-edit-component']: editComponent,
    [formTitleName + '-show-component']: showComponent,
    [formTitleName + '-delete-component']: deleteComponent,
  }
}
</script>