<template>
<template-component
  :ppTitleName="$t('messages.my_settings')"
>
  <error-msg-list-component></error-msg-list-component>
  <succeed-msg-component></succeed-msg-component>

  <form :action="routes.update" method="post">
    <input type="hidden" name="_method" value="PUT"/>
    <input type="hidden" name="_token" :value="token"/>
    <div class="row">
      <div class="col-5">
        <div class="form-group">
          <label for="myset-name">{{$t('messages.name')}}: </label>
          <input type="text" 
            class="form-control" 
            id="myset-name" 
            :placeholder="$t('messages.name')"
            name="name" 
            v-model="user.name"
          >
        </div>
      </div>

      <div class="col-5">
        <div class="form-group">
          <label for="myset-email">{{$t('messages.email')}}: </label>
          <input type="text" 
            class="form-control" 
            id="myset-email" 
            :placeholder="$t('messages.email')"
            name="email" 
            v-model="user.email"
            data-inputmask-regex="^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$" 
            data-mask
          >
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-5">
        <div class="form-group">
          <label for="mebbis-name">{{$t('messages.mebbis_name')}}: </label>
          <input type="text" 
            class="form-control" 
            id="mebbis-name" 
            :placeholder="$t('messages.mebbis_name')"
            name="mebbis_name" 
            v-model="user.mebbis_name"
          >
        </div>
      </div>

      <div class="col-5">
        <div class="form-group">
          <label for="mebbis-password">{{$t('messages.mebbis_password')}}: </label>
          <input type="password" 
            class="form-control" 
            id="mebbis-password" 
            :placeholder="$t('messages.mebbis_password')"
            name="mebbis_password" 
            value=""
          >
        </div>
      </div>
    </div>
    
    <div class="row">
      <div class="col-5">
        <div class="form-group">
          <label for="new-password">{{$t('messages.new_password')}}: </label>
          <input type="password" 
            class="form-control" 
            id="new-password" 
            :placeholder="$t('messages.new_password')"
            name="new_password" 
            value=""
          >
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-5">
        <div class="form-group">
          <label for="password">
            {{$t('messages.current_password')}}
            <span class="text-danger">(Değişiklikleri kaydetmek için mevcut şifrenizi girmeniz gerekmektedir)</span>: 
          </label>
          <input type="password" 
            class="form-control" 
            id="password" 
            :placeholder="$t('messages.current_password')"
            name="password" 
            value=""
          >
        </div>
      </div>
    </div>

    <button type="submit" class="btn btn-primary">Kaydet</button>
  </form>
</template-component>
</template>

<script>
import { mapState, mapMutations } from 'vuex';

let formTitleName = 'mySettings'

export default {
  name: 'IndexComponent',
  data () {
    return {
      formTitleName,
      user: this.ppuser,
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
    ppuser: {
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
  },
  created(){
    this.setRoutes(this.pproutes);
    this.setErrors(this.pperrors);
    this.setSucceed(this.ppsuccess);
  },
  mounted(){
    var email = document.getElementById("myset-email");

    var im = new Inputmask();
    
    im.mask(email);
  },
  components: {}
}
</script>