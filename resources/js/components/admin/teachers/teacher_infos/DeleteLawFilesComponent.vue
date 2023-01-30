<template>
<form-modal-component
  :ppmodalinfonames="{
    'titleName': $t('messages.delete'),
    'saveBtnName': $t('messages.delete'),
    'cancelBtnName': $t('messages.cancel'),
  }"
  @saveMethod="deleteForm"
>
  <form
    @submit.prevent
    :id="formIDName"
    method="post"
  >
    <error-msg-list-component></error-msg-list-component>
    <succeed-msg-component></succeed-msg-component>
    <info-msg-component></info-msg-component>

    <delete-law-files-form-component ref="deleteLawFilesFormComponent">
    </delete-law-files-form-component>
    
    <!-- <input type="hidden" name="_token" :value="token"> -->
  </form>

</form-modal-component>

</template>

<script>
import formModalComponent from './FormModalComponent';
import deleteLawFilesFormComponent from './DeleteLawFilesFormComponent.vue';

import { mapState, mapMutations } from 'vuex';

export default {
  name: 'DeleteLawFilesComponent',
  data () {
    return {
      datas: this.ppdatas,
      formElement: document.getElementById(this.$parent.$parent.modalIDName),
    };
  },
  props: {
    ppdatas: {
      type: Object,
      required: true,
    },
  },
  computed: {
    ...mapState([
      'routes',
      'token'
    ]),
    formIDName: function(){
      return this.uniqueDomID(_.toLower(this.datas.formTitleName));
    },
  },
  methods: {
    ...mapMutations([
      'setErrors',
      'setSucceed',
      'setInfoMsg',
    ]),
    deleteForm: function(){
      $.ajax({
        url: this.routes.addLawFile+'/'+this.datas.id,
        type: 'DELETE',
        dataType: 'JSON',
        cache: false,
      })
      .done((res) => {
        this.setErrors('');
        this.setSucceed(res.succeed);
        this.setInfoMsg(res.infoMsg);
        document.getElementById(this.formIDName).reset();
      })
      .fail((error) => {
        this.setSucceed('');
        this.setInfoMsg('');
        this.setErrors(error.responseJSON.errors);
      })
      .then((res) => {
        this.$parent.removeLawFile(this.datas.id, this.datas.lawKey, this.datas.fileKey);
        
        let el = this.$parent.modalSelector;
        $(el).modal('hide');
      })
      .always(() => {
        // this.$refs.createExcelFormComponent.getCategory();
        // this.formElement.scrollTo(0, 0);
      });

    },
  },
  created() {
  },
  components: {
    'form-modal-component': formModalComponent,
    'delete-law-files-form-component': deleteLawFilesFormComponent,
  }
}
</script>