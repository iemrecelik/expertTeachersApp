<template>

<template-component>
	<error-msg-list-component></error-msg-list-component>
	<succeed-msg-component></succeed-msg-component>

  <!-- <form :id="formIDName" @submit.prevent> -->
  <form :id="formIDName" 
		method="post" 
		action="/admin/document-management/document/manual-store"
		enctype="multipart/form-data"
	>
		<input type="hidden" name="_token" :value="token">

		<div class="row">

			<div class="col-3">
				<div class="form-group">
					<label for="exampleInputEmail1">
						{{ $t('messages.categoryName') }}
					</label>
					<treeselect
						id="doc-category"
						name="dc_cat_id"
						@select="setShowForm"
						:options="categoryList"
						:value=0
						:disable-branch-nodes="false"
						:show-count="true"
						:placeholder="$t('messages.enterCategoryName')"
					/>
				</div>
			</div>
			<div class="col-9"></div>

		</div>

		<div v-if="showForm">
			<file-upload-form
				:ppfieldNames="docFieldNames"
				:ppitemStatus="1"
				ref="fileUploadFormComponent"
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
										:ppitemStatus="0"
										ref="fileUploadFormComponent"
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

					<div class="row">
						<div class="col-4">
							
							<div class="form-group">
								<label for="exampleFormControlTextarea1">Notunuz</label>
								<textarea class="form-control" 
									id="exampleFormControlTextarea1" 
									rows="3"
									name="dc_com_text"
								>
								</textarea>
							</div>
							
						</div>
					</div>

					<div class="row">
						<div class="col-4">
							<div class="form-group">
								<label for="exampleFormControlSelect1">Listeler</label>
								<select class="form-control" 
									id="exampleFormControlSelect1"
									name="list_id"
								>
									<option value="0">Liste Seçiniz.</option>
									<option v-for="item in docList" :value="item.id">
										{{item.dc_list_name}}
									</option>
								</select>
							</div>
						</div>
					</div>

				</div>
			</div>
			<button id="document-submit" disabled type="submit" class="btn btn-primary">Kaydet</button>
		</div>
	</form>

	<div class="modal fade" tabindex="-1" role="dialog" 
    aria-labelledby="formModalLongTitle" aria-hidden="true"
    data-backdrop="static" id="error-modal"
  >
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Hata!</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<error-msg-list-component></error-msg-list-component>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
					<!-- <button type="button" class="btn btn-primary">Save changes</button> -->
				</div>
			</div>
		</div>
	</div>
</template-component>

</template>

<script>
import fileUpladoFormComponent from './ManualFileUploadFormComponent.vue';
import Treeselect from '@riophae/vue-treeselect'
// import the styles
import '@riophae/vue-treeselect/dist/vue-treeselect.css'

import { mapState, mapMutations } from 'vuex';

