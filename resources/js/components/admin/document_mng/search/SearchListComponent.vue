<template>
<div class="list-group">
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
                <th>messages.dc_cat_name</th>
                <th>messages.dc_number</th>
                <th>messages.dc_item_status</th>
                <th>messages.dc_subject</th>
                <th>messages.dc_date</th>
                <th>messages.processes</th>
              </tr>
            </thead>
            <tfoot>
              <tr>
                <th colspan="6">
                  <button type="button" class="btn btn-primary" >
                    asdasdasd
                  </button>
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
    
    <div class="modal-dialog modal-lg" role="document">
        <component
          v-if="formModalBody.show"
          :is="formModalBody.component"
          :ppdatas="formModalBody.datas"
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
// import deleteComponent from './DeleteComponent';
// import imagesComponent from './ImagesComponent';

let formTitleName= 'dc-search-list';

import { mapState } from 'vuex';

export default {
  name: "SearchListComponent",
  data() {
    return {
      form: this.ppformId,
      modalIDName: 'formModalLong',
      formTitleName,
      dataTable: null,
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

    processesRow: function(id){
      let row = '';
      row += this.showBtnHtml(id);
      /* row += this.editBtnHtml(id);
      row += this.deleteBtnHtml(id); */
      // row += this.imageBtnHtml(id);
      return row;
    },
    
    showBtnHtml: function(id){
      return  `
        <span 
          data-toggle="tooltip" data-placement="top" 
          title="${this.$t('messages.edit')}"
        >
          <button type="button" class="btn btn-sm btn-info"
            data-toggle="modal" data-target="${this.modalSelector}"
            data-component="${this.formTitleName}-show-component" 
            data-datas='{
              "id": ${id},
              "formTitleName": "${this.formTitleName}"
            }'
          >
            <i class="bi bi-pencil-square"></i>
          </button>
        </span>`;
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
    
    imageBtnHtml: function(id){
      return  `
        <span 
            data-toggle="tooltip" data-placement="top" 
            title="${this.$t('messages.image')}"
          >
          <button type="button" class="btn btn-sm btn-info"
            data-toggle="modal" data-target="${this.modalSelector}"
            data-component="${this.formTitleName}-images-component" 
            data-datas='{
              "id": ${id},
              "formTitleName": "${this.formTitleName}"
            }'
          >
            <i class="bi bi-image"></i>
          </button>
        </span>`;
    },
    destroyTable() {
      if (typeof this.dataTable !== 'undefined') {
        this.dataTable.destroy();
        $("#"+this.form+" tbody").empty();
      }
    },
    loadDataTable() {
      let form = $("#"+this.form);

      this.dataTable = this.dataTableRun({
        jQDomName: '.res-dt-table',
        url: this.routes.getSearchDocuments,
        data: {
          'datas': form.serializeArray(),
        },
        columns: [
          { "data": "dc_cat_id" },
          { "data": "dc_number" },
          { "data": "dc_item_status" },
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
                return this.processesRow(data);
            },
            "defaultContent": ""
          },
        ],
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
    // this.loadDataTable();
  },
  components: {
    /* [formTitleName + '-create-component']: createComponent,
    [formTitleName + '-edit-component']: editComponent, */
    [formTitleName + '-show-component']: showComponent,
    // [formTitleName + '-delete-component']: deleteComponent,
    // [formTitleName + '-images-component']: imagesComponent,
  }
}
</script>

<style>

</style>