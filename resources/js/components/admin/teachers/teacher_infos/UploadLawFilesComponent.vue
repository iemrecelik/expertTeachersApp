<template>
<form-modal-component
  :ppmodalinfonames="{
    'titleName': $t('messages.create'),
    'saveBtnName': $t('messages.save'),
    'cancelBtnName': $t('messages.cancel'),
  }"
  @saveMethod="saveForm"
>
  <form
    @submit.prevent
    :id="formIDName"
    method="post"
  >
    <error-msg-list-component></error-msg-list-component>
    <succeed-msg-component></succeed-msg-component>
    <info-msg-component></info-msg-component>

    <upload-law-files-form-component ref="uploadLawFilesFormComponent">
    </upload-law-files-form-component>
    
    <!-- <input type="hidden" name="_token" :value="token"> -->
  </form>

</form-modal-component>

</template>

<script>
import formModalComponent from './FormModalComponent';
import uploadLawFilesFormComponent from './UploadLawFilesFormComponent';

import { mapState, mapMutations } from 'vuex';

export default {
  name: 'UploadLawFilesComponent',
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
    saveForm: function(){
      const form = document.getElementById(this.formIDName);

      let datas = {
        lawf_file_name: form.elements['lawf_file_name'].value,
        lawf_file_path: this.datas.filePathName,
        dc_id: this.datas.dcId,
        law_id: this.datas.lawId,
      }

      $.ajax({
        url: this.routes.addLawFile,
        type: 'POST',
        dataType: 'JSON',
        cache: false,
        data: datas,
      })
      .done((res) => {
        this.setErrors('');
        this.setSucceed(res.succeed);
        this.setInfoMsg(res.infoMsg);
        this.$parent.addLawfile(res.lawsuitFile, this.datas.lawKey);
        document.getElementById(this.formIDName).reset();
      })
      .fail((error) => {
        this.setSucceed('');
        this.setInfoMsg('');
        this.setErrors(error.responseJSON.errors);
      })
      .then((res) => {
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
    'upload-law-files-form-component': uploadLawFilesFormComponent,
  }
}
</script>