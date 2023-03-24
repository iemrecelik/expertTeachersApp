<template>
<template-component
  :ppTitleName="$t('messages.settings')"
>
  <error-msg-list-component></error-msg-list-component>
  <succeed-msg-component></succeed-msg-component>

  <form :action="routes.update" method="post">
    <input type="hidden" name="_method" value="PUT"/>
    <input type="hidden" name="_token" :value="token"/>
    <div class="row">
      <div class="col-12">
        <div class="form-group">
          <label for="addAuthSignature">{{$t('messages.auth_signature_name')}}: </label>
          <treeselect
            :id="'addAuthSignature'"
            :multiple="true"
            :async="true"
            :load-options="loadAuthSignature"
            :defaultOptions="authSignatureOptArr"
            v-model="authSignatureArr"
            loadingText="Yükleniyor..."
            clearAllText="Hepsini sil."
            clearValueText="Değeri sil."
            noOptionsText="Hiçbir seçenek yok."
            noResultsText="Mevcut seçenek yok."
            searchPromptText="Aramak için yazınız."
            placeholder="Seçiniz..."
            name="set_auth_signature_names[]"
          />
        </div>
      </div>
    </div>
    
    <div class="row">
      <div class="col-12">
        <div class="form-group">
          <label for="addIp">{{$t('messages.ip_names')}}: </label>
          <treeselect
            :id="'addIp'"
            :multiple="true"
            :async="true"
            :load-options="loadIpNames"
            :defaultOptions="ipNamesOptArr"
            v-model="ipNamesArr"
            loadingText="Yükleniyor..."
            clearAllText="Hepsini sil."
            clearValueText="Değeri sil."
            noOptionsText="Hiçbir seçenek yok."
            noResultsText="Mevcut seçenek yok."
            searchPromptText="Aramak için yazınız."
            placeholder="Seçiniz..."
            name="set_ip_names[]"
          />
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-12">
        <div class="form-group">
          <label for="allow-file-ext">{{$t('messages.allow_file_extension')}}: </label>
          <treeselect
            :id="'allow-file-ext'"
            :multiple="true"
            :async="true"
            :load-options="loadAllowFileExt"
            :defaultOptions="allowFileExtOptArr"
            v-model="allowFileExtArr"
            loadingText="Yükleniyor..."
            clearAllText="Hepsini sil."
            clearValueText="Değeri sil."
            noOptionsText="Hiçbir seçenek yok."
            noResultsText="Mevcut seçenek yok."
            searchPromptText="Aramak için yazınız."
            placeholder="Seçiniz..."
            name="allow_file_ext[]"
          />
        </div>
      </div>
    </div>

    <button type="submit" class="btn btn-primary">Kaydet</button>
  </form>
</template-component>
</template>

<script>
import Treeselect from '@riophae/vue-treeselect'
import { ASYNC_SEARCH } from '@riophae/vue-treeselect';

const simulateAsyncOperation = fn => {
  setTimeout(fn, 1000)
}
// import the styles
import '@riophae/vue-treeselect/dist/vue-treeselect.css';
  
import { mapState, mapMutations } from 'vuex';

let formTitleName = 'settings'

export default {
  name: 'IndexComponent',
  data () {
    return {
      formTitleName,
      authSignatureOptArr: null,
      authSignatureArr: null,
      ipNamesOptArr: null,
      ipNamesArr: null,
      allowFileExtOptArr: null,
      allowFileExtArr: null,
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
    ppsuccess: {
      type: String,
      required: true,
    },
    ppipnames: {
      type: Object,
      required: true,
    },
    ppsignaturenames: {
      type: Object,
      required: true,
    },
    ppallowfileext: {
      type: Object,
      required: true,
    },
  },
  computed: {
    ...mapState([
      'routes',
      'token',
    ]),
  },
  methods: {
    ...mapMutations([
      'setRoutes',
      'setSucceed',
      'setErrors',
    ]),
    loadAuthSignature({ action, searchQuery, callback }) {
      if (action === ASYNC_SEARCH) {
        simulateAsyncOperation(() => {
          callback(null, [{id: searchQuery, label: searchQuery }]);
        })
      }
    },
    loadIpNames({ action, searchQuery, callback }) {
      if (action === ASYNC_SEARCH) {
        simulateAsyncOperation(() => {
          callback(null, [{id: searchQuery, label: searchQuery }]);
        })
      }
    },
    loadAllowFileExt({ action, searchQuery, callback }) {
      if (action === ASYNC_SEARCH) {
        simulateAsyncOperation(() => {
          callback(null, [{id: searchQuery, label: searchQuery }]);
        })
      }
    }
  },
  created(){
    this.authSignatureOptArr = this.ppsignaturenames.arr;
    this.authSignatureArr = this.ppsignaturenames.val;
    
    this.ipNamesOptArr = this.ppipnames.arr;
    this.ipNamesArr = this.ppipnames.val;

    this.allowFileExtOptArr = this.ppallowfileext.arr;
    this.allowFileExtArr = this.ppallowfileext.val;

    this.setRoutes(this.pproutes);
    this.setErrors(this.pperrors);
    this.setSucceed(this.ppsuccess);
  },
  mounted(){},
  components: {
    Treeselect
  }
}
</script>