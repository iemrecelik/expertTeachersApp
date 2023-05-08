<template>
<template-component
	:ppTitleName="$t('messages.categoryManage')"
>
  <table class="res-dt-table table table-striped table-bordered" 
  style="width:100%">
    <thead>
      <tr>
        <th>{{ $t("messages.dc_cat_name") }}</th>
        <th>{{ $t("messages.dc_up_cat_name") }}</th>
        <th>{{ $t("messages.order") }}</th>
        <th>{{ $t("messages.processes") }}</th>
      </tr>
    </thead>
    <tfoot>
      <tr>
        <th colspan="4">
          <button type="button" class="btn btn-primary"
            data-toggle="modal" 
            :data-target="modalSelector"
            :data-datas='`{"formTitleName": "\${formTitleName}"}`'
            :data-component="`${formTitleName}-create-component`"
          >
            {{ $t('messages.add') }}
          </button>
        </th>
      </tr>
    </tfoot>
  </table>

  <!-- Modal -->
  <div class="modal fade" tabindex="-1" role="dialog" 
    aria-labelledby="formModalLongTitle" aria-hidden="true"
    data-backdrop="static" :id="modalIDName"
  >
    <div class="modal-dialog" role="document">
      <div class="modal-body">
        <component
          v-if="formModalBody.show"
          :is="formModalBody.component"
          :ppdatas="formModalBody.datas"
        >
        </component>
      </div>
    </div>
  </div>
  
</template-component>
</template>

<script>
import createComponent from './CreateComponent';
import editComponent from './EditComponent';
import showComponent from './ShowComponent';
import deleteComponent from './DeleteComponent';
// import imagesComponent from './ImagesComponent';

import { mapState, mapMutations } from 'vuex';

let formTitleName = 'dc-category'