export default {
	name: 'ManualCreateComponent',
  data () {
    return {
			categoryList: [],
			formTitleName: 'document',
			docFieldNames: {
				itemStatus: 'dc_item_status',
				senderFile: 'dc_sender_file',
				senderAttachFiles: 'dc_sender_attach_files[]',
				sender: 'dc_who_send',
				receiver: 'dc_who_receiver',
				number: 'dc_number',
				subject: 'dc_subject',
				content: 'dc_content',
				showContent: 'dc_show_content',
				rawContent: 'dc_raw_content',
				date: 'dc_date',
			},
			relFormCount: [],
			showForm: false,
			docList: [],
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
		ppsuccess: {
      type: String,
      required: false,
      default: ''
    },
    ppoldinput: {
      type: Object,
      required: true,
			default: {}
    },
  },
  computed: {
    ...mapState([
      'routes',
      'token',
    ]),
		formIDName: function(){
      return this.uniqueDomID(_.toLower(this.formTitleName));
    },
  },
  methods: {
		...mapMutations([
      'setRoutes',
      'setErrors',
			'setSucceed',
			'setOld',
    ]),
		getFileInputClassName: function(rawFileName) {
			let fileName = rawFileName;
			let indexOf = fileName.indexOf('[');

			if(indexOf > 0) {
				fileName = fileName.substring(0, indexOf);
			}

			fileName = fileName.replaceAll('_', '-');
			let regex = /^[a-zA-Z-]+$/;

			fileName = fileName.match(regex);
			fileName = 'file-upload-'+fileName;

			return fileName;
		},
		checkForm: function(val) {

			let element = document.getElementById('document-submit');
			let disabled = false;

			let files = document.getElementsByClassName(
				this.getFileInputClassName(this.docFieldNames.senderFile)
			);
			
			let relFiles = document.getElementsByClassName(
				this.getFileInputClassName('rel_dc_sender_file')
			);
// console.log("relFiles.length", relFiles.length);
			for (let i = 0; i < relFiles.length; i++) {
				disabled = relFiles[i].value ? false : true;
// console.log(1, disabled);
				if (disabled === true) {
					element.disabled = disabled ? true : false
					break;
				}
			}
// console.log(2, disabled);
			if(disabled === false) {
				for (let i = 0; i < files.length; i++) {
					disabled = files[i].value ? false : true;
				}
			}
// console.log("files.length", files.length )
			if(files.length < 1) {
				disabled = true;
			}

			element.disabled = disabled ? true : false
		},
		setShowForm: function(node, instanceId) {
			this.showForm =  node.id > 0;
		},
		oldValue: function(fieldName){
      return this.$store.state.old[fieldName];
    },
		addRelFormAsync: async function() {
			this.relFormCount.push(Date.now());
		},
		addRelForm: function() {
			this.addRelFormAsync().then((response) => {
				this.checkForm();
			});
		},
		dropRelFormAsync: async function(key) {
			this.relFormCount.splice(key, 1);
		},
		dropRelForm: function(key) {
			this.dropRelFormAsync(key).then((response) => {
				this.checkForm();
			});
		},
		getDocRelFieldNames: function(key) {

			return {
				itemStatus: `rel_dc_item_status[${key}]`,
				senderFile: `rel_dc_sender_file[${key}]`,
				senderAttachFiles: `rel_dc_sender_attach_files[${key}][]`,
				sender: `rel_dc_who_send[${key}]`,
				receiver: `rel_dc_who_receiver[${key}]`,
				number: `rel_dc_number[${key}]`,
				subject: `rel_dc_subject[${key}]`,
				content: `rel_dc_content[${key}]]`,
				showContent: `rel_dc_show_content[${key}]]`,
				rawContent: `rel_dc_raw_content[${key}]]`,
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
			.then((res) => {
				this.getList();
			})
			.always(() => {});
    },
		getList: function() {
			$.get(this.routes.getList, (data) => {
				this.docList = data;
			})
			.fail(function(error) {
				setTimeout(() => {
					this.ajaxErrorCount++

					if(this.ajaxErrorCount < 3)
						this.getList();
					else
						this.ajaxErrorCount = -1;

				}, 100);
			});
		},
		modalErrorMsgShow: function(errors = false) {
			if(
				(
					this.pperrors 
					&& Object.keys(this.pperrors).length > 0
					&& Object.getPrototypeOf(this.pperrors) === Object.prototype
				) || (errors)
			) {
				$('#error-modal').modal('show');
			}
		}
  },
  created() {
		this.setRoutes(this.pproutes);
    this.setErrors(this.pperrors);
		this.setSucceed(this.ppsuccess);
    this.getCategory();		
		this.setOld(this.ppoldinput);
  },
	mounted() {
		this.modalErrorMsgShow();		
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
	padding: 60px 0px 60px 80px;
	text-align: center !important;
	width: 330px; */

	border: 1px solid #92b0b3;
	background: #f1f1f1;
	outline: 2px dashed #92b0b3;
	outline-offset: -10px;
	padding: 43px 0px 36px 38px;
	text-align: center !important;
	width: 100%;
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