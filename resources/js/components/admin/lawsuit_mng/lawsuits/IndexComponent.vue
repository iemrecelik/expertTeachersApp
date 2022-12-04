<template>
<multi-section-template-component
	:ppTitleName="$t('messages.lawsuits_list')"
>
  <error-msg-list-component></error-msg-list-component>

  <div class="row mt-3">
    <div class="col-md-12">
      <div class="card">
        <div class="card-body">
          <form :action="routes.lawInfos" method="post">
            <input type="hidden" name="_token" :value="token">
            <div class="row">
              <div class="col-2">
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
              </div><!-- /.col-2 -->
              <div class="col-10"></div>
            </div>

            <div class="row mt-3">
              <div class="col-6">
                <button type="submit" class="btn btn-info bg-gradient-info">Bilgi Notunu Çıkar</button>
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
                <th>{{ $t("messages.thr_name") }}</th>
                <th>{{ $t("messages.law_brief") }}</th>
                <th>{{ $t("messages.dc_date") }}</th>
                <th>{{ $t("messages.processes") }}</th>
              </tr>
            </thead>
            <tfoot>
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

let formTitleName = 'lawsuits'

export default {
  // name: this.componentTitleName,
  data () {
    return {
      modalIDName: 'formModalLong',
      formTitleName,
      dataTable: null,
      addLawInfoListArr: [],
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
    processesRow: function(id, dcNumber, teacherName){
      let row = '';
      row += this.editBtnHtml(id);
      row += this.deleteBtnHtml(id);
      if(teacherName == null) {
        row += this.addLawInfoBtnHtml(id, dcNumber);
      }
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
  },
  created(){
    this.setRoutes(this.pproutes);
    this.setErrors(this.pperrors);
  },
  mounted(){
    this.addLawInfoListItem();

    this.showModalBody(this.modalSelector);

    this.dataTable = this.dataTableRun({
      jQDomName: '.res-dt-table',
      url: this.routes.dataList,
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
        { "data": "law_brief" },
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
  },
  components: {
    [formTitleName + '-create-component']: createComponent,
    [formTitleName + '-edit-component']: editComponent,
    [formTitleName + '-show-component']: showComponent,
    [formTitleName + '-delete-component']: deleteComponent,
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
</style>