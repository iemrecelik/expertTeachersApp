<template>
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
</template>

<script>
import { mapState } from 'vuex';

export default {
  name: "SearchTableComponent",
  data() {
    return {
      dataTable: undefined,
    }
  },
  computed: {
    ...mapState([
      'formModalBody',
      'routes',
      'token',
    ]),
  },
  mounted(){
    setTimeout(() => {
      this.dataTable = this.dataTableRun({
        jQDomName: '.res-dt-table',
        url: this.routes.getSearchDocuments,
        columns: [
          { "data": "dc_cat_name" },
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
    }, 3000);
    
  },
}
</script>

<style>

</style>