<template>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <div class="container-fluid">
      <h2 class="text-center display-4">Evrak Arama ve Listeleme</h2>
    </div>
    <!-- /.container-fluid -->
  </section>

  <!-- Main content -->
  <section class="content">
    <div class="container-fluid">
      <form id="search-document-form" action="#">
        <search-form-component>
          <button
            type="button" 
            class="btn btn-info"
            @click="getSearchDocuments"
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
            :ppformId = "'search-document-form'" 
            ref="searchListComponent"
          >
            <!-- <search-table-component></search-table-component> -->
          </search-list-component>
        </div>
      </div>
    </div>
  </section>
</div>
</template>

<script>
// import searchTableComponent from "./SearchTableComponent.vue";
import searchListComponent from "./SearchListComponent.vue";
import searchFormComponent from "./SearchFormComponent.vue";

import { mapState, mapMutations } from 'vuex';

export default {
  name: "SearchComponent",
  data () {
    return {
      errors: {},
    };
  },
  props: {
    /* ppdatas: {
      type: Object,
      required: false,
    }, */
    pproutes: {
      type: Object,
      required: true,
    },
  },
  computed: {
    ...mapState([
      'formModalBody',
      'routes',
      'token',
    ]),
  },
  methods: {
    ...mapMutations([
      'setRoutes',
      'setErrors',
      'setEditItem',
    ]),
    getSearchDocuments: function() {
      this.$refs.searchListComponent.showTable();
      /* let form = $('#search-expert-info');

      $.ajax({
        url: this.routes.getSearchDocuments,
        type: 'POST',
        dataType: 'JSON',
        data: form.serialize(),
      })
      .done((res) => {
        this.errors = {};
        this.$refs.searchListComponent.showTable(res.likeOldFirstLike);
      })
      .fail((error) => {
        if(error.responseJSON)
          this.errors = error.responseJSON.errors;
      })
      .then((res) => {
        this.$parent.dataTable.ajax.reload();
      })
      .always(() => {
        this.formElement.scrollTo(0, 0);
      }); */
    },
  },
  created() {
    this.setRoutes(this.pproutes);
    this.setErrors(this.pperrors);
  },
  mounted() {
  },
  components: {
    // 'search-table-component': searchTableComponent,
    'search-list-component': searchListComponent,
    'search-form-component': searchFormComponent,
  }
}
</script>

<style>

</style>