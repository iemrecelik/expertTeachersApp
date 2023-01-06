<template>
<div class="row">
  <div class="col-12">
    <div class="alert alert-info" role="alert">

      <h4 class="text-center">UYARI!</h4>
      
      <div :key="dataKey" v-for="(dateVal, dateKey) in datas">
        <label>
          {{ dateKey + ' :' }}
        </label>
        <div v-for="(userVal, userKey) in dateVal">
          <label>{{ userKey + ' :' }}</label>
          <div v-for="(statVal, statKey) in userVal">
            <p>{{statKey + ' : ' + statVal.rp_count }} tane evrak kaydedilmemiş.</p>
          </div>
        </div>
        <hr/>
      </div>

    </div>
  </div>
</div>
</template>

<script>
import { mapState } from 'vuex';

export default {
  name: "RecordNeedDocumentsComponent",
  data() {
    return {
      datas: [],
      ajaxErrorCount: -1,
    }
  },
  props: {},
  computed: {
    ...mapState([
      'routes',
    ]),
  },
  methods: {
    getRecordNeedDocuments: function () {
      $.ajax({
        url: this.routes.getRecordNeedDocuments,
        type: 'GET',
      })
      .done((res) => {
        this.datas = res;
      })
      .fail((error) => {
        setTimeout(() => {
          this.ajaxErrorCount++
          if(this.ajaxErrorCount < 3)
            this.getRecordNeedDocuments();
          else
            this.ajaxErrorCount = -1;
        }, 300);
      })
      .then((res) => {})
      .always(() => {});
    }
  },
  created() {
    this.getRecordNeedDocuments();
  },
}
</script>

<style>

</style>