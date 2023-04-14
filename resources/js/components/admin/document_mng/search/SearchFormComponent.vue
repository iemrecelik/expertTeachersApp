<template>
<div class="row">
  <div class="col-md-10 offset-md-1">
    <div class="row mb-3">

      <div class="col-3">
        <label>Konu:</label>
        <div class="input-group">
          <!-- <label for="exampleInputEmail1">
            {{ $t('messages.categoryName') }}
          </label> -->
          <treeselect
            name="dc_cat_id[]"
            :multiple="true"
            :options="categoryList"
            v-model=categoryArr
            :disable-branch-nodes="true"
            :show-count="true"
            :placeholder="$t('messages.enterCategoryName')"
          />
        </div>
      </div>
      
      <div class="col-3">
        <label>Evrak Numarası:</label>
        <div class="input-group">
          <input type="search" 
            class="form-control" 
            name="dc_number" 
            placeholder="Evrak numarasını giriniz." 
            value=""
          >
          <div class="input-group-append">
          </div>
        </div>
      </div>
      
      <div class="col-3">
        <label>Evrak Durumu:</label>
        <div class="input-group">
          <select id="validationCustom04" 
            class="custom-select"
            required 
            name="dc_item_status"
          >
            <option selected value="-1">Hepsi</option>
            <option value="1">Giden Evrak</option>
            <option value="0">Gelen Evrak</option>
          </select>
        </div>
      </div>

      <div class="col-3">
        <label>Evrağın Konusu:</label>
        <div class="input-group">
          <input type="text" 
            class="form-control" 
            name="dc_subject" 
            placeholder="Buraya yazı konusunu giriniz" 
            value=""
          >
          <div class="input-group-append"></div>
        </div>
      </div>

    </div>

    <div class="row mb-3">

      <div class="col-3">
        <label>Gönderici:</label>
        <div class="input-group">
          <input type="text" 
            class="form-control" 
            name="dc_who_send" 
            placeholder="Buraya göndericiyi giriniz" 
            value=""
          >
          <div class="input-group-append">
          </div>
        </div>
      </div>

       <div class="col-3">
        <label>Alıcı:</label>
        <div class="input-group">
          <input type="text" 
            class="form-control" 
            name="dc_who_receiver" 
            placeholder="Buraya alıcıyı giriniz" 
            value=""
          >
          <div class="input-group-append">
          </div>
        </div>
      </div>
      
      <div class="col-3">
        <label>Aranacak Kelime:</label>
        <div class="input-group">
          <input type="text" 
            class="form-control" 
            name="dc_content" 
            placeholder="Aramak istediğiniz kelimeyi giriniz" 
            value=""
          >
          <div class="input-group-append">
          </div>
        </div>
      </div>

      <div class="col-3">
        <label>Evrak Tarih Aralığı:</label>
        
        <div class="input-group">
          <div class="input-group-prepend">
            <span class="input-group-text">
              <i class="far fa-calendar-alt"></i>
            </span>
          </div>
          <input type="text" 
            class="form-control float-right" 
            id="reservation"
            name="dc_date"
            autocomplete="off"
          >

          <!-- <div class="input-group-append">
            <slot></slot>
          </div> -->
        </div>
      </div>

    </div>

    <div class="row">
      <div class="col-6">
        <label>Kayıt Edilmiş Tarih Aralığı:</label>
        
        <div class="input-group">
          <div class="input-group-prepend">
            <span class="input-group-text">
              <i class="far fa-calendar-alt"></i>
            </span>
          </div>
          <input type="text" 
            class="form-control float-right reservation" 
            name="created_at"
            autocomplete="off"
          >
        </div>
      </div>

      <div class="col-6">
        <div class="form-group">
          <label for="user-list">Kullanıcılar</label>
          <select class="form-control" 
            id="user-list"
            name="user_id"
          >
            <option value="">Kullanıcı Seçiniz.</option> 
            <option v-for="item in users" :value="item.id">
              {{item.name+' ('+item.email+')'}}
            </option>
          </select>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-6">
        <div class="form-group">
          <label for="exampleFormControlSelect1">Listeler</label>
          <select class="form-control" 
            id="exampleFormControlSelect1"
            name="dc_list_id"
          >
            <option value="">Liste Seçiniz.</option> 
            <option v-for="item in list" :value="item.id">
              {{item.dc_list_name}}
            </option>
          </select>
        </div>
      </div>

      <div class="col-6">
        <div class="form-group">
          <label for="addTeacherList">Öğretmeni Ekle: </label>
          <treeselect
            :id="'addTeacherList'"
            :multiple="false"
            :async="true"
            :load-options="loadTeachers"
            v-model="teacherArr"
            loadingText="Yükleniyor..."
            clearAllText="Hepsini sil."
            clearValueText="Değeri sil."
            noOptionsText="Hiçbir seçenek yok."
            noResultsText="Mevcut seçenek yok."
            searchPromptText="Aramak için yazınız."
            placeholder="Seçiniz..."
            name="thr_id"
          />
        </div>
      </div>
    </div>
      

    <!-- <div class="row mb-3">
      <div class="col-2">
        <input type="hidden" name="dc_main_status" :value="mainStatus">
        <div class="icheck-primary d-inline">
          <input type="checkbox" 
            id="checkboxPrimary2" 
            @change="setMainStatus"
          >
          <label for="checkboxPrimary2">
            İlgi Evraklarınıda Ara
          </label>
        </div>
      </div>
    </div> -->

    <div class="row">
      <div class="col-1">
        <slot></slot>
      </div>
    </div>

  </div>
