<template>

<template-component
	:ppTitleName="$t('messages.createDocument')"
>
	<error-msg-list-component></error-msg-list-component>
	<succeed-msg-component></succeed-msg-component>
	
	<div class="alert alert-info" role="alert">
		<ul>
			<li>Sistemde yüklü olan evrağı yeniden yükelmeye çalışmayınız. Hata verecektir.</li>
			<li>İlgi göstereceğiniz evrak sistemde yüklü ise ilgi formu açmadan evrak sayısı ile ekleyiniz.</li>
		</ul>
	</div>

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
						name="dc_cat_id[]"
						:multiple="true"
						:options="categoryList"
						v-model=categoryArr
						:disable-branch-nodes="true"
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

					<div class="row my-4 border-top" :key="dcItemKey" v-for="(dcItemVal,dcItemKey) in addedDcNumbers">
						<input type="hidden" name="add_dc_number_id[]" :value="dcItemVal.id">
						<div class="col-3">
							<div>
								<label>Evrak Sayısı</label>
							</div>
							<div>
								<span>{{ dcItemVal.label }}</span>	
							</div>
						</div>
						<div class="col-3">
							<div>
								<label>Evrak Durumu</label>
							</div>
							<div>
								<span>{{ dcItemVal.itemStatus }}</span>	
							</div>
						</div>
						<div class="col-3">
							<div>
								<label>Evrak Tarihi</label>
							</div>
							<div>
								<span>{{ dcItemVal.date }}</span>	
							</div>
						</div>

						<div class="col-3">
							<div class="mt-4">
								<span v-if="extUdfControl(dcItemVal.path) && dcItemVal.content"
									data-toggle="tooltip" 
									data-placement="top" 
									:title="$t('messages.showDocument')"
								>
									<a tabindex="0" class="btn btn-sm btn-info" 
										:id="'dc-show-document-'+dcItemVal.id"
										role="button" 
										data-toggle="popover" 
										data-trigger="focus" 
										title=""
									>
										<i class="bi bi-file-text"></i>
									</a>
								</span>

								<input v-else type="hidden" :id="'dc-show-document-'+dcItemVal.id">

								<span 
									data-toggle="tooltip" 
									data-placement="top" 
									:title="$t('messages.delete')"
								>
									<button type="button" class="btn btn-sm btn-danger"
										@click="delDocument(dcItemKey)"
									>
										<i class="bi bi-trash"></i>
									</button>
								</span>
							</div>
						</div>
					</div>

					<div class="row mt-3">
						<div class="col-12">
							<label for="addTeacherList">Evrak Numarası Seçiniz: </label>
						</div>
					</div>
					<div class="row mb-2">
						<div class="col-3">
							<div class="form-group">
								<treeselect
									:id="'addTeacherList'"
									:multiple="false"
									:async="true"
									:load-options="loadDcNumbers"
									:cacheOptions="false"
									v-model="selectedDcNumber"
									loadingText="Yükleniyor..."
									clearAllText="Hepsini sil."
									clearValueText="Değeri sil."
									noOptionsText="Hiçbir seçenek yok."
									noResultsText="Mevcut seçenek yok."
									searchPromptText="Aramak için yazınız."
									placeholder="Evrak Numarasını Seçiniz..."
									name=""
								/>
							</div>
						</div>
						<div class="col-4">
							<button type="button" class="btn btn-primary" @click="addDcNumber">
								Ekle
							</button>
							<button type="button" class="btn btn-primary ml-2" @click="addRelForm()">
								İlgi Formu Ekle
							</button>
						</div>
					</div>

					<!-- <div class="row">
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

						<div class="col-4">
							<div class="form-group">
								<label for="addTeacherList">İlgili Öğretmen(ler)i Ekle: </label>
								<treeselect
									:id="'addTeacherList'"
									:multiple="true"
									:async="true"
									:load-options="loadOptions"
									loadingText="Yükleniyor..."
									clearAllText="Hepsini sil."
									clearValueText="Değeri sil."
									noOptionsText="Hiçbir seçenek yok."
									noResultsText="Mevcut seçenek yok."
									searchPromptText="Aramak için yazınız."
									placeholder="Seçiniz..."
									name="thr_id[]"
								/>
							</div>
							
						</div>
					</div> -->

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
import Treeselect from '@riophae/vue-treeselect';
import { ASYNC_SEARCH } from '@riophae/vue-treeselect';

const simulateAsyncOperation = fn => {
  setTimeout(fn, 2000)
}
// import the styles
import '@riophae/vue-treeselect/dist/vue-treeselect.css';

import fileUpladoFormComponent from './FileUploadFormComponent.vue';
import { mapState, mapMutations } from 'vuex';

