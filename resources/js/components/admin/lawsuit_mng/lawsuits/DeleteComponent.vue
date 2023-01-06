<template>

<form-modal-component
  :ppmodalinfonames="{
    'titleName': $t('messages.delete'),
    'saveBtnName': $t('messages.delete'),
    'cancelBtnName': $t('messages.cancel'),
  }"
  @saveMethod="deleteData"
>
  <error-msg-list-component></error-msg-list-component>
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
      return this.routes.index + `/${this.datas.id}`;
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
        if(error.responseJSON.message) {
          this.setErrors(
            {'permissionMessage': [error.responseJSON.message]}
          );
        }
      })
      .then((res) => {
        this.$parent.$parent.dataTable.ajax.reload();
        let el = this.$parent.$parent.modalSelector;
        this.$parent.$parent.addLawInfoListItem();
        $(el).modal('hide');
      });
    },
  },
  components: {
    'form-modal-component': formModalComponent,
  }
}
</script>