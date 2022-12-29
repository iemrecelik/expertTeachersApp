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
              <div class="col-2" :key="userKey" v-for="(userVal, userKey) in users">
                <input type="hidden" name="user_id[]" :value="userVal.id">
                
                <div class="form-group">
                  <label v-html="userVal.name"></label>
                  <input type="text" class="form-control" 
                    name="rp_count[]" 
                    :placeholder="userVal.name"
                    :value="userVal.rpCount ?? 0"
                  />
                </div>
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
              <span>{{ dcReportNeededCount }}</span>
            </div>
            <div class="col-4">
              <label>Girilen toplam giden evrak: </label>
              <span>{{ recordedDocumentsCount }}</span>
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
                <th>{{ $t("messages.dc_date") }}</th>
              </tr>
            </thead>
            <!-- <tfoot>
              <tr>
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
              </tr>
            </tfoot> -->
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
import { mapState, mapMutations } from 'vuex';

let formTitleName = 'lawsuits'

export default {
  name: 'IndexComponent',
  data () {
    return {
      modalIDName: 'formModalLong',
      formTitleName,
      dataTable: null,
      ajaxErrorCount: -1,
      datas: this.ppdatas,
      formIDName: 'save-document-record-count',
      dcReportNeededCount: 0,
      recordedDocumentsCount: this.ppdatas.sum,
      users: this.ppdatas.users
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
      'setSucceed',
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
    loadReportCountOnDate() {
      
      setTimeout(() => {
        $.ajax({
          url: this.routes.getReportCountOnDate,
          type: 'GET',
          dataType: 'JSON',
          data: {
            date: document.getElementById('reservationdate').value
          },
        })
        .done((res) => {
          this.dcReportNeededCount = res.sum;
          
          this.users = [];

          res.users.forEach(user => {
            this.users.push(user);
          });          
          
          this.ajaxErrorCount = -1;
        })
        .fail((error) => {
          setTimeout(() => {
            this.ajaxErrorCount++
            if(this.ajaxErrorCount < 3)
              this.loadReportCountOnDate();
            else
              this.ajaxErrorCount = -1;
          }, 100);
        })
        .then((res) => {})
        .always(() => {});  
      }, 100);
    },
    loadDataTable() {
      if(this.dataTable) {
        this.destroyTable();
      }
      
      let rpDate = document.getElementById('reservationdate').value;
      
      this.dataTable = this.dataTableRun({
        jQDomName: '.res-dt-table',
        url: this.routes.getDocumentOnDate,
        method: 'GET',
        /* data: {
          'datas': form.serializeArray(),
        }, */
        data: {rpDate},
        columns: [
          { "data": "dc_number" },
          { 
            "data": "dc_date",
            "render": (data, type, row) => {
              return this.unixTimestamp(data);
            }
          },
          { 
            "data": "user_name",
            "render": ( data, type, row ) => {
              return data;
            },
          }
        ],
        initComplete: (settings, json) => {
          this.recordedDocumentsCount = json.recordsFiltered;
        }
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
        this.dcReportNeededCount = res.sum;
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
    // this.showModalBody(this.modalSelector);
    //Date picker
    var today = new Date();
    var dd = String(today.getDate()).padStart(2, '0');
    var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
    var yyyy = today.getFullYear();

    today = dd + '.' + mm + '.' + yyyy;
    $('#reservationdate').datepicker();
    $('#reservationdate').val(today);
    
    $('#reservationdate').change(() => {
      this.loadDataTable();
      this.loadReportCountOnDate();
    });

    const loadDataTableInterval = setInterval(() => {
      if(document.getElementById('reservationdate').value) {
        this.loadDataTable();
        clearInterval(loadDataTableInterval);
      }
    }, 100);
  },
}
</script>

<style scoped>
</style>