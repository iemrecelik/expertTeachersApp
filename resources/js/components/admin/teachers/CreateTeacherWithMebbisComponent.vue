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

    <create-teacher-mebbis-form-component ref="createTeacherWithMebbisFormComponent">
    </create-teacher-mebbis-form-component>
    
    <!-- <input type="hidden" name="_token" :value="token"> -->
  </form>

</form-modal-component>

</template>

<script>
import formModalComponent from './FormModalComponent';
import createTeacherWithMebbisFormComponent from './CreateTeacherWithMebbisFormComponent';

import { mapState, mapMutations } from 'vuex';

export default {
  name: 'CreateTeacherWithMebbisComponent',
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
      // let form = document.getElementById(this.formIDName);
      let form = $('#'+this.formIDName);

      $.ajax({
        url: this.routes.storeWithMebbis,
        type: 'POST',
        dataType: 'JSON',
        data: form.serialize(),
        beforeSend: function () {
          $('.loading-btn').show();
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
        $('.loading-btn').hide();
      });

    },
  },
  components: {
    'form-modal-component': formModalComponent,
    'create-teacher-mebbis-form-component': createTeacherWithMebbisFormComponent,
  }
}
</script>