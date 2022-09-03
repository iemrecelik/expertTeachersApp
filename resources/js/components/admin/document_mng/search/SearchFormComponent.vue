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
            name="dc_cat_id"
            :options="categoryList"
            :value=0
            :disable-branch-nodes="false"
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
        <label>Tarih aralığı:</label>
        
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
      <div class="col-12">
        <div class="form-group">
          <label for="exampleFormControlSelect1">Listeler</label>
          <select class="form-control" 
            id="exampleFormControlSelect1"
            name="dc_list_id"
          >
            <!-- <option value=""></option> -->
            <option v-for="item in list" :value="item.id">
              {{item.dc_list_name}}
            </option>
          </select>
        </div>
      </div>
    </div>

    <div class="row mb-3">
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
    </div>

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
// import the styles
import '@riophae/vue-treeselect/dist/vue-treeselect.css'

import { mapState } from 'vuex';

export default {
  name: "SearchFormComponent",
    data () {
    return {
      categoryList: [],
      list: [],
      ajaxErrorCount: -1,
      mainStatus: 0,
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
      console.log('sdsadasd');
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
    }
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