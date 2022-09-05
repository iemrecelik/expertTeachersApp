<template>
<div class="modal-content">
  <div class="modal-header">
    <h5 class="modal-title">Modal title</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </div>

  <ul class="nav nav-tabs" id="myTab" role="tablist">
    <li class="nav-item" role="presentation">
      <button class="nav-link active" id="home-tab" data-toggle="tab" data-target="#home" type="button" role="tab" aria-controls="home" aria-selected="true">Evrak</button>
    </li>

    <li class="nav-item" role="presentation" v-for="(item, key) in items.dc_ralatives">
      <button class="nav-link" 
        :id="'relative'+key+'-tab'" 
        data-toggle="tab" 
        :data-target="'#relative'+key" 
        type="button" 
        role="tab" 
        :aria-controls="'relative'+key" 
        aria-selected="false"
      >
        İlgi
      </button>
    </li>

  </ul>
  <div class="tab-content" id="myTabContent">
    <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
      <div class="modal-body" v-html="items.dc_show_content"></div>
    </div>

    <div class="tab-pane fade" 
      :id="'relative'+key" 
      role="tabpanel" 
      :aria-labelledby="'relative'+key+'-tab'"
      v-for="(item, key) in items.dc_ralatives"
      v-html="item.dc_show_content"
    >
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
      items: {}
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
    deneme(a) {
      console.log(a);
    }
  },
  created() {
    $.get(this.showUrl, (data) => {
      this.items = data;
      this.formShow = true;
    })
    .fail(function(error) {
      console.log(error);
    });
  },
  components: {}
  
}
</script>