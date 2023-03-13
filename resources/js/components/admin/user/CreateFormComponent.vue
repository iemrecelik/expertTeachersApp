<template>
<div>
	<div class="row">
    <div class="col-5">
      <div class="form-group">
        <label for="myset-name">{{$t('messages.name')}}: </label>
        <input type="text" 
          class="form-control" 
          id="myset-name" 
          :placeholder="$t('messages.name')"
          name="name" 
          value=""
        >
      </div>
    </div>

    <div class="col-5">
      <div class="form-group">
        <label for="user-email">{{$t('messages.email')}}: </label>
        <input type="text" 
          class="form-control" 
          id="user-email" 
          :placeholder="$t('messages.email')"
          name="email" 
          value=""
          data-inputmask-regex="^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$" 
          data-mask
        >
      </div>
    </div>
  </div>
  
  <div class="row">
    <div class="col-5">
      <div class="form-group">
        <label for="password">{{$t('messages.password')}}: </label>
        <input type="password" 
          class="form-control" 
          id="password" 
          :placeholder="$t('messages.password')"
          name="password" 
          value=""
        >
      </div>
    </div>
  </div>

</div>
</template>

<script>
// import createLangFormComponent from './CreateLangFormComponent';

import Treeselect from '@riophae/vue-treeselect'
// import the styles
import '@riophae/vue-treeselect/dist/vue-treeselect.css'

import { mapState } from 'vuex';

export default {
  name: 'CreateFormComponent',
  data () {
    return {
    }
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
  },
  created() {},
  mounted() {
    /* mask oluşturma başla */
    var email = document.getElementById("user-email");
    var im = new Inputmask();
    im.mask(email);
    /* mask oluşturma bitiş */
  },
  components: {
    Treeselect
    // 'create-lang-form-component': createLangFormComponent,
  }
  
}
</script>