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
      return `/admin/teachers/infos/del-document-teacher/${this.datas.thrId}/${this.datas.dcId}`;
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
        
        
        this.$parent.teacher

        for (let i = 0; i < this.$parent.teacher.dc_documents.length; i++) {
          const item = this.$parent.teacher.dc_documents[i];

          if(item.id == this.datas.dcId) {
            this.$parent.teacher.dc_documents.splice(i, 1);
            this.$parent.loadDataTable();
          }
        }

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