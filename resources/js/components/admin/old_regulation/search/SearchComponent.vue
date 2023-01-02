<template>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <div class="container-fluid">
      <h2 class="text-center display-4">Uzmanlık Arama</h2>
    </div>
    <!-- /.container-fluid -->
  </section>

  <!-- Main content -->
  <section class="content">
    <div class="container-fluid">
      <form id="search-expert-info" action="#">
        <search-form-component>
          <button
            type="button" 
            class="btn btn-lg btn-default"
            @click="getSearchExpertInfoList"
          >
            <i class="fa fa-search"></i>
            Ara
          </button>
        </search-form-component>  
      </form >
      
      <div class="row mt-3">
        <div class="col-md-10 offset-md-1">
          <error-msg2-component :pperrors=errors ref="errorMsgChildComponent"></error-msg2-component>
        </div>
      </div>

      <div class="row mt-3">
        <div class="col-md-10 offset-md-1">
          <!-- <search-table-component></search-table-component> -->
          <table id="example" class="display" width="100%"></table>
          <search-list-component 
            :ppformId = "'old-first-expert-app'" 
            :ppdatas = datas
            ref="oldFirstExpApp"
          >
          </search-list-component>
          
          <search-list-component 
            :ppformId = "'old-first-expert-app2'" 
            :ppdatas = datas
            ref="oldFirstExpApp2"
          >
          </search-list-component>
          <!-- <search-list-component></search-list-component>
          <search-list-component></search-list-component>
          <search-list-component></search-list-component>
          <search-list-component></search-list-component>
          <search-list-component></search-list-component> -->
        </div>
      </div>
    </div>
  </section>
</div>
</template>

<script>
import searchTableComponent from "./SearchTableComponent.vue";
import searchListComponent from "./SearchListComponent.vue";
import searchFormComponent from "./SearchFormComponent.vue";

export default {
  name: "SearchComponent",
  data () {
    return {
      errors: {},
      datas: this.ppdatas,
    };
  },
  props: {
    ppdatas: {
      type: Object,
      required: false,
    }
  },
  methods: {
    getSearchExpertInfoList: function() {
      let form = $('#search-expert-info');

      $.ajax({
        url: this.datas.getSearchExpertInfoListSrc,
        type: 'POST',
        dataType: 'JSON',
        data: form.serialize(),
      })
      .done((res) => {
        /* this.setErrors('');
        this.setSucceed(res.succeed); */
        this.errors = {};
        this.$refs.oldFirstExpApp.showTable(res.likeOldFirstLike);
        this.$refs.oldFirstExpApp2.showTable(res.likeOldFirstLike);
        /* this.$refs.oldSecondExpApp.showTable(res.oldSecondExpApp);
        this.$refs.allExams.showTable(res.allExams); */
      })
      .fail((error) => {
        if(error.responseJSON)
          this.errors = error.responseJSON.errors;
          // this.$refs.errorMsgChildComponent.setErrors(error.responseJSON.errors);
          // this.setSucceed('');
          // this.setErrors(error.responseJSON.errors);
      })
      .then((res) => {
        // this.$parent.dataTable.ajax.reload();
      })
      .always(() => {
        // this.formElement.scrollTo(0, 0);
      });
    },
  },
  mounted() {
  },
  components: {
    'search-table-component': searchTableComponent,
    'search-list-component': searchListComponent,
    'search-form-component': searchFormComponent,
  }
}
</script>

<style>

</style>