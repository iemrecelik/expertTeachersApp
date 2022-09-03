<template>
<div>
  <div class="row">
    <div class="col-12">

      <div class="row">
        <div class="col-12">
          <ul>
            <li>
              <label for="exampleFormControlTextarea1">Alı Rıza TAŞÇI</label>
              <br/>
              <span>
                Bakanlığımızda görev yapan öğretmenlerin, uzman öğretmenlik ve başöğretmenlik eğitim programına başvuruları tamamlanmıştır.
    Van ili İpekyolu ilçesi İpekyolu Borsa İstanbul Fen Lisesinde öğretmen olarak görev yapan Evin KESKİN(41731432364)'in, Uzman Öğretmenlik Eğitim Programına başvuruda bulunduğu ancak açığa alındığı
              </span>
              <hr/>
            </li>
            
            <li>
              <label for="exampleFormControlTextarea1">Necati UĞUR</label>
              <br/>
              <span>
                Bakanlığımızda görev yapan öğretmenlerin, uzman öğretmenlik ve başöğretmenlik eğitim programına başvuruları tamamlanmıştır.
    Van ili İpekyolu ilçesi İpekyolu Borsa İstanbul Fen Lisesinde öğretmen olarak görev yapan Evin KESKİN(41731432364)'in, Uzman Öğretmenlik Eğitim Programına başvuruda bulunduğu ancak açığa alındığı
              </span>
              <hr/>
            </li>
          </ul>
          
          <!-- <ul>
            <li v-for="item in list.selected">
              <span>{{ item.dc_list_name }}</span>
              <span class="float-right"
                @click="deleteList(item.id)"
              >
                <i class="bi bi-x-circle-fill delete-list-icon"></i>
              </span>
              <hr/>
            </li>
          </ul> -->



        </div>
      </div>

      <div class="row">
        <div class="col-12">
          
          <div class="form-group">
            <label for="exampleFormControlTextarea1">Example textarea</label>
            <textarea class="form-control" 
              id="exampleFormControlTextarea1" 
              rows="3"
              name="dc_com_text"
              value=""
            >
            </textarea>
          </div>

          <input type="hidden" name="dc_id" :value="datas.id">
          
        </div>
      </div>

    </div>
  </div>

  <input type="hidden" name="dc_id" :value="datas.id"/>
</div>
</template>

<script>
// import createLangFormComponent from './CreateLangFormComponent';
import Treeselect from '@riophae/vue-treeselect'
// import the styles
import '@riophae/vue-treeselect/dist/vue-treeselect.css'
import { mapState } from 'vuex';
export default {
  name: 'AddCommentFormComponent',
  data () {
    return {
      list: [],
      ajaxErrorCount: -1,
      datas: this.ppdatas
    }
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
  },
  methods: {
    oldValue: function(fieldName){
      return this.$store.state.old[fieldName];
    },
    getListAndSelectedList: function() {
      $.ajax({
        url: this.routes.getListAndSelected,
        type: 'GET',
        dataType: 'JSON',
        data: {'dc_id': this.datas.id}
      })
      .done((res) => {
        this.list = res;
        this.ajaxErrorCount = -1;
      })
      .fail((error) => {
        setTimeout(() => {
          this.ajaxErrorCount++
          if(this.ajaxErrorCount < 3)
            this.getListAndSelectedList();
          else
            this.ajaxErrorCount = -1;
        }, 100);
        
      })
      .then((res) => {})
      .always(() => {});
    },
    deleteList: function(id){
      $.ajax({
        url: this.routes.deleteList,
        type: 'POST',
        dataType: 'JSON',
        data: {
          'id': id,
          'dc_id': this.datas.id,
        }
      })
      .done((res) => {
        this.getListAndSelectedList();
      })
      .fail((error) => {
        setTimeout(() => {
          this.ajaxErrorCount++
          if(this.ajaxErrorCount < 3)
            this.deleteList();
          else
            this.ajaxErrorCount = -1;
        }, 100);
        
      })
      .then((res) => {})
      .always(() => {});
    }
  },
  created() {
    this.getListAndSelectedList();
  },
  components: {
    Treeselect
    // 'create-lang-form-component': createLangFormComponent,
  }
  
}
</script>