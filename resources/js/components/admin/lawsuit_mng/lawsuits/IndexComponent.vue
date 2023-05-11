<template>
<multi-section-template-component
	:ppTitleName="$t('messages.lawsuits_list')"
>
  <error-msg-list-component></error-msg-list-component>

  <div class="row mt-3">
    <div class="col-md-12">
      <div class="card">
        <div class="card-body">
          <div class="row">
            <div class="col-2">

              <form :action="routes.lawInfos" method="post">
                <input type="hidden" name="_token" :value="token">
                <div class="row">
                  <div class="col-12">
                    <h5 class="text-center">Bilgi Notu Listesi</h5>

                    <div class="law-info-card-list">
                      <div class="row" :key="key" v-for="(item, key) in addLawInfoListArr">
                        <input type="hidden" name="law_id[]" :value="parseInt(item.id)">
                        <div class="col-10">{{item.label}}</div>
                        <div class="col-2 text-right">
                          <span
                            @click="delSubject(key)"
                          >
                            <i class="bi bi-x-circle-fill delete-list-icon"></i>
                          </span>
                        </div>
                        <hr>
                      </div>

                    </div><!-- /.info-card-list -->
                  </div><!-- /.col-12 -->
                </div>

                <div class="row mt-3">
                  <div class="col-12">
                    <button type="submit" class="btn btn-info bg-gradient-info">Bilgi Notunu Çıkar</button>
                  </div>
                </div>
              </form>

            </div><!-- /.col-2 -->

            <div class="col-10 border-left">
              <form id="lawsuit-search" 
                action="#"
                @submit.prevent
              >
                <div class="row">
                  <div class="col-3">
                    <div class="form-group">
                      <div class="form-group">
                        <label for="addTeacherList">Tc Kimlik No: </label>
                        <treeselect
                          :id="'addTeacherList'"
                          :multiple="true"
                          :async="true"
                          :load-options="loadTeachers"
                          v-model="teacherArr"
                          loadingText="Yükleniyor..."
                          clearAllText="Hepsini sil."
                          clearValueText="Değeri sil."
                          noOptionsText="Hiçbir seçenek yok."
                          noResultsText="Mevcut seçenek yok."
                          searchPromptText="Aramak için yazınız."
                          placeholder="Seçiniz..."
                          name="thr_ids[]"
                        />
                      </div>
                    </div>
                  </div>
                  <div class="col-3">
                    <div class="form-group">
                      <div class="form-group">
                        <label for="unions">{{$t('messages.uns_name')}} :</label>
                        <select class="form-control" id="unions"
                          name="uns_id"
                        >
                          <option selected value="">{{$t('messages.uns_name')}}</option>
                          <option :key="key" v-for="(union,key) in unions" :value="union.id">{{union.uns_name}}</option>
                        </select>
                      </div>
                    </div>
                  </div>
                  <div class="col-3">
                    <div class="form-group">
                      <label for="addTeacherList">Evrak Numarası </label>
                      <treeselect
                        :id="'addTeacherList'"
                        :multiple="true"
                        :async="true"
                        :load-options="loadDcNumbers"
                        loadingText="Yükleniyor..."
                        clearAllText="Hepsini sil."
                        clearValueText="Değeri sil."
                        noOptionsText="Hiçbir seçenek yok."
                        noResultsText="Mevcut seçenek yok."
                        searchPromptText="Aramak için yazınız."
                        placeholder="Seçiniz..."
                        name="dc_ids[]"
                      />
                    </div>
                  </div>
                  <div class="col-3">
                    <div class="form-group">
                      <label>Tarih aralığı:</label>
          
                      <div class="input-group">
                        <div class="input-group-prepend">
                          <span class="input-group-text">
                            <i class="far fa-calendar-alt"></i>
                          </span>
                        </div>
                        <input type="text" 
                          class="form-control float-right" 
                          id="reservation"
                          name="dc_date"
                          autocomplete="off"
                        >
                      </div>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-3">
                    <div class="form-group mb-1">
                      <label for="inputBaseNumber">Dava Esas Numarası</label>
                      <input type="text" class="form-control" id="inputBaseNumber" 
                        aria-describedby="baseNumberHelp" 
                        name="dc_base_number"
                        data-inputmask-regex="^20[0-9]{2}/[0-9]*$" 
                        data-mask
                      >
                    </div>
                  </div>

                  <div class="col-3">
                    <label>Kayıt Edilmiş Tarih Aralığı:</label>
                    
                    <div class="input-group">
                      <div class="input-group-prepend">
                        <span class="input-group-text">
                          <i class="far fa-calendar-alt"></i>
                        </span>
                      </div>
                      <input type="text" 
                        class="form-control float-right reservation" 
                        name="created_at"
                        autocomplete="off"
                      >
                    </div>
                  </div>

                  <div class="col-3">
                    <div class="form-group">
                      <label for="user-list">Kullanıcılar</label>
                      <select class="form-control" 
                        id="user-list"
                        name="user_id"
                      >
                        <option value="">Kullanıcı Seçiniz.</option> 
                        <option v-for="user in datas.users" :value="user.id">
                          {{user.name+' ('+user.email+')'}}
                        </option>
                      </select>
                    </div>
                  </div>
                  
                </div><!-- /.row -->

                <div class="col-2">
                  <div class="form-group text-right">
                    <button type="submit" class="btn btn-primary bg-gradient-primary w-100"
                      @click="loadDataTable"
                    >
                      Ara
                    </button>
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
          <table class="res-dt-table table table-striped table-bordered dt-responsive nowrap" 
            style="width:100%">
            <thead>
              <tr>
                <th>{{ $t("messages.dc_id") }}</th>
                <th>{{ $t("messages.thr_name") }}</th>
                <th>{{ $t("messages.dc_base_number") }}</th>
                <th>{{ $t("messages.law_brief") }}</th>
                <th>{{ $t("messages.dc_date") }}</th>
                <th>{{ $t('messages.record_personel') }}</th>
                <th>{{ $t('messages.updated_personel') }}</th>
                <th data-priority="2">{{ $t("messages.processes") }}</th>
              </tr>
            </thead>
            <tfoot>
              <tr>
                <th colspan="8">
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
            </tfoot>
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
import createComponent from './CreateComponent';
import editComponent from './EditComponent';
import showComponent from './ShowComponent';
import deleteComponent from './DeleteComponent';

