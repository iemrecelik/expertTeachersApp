<template>
<multi-section-template-component
	:ppTitleName="$t('messages.log_records')"
>
  <div class="row mt-3">
    <div class="col-md-12">
      <div class="card">
        <div class="card-body">
          <form
            @submit.prevent
            :id="formIDName"
          >
            <div class="row">
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
                    id="single-reservation"
                    name="log_date"
                    autocomplete="off"
                  >
                </div>
              </div>

              <div class="col-3">
                <label>Kullanıcı:</label>
                <div class="input-group">
                  <select id="validationCustom04" 
                    class="custom-select"
                    required 
                    name="email"
                  >
                    <option :value="userVal.email" :key="userKey" v-for="(userVal, userKey) in datas.users">
                      {{ userVal.name + ' ' + userVal.email }}
                    </option>
                  </select>
                </div>
              </div>

              <div class="col-2">
                <label> - </label>
                <div class="form-group text-right">
                  <button type="submit" class="btn btn-primary bg-gradient-primary w-100"
                    @click="loadLogsData"
                  >
                    Ara
                  </button>
                </div>
              </div>
            </div><!-- /.row -->
          </form>

        </div><!-- /.card-body -->
      </div><!-- /.card -->
    </div><!-- /.col-md-12 -->
  </div><!-- /.row mt-3 -->

  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">Expandable Table</h3>
        </div>
        <!-- ./card-header -->
        <div class="card-body">
          <table class="table table-bordered table-hover">
            <thead>
              <tr>
                <th>Tarih ve Saat</th>
                <th>Modül</th>
                <th>İşlem</th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="logVal['time']" data-widget="expandable-table" aria-expanded="false" :key="logKey" v-for="(logVal, logKey) in logs">
                <td>{{logVal['time']}}</td>
                <td>{{logVal['moduleName']}}</td>
                <td>{{logVal['process']}}</td>
              </tr>
              <tr v-else class="expandable-body d-none">
                <td colspan="3">
                  <pre style="display: none;" v-html="logVal['content']"></pre>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <!-- /.card-body -->
      </div>
      <!-- /.card -->
    </div>
  </div>
</multi-section-template-component>
</template>

<script>
import { mapState, mapMutations } from 'vuex';

export default {
  name: 'IndexComponent',
  data () {
    return {
      datas: this.ppdatas,
      logs: [],
      formIDName: 'logs-form'
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
    ppdatas: {
      type: Object,
      required: true,
    },
  },
  computed: {
    ...mapState([
      'routes',
    ])
  },
  methods: {
    ...mapMutations([
      'setRoutes',
      'setErrors',
    ]),
    loadLogsData: function() {
      let form = $('#' + this.formIDName);

      $.ajax({
        url: this.routes.getLogsList,
        type: 'POST',
        dataType: 'JSON',
        data: form.serialize(),
      })
      .done((res) => {
        this.logs = res;
        // this.setErrors('');
        // this.setSucceed(res.succeed);
        // document.getElementById(this.formIDName).reset();
      })
      .fail((error) => {
        this.setSucceed('');
        // this.setErrors(error.responseJSON.errors);
        if(error.responseJSON) {
          if(error.responseJSON.errors) {
            this.setErrors(error.responseJSON.errors);
          }else if(error.responseJSON.message) {
            this.setErrors(
              {'permissionMessage': [error.responseJSON.message]}
            );
          }
        }
      })
      .then((res) => {})
      .always(() => {});
    }
  },
  created(){
    this.setRoutes(this.pproutes);
    this.setErrors(this.pperrors);
  },
}
</script>

<style>

</style>