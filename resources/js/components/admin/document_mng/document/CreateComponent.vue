<template>

<template-component>
	<error-msg-list-component></error-msg-list-component>

  <!-- <form :id="formIDName" @submit.prevent> -->
  <form :id="formIDName" method="post" data action="/admin/document-management/document/store">
		<div class="row">
			<div class="col-12">
				<div class="form-group">
					<label for="exampleInputEmail1">
						{{ $t('messages.categoryName') }}
					</label>
					<treeselect
						name="dc_cat_id"
						:options="categoryList"
						:value=0
						:disable-branch-nodes="false"
						:show-count="true"
						:placeholder="$t('messages.enterCategoryName')"
					/>
				</div>
			</div>
		</div>

		<file-upload-form
			:ppfieldNames="docFieldNames"
		>
		</file-upload-form>
		
		<div class="row">
			<div class="col-12">

				<!-- SELECT2 EXAMPLE -->
        <div class="card card-default" :key="key" v-for="(key, val) in relFormCount">
          <div class="card-header">
            <h3 class="card-title">İlgili Evrak</h3>

            <div class="card-tools">
              <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
              </button>
              <!-- <button type="button" class="btn btn-tool" 
								data-card-widget="remove"
								@click="dropRelForm(key)"
							> -->
							<button type="button" class="btn btn-tool" 
								data-card-widget="remove"
								@click="dropRelForm(val)"
							>
                <i class="fas fa-times"></i>
              </button>
            </div>
          </div>
          <!-- /.card-header -->
          <div class="card-body">
            <div class="row">
							<div class="col-12">
								<file-upload-form
									:ppfieldNames="getDocRelFieldNames(val)"
								>
								</file-upload-form>
							</div>
						</div>
            <!-- /.row -->
          </div>
          <!-- /.card-body -->
          <div class="card-footer"></div>
        </div>
        <!-- /.card -->

				<div class="row">
					<div class="col-12 mb-2">
						<button type="button" class="btn btn-primary" @click="addRelForm()">
							İlgi Formu Ekle
						</button>
					</div>
				</div>
				
				<p>
					<button class="btn btn-primary" type="button" data-toggle="collapse" data-target="#collapseExample2" aria-expanded="false" aria-controls="collapseExample">
						Gelişmiş Ekleme
					</button>
				</p>
				
				<!-- <div class="collapse" id="collapseExample2">
					<div class="card card-body">
						<div class="form-group">
							<label for="exampleInputEmail1">Açıklama Ekle</label>
							<textarea class="form-control" id="validationTextarea" placeholder="Açıklama ekleyiniz." required="" style="height: 123px;" name="note"></textarea>
							<small id="emailHelp" class="form-text text-muted">Evrak detaylarını yazınız.</small>
						</div>
						
						<div class="form-group">
							<label for="validationCustom04">Eklemek İstediğiniz Listeyi Seçiniz</label>
							<select class="custom-select" id="validationCustom04" required name="list_name">
								<option selected disabled value="">Seçiniz...</option>
								<option>İl komisyonu kararları</option>
								<option>Sendika davaları</option>
								<option>Dış kurum uzman öğretmen listesi</option>
							</select>
							<div class="invalid-feedback">
								Please select a valid state.
							</div>
						</div>
						
					</div>
				</div> -->
			</div>
		</div>
		<button type="submit" class="btn btn-primary">Submit</button>
	</form>
</template-component>

</template>

<script>
import fileUpladoFormComponent from './FileUploadFormComponent.vue';
import Treeselect from '@riophae/vue-treeselect'
// import the styles
import '@riophae/vue-treeselect/dist/vue-treeselect.css'

import { mapState, mapMutations } from 'vuex';

export default {
	name: 'CreateComponent',
  data () {
    return {
			categoryList: [],
			formTitleName: 'deneme',
			docFieldNames: {
				senderFile: 'dc_sender_file',
				senderAttachFiles: 'dc_sender_attach_files[]',
				sender: 'dc_who_send',
				receiver: 'dc_who_receiver',
				number: 'dc_number',
				subject: 'dc_subject',
				date: 'dc_date',
			},
			docRelFieldNames: {
				senderFile: 'rel_sender_file[]',
				senderAttachFiles: 'rel_sender_attach_files[][]',
				sender: 'rel_dc_who_send[]',
				receiver: 'rel_dc_who_receiver[]',
				number: 'rel_dc_number[]',
				subject: 'rel_dc_subject[]',
				date: 'rel_dc_date[]',
			},
			relFormCount: [],
		}
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
  },
  computed: {
    ...mapState([
      'routes',
    ]),
		formIDName: function(){
      return this.uniqueDomID(_.toLower(this.formTitleName));
    },
  },
  methods: {
		...mapMutations([
      'setRoutes',
      'setErrors',
    ]),
		addRelForm: function() {
			this.relFormCount.push(Date.now());
		},
		dropRelForm: function(key) {
			console.log(key);
			this.relFormCount.splice(key, 1);
			console.log('-----');
			console.log(this.relFormCount);
		},
		getDocRelFieldNames: function(key) {

			return {
				senderFile: `rel_sender_file[${key}]`,
				senderAttachFiles: `rel_sender_attach_files[${key}][]`,
				sender: `rel_dc_who_send[${key}]`,
				receiver: `rel_dc_who_receiver[${key}]`,
				number: `rel_dc_number[${key}]`,
				subject: `rel_dc_subject[${key}]`,
				date: `rel_dc_date[${key}]`,
			}
		},
		getCategory: function() {
			$.ajax({
				url: this.routes.getCategory,
				type: 'GET',
				dataType: 'JSON',
			})
			.done((res) => {
				this.categoryList = res;
				this.ajaxErrorCount = -1;
			})
			.fail((error) => {

				setTimeout(() => {
					this.ajaxErrorCount++

					if(this.ajaxErrorCount < 3)
						this.getCategory();
					else
						this.ajaxErrorCount = -1;

				}, 100);
				
			})
			.then((res) => {})
			.always(() => {});
    },
  },
  created() {
		this.setRoutes(this.pproutes);
    this.setErrors(this.pperrors);
    this.getCategory();
  },
  components: {
    Treeselect,
		'file-upload-form': fileUpladoFormComponent,
  }
}
</script>

<style>
.upload-container {
	position: relative;
}
.upload-container input {
	/* border: 1px solid #92b0b3;
	background: #f1f1f1;
	outline: 2px dashed #92b0b3;
	outline-offset: -10px;
	padding: 100px 0px 100px 250px;
	text-align: center !important;
	width: 500px; */
	
	border: 1px solid #92b0b3;
    background: #f1f1f1;
    outline: 2px dashed #92b0b3;
    outline-offset: -10px;
    padding: 60px 0px 60px 80px;
    text-align: center !important;
    width: 330px;
}
.upload-container input:hover {
	background: #ddd;
}   
/* .upload-container:before {
	position: absolute;
	bottom: 50px;
	left: 245px;
	content: " (or) Drag and Drop files here. ";
	color: #3f8188;
	font-weight: 900;
}  */  
.upload-btn {
	margin-left: 300px;
	padding: 7px 20px;
}     
</style>