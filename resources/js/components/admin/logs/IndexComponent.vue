<template>
<template-component
	:ppTitleName="$t('messages.categoryManage')"
>
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">Expandable Table</h3>
        </div>
        <!-- ./card-header -->
        <div class="card-body">
          <table class="table table-bordered table-hover">
            <thead>
              <tr>
                <th>Tarih ve Saat</th>
                <th>Modül</th>
                <th>İşlem</th>
              </tr>
            </thead>
            <tbody>
              <tr data-widget="expandable-table" aria-expanded="false">
                <td>2022-07-12 15:30:19</td>
                <td>Evrak Modülü</td>
                <td>Düzenleme</td>
              </tr>
              <tr class="expandable-body">
                <td colspan="5">
                  <p>
                    Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.
                  </p>
                </td>
              </tr>
              <tr data-widget="expandable-table" aria-expanded="true">
                <td>2022-07-12 15:30:19</td>
                <td>Dava Modülü</td>
                <td>Ekleme</td>
              </tr>
              <tr class="expandable-body">
                <td colspan="5">
                  <p>
                    Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.
                  </p>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <!-- /.card-body -->
      </div>
      <!-- /.card -->
    </div>
  </div>
</template-component>
</template>

<script>
export default {
  name: 'IndexComponent',
  data () {
    return {};
  },
}
</script>

<style>

</style>