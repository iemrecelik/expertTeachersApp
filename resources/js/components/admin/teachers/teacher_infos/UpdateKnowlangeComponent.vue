<template>
<div class="row">
  <div class="row">
    <div class="col-4">
      <select @change="changeForm($event)" class="form-select" aria-label="Default select example">
        <option selected>Düzenlenecek Alanı Seçiniz</option>
        <option value="1">Sınav Bilgileri</option>
        <option value="2">2006 Başvuru</option>
        <option value="3">2011 Başvuru</option>
        <option value="4">Sertifika İade Durumu</option>
        <option value="5">Mahkeme Kararı</option>
      </select>
    </div>
    <div class="col-4"></div>
    <div class="col-4"></div>
  </div>

  <div class="row mt-3">
    <example-infos-component v-if="selectedForm == 1"></example-infos-component>
  </div>
  
</div>
</template>

<script>
import ExampleInfosComponent from "./form/ExampleInfosComponent.vue";

export default {
  name: "UpdateKnowlangeComponent",
  data() {
    return {
      selectedForm: 0,
    }
  },
  methods: {
    changeForm: function(event) {
      // console.log(event.target.value)
      this.selectedForm = event.target.value;
    }
  },
  components: {
    "example-infos-component": ExampleInfosComponent,
  }
}
</script>

<style>

</style>