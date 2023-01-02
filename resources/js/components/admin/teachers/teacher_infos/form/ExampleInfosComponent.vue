<template>
<!-- left column -->
<div class="col-md-12">
  <!-- general form elements -->
  <div class="card card-primary">
    <div class="card-header">
      <h3 class="card-title">Sınav Bilgileri</h3>
    </div>
    <!-- /.card-header -->
    <!-- form start -->
    <form>
      <div class="card-body">
        <div class="row">
          <div class="col-6">
            <div class="form-group">
              <label>Sınav Durumu</label>
              <select @change="changeDisabled($event)" class="custom-select">
                <option value="1">Girdi</option>
                <option value="2">Girmedi</option>
              </select>
            </div>
          </div>
        </div>
        
        <div class="row">
          <div class="col-6">
            <div class="form-group">
              <label for="exampleInputPassword1">Puanı</label>
              <input type="text" 
                :disabled="disabled"
                class="form-control" 
                placeholder="Aldığı Puanı Giriniz."
              >
            </div>
          </div>
        </div>
      </div>
      <!-- /.card-body -->

      <div class="card-footer">
        <button type="submit" class="btn btn-primary">Kaydet</button>
      </div>
    </form>
  </div>
  <!-- /.card -->
</div>
</template>

<script>
export default {
  name: "ExampleInfosComponent",
  data() {
    return  {
      disabled: false,
    }
  },
  methods: {
    changeDisabled(event) {
      this.disabled = event.target.value != 1?true:false;
    }
  }
}
</script>

<style>

</style>