<template>

<form-modal-component
  :ppmodalinfonames="{
    'titleName': $t('messages.delete'),
    'saveBtnName': $t('messages.delete'),
    'cancelBtnName': $t('messages.cancel'),
  }"
  @saveMethod="deleteData"
>
  <span>{{ $t('messages.delete_info') }}</span>
</form-modal-component>

</template>

<script>
import formModalComponent from './FormModalComponent';

import { mapState, mapMutations, mapActions } from 'vuex';

export default {
  name: 'deleteComponent',
  data () {
    return {
      datas: this.ppdatas,
    };
  },
  props: {
    ppdatas: {
      type: Object,
      required: true,
    }
  },
  computed: {
    ...mapState([
      'routes',
    ]),
    deleteUrl: function(){
      return `/admin/document-management/document/${this.datas.id}`;
    },
  },
  methods: {
    ...mapMutations([
      'setErrors',
      'setSucceed',
    ]),

    deleteData: function(){

      $.ajax({
        url: this.deleteUrl,
        type: 'DELETE',
      })
      .fail((error) => {
        console.log(error);
      })
      .then((res) => {
        this.$parent.dataTable.ajax.reload();
        let el = this.$parent.modalSelector;
        $(el).modal('hide');
      });
    },
  },
  components: {
    'form-modal-component': formModalComponent,
  }
}
</script>