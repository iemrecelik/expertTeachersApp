<template>
<multi-section-template-component
	:ppTitleName="$t('messages.teachersListManage')"
>
  <error-msg-list-component></error-msg-list-component>

  <div class="alert alert-info" role="info"
    v-if="succeed != ''"
    v-html="infoMsg()"
  >
  </div>

  <div class="row mt-3">
    <div class="col-md-12">
      <div class="card">
        <div class="card-body">
          <div class="row">

            <div class="col-12">
              <form id="teacher-search" 
                action="#"
                @submit.prevent
              >
                <div class="row">
                  <div class="col-2">
                    <div class="form-group">
                      <label for="tc-no">{{$t('messages.thr_tc_no')}}</label>
                      <input type="text" 
                        class="form-control" 
                        id="tc-no" 
                        :placeholder="$t('messages.thr_tc_no')"
                        name="thr_tc_no" 
                        value=""
                        data-inputmask='"mask": "99999999999"' 
                        data-mask
                      >
                    </div>
                  </div>
                  <div class="col-2">
                    <form-form-component
                      :ppsettings="{
                        type: 'text', 
                        fieldName: 'thr_name', 
                        value: ''
                      }"
                    >
                    </form-form-component>
                  </div>
                  <div class="col-2">
                    <form-form-component
                      :ppsettings="{
                        type: 'text', 
                        fieldName: 'thr_surname', 
                        value: ''
                      }"
                    >
                    </form-form-component>
                  </div>
                  <div class="col-2">
                    <div class="form-group">
                      <label for="add-province">İller </label>
                      <treeselect
                        :id="'add-province'"
                        :multiple="false"
                        :async="true"
                        :load-options="loadProvinces"
                        loadingText="Yükleniyor..."
                        clearAllText="Hepsini sil."
                        clearValueText="Değeri sil."
                        noOptionsText="Hiçbir seçenek yok."
                        noResultsText="Mevcut seçenek yok."
                        searchPromptText="Aramak için yazınız."
                        placeholder="Seçiniz..."
                        name="prv_id"
                        @select="getTownsList"
                        @input="clearTownsList"
                      />
                    </div>
                  </div>
                  <div class="col-2">
                    <div class="form-group">
                      <label for="add-town">İlçeler </label>
                      <select class="form-control" name="twn_id">
                        <option value="" selected>Seçiniz</option>
                        <option :value="town.id" :key="key" v-for="(town, key) in townsArr">
                          {{town.label}}
                        </option>
                      </select>
                    </div>
                  </div>
                  <div class="col-2">
                    <div class="form-group">
                      <label for="thr-email">{{$t('messages.thr_email')}}</label>
                      <input type="text" 
                        class="form-control" 
                        id="thr-email" 
                        :placeholder="$t('messages.thr_email')"
                        name="thr_email" 
                        value=""
                        data-inputmask-regex="^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$" 
                        data-mask
                      >
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-2">

                    <div class="form-group">
                      <label for="thr-career-ladder">{{$t('messages.thr_career_ladder')}} :</label>
                      <select class="form-control" id="thr-career-ladder"
                        name="thr_career_ladder"
                      >
                        <option selected value="">{{$t('messages.thr_career_ladder')}}</option>
                        <option value="-1">Bilinmiyor</option>
                        <option value="0">Öğretmen</option>
                        <option value="1">Uzman Öğretmen</option>
                        <option value="2">Başöğretmen</option>
                      </select>
                    </div>
                  </div>
                  <div class="col-2">
                    <form-form-component
                      :ppsettings="{
                        type: 'text', 
                        fieldName: 'thr_degree', 
                        value: ''
                      }"
                    >
                    </form-form-component>
                  </div>
                  <div class="col-2">
                    <form-form-component
                      :ppsettings="{
                        type: 'text', 
                        fieldName: 'thr_task', 
                        value: ''
                      }"
                    >
                    </form-form-component>
                  </div>
                  <div class="col-2">
                    <form-form-component
                        :ppsettings="{
                          type: 'text', 
                          fieldName: 'thr_education_st', 
                          value: ''
                        }"
                      >
                      </form-form-component>
                  </div>
                  <div class="col-2">
                    <form-form-component
                        :ppsettings="{
                          type: 'text', 
                          fieldName: 'thr_place_of_task', 
                          value: ''
                        }"
                      >
                      </form-form-component>
                  </div>
                  <div class="col-2">
                    <div class="form-group">
                      <label for="date-of-birth">{{$t('messages.dateOfBirth')}}</label>
                      <input type="text" 
                        class="form-control" 
                        id="date-of-birth" 
                        :placeholder="$t('messages.dateOfBirth')"
                        name="thr_birth_day" 
                        value=""
                        data-inputmask='"mask": "99/99/9999"' 
                        data-mask
                      >
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-10"></div>
                  <div class="col-2">
                    <div class="form-group text-right">
                      <button type="submit" class="btn btn-primary bg-gradient-primary w-100"
                        @click="loadDataTable"
                      >
                        Ara
                      </button>
                    </div>
                  </div>
                </div>
              </form>
            </div>

          </div><!-- /.row -->
          
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
                  
                  <!-- <form id="export-excel" :action="routes.exportExcelDatas" method="POST" @submit.prevent> -->
                    <!-- <input type="hidden" name="list"> -->
                    <button type="button" class="btn btn-primary"
                      @click="exportExcelDatas"
                    >
                      {{ $t('messages.exportExcel') }}
                    </button>
                  <!-- </form> -->
                  
                  
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
        </div><!-- /.card-body-->
      </div><!-- /.card-->
    </div><!-- /.col-md-12-->
  </div><!-- /.row-->

  <form id="export-excel" :action="routes.exportExcelDatas" method="POST" @submit.prevent>
    <input type="hidden" name="thr_tc_no">
    <input type="hidden" name="thr_name">
    <input type="hidden" name="thr_surname">
    <input type="hidden" name="prv_id">
    <input type="hidden" name="twn_id">
    <input type="hidden" name="thr_email">
    <input type="hidden" name="thr_career_ladder">
    <input type="hidden" name="thr_degree">
    <input type="hidden" name="thr_education_st">
    <input type="hidden" name="thr_place_of_task">
    <input type="hidden" name="thr_birth_day">
    <input type="hidden" name="_token" :value="token">
  </form>

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
import createComponent from './CreateComponent';
import createExcelComponent from './CreateExcelComponent';
import editComponent from './EditComponent';
import showComponent from './ShowComponent';
import deleteComponent from './DeleteComponent';
import createImagesComponent from './CreateImagesComponent';

