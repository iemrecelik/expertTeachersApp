<template>
<div class="list-group">
  <!-- /.row -->
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">2006 Başvuruları</h3>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
          <!-- <table id="old-first-expert-app" class="display" width="100%"></table> -->
          <table :id=form class="res-dt-table table table-striped table-bordered" 
          style="width:100%">
            <thead>
              <tr>
                <th>TC NO</th>
                <th>AD</th>
                <th>SOYAD</th>
                <th>ESAS PUAN</th>
                <th>MİNİMUM PUAN</th>
              </tr>
            </thead>
            <tfoot>
              <tr>
                <th colspan="5">
                  <button type="button" class="btn btn-primary"
                  >
                    EKLE
                  </button>
                </th>
              </tr>
            </tfoot>
          </table>
          <!-- <table id="old-first-expert-app" class="table table-hover text-nowrap">
            <thead>
              <tr>
                <th>ID</th>
                <th>User</th>
                <th>Date</th>
                <th>Status</th>
                <th>Reason</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>183</td>
                <td>John Doe</td>
                <td>11-7-2014</td>
                <td><span class="tag tag-success">Approved</span></td>
                <td>Bacon ipsum dolor sit amet salami venison chicken flank fatback doner.</td>
              </tr>
              <tr>
                <td>219</td>
                <td>Alexander Pierce</td>
                <td>11-7-2014</td>
                <td><span class="tag tag-warning">Pending</span></td>
                <td>Bacon ipsum dolor sit amet salami venison chicken flank fatback doner.</td>
              </tr>
            </tbody>
          </table> -->
        </div>
        <!-- /.card-body -->
      </div>
      <!-- /.card -->
    </div>
  </div>
</div>
</template>

<script>
export default {
  name: "SearchListComponent",
  data() {
    return {
      datas: this.ppdatas,
      form: this.ppformId,
      dataTable: undefined,
    }
  },
  props: {
    ppdatas: {
      type: Object,
      required: true,
    },
    ppformId: {
      type: String,
      required: true,
    },
  },
  methods: {
    destroyTable() {
      if (typeof this.dataTable !== 'undefined') {
        this.dataTable.destroy();
        $("#"+this.form+" tbody").empty();
      }
    },
    loadDataTable(vals) {
      this.dataTable = $("#"+this.form).DataTable({
        // jQDomName: '.res-dt-table',
        data: vals.vals,
        columns: vals.cols,
      });
    },
    showTable(vals) {
      this.destroyTable();
      this.loadDataTable(vals);
      /* console.log(this.form);
      console.log(val); */
    }
  },
  mounted() {
    // this.loadDataTable();
  }
}
</script>

<style>

</style>