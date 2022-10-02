<template>
<div>
  <table class="res-dt-table table table-striped table-bordered" 
    style="width:100%">
    <thead>
      <tr>
        <th>{{ $t("messages.dc_item_status") }}</th>
        <th>{{ $t("messages.dc_number") }}</th>
        <th>{{ $t("messages.dc_subject") }}</th>
        <th>{{ $t("messages.dc_date") }}</th>
        <th>{{ $t("messages.processes") }}</th>
      </tr>
    </thead>
  </table>
</div>

</template>

<script>
export default {
  name: 'CorrespondenceComponent',
  data() {
    return {
      teacher: this.ppteacher,
    }
  },
  props: {
    ppteacher: {
      type: Object,
      required: true,
    }
  },
  mounted() {
    $('.res-dt-table').DataTable({
      data: this.teacher.dc_documents,
      columns: [
        { data: 'dc_item_status' },
        { data: 'dc_number' },
        { data: 'dc_subject' },
        { data: 'dc_date' },
        { data: 'id' },
      ],
  });
  }
}
</script>

<style>

</style>