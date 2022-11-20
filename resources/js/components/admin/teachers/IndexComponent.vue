<template>
<template-component
	:ppTitleName="$t('messages.teachersListManage')"
>
  <div class="alert alert-info" role="info"
		v-if="succeed != ''"
    v-html="infoMsg()"
	>
	</div>


  <table class="res-dt-table table table-striped table-bordered" 
    style="width:100%">
    <thead>
      <tr>
        <th>{{ $t("messages.thr_tc_no") }}</th>
        <th>{{ $t("messages.thr_name") }}</th>
        <th>{{ $t("messages.thr_surname") }}</th>
        <th>{{ $t("messages.thr_career_ladder") }}</th>
        <th>{{ $t("messages.thr_institution") }}</th>
        <th>{{ $t("messages.processes") }}</th>
      </tr>
    </thead>
    <tfoot>
      <tr>
        <th colspan="6">
          <button type="button" class="btn btn-primary"
            data-toggle="modal" 
            :data-target="modalSelector"
            :data-datas='`{"formTitleName": "\${formTitleName}"}`'
            :data-component="`${formTitleName}-create-component`"
          >
            {{ $t('messages.add') }}
          </button>
          <button type="button" class="btn btn-primary"
            data-toggle="modal" 
            :data-target="modalSelector"
            :data-datas='`{"formTitleName": "\${formTitleName}"}`'
            :data-component="`${formTitleName}-create-excel-component`"
          >
            {{ $t('messages.addLikeExcel') }}
          </button>
          
          <!-- <form :action="pproutes.addExcel" method="post" enctype='multipart/form-data'>
            <input type="hidden" name="_token" :value="token">
            <div class="form-group">
              <label for=""></label>
              <input type="file" class="form-control-file" name="excel_file" id="" placeholder="" aria-describedby="fileHelpId">
              <small id="fileHelpId" class="form-text text-muted">Help text</small>
            </div>

            <button type="submit" class="btn btn-primary">
              post ile exceli yükle
            </button>

          </form> -->
          
          
          <button type="button" class="btn btn-primary"
            data-toggle="modal" 
            :data-target="modalSelector"
            :data-datas='`{"formTitleName": "\${formTitleName}"}`'
            :data-component="`${formTitleName}-create-images-component`"
          >
            {{ $t('messages.addImages') }}
          </button>
        </th>
      </tr>
    </tfoot>
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
import createExcelComponent from './CreateExcelComponent';
import editComponent from './EditComponent';
import showComponent from './ShowComponent';
import deleteComponent from './DeleteComponent';
import createImagesComponent from './CreateImagesComponent';

import { mapState, mapMutations } from 'vuex';

let formTitleName = 'teachers'

export default {
  // name: this.componentTitleName,
  data () {
    return {
      modalIDName: 'formModalLong',
      formTitleName,
      dataTable: null,
      datas: this.ppdatas,
      succeed: this.ppdatas.succeed,
      insertErrorArr: this.ppdatas.insertErrorArr,
      sumInsertData: this.ppdatas.sumInsertData,
      sumErrorData: this.ppdatas.sumErrorData,
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
    infoMsg: function() {
      let msg = '';
      if(this.succeed != '') {
        msg += this.sumInsertData + ' Tane ' + this.succeed + ' ' + this.sumErrorData + ' tane veri eklenemedi.';
        msg += '<br/>';

        if(this.sumErrorData > 0) {
          msg += 'Aşağıdaki Tc Numaraları Yüklenememiştir:' ;
          msg += '<br/>';
          msg += this.insertErrorArr.join(', ');
        }
      }

      return msg;
    },
    processesRow: function(id){
      let row = '';
      row += this.editBtnHtml(id);
      row += this.deleteBtnHtml(id);
      // row += this.imageBtnHtml(id);
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
        { "data": "thr_tc_no" },
        { "data": "thr_name" },
        { "data": "thr_surname" },
        { 
          "data": "thr_career_ladder",
          "render": ( data, type, row ) => {
              return  data == 0 ? 'Öğretmen' :
                      data == 1 ? 'Uzman Öğretmen' :
                      data == 2 ? 'Başöğretmen' : 'Bilinmiyor'
          },
        },
        { "data": "inst_name" },
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
    [formTitleName + '-create-excel-component']: createExcelComponent,
    [formTitleName + '-edit-component']: editComponent,
    [formTitleName + '-show-component']: showComponent,
    [formTitleName + '-delete-component']: deleteComponent,
    [formTitleName + '-create-images-component']: createImagesComponent,
  }
}
</script>