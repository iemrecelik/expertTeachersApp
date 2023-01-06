<template>
<!-- Profile Image -->
<div class="card card-primary card-outline">
  <div class="card-body box-profile">
    <div class="text-center">
      <img class="profile-user-img img-fluid img-circle" 
        :src="getTeacherPhoto(teacher.thr_photo)"
      />
    </div>
    <div class="text-center">
      <!-- <img class="profile-user-img img-fluid img-circle"
            src="../../dist/img/user4-128x128.jpg"
            alt="User profile picture"> -->
    </div>

    <h3 class="profile-username text-center">
      {{ teacher.thr_name}} {{ teacher.thr_surname}} 
      <a class="text-success" v-if="tcValid">
        <i class="bi bi-check-circle-fill"></i>
      </a>
      <a class="text-danger" v-else>
        <i class="bi bi-exclamation-circle-fill"></i>
      </a>
    </h3>

    <p class="text-muted text-center">{{ getCareerLadder(teacher.thr_career_ladder)}}</p>

    <ul class="list-group list-group-unbordered mb-3">
      <li class="list-group-item">
        <b>TC Kimlik No :</b> <a class="float-right">{{ teacher.thr_tc_no}}</a>
      </li>
      <li class="list-group-item">
        <b>Cinsiyet :</b> <a class="float-right">{{ teacher.thr_gender == 0 ? 'Erkek' : 'Bayan' }}</a>
      </li>
      <li class="list-group-item">
        <b>Doğum Tarihi :</b> <a class="float-right">{{ teacher.thr_birth_day }}</a>
      </li>
      <li class="list-group-item">
        <b>İl :</b> <a class="float-right">{{ teacher.province.prv_name }}</a>
      </li>
      <li class="list-group-item">
        <b>İlçe :</b> <a class="float-right">{{ teacher.town.twn_name }}</a>
      </li>
      <li class="list-group-item">
        <b>Ünvanı :</b> <a class="float-right">{{ teacher.thr_degree}}</a>
      </li>
      <li class="list-group-item">
        <b>Görevi :</b> <a class="float-right">{{ teacher.thr_task}}</a>
      </li>
      <li class="list-group-item">
        <b>Öğrenim Durumu :</b> <a class="float-right">{{ teacher.thr_education_st}}</a>
      </li>
      <li class="list-group-item">
        <b>Cep No :</b> <a class="float-right">{{ teacher.thr_mobile_no}}</a>
      </li>
      <li class="list-group-item">
        <b>Görevli Olduğu Yer :</b> <a class="float-right">{{ teacher.thr_place_of_task}}</a>
      </li>
      <li class="list-group-item">
        <b>Kurumu :</b> <a class="float-right">{{ teacher.institution[0].inst_name}}</a>
      </li>
    </ul>

    <!-- <a href="#" class="btn btn-primary btn-block"><b>Follow</b></a> -->
  </div>
  <!-- /.card-body -->
</div>
<!-- /.card -->
</template>

<script>
export default {
  name: "ProfileComponent",
  data() {
    return {
      teacher: this.ppteacher,
      tcValid: this.pptcvalid,
    }
  },
  methods: {
    getCareerLadder: function(key) {
      let value = 'Bilinmiyor';
      switch (parseInt(key)) {
        case 0:
          value = 'Öğretmen'
          break;
        case 1:
          value = 'Uzman Öğretmen'
          break;
        case 2:
          value = 'Başöğretmen'
          break;
        default:
          break;
      }
      return value;
    },
    getTeacherPhoto: function (path) {
      let rawPath;

      if(path) {
        rawPath = '/storage/upload/images/raw/'+path;
      }else {
        rawPath = '/images/logo/meb-logo.png';
      }

      return rawPath;
    }
  },
  props: {
    ppteacher: {
      type: Object,
      required: true,
    },
    pptcvalid: {
      type: Boolean,
      required: true,
    },
  },
}
</script>

<style>

</style>