</div>
</template>

<script>
import Treeselect from '@riophae/vue-treeselect'
import { ASYNC_SEARCH } from '@riophae/vue-treeselect';

const simulateAsyncOperation = fn => {
  setTimeout(fn, 2000)
}
// import the styles
import '@riophae/vue-treeselect/dist/vue-treeselect.css';

import { mapState } from 'vuex';

export default {
  name: "SearchFormComponent",
    data () {
    return {
      categoryList: [],
      list: [],
      users: [],
      ajaxErrorCount: -1,
      mainStatus: 0,
      categoryArr: [],
      teacherArr: null,
    }
  },
  computed: {
    ...mapState([
      'routes',
    ]),
  },
  methods: {
    setMainStatus: function(event) {
      if(event.target.checked) {
        this.mainStatus = 1
      }else {
        this.mainStatus = 0
      }
    },
    getCategoryAndList: function() {
      $.ajax({
        url: this.routes.getCategoryAndList,
        type: 'POST',
        dataType: 'JSON',
        data: {
          'startData': {
            'id': 0,
            'label': 'Tüm Konular'
          },
        }
      })
      .done((res) => {
        this.categoryList = res.category;
        this.list = res.list;
        this.users = res.users;
        this.ajaxErrorCount = -1;
      })
      .fail((error) => {

        setTimeout(() => {
          this.ajaxErrorCount++

          if(this.ajaxErrorCount < 3)
            this.getCategoryAndList();
          else
            this.ajaxErrorCount = -1;

        }, 100);
        
      })
      .then((res) => {})
      .always(() => {});
    },
    loadTeachers({ action, searchQuery, callback }) {
      if (action === ASYNC_SEARCH) {
        simulateAsyncOperation(() => {

          if(searchQuery.length > 2) {
            this.getTeachersSearchList(searchQuery, callback);
          }else {
            callback(null, [])    
          }
        })
      }
    },
    getTeachersSearchList: function(searchTcNo, callback) {
      $.ajax({
        url: this.routes.getTeachersSearchList,
        type: 'GET',
        dataType: 'JSON',
				data: {'searchTcNo': searchTcNo}
      })
      .done((res) => {
				callback(null, res)
        this.ajaxErrorCount = -1;
      })
      .fail((error) => {
        setTimeout(() => {
          this.ajaxErrorCount++

          if(this.ajaxErrorCount < 3)
            this.getTeachersSearchList(searchTcNo, callback);
          else
            this.ajaxErrorCount = -1;

        }, 100);
        
      })
      .then((res) => {})
		},
  },
  created() {
    this.getCategoryAndList();
  },
  components: {
    Treeselect
  }
}
</script>

<style>

</style>