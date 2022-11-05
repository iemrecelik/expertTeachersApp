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

    <create-excel-form-component ref="createExcelFormComponent">
    </create-excel-form-component>
  </form>

</form-modal-component>

</template>

<script>
import formModalComponent from './FormModalComponent';
import createExcelFormComponent from './CreateExcelFormComponent';

import { mapState, mapMutations } from 'vuex';

export default {
  name: 'createComponent',
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

      let file = document.getElementById('excel-file');
      let data = new FormData();
      
      data.append(file.name, file.files[0]);

      let otherDatas = form.serializeArray();

      otherDatas.forEach(item => {
        data.append(item.name, item.value);
      });

      $.ajax({
        url: this.routes.addExcel,
        enctype: 'multipart/form-data',
        type: 'POST',
        dataType: 'JSON',
        processData: false,
        contentType: false,
        cache: false,
        data: data,
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
        this.$parent.$parent.dataTable.ajax.reload();
      })
      .always(() => {
        // this.$refs.createExcelFormComponent.getCategory();
        this.formElement.scrollTo(0, 0);
      });

    },
  },
  components: {
    'form-modal-component': formModalComponent,
    'create-excel-form-component': createExcelFormComponent,
  }
}
</script>