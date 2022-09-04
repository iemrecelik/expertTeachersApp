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
  >
    <error-msg-list-component></error-msg-list-component>
    <succeed-msg-component></succeed-msg-component>

    <add-comment-form-component 
      ref="addCommentFormComponent"
      :ppdatas=this.datas
    >
    </add-comment-form-component>
  </form>

</form-modal-component>

</template>

<script>
import formModalComponent from './FormModalComponent';
import addCommentFormComponent from './AddCommentFormComponent';

import { mapState, mapMutations } from 'vuex';

export default {
  name: 'AddCommentComponent',
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
    ]),
    formIDName: function(){
      return this.uniqueDomID(_.toLower(this.datas.formTitleName));
    },
  },
  methods: {
    ...mapMutations([
      'setErrors',
      'setSucceed',
    ]),
    saveForm: function(){
      let form = $('#' + this.formIDName);

      $.ajax({
        url: this.routes.addComment,
        type: 'POST',
        dataType: 'JSON',
        data: form.serialize(),
      })
      .done((res) => {
        this.setErrors('');
        this.setSucceed(res.succeed);
        document.getElementById(this.formIDName).reset();
      })
      .fail((error) => {
        this.setSucceed('');
        this.setErrors(error.responseJSON.errors);
      })
      .then((res) => {
        // this.$parent.$parent.dataTable.ajax.reload();
      })
      .always(() => {
        this.$refs.addCommentFormComponent.getComments();
        this.formElement.scrollTo(0, 0);
      });

    },
  },
  components: {
    'form-modal-component': formModalComponent,
    'add-comment-form-component': addCommentFormComponent,
  }
}
</script>