import { mapState, mapMutations } from 'vuex';

import Treeselect from '@riophae/vue-treeselect'
import { ASYNC_SEARCH } from '@riophae/vue-treeselect';

const simulateAsyncOperation = fn => {
  setTimeout(fn, 2000)
}

// import the styles
import '@riophae/vue-treeselect/dist/vue-treeselect.css'

let formTitleName = 'lawsuits'

export default {
  // name: this.componentTitleName,
  data () {
    return {
      modalIDName: 'formModalLong',
      formTitleName,
      dataTable: null,
      addLawInfoListArr: [],
      teacherArr: null,
      unions: [],
      ajaxErrorCount: -1,
      datas: this.ppdatas
    };
  },
  props: {
    ppdatas: {
      type: Object,
      required: true,
    },
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
    processesRow: function(id, dcNumber, teacherName){
      let row = '';
      row += this.editBtnHtml(id);
      row += this.deleteBtnHtml(id);
      if(teacherName == null) {
        row += this.addLawInfoBtnHtml(id, dcNumber);
      }

      this.addLawInfoListItem();
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

    addLawInfoBtnHtml: function(id, dcNumber){
      return  `
        <span 
            data-toggle="tooltip" data-placement="top" 
            title="${this.$t('messages.addlawInfoList')}"
          >
          <button type="button" class="btn btn-sm btn-info add-law-info-list"
            data-datas='{
              "id": ${id},
              "label": "${dcNumber}"
            }'
          >
            <i class="bi bi-list-ol"></i>
          </button>
        </span>`;
    },

    addLawInfoListItem() {
      setTimeout(() => {
        let el = $('.add-law-info-list');
        let vueThis = this;

        if(el.length > 0) {
          el.click(function(e) {
            
            let datas = $(this).data('datas');

            let co = 0;
            let exist = false;
            if(vueThis.addLawInfoListArr.length > 0) {
              vueThis.addLawInfoListArr.find((item) => {
                co++;
    
                if(item.id == datas.id) {
                  exist = true;
                }

                if(co == vueThis.addLawInfoListArr.length && !exist) {
                  vueThis.addLawInfoListArr.push(datas);
                }
              });
            }else {
              vueThis.addLawInfoListArr.push(datas);
            }
            
          });
        }else{
          this.addLawInfoListItem()
        }
      }, 100);
    },

    delSubject: function(index) {
      this.addLawInfoListArr.splice(index, 1);
    },

    loadTeachers({ action, searchQuery, callback }) {
      if (action === ASYNC_SEARCH) {
        simulateAsyncOperation(() => {

          if(searchQuery.length > 2) {
            this.getTeachersSearchList(searchQuery, callback);
          }else {
            callback(null, [])    
          }
        })
      }
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
    getTeachersSearchList: function(searchTcNo, callback) {
      $.ajax({
        url: this.routes.getTeachersSearchList,
        type: 'GET',
        dataType: 'JSON',
				data: {'searchTcNo': searchTcNo}
      })
      .done((res) => {
				callback(null, res)
        this.ajaxErrorCount = -1;
      })
      .fail((error) => {
        setTimeout(() => {
          this.ajaxErrorCount++

          if(this.ajaxErrorCount < 3)
            this.getTeachersSearchList(searchTcNo, callback);
          else
            this.ajaxErrorCount = -1;

        }, 100);
        
      })
      .then((res) => {})
		},
    getUnions: function() {
      $.ajax({
        url: this.routes.getUnions,
        type: 'GET',
      })
      .done((res) => {
        this.unions = res;
        this.ajaxErrorCount = -1;
      })
      .fail((error) => {
        setTimeout(() => {
          this.ajaxErrorCount++

          if(this.ajaxErrorCount < 3)
            this.getUnions();
          else
            this.ajaxErrorCount = -1;

        }, 100);
        
      })
      .then((res) => {})
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
      
      // let form = $("#lawsuit-search");
      let datas = [];
      let form = document.getElementById("lawsuit-search");

      if(form.elements['thr_ids[]']) {
        datas['thr_ids'] = [];

        if (form.elements['thr_ids[]'].value == '') {
          for (let i = 0; i < form.elements['thr_ids[]'].length; i++) {
            const element = form.elements['thr_ids[]'][i];
            datas['thr_ids'].push(element.value);
          }  
        }else {
          datas['thr_ids'].push(form.elements['thr_ids[]'].value);
        }
      }

      
      if(form.elements['dc_ids[]']) {
        datas['dc_ids'] = [];

        if (form.elements['dc_ids[]'].value == '') {
          for (let i = 0; i < form.elements['dc_ids[]'].length; i++) {
            const element = form.elements['dc_ids[]'][i];
            datas['dc_ids'].push(element.value);
          }
        }else {
          datas['dc_ids'].push(form.elements['dc_ids[]'].value);
        }
      }
      
      if(form.elements['uns_id']) {
        datas['uns_id'] = form.elements['uns_id'].value;
      }
      
      if(form.elements['dc_date']) {
        datas['dc_date'] = form.elements['dc_date'].value;
      }

      if(form.elements['dc_base_number']) {
        datas['dc_base_number'] = form.elements['dc_base_number'].value;
      }

      if(form.elements['created_at']) {
        datas['created_at'] = form.elements['created_at'].value;
      }

      if(form.elements['user_id']) {
        datas['user_id'] = form.elements['user_id'].value;
      }
      
      this.dataTable = this.dataTableRun({
        jQDomName: '.res-dt-table',
        url: this.routes.dataList,
        /* data: {
          'datas': form.serializeArray(),
        }, */
        data: datas,
        columns: [
          {
            "orderable": false,
            "searchable": false,
            "sortable": false,
            "data": "dc_id",
            "render": ( data, type, row ) => {
              return row.dc_number;
            },
            "defaultContent": ""
          },
          {
            "orderable": false,
            "searchable": false,
            "sortable": false,
            "data": "thr_name",
            "render": ( data, type, row ) => {
              return row.thr_name != null 
                ? '(' + row.thr_tc_no + ') ' + row.thr_name + ' ' + row.thr_surname 
                : row.uns_name;
            },
            "defaultContent": ""
          },
          { "data": "dc_base_number" },
          { "data": "law_brief" },
          { 
            "data": "dc_date",
            "render": (data, type, row) => {
              return this.unixTimestamp(data);
            }
          },
          { "data": "created_by_name" },
          { "data": "updated_by_name" },
          {
            "orderable": false,
            "searchable": false,
            "sortable": false,
            "data": "id",
            "render": ( data, type, row ) => {
                return this.processesRow(
                  data, 
                  row.dc_number, 
                  row.thr_name
                );
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
    this.loadDataTable()
    
    this.showModalBody(this.modalSelector);

    this.getUnions();

    var inputBaseNumber = document.getElementById("inputBaseNumber");
		var im = new Inputmask();
		im.mask(inputBaseNumber);
  },
  components: {
    [formTitleName + '-create-component']: createComponent,
    [formTitleName + '-edit-component']: editComponent,
    [formTitleName + '-show-component']: showComponent,
    [formTitleName + '-delete-component']: deleteComponent,
    Treeselect,
  }
}
</script>

<style scoped>
span > i.delete-list-icon {
    font-size: 16px;
}
div.law-info-card-list {
  border: 1px solid rgb(195 195 195);
  padding: 6px;
}
div.law-info-card-list > div.row {
  margin: 6px 0px 0px 0px;
  border-bottom: 1px solid rgb(195 195 195);
}
tfoot tr th {
  display: table-cell !important;
}
</style>