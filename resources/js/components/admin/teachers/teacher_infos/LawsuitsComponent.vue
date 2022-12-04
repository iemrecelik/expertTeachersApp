<template>
<div class="card">
  <div class="card-header">
    <h3 class="card-title">Açılan Davalar:</h3>
  </div>
  <!-- /.card-header -->
  <div class="card-body">
    <!-- we are adding the accordion ID so Bootstrap's collapse plugin detects it -->
    <div id="accordion">

      <div class="card card-danger" :key="lawKey" v-for="(lawsuit, lawKey) in teacher.lawsuits">
        <div class="card-header">
          <h4 class="card-title w-100">
            <a class="d-block w-100" data-toggle="collapse" :href="'#collapseOne'+lawKey">
              #{{(lawKey+1)}}
            </a>
          </h4>
        </div>
        <div :id="'collapseOne'+lawKey" :class="lawKey < 1 ? 'collapse show' : 'collapse'" data-parent="#accordion">
          <div class="card-body">

            <div class="row">
              <div class="col-12">
                <label>Dava Konu(ları)su;</label>
                <ul :key="key" v-for="(subject, key) in lawsuit.subjects">
                  <li v-html="subject.sub_description"></li>
                </ul>
              </div>
            </div>
            
            <div class="row">
              <div class="col-3">
                <label>1) Yazı Sayısı: </label> {{lawsuit.dc_document.dc_number}}
              </div>
              <div class="col-3">
                <label>Durumu: </label> {{itemStatus(lawsuit.dc_document.dc_item_status)}}
              </div>
              <div class="col-3">
                <label>Tarihi: </label> {{lawsuit.dc_document.dc_date}}
              </div>
              <div class="col-3">
                <a  type="button" class="btn btn-sm btn-primary"
                  data-file-download
                  :href="'/storage/upload/images/raw'+ lawsuit.dc_document.dc_files.dc_file_path"
                  download
                >
                  İndir
                </a>
              </div>
            </div>
            <div class="row">
              <div class="col-12">
                <ul class="attach-files-list">
                  <li :key="key" v-for="(attcFiles, key) in lawsuit.dc_document.dc_attach_files">
                    
                    <a target="_blank" :href="'/storage/upload/images/raw'+attcFiles.dc_att_file_path">
                      {{attcFiles.dc_att_file_path | getFileNameInPath}}
                    </a>
                    
                    <!-- <a class="float-right" href="#">Dava Dosyasına Ekle</a> -->

                    <a v-if="fileInLawFiles(attcFiles.dc_att_file_path, lawsuit.lawsuit_files)" class="float-right" href="#" 
                      @click="addLawfile(
                        { 
                          lawId: lawsuit.id, 
                          dcId: lawsuit.dc_document.id, 
                          formTitleName, 
                          filePathName: attcFiles.dc_att_file_path,
                          lawKey
                        }
                      )"
                    >
                      Dava Dosyasına Ekle
                    </a>
                  </li>
                </ul>
              </div>
            </div>

            <div class="row" :key="key" v-for="(dc_doc,key) in lawsuit.dc_documents">
              <div class="col-12">
                <div class="row p-0">
                  <div class="col-3">
                    <label>{{key+2}}) Yazı Sayısı: </label> {{dc_doc.dc_number}}
                  </div>
                  <div class="col-3">
                    <label>Durumu: </label> {{itemStatus(dc_doc.dc_item_status)}}
                  </div>
                  <div class="col-3">
                    <label>Tarihi: </label> {{dc_doc.dc_date}}
                  </div>
                  <div class="col-3">
                    <a  type="button" class="btn btn-sm btn-primary"
                      data-file-download
                      :href="'/storage/upload/images/raw'+ dc_doc.dc_files.dc_file_path"
                      download
                    >
                      İndir
                    </a>
                  </div>
                </div>
              </div>
              
              <div class="col-12">
                <div class="row p-0">
                  <div class="col-12">
                    <ul class="attach-files-list">
                      <li :key="key" v-for="(downAttcFiles, key) in dc_doc.dc_attach_files">
                        <a target="_blank" :href="'/storage/upload/images/raw'+downAttcFiles.dc_att_file_path">
                          {{downAttcFiles.dc_att_file_path | getFileNameInPath}}
                        </a>
                        
                        <!-- <a v-if="fileInLawFiles(downAttcFiles.dc_att_file_path, lawsuit.lawsuit_files)" class="float-right" href="#" 
                          @click="openModal($event)"
                          :data-target="modalSelector"
                          :data-component="formTitleName+'-upload-law-files-component'" 
                          :data-datas='`{ 
                            "lawId": ${lawsuit.id}, 
                            "dcId": ${dc_doc.id}, 
                            "formTitleName": "${formTitleName}", 
                            "filePathName": "${downAttcFiles.dc_att_file_path}",
                            "lawKey": ${lawKey}
                          }`'
                        > -->
                        <a v-if="fileInLawFiles(downAttcFiles.dc_att_file_path, lawsuit.lawsuit_files)" class="float-right" href="#" 
                          @click="addLawfile(
                            { 
                              lawId: lawsuit.id, 
                              dcId: dc_doc.id, 
                              formTitleName, 
                              filePathName: downAttcFiles.dc_att_file_path,
                              lawKey
                            }
                          )"
                        >
                          Dava Dosyasına Ekle
                        </a>
                      </li>
                    </ul>
                  </div>
                </div>
              </div>

            </div>
            
            <div class="row">
              <div class="col-12">
                <label>Dava Dosyaları: </label>
                <ul class="attach-files-list">
                  <li :key="fileKey" v-for="(file, fileKey) in lawsuit.lawsuit_files">
                    <a target="_blank" :href="'/storage/upload/images/raw'+file.lawf_file_path">
                      {{file.lawf_file_name}} ( {{file.lawf_file_path | getFileNameInPath}} )
                    </a>

                    <!-- <a class="float-right" href="#" 
                      @click="openModal($event)"
                      :data-target="modalSelector"
                      :data-component="formTitleName+'-delete-law-files-component'" 
                      :data-datas='`{ 
                        "id": ${file.id}, 
                        "fileName": "${file.lawf_file_name} (${getFileNameInPathFunc(file.lawf_file_path)})", 
                        "lawKey": ${lawKey},
                        "fileKey": ${fileKey}
                      }`'
                    > -->
                    <a class="float-right" href="#" 
                      @click="removeLawFile(
                        { 
                          id: file.id, 
                          fileName: file.lawf_file_name +' ( '+ getFileNameInPathFunc(file.lawf_file_path) + ' )', 
                          lawKey: lawKey,
                          fileKey: fileKey
                        },
                      )"
                    >
                      Dava Dosyasını Kaldır
                    </a>
                  </li>
                </ul>
              </div>
            </div>

          </div>
        </div>
      </div>

    </div>
  </div>
  <!-- /.card-body -->

  <!-- Modal -->
  <div class="modal fade" tabindex="-1" role="dialog" 
    aria-labelledby="faddLawFilesFormModalLongTitle" aria-hidden="true"
    data-backdrop="static" :id="modalIDName"
  >
    <div class="modal-dialog modal-xl" role="addLawFiles">
        <component
          v-if="this.modal.show"
          :is="this.modal.component"
          :ppdatas="this.modal.datas"
        >
        </component>
    </div>

  </div>
</div>
<!-- /.card -->
</template>

<script>
import uploadLawFilesComponent from './UploadLawFilesComponent';
import deleteLawFilesComponent from './DeleteLawFilesComponent';

import { mapState, mapMutations } from 'vuex';

let formTitleName= 'show-teacher-lawsuits';

export default {
  name: 'LawsuitsComponent',
  data() {
    return {
      teacher: this.ppteacher,
      formTitleName,
      modalIDName: 'addLawFilesFormModalLong',
      modal: {
        show: false,
        component: '',
        datas: {},
      }
    }
  },
  props: {
    ppteacher: {
      type: Object,
      required: true,
    }
  },
  computed: {
    ...mapState([
      'formModalBody',
      'routes',
    ]),
    modalSelector: function(){
      return '#' + this.modalIDName;
    }
  },
  methods: {
    ...mapMutations([
      'setErrors',
      'setSucceed',
    ]),
    openModal(e){
      $(this.modalSelector).modal('show', e.target);
    },
    itemStatus(value){
      return value < 1 ? 'Gelen Evrak' : 'Giden Evrak'
    },
    fileInLawFiles(filePath, lawFiles){
      let bool = true;
      for (let i = 0; i < lawFiles.length; i++) {
        
        if(lawFiles[i].lawf_file_path == filePath) {
          bool = false;
        }

        if(lawFiles.length - 1  == i) {
          return bool;
        }
      }
    },
    removeLawFile(rmLawFileInfo) {

      const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
          confirmButton: 'btn btn-success mr-3',
          cancelButton: 'btn btn-danger'
        },
        buttonsStyling: false
      })

      swalWithBootstrapButtons.fire({
        title: 'Dosyayı silmek istediğinizden emin misiniz?',
        text: "Bu değişikliği geri döndüremeyeceksiniz.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Evet, bunu sil!',
        cancelButtonText: 'Hayır, iptal et!',
        // reverseButtons: true
      }).then((result) => {
        if (result.isConfirmed) {

          $.ajax({
            url: this.routes.addLawFile+'/'+rmLawFileInfo.id,
            type: 'DELETE',
            dataType: 'JSON',
            cache: false,
          })
          .done((res) => {
            swalWithBootstrapButtons.fire(
              'Silindi!',
              'Dava dosyası silindi.',
              'success'
            );

            this.teacher.lawsuits[rmLawFileInfo.lawKey].lawsuit_files.splice(rmLawFileInfo.fileKey, 1);
          })
          .fail((error) => {
            swalWithBootstrapButtons.fire(
              'Bir sorunla karşılaşıldı.',
              'Dava dosyası silinemedi. Lütfen tekrar deneyiniz. Yada yöneticiye danışınız.',
              'error'
            )
          })
          .then((res) => {})
          .always(() => {});
          
        } else if (
          /* Read more about handling dismissals below */
          result.dismiss === Swal.DismissReason.cancel
        ) {
          swalWithBootstrapButtons.fire(
            'İptal Edildi',
            'Dava dosyası silinmedi.',
            'error'
          )
        }
      })
    },
    /* addLawfile(lawsuitFile, lawKey) {
      this.teacher.lawsuits[lawKey].lawsuit_files.push(lawsuitFile);
    }, */
    addLawfile(addLawFileInfo) {
      const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
          confirmButton: 'btn btn-success mr-3',
          cancelButton: 'btn btn-danger'
        },
        buttonsStyling: false
      })

      swalWithBootstrapButtons.fire({
        title: 'Lütfen Dava dosyasının adını giriniz.',
        input: 'text',
        // inputLabel: 'Dava ismi',
        inputValue: '',
        showCancelButton: true,
        confirmButtonText: 'Evet, bunu ekle!',
        cancelButtonText: 'Hayır, iptal et!',
        inputValidator: (value) => {
          if (!value) {
            return 'Dosya ismi boş olamaz!'
          }
        }
      }).then((result) => {
        if (result.isConfirmed) {

          let datas = {
            lawf_file_name: result.value,
            lawf_file_path: addLawFileInfo.filePathName,
            dc_id: addLawFileInfo.dcId,
            law_id: addLawFileInfo.lawId,
          }

          $.ajax({
            url: this.routes.addLawFile,
            type: 'POST',
            dataType: 'JSON',
            cache: false,
            data: datas,
          })
          .done((res) => {
            this.teacher.lawsuits[addLawFileInfo.lawKey].lawsuit_files.push(res.lawsuitFile);

            swalWithBootstrapButtons.fire(
              `Dosya Eklendi.`,
              `${result.value} (${this.getFileNameInPathFunc(addLawFileInfo.filePathName)}) Adlı dosya eklendi.`,
              'success'
            )
          })
          .fail((error) => {
            let html = '<ol>';
            let errorKeys = Object.keys(error.responseJSON.errors);

            for (let i = 0; i < errorKeys.length; i++) {
              const item = error.responseJSON.errors[errorKeys[i]];
              html += '<li class="text-left">'+item[0]+'</li>';

              if(errorKeys.length - 1 == i) {
                html += '</ol>';

                swalWithBootstrapButtons.fire(
                  'Bir sorunla karşılaşıldı.',
                  html,
                  'error'
                )
              }
            }
          })
          .then((res) => {})
          .always(() => {});
          
        } else if (
          /* Read more about handling dismissals below */
          result.dismiss === Swal.DismissReason.cancel
        ) {
          swalWithBootstrapButtons.fire(
            'İptal Edildi',
            'Dava dosyası eklenmedi.',
            'error'
          )
        }
      });
    }
  },
  mounted() {
    $(this.modalSelector).on('show.bs.modal', (event) => {

      let button = $(event.relatedTarget)
        , datas = button.attr('data-datas')
        , component = button.attr('data-component');

      this.modal.component = component;
      this.modal.datas = JSON.parse(datas);
      this.modal.show = true;
    });

    $(this.modalSelector).on('hide.bs.modal', (event) => {
      this.modal.show = false;
      
      this.setSucceed('');
      this.setErrors('');
    });
  },
  components: {
    [formTitleName + '-upload-law-files-component']: uploadLawFilesComponent,
    [formTitleName + '-delete-law-files-component']: deleteLawFilesComponent,
  }
}
</script>

<style>
ul.attach-files-list li {
  border-bottom: 1px solid #000;
}
</style>