export default {
	name: 'CreateComponent',
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
				baseNumber: 'dc_base_number',
				number: 'dc_number',
				subject: 'dc_subject',
				content: 'dc_content',
				showContent: 'dc_show_content',
				rawContent: 'dc_raw_content',
				date: 'dc_date',
				commentText: 'dc_com_text',
				listId: 'list_id',
				teacherId: 'thr_id[]',
				/* selectList: 'dc_sel_list',
				selectTeacher: 'dc_sel_list', */
			},
			relFormCount: [],
			docList: [],
			searchedDcNumber: [],
			selectedDcNumber: null,
			addedDcNumbers: [],
			categoryArr: []
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
		showForm: function() {
			return this.categoryArr.length > 0;
		}
  },
  methods: {
		...mapMutations([
      'setRoutes',
      'setErrors',
			'setSucceed',
			'setOld',
    ]),
		/* validationForm: function (){
			let submit = document.getElementById('document-submit');

		}, */
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

			for (let i = 0; i < relFiles.length; i++) {
				disabled = relFiles[i].value ? false : true;

				if (disabled === true) {
					element.disabled = disabled ? true : false
					break;
				}
			}

			if(disabled === false) {
				for (let i = 0; i < files.length; i++) {
					disabled = files[i].value ? false : true;
				}
			}

			if(files.length < 1) {
				disabled = true;
			}

			element.disabled = disabled ? true : false
		},
		/* setShowForm: function(node, instanceId) {
			console.log(node);
			console.log(this.categoryArr);
			this.showForm =  node.id > 0;
		}, */
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
				baseNumber: `rel_dc_base_number[${key}]`,
				number: `rel_dc_number[${key}]`,
				subject: `rel_dc_subject[${key}]`,
				content: `rel_dc_content[${key}]]`,
				showContent: `rel_dc_show_content[${key}]]`,
				rawContent: `rel_dc_raw_content[${key}]]`,
				date: `rel_dc_date[${key}]`,
				commentText: `rel_dc_com_text[${key}]`,
				listId: `rel_list_id[${key}]`,
				teacherId: `rel_thr_id[${key}][]`,
				catId: `rel_dc_cat_id[${key}][]`,
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
			$.get(this.routes.getReqList, (data) => {
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
		},
		loadOptions({ action, searchQuery, callback }) {
			if (action === ASYNC_SEARCH) {
				simulateAsyncOperation(() => {
					
					if(searchQuery.length > 2) {
						this.getTeachersSearchList(searchQuery, callback);
					}else {
						callback(null, [])    
					}
				})
			}
		},
		getTeachersSearchList: function(searchTcNo, callback) {
			$.ajax({
				url: this.routes.getTeachersSearchList,
				type: 'GET',
				dataType: 'JSON',
				data: {
					'searchTcNo': searchTcNo,
					'allData': true
				}
			})
			.done((res) => {
				callback(null, res);
				this.ajaxErrorCount = -1;
			})
			.fail((error) => {
				setTimeout(() => {
					this.ajaxErrorCount++

					if(this.ajaxErrorCount < 3)
						this.getTeachersSearchList(searchTcNo, callback);
					else
						this.ajaxErrorCount = -1;

				}, 100);
				
			})
			.then((res) => {})
		},
		loadDcNumbers({ action, searchQuery, callback }) {
			if (action === ASYNC_SEARCH) {
				simulateAsyncOperation(() => {

					if(searchQuery.length > 2) {
						this.getDocumentSearchList(searchQuery, callback);
					}else {
						callback(null, [])    
					}
				})
			}
		},
		getDocumentSearchList: function(dcNumber, callback) {
			$.ajax({
				url: this.routes.getDocumentSearchList,
				type: 'GET',
				dataType: 'JSON',
				data: {'dcNumber': dcNumber}
			})
			.done((res) => {
				callback(null, res);
				this.searchedDcNumber = res;
				this.ajaxErrorCount = -1;
			})
			.fail((error) => {
				setTimeout(() => {
					this.ajaxErrorCount++

					if(this.ajaxErrorCount < 3)
						this.getDocumentSearchList(dcNumber, callback, instanceId);
					else
						this.ajaxErrorCount = -1;

				}, 100);
				
			})
			.then((res) => {})
		},
		loadPoppever: function (key, content, path = '') {
			if(this.extUdfControl(path)) {
				setTimeout(() => {
					if($(`#dc-show-document-${key}`).length > 0) {
						$(`#dc-show-document-${key}`).popover({
							html: true,
							content: content,
							placement: 'left',
							trigger: 'focus',
							boundary: 'window',
							template: `
								<div class="popover" role="tooltip">
									<div class="arrow"></div>
									<h3 class="popover-header"></h3>
									<div class="popover-body"></div>
								</div>
							`
						});	
					}else {
						this.loadPoppever(key, content, path);
					}
				}, 100);
			}
		},
		addDcNumber: function() {
			for (let i = 0; i < this.searchedDcNumber.length; i++) {
				const item = this.searchedDcNumber[i];
				
				if(this.selectedDcNumber == item.id) {
					if(Object.keys(this.addedDcNumbers).length > 0) {
						for (let j = 0; j < this.addedDcNumbers.length; j++) {
							const addedItem = this.addedDcNumbers[j];

							if(addedItem.id == this.selectedDcNumber) {
								break;
							}

							if(Object.keys(this.addedDcNumbers).length == (j+1)) {
								this.addedDcNumbers.push(item);
								// this.loadPoppever((this.addedDcNumbers.length-1), item.content);
								this.loadPoppever(item.id, item.content, item.path);
							}
						}
					}else {
						this.addedDcNumbers.push(item);
						// this.loadPoppever((this.addedDcNumbers.length-1), item.content);
						this.loadPoppever(item.id, item.content, item.path);
					}
				}//if (this.selectedDcNumber == item.id) end
			}//for end
		},
		delDocument: function (key) {
			this.addedDcNumbers.splice(key, 1);
			this.addedDcNumbers.forEach(dcNumber => {
				this.loadPoppever(dcNumber.id, dcNumber.content, dcNumber.path);
			});
		},
		extUdfControl: function(path) {
			let ext = path.match(/\.[0-9a-z]+$/i)[0];
			let bool = true;
			if(ext != '.udf') {
				bool = false
			}

			return bool;
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

<style scoped>
div.card-header {
	background-color: tomato;
	color: white;
}
div.card-header button i{
	color: white;
}
div.card-footer {
	background-color: tomato;
	color: white;
}
</style>