<template>
<template-component
	:ppTitleName="$t('messages.teachersListManage')"
>
  <error-msg-list-component></error-msg-list-component>
  <succeed-msg-component></succeed-msg-component>
  
  <table class="res-dt-table table table-striped table-bordered" 
    style="width:100%">
    <thead>
      <tr>
        <th>{{ $t("messages.thr_tc_no") }}</th>
        <th>{{ $t("messages.thr_name") }}</th>
        <th>{{ $t("messages.thr_surname") }}</th>
        <th>{{ $t("messages.thr_province") }}</th>
        <th>{{ $t("messages.thr_town") }}</th>
        <th>{{ $t("messages.thr_email") }}</th>
        <th>{{ $t("messages.thr_mobile_no") }}</th>
        <th>{{ $t("messages.thr_gender") }}</th>
        <th>{{ $t("messages.thr_career_ladder") }}</th>
        <th>{{ $t("messages.thr_degree") }}</th>
        <th>{{ $t("messages.thr_task") }}</th>
        <th>{{ $t("messages.thr_place_of_task") }}</th>
        <th>{{ $t("messages.thr_education_st") }}</th>
        <th>{{ $t("messages.thr_institution") }}</th>
      </tr>
    </thead>
    <tbody v-html="tbodyHtml"></tbody>
    <tfoot>
      <tr>
        <th colspan="14">
          <form :action="this.routes.storeExcel" method="post">
            <!-- <input type="hidden" name="insertArr" v-model="insertArr"> -->
            <input type="hidden" name="_token" :value="token">
            <!-- <input type="hidden" name="updateArr" v-model="updateArr"> -->
            <button type="submit" class="btn btn-primary">
              KAYDET
            </button>
          </form>
        </th>
      </tr>
    </tfoot>
  </table>
  
</template-component>
</template>

<script>
import { mapState, mapMutations } from 'vuex';

let formTitleName = 'preview'

export default {
  name: 'TeachersPreviewComponent',
  data () {
    return {
      modalIDName: 'formModalLong',
      formTitleName,
      tbodyHtml: this.ppdatas.tbodyHtml,
      /* insertArr : this.ppdatas.insertArr,
      updateArr : this.ppdatas.updateArr, */
    };
  },
  props: {
    pproutes: {
      type: Object,
      required: true,
    },
    /* ppsucceed: {
      type: Object,
      required: true,
    }, */
    pperrors: {
      type: Object,
      required: true,
    },
    ppdatas: {
      type: Object,
      required: true,
    }
  },
  computed: {
    ...mapState([
      'routes',
      'errors',
      'token',
    ]),
    cformTitleName: function(){
      return _.capitalize(this.formTitleName);
    },
    componentTitleName: function(){
      return _.capitalize(this.formTitleName) + 'Component';
    },
    modalSelector: function(){
      return '#' + this.modalIDName;
    },
  },
  methods: {
    ...mapMutations([
      'setRoutes',
      'setSucceed',
      'setErrors',
    ]),
  },
  created(){
    this.tbodyHtml += `
      <tr>
        <th colspan="14">...</th>
      </tr>
      <tr>
        <th colspan="14">...</th>
      </tr>
      <tr>
        <th colspan="14">...</th>
      </tr>
    `
    this.setRoutes(this.pproutes);
    this.setSucceed(this.ppdatas.succeed);
    this.setErrors(this.pperrors);
  },
  mounted(){},
  components: {}
}
</script>