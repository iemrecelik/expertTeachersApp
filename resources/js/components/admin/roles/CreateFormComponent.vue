<template>
<div>
	<div class="row">
    <div class="col-5">
      <div class="form-group">
        <label for="role-name">{{$t('messages.role_name')}}: </label>
        <input type="text" 
          class="form-control" 
          id="role-name" 
          :placeholder="$t('messages.role_name')"
          name="name" 
          value=""
        >
      </div>
    </div>
  </div>
  
  <div class="row">
    <div class="col-5">
      <div class="form-group">
        <label for="role-name">{{$t('messages.nickname')}}: </label>
        <input type="text" 
          class="form-control" 
          id="role-name" 
          :placeholder="$t('messages.nickname')"
          name="nickname" 
          value=""
        >
      </div>
    </div>
  </div><!-- /.row -->
</div>
</template>

<script>
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
  }  
}
</script>