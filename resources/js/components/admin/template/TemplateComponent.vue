<template>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <div class="container-fluid">
      <h2 class="text-center display-4">{{ $t("messages.doc_mng_category") }}</h2>
    </div>
    <!-- /.container-fluid -->
  </section>

  <!-- Main content -->
  <section class="content">
    <div class="container-fluid">
      <div class="row mt-3">
        <div class="col-md-12">
          <div class="card">
            <div class="card-body">
              <slot></slot>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>
</template>

<script>
export default {
  name: 'TemplateComponent'
}
</script>

<style>

</style>