export default {
  // name: this.componentTitleName,
  data () {
    return {
      modalIDName: 'formModalLong',
      formTitleName,
      dataTable: null,
      Toast: Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
          toast.addEventListener('mouseenter', Swal.stopTimer)
          toast.addEventListener('mouseleave', Swal.resumeTimer)
        }
      })
    };
  },
  props: {
    pproutes: {
      type: Object,
      required: true,
    },
    pperrors: {
      type: Object,
      required: true,
    },
    ppimgfilters: {
      type: Object,
      required: false,
    },
  },
  computed: {
    ...mapState([
      'formModalBody',
      'routes',
      'errors',
      'token',
      'imgFilters',
    ]),
    cformTitleName: function(){
      return _.capitalize(this.formTitleName);
    },
    componentTitleName: function(){
      return _.capitalize(this.formTitleName) + 'Component';
    },
    modalSelector: function(){
      return '#' + this.modalIDName;
    },
  },
  methods: {
    ...mapMutations([
      'setRoutes',
      'setErrors',
      'setEditItem',
      'setImgFilters',
    ]),
    triggerUpdateOrder: function(){
      let updateOrderElements = document.querySelectorAll("button.update-order-submit");

      if(updateOrderElements.length < 1) {
        setTimeout(() => {
          this.triggerUpdateOrder();
        }, 300);
      }

      for (var i = 0; i < updateOrderElements.length; i++) {
        updateOrderElements[i].addEventListener(
          'click', 
          (event) => {
            let element = event.target;
            element = element.closest('div.row').querySelector('form.update-order-form');

            let formData = new FormData(element);
            let datas = Object.fromEntries(formData.entries());

            this.updateOrder(datas);
          },
          false
        );
      }
    },
    updateOrder: function(datas) {
      $.ajax({
        url: this.routes.updateOrder,
        type: 'PUT',
        dataType: 'JSON',
        data: datas
      })
      .done((res) => {
        this.Toast.fire({
          icon: 'success',
          title: 'Kategorinin sırası kaydedildi.'
        });
      })
      .fail((error) => {
        let htmlItems = '';
        for (const key in error.responseJSON.errors) {
          if (Object.hasOwnProperty.call(error.responseJSON.errors, key)) {
            htmlItems += '<li>'+error.responseJSON.errors[key]+'</li>';
          }
        }

        let titleHtml = `
          <ol>
            ${htmlItems}
          </ol>
        `
        this.Toast.fire({
          icon: 'error',
          html: titleHtml
        })
      })
      .then((res) => {})
      .always(() => {});
    },
    orderProcess: function(orderNumber, id) {
      orderNumber = orderNumber ?? '';

      return `
        <div class="row">
          <div class="col-4">
            <form class="update-order-form">
              <input class="form-control" type="text" name="dc_order" value="${orderNumber}"/>
              <input class="form-control" type="hidden" name="id" value="${id}"/>
            </form>
          </div>
          <div class="col-4">
            <span 
              data-toggle="tooltip" data-placement="top" 
              title="${this.$t('messages.edit_order')}"
            >
              <button type="button" class="btn btn-sm btn-primary update-order-submit">
                <i class="bi bi-pencil-square"></i>
              </button>
            </span>
          </div>
        </div>
      `;
    },
    processesRow: function(id){
      let row = '';
      row += this.editBtnHtml(id);
      row += this.deleteBtnHtml(id);
      // row += this.imageBtnHtml(id);
      return row;
    },
    
    editBtnHtml: function(id){
      return  `
        <span 
          data-toggle="tooltip" data-placement="top" 
          title="${this.$t('messages.edit')}"
        >
          <button type="button" class="btn btn-sm btn-success"
            data-toggle="modal" data-target="${this.modalSelector}"
            data-component="${this.formTitleName}-edit-component" 
            data-datas='{
              "id": ${id},
              "formTitleName": "${this.formTitleName}"
            }'
          >
            <i class="bi bi-pencil-square"></i>
          </button>
        </span>`;
    },

    deleteBtnHtml: function(id){
      return  `
        <span 
            data-toggle="tooltip" data-placement="top" 
            title="${this.$t('messages.delete')}"
          >
          <button type="button" class="btn btn-sm btn-danger"
            data-toggle="modal" data-target="${this.modalSelector}"
            data-component="${this.formTitleName}-delete-component" 
            data-datas='{
              "id": ${id},
              "formTitleName": "${this.formTitleName}"
            }'
          >
            <i class="bi bi-trash"></i>
          </button>
        </span>`;
    },
    
    imageBtnHtml: function(id){
      return  `
        <span 
            data-toggle="tooltip" data-placement="top" 
            title="${this.$t('messages.image')}"
          >
          <button type="button" class="btn btn-sm btn-info"
            data-toggle="modal" data-target="${this.modalSelector}"
            data-component="${this.formTitleName}-images-component" 
            data-datas='{
              "id": ${id},
              "formTitleName": "${this.formTitleName}"
            }'
          >
            <i class="bi bi-image"></i>
          </button>
        </span>`;
    }
  },
  created(){
    this.setRoutes(this.pproutes);
    this.setErrors(this.pperrors);
    // this.setImgFilters(this.ppimgfilters);
  },
  mounted(){
    this.showModalBody(this.modalSelector);

    this.dataTable = this.dataTableRun({
      jQDomName: '.res-dt-table',
      url: this.routes.dataList,
      columns: [
        { "data": "dc_cat_name" },
        { 
          "data": "dc_cat_id",
          "render": ( data, type, row ) => {
              return row.dc_up_cat_name;
          },
        },
        { 
          "data": "dc_order",
          "render": ( data, type, row ) => {
              return this.orderProcess(data, row.id);
          },
        },
        /* { 
          "data": "bks_start_date",
          "render": (data, type, row) => {
            return this.unixTimestamp(data);
          }
        }, */
        {
          "orderable": false,
          "searchable": false,
          "sortable": false,
          "data": "id",
          "render": ( data, type, row ) => {
              return this.processesRow(data);
          },
          "defaultContent": ""
        },
      ],
      order: [[2, 'asc']],
      drawCallback: () => {
        this.triggerUpdateOrder();
      }
    });
  },
  components: {
    [formTitleName + '-create-component']: createComponent,
    [formTitleName + '-edit-component']: editComponent,
    [formTitleName + '-show-component']: showComponent,
    [formTitleName + '-delete-component']: deleteComponent,
    // [formTitleName + '-images-component']: imagesComponent,
  }
}
</script>