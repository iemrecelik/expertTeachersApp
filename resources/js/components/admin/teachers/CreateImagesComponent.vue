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
    enctype='multipart/form-data'
  >
    <error-msg-list-component></error-msg-list-component>
    <succeed-msg-component></succeed-msg-component>
    <info-msg-component></info-msg-component>

    <create-images-form-component ref="createImagesFormComponent">
    </create-images-form-component>
    
    <!-- <input type="hidden" name="_token" :value="token"> -->
  </form>

</form-modal-component>

</template>

<script>
import formModalComponent from './FormModalComponent';
import createImagesFormComponent from './CreateImagesFormComponent';

import { mapState, mapMutations } from 'vuex';

export default {
  name: 'createImagesComponent',
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
      let file = document.getElementById('images-file');
      let data = new FormData();

      for (let i = 0; i < file.files.length; i++) {
        data.append('images_file[]', file.files[i]);
      }

      $.ajax({
        url: this.routes.storeImages,
        enctype: 'multipart/form-data',
        type: 'POST',
        dataType: 'JSON',
        processData: false,
        contentType: false,
        cache: false,
        data: data,
        beforeSend: function () {
          $('.images-loading').show();
        }
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
        if(error.responseJSON) {
          if(error.responseJSON.errors) {
            this.setErrors(error.responseJSON.errors);
          }else if(error.responseJSON.message) {
            this.setErrors(
              {'permissionMessage': [error.responseJSON.message]}
            );
          }
        }
        // this.setErrors(error.responseJSON.errors);
      })
      .then((res) => {
        this.$parent.$parent.dataTable.ajax.reload();
      })
      .always(() => {
        // this.$refs.createExcelFormComponent.getCategory();
        this.formElement.scrollTo(0, 0);
        $('.images-loading').hide();
      });

    },
  },
  components: {
    'form-modal-component': formModalComponent,
    'create-images-form-component': createImagesFormComponent,
  }
}
</script>