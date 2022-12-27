<template>
<multi-section-template-component
	:ppTitleName="$t('messages.document_record_list')"
>
  <error-msg-list-component></error-msg-list-component>

  <div class="row mt-3">
    <div class="col-md-12">
      <div class="card">
        <div class="card-body">
          <form 
            @submit.prevent
            id="save-document-record-count"
          >
            <div class="row">
              <div class="col-3">
                <!-- Date -->
                <div class="form-group">
                  <label>Tarih:</label>
                  <div class="input-group date">
                    <input type="text" 
                      id="reservationdate" 
                      class="form-control datetimepicker-input"
                      name="rp_date"
                    />
                    <div class="input-group-append">
                      <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-2" v-for="(userVal, userKey) in datas.users">
                <input type="hidden" name="user_id[]">
                <form-form-component
                  :ppsettings="{
                    type: 'text', 
                    fieldName: 'rp_count[]', 
                    ppfieldLabelName: userVal.name,
                    value: oldValue('rp_count')
                  }"
                >
                </form-form-component>
              </div>
            </div><!-- /.row -->

            <div class="row">
              <div class="col-2">
                <button type="button" class="btn btn-primary"
                  @click="saveDocumentRecordCount"
                >
                  {{ $t('messages.add') }}
                </button>
              </div>
            </div>
          </form>
          
          <div class="row mt-2">
            <div class="col-4">
              <label>Girilmesi gereken toplam giden evrak: </label>
              <span>0</span>
            </div>
            <div class="col-4">
              <label>Girilen toplam giden evrak: </label>
              <span>0</span>
            </div>
          </div>
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
                <!-- <th>{{ $t("messages.processes") }}</th> -->
              </tr>
            </thead>
            <tfoot>
              <tr>
                <th colspan="4">
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
/* import createComponent from './CreateComponent';
import editComponent from './EditComponent';
import showComponent from './ShowComponent';
import deleteComponent from './DeleteComponent';
 */
import { mapState, mapMutations } from 'vuex';


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
      ajaxErrorCount: -1,
      datas: this.ppdatas,
      formIDName: 'save-document-record-count'
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
    saveDocumentRecordCount: function(){
      let form = $('#'+this.formIDName);

      $.ajax({
        url: this.routes.saveDocumentRecordCount,
        type: 'POST',
        dataType: 'JSON',
        data: form.serialize(),
      })
      .done((res) => {
        this.setErrors('');
        this.setSucceed(res.succeed);
        // document.getElementById(this.formIDName).reset();
      })
      .fail((error) => {
        this.setSucceed('');
        this.setErrors(error.responseJSON.errors);
      })
      .then((res) => {
        // this.$parent.$parent.dataTable.ajax.reload();
      })
      .always(() => {});

    },
  },
  created(){
    this.setRoutes(this.pproutes);
    this.setErrors(this.pperrors);
  },
  mounted(){
    // this.loadDataTable()
    
    // this.showModalBody(this.modalSelector);
    
    //Date picker
    var today = new Date();
    var dd = String(today.getDate()).padStart(2, '0');
    var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
    var yyyy = today.getFullYear();

    today = dd + '.' + mm + '.' + yyyy;
    $('#reservationdate').datepicker();
    $('#reservationdate').val(today);
  },
  /* components: {
    [formTitleName + '-create-component']: createComponent,
    [formTitleName + '-edit-component']: editComponent,
    [formTitleName + '-show-component']: showComponent,
    [formTitleName + '-delete-component']: deleteComponent,
    Treeselect,
  } */
}
</script>

<style scoped>
</style>