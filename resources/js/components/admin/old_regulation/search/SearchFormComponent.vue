<template>
<div class="row">
  <div class="col-md-10 offset-md-1">
    <div class="row">
      
      <div class="col-md-4">
        <div class="input-group input-group-lg">
          <input type="search" class="form-control form-control-lg" name="tc_num" placeholder="Buraya Tc kimlik no giriniz" value="24177137609">
          <div class="input-group-append">
            <!-- <button type="submit" class="btn btn-lg btn-default">
              <i class="fa fa-search"></i>
            </button> -->
          </div>
        </div>
      </div>
      
      <div class="col-md-4">
        <div class="input-group input-group-lg">
          <input type="text" class="form-control form-control-lg" name="name" placeholder="Buraya ismi giriniz" value="">
          <div class="input-group-append">
            <!--  <button type="submit" class="btn btn-lg btn-default">
              <i class="fa fa-search"></i>
            </button> -->
          </div>
        </div>
      </div>
      
      <div class="col-md-4">
        <div class="input-group input-group-lg">
          <input type="text" class="form-control form-control-lg" name="surname" placeholder="Buraya soyad giriniz" value="">
          <div class="input-group-append">
            <slot></slot>
          </div>
        </div>
      </div>

    </div>

  </div>
</div>
</template>

<script>
export default {
  name: "SearchFormComponent",
}
</script>

<style>

</style>