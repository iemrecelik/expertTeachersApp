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
    ]),
    previewForm: function(){
      this.preview = true;

      setTimeout(() => {
        let form = document.getElementById(this.formIDName);
        form.action = this.routes.addExcel;
        
        if(this.preview === true) {
          form.submit();
        }else {
          this.previewForm();
        }
      }, 100);
    },
    saveForm: function(){
      this.preview = false;

      setTimeout(() => {
        let form = document.getElementById(this.formIDName);
        form.action = this.routes.addExcel;

        if(this.preview === false) {
          form.submit();
        }else {
          this.saveForm();
        }
      }, 100);
    }
    /* saveForm: function(){
      let form = $('#' + this.formIDName);

      let file = document.getElementById('excel-file');
      let data = new FormData();
      
      data.append(file.name, file.files[0]);

      let otherDatas = form.serializeArray();

      otherDatas.forEach(item => {
        data.append(item.name, item.value);
      });

      data.append('preview', false);

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
        url: this.routes.addExcel,
        enctype: 'multipart/form-data',
        type: 'POST',
        dataType: 'JSON',
        processData: false,
        contentType: false,
        cache: false,
        data: data,
        beforeSend: function( xhr ) {
        }
      })
      .done((res) => {
        // this.setErrors('');
        // this.setSucceed(res.succeed);
        // document.getElementById(this.formIDName).reset();
        // location.href = this.routes.preview;
        console.log('sadasdas');
        window.open(this.routes.preview, '_blank');
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
    }, */
  },
  components: {
    'excel-form-modal-component': excelFormModalComponent,
    'create-excel-form-component': createExcelFormComponent,
  }
}
</script>