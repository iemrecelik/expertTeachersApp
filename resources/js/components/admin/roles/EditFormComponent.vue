<template>
<div>
  <div class="row">
    <div class="col-12">
      <form-form-component
        :ppsettings="{
          type: 'text', 
          fieldName: 'name', 
          value: value('name')
        }"
      >
      </form-form-component>
    </div>
  </div>

</div>
</template>

<script>
import { mapState } from 'vuex';

export default {
  name: 'EditFormComponent',
  data () {
    return {}
  },
  props: {
    ppitem: {
      type: Object,
      required: true,
    }
  },
  computed: {
    item: function(){
      return this.ppitem;
    },
    ...mapState([
      'routes',
    ]),
  },
  methods: {
    value: function(fieldName){
      return this.$store.state.old[fieldName] || this.item[fieldName];
    },
  }
}
</script>