<template>
<div class="modal-content">
  <div class="modal-header">
    <h5 class="modal-title">Evrak Detayı</h5>
    <button class="btn btn-sm btn-danger ml-3"
      @click="removeMarked"
    >
      {{$t('messages.removeMarked')}}
    </button>

    <span class="ml-4"><b>{{datas.userName}}</b> Kullanıcısı tarafından eklendi.</span>

    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </div>

  <ul class="nav nav-tabs" id="myTab" role="tablist">
    <li class="nav-item" role="presentation">
      <button class="nav-link active" id="home-tab" data-toggle="tab" data-target="#home" type="button" role="tab" aria-controls="home" aria-selected="true">Evrak</button>
    </li>

    <li class="nav-item" 
      role="presentation" 
      v-for="(item, key) in items.dc_ralatives"
    >
      <button class="nav-link" 
        :id="'relative'+key+'-tab'" 
        data-toggle="tab" 
        :data-target="'#relative'+key" 
        type="button" 
        role="tab" 
        :aria-controls="'relative'+key" 
        aria-selected="false"
      >
        İlgi
      </button>
    </li>

  </ul>

  <div class="tab-content" id="myTabContent">
    <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
      
      <div v-if="items.dc_show_content" class="modal-body" v-html="items.dc_show_content"></div>
      <div v-else class="modal-body p-5">
        <a type="button" 
            :href="'/storage/upload/images/raw'+items.dc_files.dc_file_path"
            target="_blank"
          >
            {{ $t('messages.readDocumentLinkClick') }}
          </a>
      </div>

      <div class="pl-5">
        
        <div class="row">
          <div class="col-12">
            <u>EKLENMİŞ DOSYALAR:</u>
          </div>
        </div>
        
        <div class="row" v-if="items.dc_attach_files">
          <div class="col-12" v-for="dc_att_file in items.dc_attach_files">
            <a v-if="fileExtensionControl(dc_att_file.dc_att_file_path)" :href="'/storage/upload/images/raw'+dc_att_file.dc_att_file_path"
              download
            >
              {{splitFileName(dc_att_file.dc_att_file_path)}}
            </a>
            <a v-else :href="'/storage/upload/images/raw'+dc_att_file.dc_att_file_path"
              target="_blank"
            >
              {{splitFileName(dc_att_file.dc_att_file_path)}}
            </a>
          </div>
        </div>
        <div v-else><b>DOSYA YOK</b></div>

      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">
          {{ $t('messages.close') }}
        </button>
        <span 
          data-toggle="tooltip" data-placement="top" 
          :title="$t('messages.downloadFile')"
        >
          <a type="button" class="btn btn-md btn-success"
            data-file-download
            :href="'/storage/upload/images/raw'+items.dc_files.dc_file_path"
            download
          >
            {{ $t('messages.downloadFile') }}
            <i class="bi bi-file-earmark-arrow-down"></i>
          </a>
        </span>
      </div>
    </div>

    <div class="tab-pane fade" 
      :id="'relative'+key" 
      role="tabpanel" 
      :aria-labelledby="'relative'+key+'-tab'"
      v-for="(item, key) in items.dc_ralatives"
    >
      <div v-if="item.dc_show_content" class="modal-body" v-html="item.dc_show_content"></div>
      <div v-else class="modal-body p-5">
        <a type="button" 
            :href="'/storage/upload/images/raw'+item.dc_files.dc_file_path"
            target="_blank"
          >
            {{ $t('messages.readDocumentLinkClick') }}
          </a>
      </div>

      <div class="pl-5">
        
        <div class="row">
          <div class="col-12">
            <u>EKLENMİŞ DOSYALAR:</u>
          </div>
        </div>
        
        <div class="row" v-if="item.dc_attach_files">
          <div class="col-12" v-for="dc_att_file in item.dc_attach_files">

            <a v-if="fileExtensionControl(dc_att_file.dc_att_file_path)" :href="'/storage/upload/images/raw'+dc_att_file.dc_att_file_path"
              download
            >
              {{splitFileName(dc_att_file.dc_att_file_path)}}
            </a>
            <a v-else :href="'/storage/upload/images/raw'+dc_att_file.dc_att_file_path"
              target="_blank"
            >
              {{splitFileName(dc_att_file.dc_att_file_path)}}
            </a>
          </div>
        </div>
        <div v-else><b>DOSYA YOK</b></div>

      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">
          {{ $t('messages.close') }}
        </button>
        <span 
          data-toggle="tooltip" data-placement="top" 
          :title="$t('messages.downloadFile')"
        >
          <a type="button" class="btn btn-md btn-success"
            data-file-download
            :href="'/storage/upload/images/raw'+item.dc_files.dc_file_path"
            download
          >
            {{ $t('messages.downloadFile') }}
            <i class="bi bi-file-earmark-arrow-down"></i>
          </a>
        </span>
      </div>
    </div>

  </div>

  
</div>
</template>

<script>

import { mapState } from 'vuex';

export default {
  name: 'ShowComponent',
  data () {
    return {
      datas: this.ppdatas,
      items: {},
      dcContent: this.ppDcContent,
      markInstance: null,
    }
  },
  props: {
    ppdatas: {
      type: Object,
      required: true,
    },
    ppDcContent: {
      type: String,
      required: true,
    }
  },
  computed: {
    ...mapState([
      'routes',
    ]),
    showUrl: function(){
      return this.routes.show + `/${this.datas.id}`;
    },
  },
  methods: {
    fileExtensionControl(val) {
      if(val) {
        let ext = val.split('.').pop();
        let blankFile = ['jpg', 'JPG', 'jpeg', 'JPEG', 'png', 'PNG', 'pdf', 'PDF', 'tif', 'TIF', ];
        let blank;

        if(blankFile.includes(ext)) {
          blank = false;
        }else {
          blank = true;
        }
        return blank;
      }
    },
    splitFileName(val) {
      if(val) {
        let arr = val.split('/');

        return arr[arr.length - 1];
      }
    },
    markSearch() {
      /* let regex = new RegExp(`\\b${this.dcContent}\\b`, 'gi');

      return val.replaceAll(regex, `<span class="bg-warning">${this.dcContent}</span>`);  */

      this.markInstance = new Mark(document.querySelectorAll(".modal-body"));

      let options = {
        diacritics: true,
        separateWordSearch: true
      }

      this.markInstance.mark(this.dcContent ,options);
    },
    removeMarked() {
      let options = {
        diacritics: true,
        separateWordSearch: true
      }
      this.markInstance.unmark();
    }
  },
  created() {
    $.get(this.showUrl, (data) => {
      this.items = data;
      this.formShow = true;
    })
    .fail(function(error) {
      console.log(error);
    })
    .then((res) => {
      this.markSearch();
    });
  },
  components: {}
  
}
</script>

<style>
mark{
    background: rgb(255, 251, 0);
    color: black;
}
</style>