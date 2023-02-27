<template>
<template-component
	:ppTitleName="$t('messages.user_manage')"
>
  <table class="res-dt-table table table-striped table-bordered" 
  style="width:100%">
    <thead>
      <tr>
        <th>{{ $t("messages.name") }}</th>
        <th>{{ $t("messages.email") }}</th>
        <th>{{ $t("messages.processes") }}</th>
      </tr>
    </thead>
    <tfoot>
      <!-- <tr>
        <th colspan="3">
          <button type="button" class="btn btn-primary"
            data-toggle="modal" 
            :data-target="modalSelector"
            :data-datas='`{"formTitleName": "\${formTitleName}"}`'
            :data-component="`${formTitleName}-create-component`"
          >
            {{ $t('messages.add') }}
          </button>
        </th>
      </tr> -->
    </tfoot>
  </table>

  <!-- Modal -->
  <div class="modal fade" tabindex="-1" role="dialog" 
    aria-labelledby="formModalLongTitle" aria-hidden="true"
    data-backdrop="static" :id="modalIDName"
  >
    <div class="modal-dialog modal-lg" role="document">
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
import editPermssionComponent from './EditPermissionComponent';

import { mapState, mapMutations } from 'vuex';

let formTitleName = 'user'

export default {
  // name: this.componentTitleName,
  data () {
    return {
      modalIDName: 'formModalLong',
      formTitleName,
      dataTable: null,
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
    ppimgfilters: {
      type: Object,
      required: false,
    },
  },
  computed: {
    ...mapState([
      'formModalBody',
      'routes',
      'errors',
      'token',
      'imgFilters',
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
      'setImgFilters',
    ]),
    processesRow: function(id){
      let row = '';
      row += this.editBtnHtml(id);
      row += this.deleteBtnHtml(id);
      row += this.permissionBtnHtml(id);
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
    
    permissionBtnHtml: function(id){
      return  `
        <span 
            data-toggle="tooltip" data-placement="top" 
            title="${this.$t('messages.user_permission')}"
          >
          <button type="button" class="btn btn-sm btn-info"
            data-toggle="modal" data-target="${this.modalSelector}"
            data-component="${this.formTitleName}-edit-permission-component" 
            data-datas='{
              "id": ${id},
              "formTitleName": "${this.formTitleName}"
            }'
          >
            <i class="bi bi-key"></i>
          </button>
        </span>`;
    },

  },
  created(){
    this.setRoutes(this.pproutes);
    this.setErrors(this.pperrors);
    // this.setImgFilters(this.ppimgfilters);
  },
  mounted(){
    this.showModalBody(this.modalSelector);

    this.dataTable = this.dataTableRun({
      jQDomName: '.res-dt-table',
      url: this.routes.dataList,
      columns: [
        { "data": "name" },
        { "data": "email" },
        /* { 
          "data": "bks_start_date",
          "render": (data, type, row) => {
            return this.unixTimestamp(data);
          }
        }, */
        {
          "orderable": false,
          "searchable": false,
          "sortable": false,
          "data": "id",
          "render": ( data, type, row ) => {
              return this.processesRow(data);
          },
          "defaultContent": ""
        },
      ],
    });
  },
  components: {
    [formTitleName + '-create-component']: createComponent,
    [formTitleName + '-edit-component']: editComponent,
    [formTitleName + '-show-component']: showComponent,
    [formTitleName + '-delete-component']: deleteComponent,
    [formTitleName + '-edit-permission-component']: editPermssionComponent,
    // [formTitleName + '-images-component']: imagesComponent,
  }
}
</script>