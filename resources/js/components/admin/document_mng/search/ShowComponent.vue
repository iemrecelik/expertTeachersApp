<template>
<div class="modal-content">
  <div class="modal-header">
    <h5 class="modal-title">Modal title</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </div>
  <div class="modal-body">
    <div class="row mb-5">
      <div class="col-4"></div>
      <div class="col-4 text-center">
                T.C.
        MİLLÎ EĞİTİM BAKANLIĞI
        Öğretmen Yetiştirme ve Geliştirme Genel Müdürlüğü
      </div>
      <div class="col-4"></div>
    </div>

    <div class="row">
      <div class="col-1"></div>
      <div class="col-9">
        Sayı	: E-20299769-774.07-54304360	
      </div>
      <div class="col-2">
        28.07.2022
      </div>
    </div>

    <div class="row mb-5">
      <div class="col-1"></div>
      <div class="col-11">
        Konu	: Uzman Öğretmenlik/Başöğretmenlik
               Eğitim Başvuruları İztirazları Hk.
      </div>
    </div>
  

    <div class="row mb-5">
      <div class="col-4"></div>
      <div class="col-4 text-center">
        MALATYA VALİLİĞİNE 
        (İl Millî Eğitim Müdürlüğü)
      </div>
      <div class="col-4"></div>
    </div>

    <div class="row">
      <div class="col-1"></div>
      <div class="col-10">
        İlgi	:  a) 20/06/2022 tarih ve E-14770658-774.01.01-52208240 sayılı yazınız.
                b) Uzman Öğretmenlik ve Başöğretmenlik Eğitim Programı ile Öğretmenlik Kariyer  Basamakları Sınav Duyurusu.
                c) 12 Mayıs 2022 tarih ve 31833 sayılı Resmi Gazete'de yayımlanan Aday Öğretmenlik ve Öğretmenlik Kariyer Basamakları Yönetmeliği.
      </div>
      <div class="col-1"></div>
    </div>

    <div class="row mb-5">
      <div class="col-1"></div>
      <div class="col-10">
        Bakanlığımız resmî eğitim kurumlarında görev yapan  öğretmenlerin, ilgi (b) duyuru doğrultusunda uzman öğretmenlik ve başöğretmenlik eğitim programına başvuruları tamamlanmıştır. 
        Söz konusu eğitim programlarına yönelik  başvuru süreci ile ilgili itirazlar; ilgi (c) Yönetmeliğin 21'inci maddesine göre il değerlendirme komisyonlarınca yapılmıştır.
        İlgi (a) yazı eki komisyon tutanaklarının incelenmesinden; başvuru süresi içinde şartları taşımalarına rağmen başvuruda bulunmadıkları halde itiraz edenlerin itirazlarının kabul edilerek eğitim programlarına alınmaları gerektiği yönünde tutanakların olduğu görülmüştür. 
        Bu bağlamda ekli listede yer alanlara ilişkin tutanakların yeniden düzenlenerek Genel Müdürlüğümüze gönderilmesi ve ilgililere tebliğ edilmesi gerekmektedir.
          Bilgilerini ve gereğini rica ederim.
      </div>
      <div class="col-1"></div>
    </div>

    <div class="row">
      <div class="col-9"></div>
      <div class="col-3">
          Cevdet VURAL
              Bakan a.
          Genel Müdür
      </div>
    </div>

    <div class="row">
      <div class="col-1"></div>
      <div class="col-11">
        Ek: Liste 
      </div>
    </div>

    <div class="row">
      <div class="col-12">

      </div>
    </div>

  </div>


  <div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
    <button type="button" class="btn btn-primary">Save changes</button>
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
      item: {}
    }
  },
  props: {
    ppdatas: {
      type: Object,
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
  
  },
  created() {
    $.get(this.showUrl, (data) => {
      this.item = data;
      this.formShow = true;
    })
    .fail(function(error) {
      console.log(error);
    });
  },
  components: {}
  
}
</script>