<template>
<div>
  <div class="row">
    <div class="col-12">

      <div class="row">
        <div class="col-12">
          <ul>
            <li v-for="comment in comments">
              <label for="exampleFormControlTextarea1">
                {{comment.user.name}}
              </label>

              <span v-if="comment.user.auth"
                @click="deleteComment(comment.id)"
              >
                <i class="bi bi-x-circle-fill delete-list-icon"></i>
              </span>

              <br/>
              <span>
                {{comment.dc_com_text}}
              </span>
              <hr/>
            </li>
          </ul>

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
              v-model="userComment"
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
      comments: [],
      userComment: '',
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
    getComments: function(){
      $.ajax({
        url: this.routes.getComments,
        // url: '/admin/document-management/comment/get-comments/' + this.datas.id,
        type: 'GET',
        dataType: 'JSON',
        data: {'dc_id': this.datas.id}
      })
      .done((res) => {
        this.comments = res;
        this.ajaxErrorCount = -1;

        this.comments.forEach((comment) => {
          if(comment.user.auth) {
            this.userComment = comment.dc_com_text;
          }
        });
      })
      .fail((error) => {
        setTimeout(() => {
          this.ajaxErrorCount++
          if(this.ajaxErrorCount < 3)
            this.getComments();
          else
            this.ajaxErrorCount = -1;
        }, 100);
      })
      .then((res) => {})
      .always(() => {});
    },
    deleteComment: function(id){
      $.ajax({
        url: this.routes.deleteComment,
        type: 'POST',
        dataType: 'JSON',
        data: {
          'id': id,
          'dc_id': this.datas.id,
        }
      })
      .done((res) => {
        this.getComments();
        this.userComment = '';
      })
      .fail((error) => {
        setTimeout(() => {
          this.ajaxErrorCount++
          if(this.ajaxErrorCount < 3)
            this.deleteComment();
          else
            this.ajaxErrorCount = -1;
        }, 100);
        
      })
      .then((res) => {})
      .always(() => {});
    }
  },
  created() {
    this.getComments();
  },
  components: {
    Treeselect
    // 'create-lang-form-component': createLangFormComponent,
  }
  
}
</script>