<template>
<template-component
	:ppTitleName="$t('messages.teachersListManage')"
>
  <table class="res-dt-table table table-striped table-bordered" 
    style="width:100%">
    <thead>
      <tr>
        <th>{{ $t("messages.thr_tc_no") }}</th>
        <th>{{ $t("messages.thr_name") }}</th>
        <th>{{ $t("messages.thr_surname") }}</th>
        <th>{{ $t("messages.thr_career_ladder") }}</th>
        <th>{{ $t("messages.thr_institution") }}</th>
        <th>{{ $t("messages.processes") }}</th>
      </tr>
    </thead>
    <tbody v-html="tbodyHtml"></tbody>
    <tfoot>
      <tr>
        <th colspan="6">
          <button type="button" class="btn btn-primary">
            KAYDET
          </button>
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
      tbodyHtml: this.pptbodyHtml,
    };
  },
  props: {
    pproutes: {
      type: Object,
      required: true,
    },
    pperrors: {
      type: Object,
      required: true,
    },
    pptbodyHtml: {
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
      'setErrors',
      'setEditItem',
    ]),
  },
  created(){
    this.setRoutes(this.pproutes);
    this.setErrors(this.pperrors);
  },
  mounted(){},
  components: {}
}
</script>