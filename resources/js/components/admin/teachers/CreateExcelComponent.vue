<template>
<excel-form-modal-component
  :ppmodalinfonames="{
    'titleName': $t('messages.create'),
    'saveBtnName': $t('messages.save'),
    'cancelBtnName': $t('messages.cancel'),
  }"
  @saveMethod="saveForm"
  @previewMethod="previewForm"
>
  <form
    @submit.prevent
    :id="formIDName"
    method="post"
    enctype='multipart/form-data'
  >
    <error-msg-list-component></error-msg-list-component>
    <succeed-msg-component></succeed-msg-component>

    <create-excel-form-component ref="createExcelFormComponent">
    </create-excel-form-component>
    
    <input type="hidden" name="preview" v-model="preview">
    <input type="hidden" name="_token" :value="token">
  </form>

</excel-form-modal-component>

</template>

<script>
import excelFormModalComponent from './ExcelFormModalComponent';
import createExcelFormComponent from './CreateExcelFormComponent';

import { mapState, mapMutations } from 'vuex';

export default {
  name: 'createComponent',
  data () {
    return {
      datas: this.ppdatas,
      formElement: document.getElementById(this.$parent.$parent.modalIDName),
      preview:false,
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
    previewFormSubmit: function(){
      this.preview = true;

      setTimeout(() => {
        let form = document.getElementById(this.formIDName);
        form.action = this.routes.addExcel;
        
        if(this.preview === true) {
          form.submit();
        }else {
          this.previewFormSubmit();
        }
      }, 100);
    },
    previewForm: function(){
      let form = $('#' + this.formIDName);

      let file = document.getElementById('excel-file');
      let data = new FormData();
      
      data.append(file.name, file.files[0]);

      let otherDatas = form.serializeArray();

      otherDatas.forEach(item => {
        data.append(item.name, item.value);
      });

      data.append('preview', true);

      $.ajax({
        url: this.routes.addExcelValidation,
        enctype: 'multipart/form-data',
        type: 'POST',
        dataType: 'JSON',
        processData: false,
        contentType: false,
        cache: false,
        data: data,
      })
      .done((res) => {
        this.previewFormSubmit();
        /* this.setErrors('');
        this.setSucceed(res.succeed);
        document.getElementById(this.formIDName).reset(); */
      })
      .fail((error) => {
        this.setSucceed('');
        this.setInfoMsg('');
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
    saveFormSubmit: function(){
      this.preview = false;

      setTimeout(() => {
        let form = document.getElementById(this.formIDName);
        form.action = this.routes.addExcel;

        if(this.preview === false) {
          form.submit();
        }else {
          this.saveFormSubmit();
        }
      }, 100);
    },
    saveForm: function(){
      let form = $('#' + this.formIDName);

      let file = document.getElementById('excel-file');
      let data = new FormData();
      
      data.append(file.name, file.files[0]);

      let otherDatas = form.serializeArray();

      otherDatas.forEach(item => {
        data.append(item.name, item.value);
      });

      data.append('preview', true);

      $.ajax({
        url: this.routes.addExcelValidation,
        enctype: 'multipart/form-data',
        type: 'POST',
        dataType: 'JSON',
        processData: false,
        contentType: false,
        cache: false,
        data: data,
      })
      .done((res) => {
        this.saveFormSubmit();
        /* this.setErrors('');
        this.setSucceed(res.succeed);
        document.getElementById(this.formIDName).reset(); */
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
    }
  },
  components: {
    'excel-form-modal-component': excelFormModalComponent,
    'create-excel-form-component': createExcelFormComponent,
  }
}
</script>