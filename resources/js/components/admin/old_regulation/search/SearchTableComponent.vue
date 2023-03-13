<template>
<table class="res-dt-table table table-striped table-bordered" 
  style="width:100%">
  <thead>
    <tr>
      <!-- <th>{{ $t("messages.bks_lang_name") }}</th>
      <th>{{ $t("messages.bks_lang_subject") }}</th>
      <th>{{ $t("messages.bks_edition") }}</th>
      <th>{{ $t("messages.bks_salary") }}</th>
      <th>{{ $t("messages.bks_start_date") }}</th> -->
      <th>messages.bks_lang_name</th>
      <th>messages.bks_lang_subject</th>
      <th>messages.bks_edition</th>
      <th>messages.bks_salary</th>
      <th>messages.bks_start_date</th>
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
export default {
  name: "SearchTableComponent",

  mounted(){

    this.dataTable = this.dataTableRun({
      jQDomName: '.res-dt-table',
      url: '/admin/eski-yonetmelik/uzman-arama-list',
      columns: [
        { "data": "bks_lang_name" },
        { "data": "bks_lang_subject" },
        { "data": "bks_edition" },
        { "data": "bks_salary" },
        { 
          "data": "bks_start_date",
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
}
</script>

<style>

</style>