import { mapState, mapMutations } from 'vuex';

import Treeselect from '@riophae/vue-treeselect'
import { ASYNC_SEARCH } from '@riophae/vue-treeselect';

const simulateAsyncOperation = fn => {
  setTimeout(fn, 2000)
}

// import the styles
import '@riophae/vue-treeselect/dist/vue-treeselect.css'

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
      ajaxErrorCount: -1,
      townsArr: [],
      // exportExcelDatas: null,
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
    processesRow: function(id, tcNo){
      let row = '';
      row += this.editBtnHtml(id);
      row += this.deleteBtnHtml(id);
      row += this.showTeacherBtnHtml(tcNo);
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

    showTeacherBtnHtml: function(tcNo){
      return  `
        <form class="d-inline-block" action="${this.routes.showTeacherInfos}" method="post">
          <div class="input-group">
            <input type="hidden" name="thr_tc_no" value="${tcNo}">
            <input type="hidden" name="_token" value="${this.token}">
            <span 
              data-toggle="tooltip" data-placement="top" 
              title="${this.$t('messages.showTeacher')}"
            >
              <button type="submit" class="btn btn-sm btn-info">
                <i class="bi bi-info-circle-fill"></i>
              </button>
            </span>
          </div>
        </form>
      `;
    },

    loadProvinces({ action, searchQuery, callback }) {
      if (action === ASYNC_SEARCH) {
        simulateAsyncOperation(() => {

          if(searchQuery.length > 2) {
            this.getProvincesList(searchQuery, callback);
          }else {
            callback(null, [])    
          }
        })
      }
    },
    getProvincesList: function(searchWords, callback) {
      $.ajax({
        url: this.routes.getProvincesList,
        type: 'GET',
        dataType: 'JSON',
				data: {'searchWords': searchWords}
      })
      .done((res) => {
				callback(null, res)
        this.ajaxErrorCount = -1;
      })
      .fail((error) => {
        setTimeout(() => {
          this.ajaxErrorCount++

          if(this.ajaxErrorCount < 3)
            this.getProvincesList(searchWords, callback);
          else
            this.ajaxErrorCount = -1;

        }, 100);
      })
      .then((res) => {})
		},

    getTownsList: function(node) {
      $.ajax({
        url: this.routes.getTownsList,
        type: 'GET',
        dataType: 'JSON',
				data: {'prv_id': node.id}
      })
      .done((res) => {
				this.townsArr = res;
        this.ajaxErrorCount = -1;
      })
      .fail((error) => {
        setTimeout(() => {
          this.ajaxErrorCount++

          if(this.ajaxErrorCount < 3)
            this.getTownsList(node);
          else
            this.ajaxErrorCount = -1;

        }, 100);
      })
      .then((res) => {})
		},

    clearTownsList: function() {
      this.townsArr = [];
    },

    destroyTable() {
      if (typeof this.dataTable !== 'undefined') {
        this.dataTable.destroy();
        // $("#"+this.form+" tbody").empty();
      }
    },
    getSearchDatas() {
      let datas = {};
      let form = document.getElementById("teacher-search");

      if(form.elements['thr_tc_no']) {
        datas['thr_tc_no'] = form.elements['thr_tc_no'].value;
      }
      
      if(form.elements['thr_name']) {
        datas['thr_name'] = form.elements['thr_name'].value;
      }

      if(form.elements['thr_surname']) {
        datas['thr_surname'] = form.elements['thr_surname'].value;
      }

      if(form.elements['prv_id']) {
        datas['prv_id'] = form.elements['prv_id'].value;
      }

      if(form.elements['twn_id']) {
        datas['twn_id'] = form.elements['twn_id'].value;
      }

      if(form.elements['thr_email']) {
        datas['thr_email'] = form.elements['thr_email'].value;
      }

      if(form.elements['thr_career_ladder']) {
        datas['thr_career_ladder'] = form.elements['thr_career_ladder'].value;
      }

      if(form.elements['thr_degree']) {
        datas['thr_degree'] = form.elements['thr_degree'].value;
      }

      if(form.elements['thr_education_st']) {
        datas['thr_education_st'] = form.elements['thr_education_st'].value;
      }

      if(form.elements['thr_place_of_task']) {
        datas['thr_place_of_task'] = form.elements['thr_place_of_task'].value;
      }

      if(form.elements['thr_birth_day']) {
        datas['thr_birth_day'] = form.elements['thr_birth_day'].value;
      }

      return datas;
    },
    loadDataTable() {
      if(this.dataTable) {
        this.destroyTable();
      }

      let datas = this.getSearchDatas();

      this.dataTable = this.dataTableRun({
        jQDomName: '.res-dt-table',
        url: this.routes.dataList,
        data: datas,
        initComplete: () => {
          // this.exportExcelDatas = this.dataTable.rows().data();
        },
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
                return this.processesRow(data, row.thr_tc_no);
            },
            "defaultContent": ""
          },
        ],
      });
    },
    exportExcelDatas() {
      let datas = this.getSearchDatas();

      let exportExcelForm = document.forms['export-excel'];
      let length = 0;

      for (const dataKey in datas) {
        length++;
        if (Object.hasOwnProperty.call(datas, dataKey)) {
          const data = datas[dataKey];
          exportExcelForm.elements[dataKey].value = data ?? '';
        }

        if(length == Object.keys(datas).length) {
          exportExcelForm.submit();
        }
      }

      /* $.ajax({
        url: this.routes.exportExcelDatas,
        type: 'POST',
        dataType: 'JSON',
        data: datas,
      })
      .done((res) => {

        // this.setErrors('');
        // this.setSucceed(res.succeed);
        // document.getElementById(this.formIDName).reset();
      })
      .fail((error) => {
        this.setSucceed('');
        // this.setErrors(error.responseJSON.errors);
        if(error.responseJSON) {
          if(error.responseJSON.errors) {
            this.setErrors(error.responseJSON.errors);
          }else if(error.responseJSON.message) {
            this.setErrors(
              {'permissionMessage': [error.responseJSON.message]}
            );
          }
        }
      })
      .then((res) => {
        this.$parent.$parent.dataTable.ajax.reload();
      })
      .always(() => {
        // this.$refs.createExcelFormComponent.getCategory();
        this.formElement.scrollTo(0, 0);
      }); */
    }
  },
  created(){    
    this.setRoutes(this.pproutes);
    this.setErrors(this.pperrors);
    // this.setImgFilters(this.ppimgfilters);
  },
  mounted(){
    this.showModalBody(this.modalSelector);
    this.loadDataTable();

    var tcNo = document.getElementById("tc-no");
    var dateOfBirth = document.getElementById("date-of-birth");
    var email = document.getElementById("thr-email");

    var im = new Inputmask();
    
    im.mask(tcNo);
    im.mask(dateOfBirth);
    im.mask(email);
  },
  components: {
    [formTitleName + '-create-component']: createComponent,
    [formTitleName + '-create-excel-component']: createExcelComponent,
    [formTitleName + '-edit-component']: editComponent,
    [formTitleName + '-show-component']: showComponent,
    [formTitleName + '-delete-component']: deleteComponent,
    [formTitleName + '-create-images-component']: createImagesComponent,
    Treeselect
  }
}
</script>