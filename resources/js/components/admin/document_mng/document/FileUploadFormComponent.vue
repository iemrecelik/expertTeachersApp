<template>
<div>
	<div v-if="ppitemStatus == 0" class="row">

		<div class="col-3">
			<div class="form-group">
				<label for="exampleInputEmail1">
					{{ $t('messages.categoryName') }}
				</label>
				<treeselect
					id="doc-category"
					:name="fieldNames.catId"
					:multiple="true"
					:options="categoryList"
					v-model=relCategoryArr
					:disable-branch-nodes="true"
					:show-count="true"
					:placeholder="$t('messages.enterCategoryName')"
				/>
			</div>
		</div>
		<div class="col-9"></div>

	</div>

	<div class="row">
		
		<div class="col-3">
			<div class="form-group">
				<label for="validationCustom04">Evrağın Durumu</label>
				<select id="validationCustom04" 
					class="custom-select"
					required 
					:name="fieldNames.itemStatus"
					@change="showingForm"
				>
					<option selected value="2">Evrak Durumunu Seçiniz.</option>
					<option value="1">Giden Evrak</option>
					<option value="0">Gelen Evrak</option>
					<!-- <option :selected=itemStatus value="0">Gelen Evrak</option> -->
				</select>
				<div class="invalid-feedback">
					Lütfen evrağın durumunu seçiniz.
				</div>
			</div>
		</div>

		<div class="col-9"></div>
	</div>

	<div class="row mb-3">
		<div class="col-1 border-right">
			<button type="button" 
				class="btn btn-sm btn-danger"
				@click="resetFieldValues"
			>
				{{$t('messages.resetForm')}}
			</button>
		</div>
		<div class="col-2 pl-4">
			<div class="form-check form-check-inline">
				<!-- <input class="form-check-input" type="checkbox" id="inlineCheckbox1" v-model="manuelEnter" @change="manuelEnterCheck"> -->
				<!-- <input class="form-check-input" type="checkbox" :id="'inlineCheckbox1'+elUniqueID" @change="manuelEnterCheck"> -->
				<input class="form-check-input" type="checkbox" :id="'inlineCheckbox1'+elUniqueID" v-model="manuelEnter">
				<label class="form-check-label" :for="'inlineCheckbox1'+elUniqueID">
					Manuel giriş yapınız.
				</label>
			</div>
		</div>

		<!-- <div class="col-2 pl-4">
			<div class="form-check form-check-inline">
				<input class="form-check-input" type="checkbox" :id="'sel_list'+elUniqueID">
				<label class="form-check-label" :for="'sel_list'+elUniqueID">
					Liste'ye Ekle
				</label>
			</div>
		</div>

		<div class="col-2 pl-4">
			<div class="form-check form-check-inline">
				<input class="form-check-input" type="checkbox" :id="'teach_list'+elUniqueID">
				<label class="form-check-label" :for="'teach_list'+elUniqueID">
					Öğretmeni Ekle
				</label>
			</div>
		</div> -->
	</div>

	<div class="row" v-if="showForm">
		<input type="hidden" :name="fieldNames.content" :value="fieldValues.content">
		<input type="hidden" :name="fieldNames.rawContent" :value="fieldValues.rawContent">
		<input type="hidden" :name="fieldNames.showContent" :value="fieldValues.showContent">
		<input type="hidden" name="dc_manuel" :value="manuel">
		
		<div class="col-4">
			<div class="form-group">

				<label for="file_upload">Evrak Dosyasını Ekle</label>
				<div class="upload-container">
					<input type="file" 
						:class="getFileInputClassName(fieldNames.senderFile)" 
						:name="fieldNames.senderFile" 
						@change="uploadForm"
					/>
				</div>
				<small id="emailHelp" class="form-text text-muted">
					udf formatındaki evrağı yükleyin.
				</small>

			</div>

			<div class="form-group">

				<label for="file_upload">Evrak Ek(leri)ini Ekle</label>

				<div id="dropContainer"
					@dragover="fileOnDragenter"
					@drop="fileOnDrop"
				>
					Dosya(ları) buraya sürükleyip bırakınız.
					<div id="fileNameList">
						<div class="mt-2" :key="key" v-for="(item, key) in htmlFileList">
							<div v-if="item.uploaded == false">
								<b><u>Ön yükleme tamamlandı.</u></b> 
								<a class="text-success">
									<i class="bi bi-check-circle-fill"></i>
								</a>
							</div>
							<div v-else>
								<div class="progress">
									<div :class="`progress-bar progress-bar-striped progress-bar-animated progress-${elUniqueID}-${item.fileKey}`"
										role="progressbar" 
										aria-valuenow="0" 
										aria-valuemin="0" 
										aria-valuemax="100" 
										style="width: 0%"
									>
									</div>
								</div>
							</div>

							<div class="w-75 float-left">{{item.fileName}}</div>
							<div class="float-right" style="cursor:pointer" @click="delFileList(item.fileKey)">
								<i class="bi bi-x-circle-fill delete-list-icon"></i>
							</div>
						</div>
					</div>
				</div>
					<!-- Manuel olarak eklemek için: -->
				<input type="file" :id="fileInputId" multiple
					:name="fieldNames.senderAttachFiles"
				/>
			</div>
		</div>

		<div class="col-4">

			<div class="row">
				<div class="col-12">
					
					<div class="form-group mb-1">
						<label for="exampleInputEmail1">Evrak Numarası</label>
						<input type="text" class="form-control" id="exampleInputEmail1" 
							:readonly="inputReadonly"
							aria-describedby="emailHelp" 
							:name="fieldNames.number"
							v-model="fieldValues.number"
						>
						<!-- <small id="emailHelp" class="form-text text-muted">
							Evrağın benzersiz numarası.
						</small> -->
					</div>

					<div class="form-group">
						<label for="exampleInputEmail1">Evrak Tarihi</label>
						<input type="text" class="form-control" id="exampleInputEmail1" 
							:readonly="inputReadonly"
							aria-describedby="emailHelp" 
							:name="fieldNames.date"
							v-model="fieldValues.date"
						>
						<small id="emailHelp" class="form-text text-muted">
							Evrağın gönderildiği tarih.
						</small>
					</div>
					
					<div class="form-group">
						<label for="exampleInputEmail1">Evrak Konusu</label>
						<textarea id="exampleInputEmail1" class="form-control" 
							:readonly="inputReadonly" 
							rows="4"
							aria-describedby="emailHelp"
							:name="fieldNames.subject"
							v-model="fieldValues.subject" 
						>
						</textarea>
						<small id="emailHelp" class="form-text text-muted">
							Evrağın konusunu içerir.
						</small>
					</div>

				</div>
			</div>
			<div class="row">
				<div class="col-12">
					<div class="form-group mb-1">
						<label>Dava Esas Numarası</label>
						<input type="text" class="form-control input-base-number"
							aria-describedby="baseNumberHelp" 
							:name="fieldNames.baseNumber"
							v-model="fieldValues.baseNumber"
							data-inputmask-regex="^20[0-9]{2}/[0-9]*$" 
							data-mask
						>
					</div>
				</div>
			</div>
			
		</div>

		<div class="col-4">
			
			<div class="form-group">
				<label for="exampleInputEmail1">Gönderen</label>
				<textarea id="exampleInputEmail1" class="form-control" 
					rows="4"
					:readonly="inputReadonly"  
					aria-describedby="emailHelp" 
					:name="fieldNames.sender"
					v-model="fieldValues.sender"
				>
				</textarea>
				<small id="emailHelp" class="form-text text-muted">Evrağı gönderen yer.</small>
			</div>

			<div class="form-group">

				<label for="exampleInputEmail1">Gönderilen</label>
				<textarea id="exampleInputEmail1" class="form-control" 
					rows="4"
					:readonly="inputReadonly"
					aria-describedby="emailHelp" 
					:name="fieldNames.receiver"
					v-model="fieldValues.receiver" 
				>
				</textarea>
				<small id="emailHelp" class="form-text text-muted">
					Evrağın gönderildiği yer.
				</small>

			</div>

		</div>
	</div>

	<div class="row">
		<div class="col-4">
			
			<div class="form-group">
				<label for="exampleFormControlTextarea1">Notunuz</label>
				<textarea class="form-control" 
					id="exampleFormControlTextarea1" 
					rows="3"
					:name="fieldNames.commentText"
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
					:name="fieldNames.listId"
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
					:name="fieldNames.teacherId"
				/>
			</div>
			
		</div>
	</div>
</div>
</template>

<script>
import Treeselect from '@riophae/vue-treeselect';
import { ASYNC_SEARCH } from '@riophae/vue-treeselect';

const simulateAsyncOperation = fn => {
  setTimeout(fn, 2000)
}
// import the styles
import '@riophae/vue-treeselect/dist/vue-treeselect.css';

import { mapState, mapMutations } from 'vuex';

export default {
	name: 'FileUploadFormComponent',
	data () {
    return {
			fieldNames: this.ppfieldNames,
			fieldValues: {
				'sender': '',
				'subjectNumber': '',
				'number': '',
				'date': '',
				'subject': '',
				'receiver': '',
				'content': '',
				'rawContent': '',
				'showContent': '',
				'commentText': '',
				'listId': '',
				'teacherId': '',
			},
			itemStatus: this.ppitemStatus == 0 ? "selected" : "",
			showForm: false,
			inputReadonly: false,
			manuel: 0,
			dT: new DataTransfer(),
			htmlFileList: [],
			reader: new FileReader(),
			dropFileErrors: {},
			// fileInputId: this.getFileInputIdGenerate(),
			fileInputId: this.uniqueDomID('fileInput'),
			elUniqueID: this.uniqueID(),
			// manuelEnter: 'manuelEnter'+elUniqueID;
			manuelEnter: false,
			docList: this.$parent.$parent.docList,
			relCategoryArr: [],
			categoryList: this.$parent.$parent.categoryList,
		}
  },
	props: {
		ppfieldNames: {
			type: Object,
			required: true,
		},
		ppitemStatus: {
			type: Number,
			required: true,
		},
	},
	watch: {
    manuelEnter(newManuelEnter, oldManuelEnter) {
      this.inputReadonly = newManuelEnter === true ? false: true;
    }
  },
  computed: {
    ...mapState([
      'routes',
    ])
  },
	methods: {
		...mapMutations([
      'setErrors',
    ]),
		/* manuelEnterCheck(event) {
			console.log(event.target.value);
			// console.log(this.manuelEnter);
			// console.log(event.target.value == 'on');
			this.inputReadonly = event.target.value === true ? false: true;
			// this.inputReadonly = this.manuelEnter === true ? false: true;
		}, */
		getFileInputElement() {
			return document.getElementById(this.fileInputId);
		},
		resetFieldValues() {
			if(this.inputReadonly === false) {
				this.fieldValues = {
					'sender': '',
					'subjectNumber': '',
					'number': '',
					'date': '',
					'subject': '',
					'receiver': '',
					'content': '',
					'rawContent': '',
					'showContent': '',
					'baseNumber': '',
				};
			}
		},
		setShowForm: async function(event) {
			this.showForm = event.target.value < 2 ? true : false;
			if(this.showForm) {
				this.$nextTick(function () {
					var inputBaseNumber = document.getElementsByClassName("input-base-number");
					
					for (let i = 0; i < inputBaseNumber.length; i++) {
						const element = inputBaseNumber[i];
						var im = new Inputmask();
						im.mask(element);
					}
				})
			}
		},
		showingForm: function(event) {
			this.setShowForm(event).then(() => {
				this.$parent.$parent.checkForm();
			});
		},
		getFileInputClassName: function(rawFileName) {
			return this.$parent.$parent.getFileInputClassName(rawFileName);
		},
		uploadForm: function(event) {
			let files = event.target.files;

			let data = new FormData();

			for (let i = 0; i < files.length; i++) {
				data.append(event.target.name, files[i])
			}
			
      /* let btn = event.target;
      btn.classList.add("running"); */

      $.ajax({
        url: this.routes.udfControl,
        enctype: 'multipart/form-data',
        type: 'POST',
        data: data,
        processData: false,
        contentType: false,
        cache: false,
      })
      .done((res) => {
				if(res.showContent) {
					this.inputReadonly = true;
					this.fieldValues = res;
				}else if(res.content) {
					this.fieldValues.content = res.content;
					this.inputReadonly = false;
				}else {
					event.target.value = "";
				}
        /* this.setErrors('');
        this.setSucceed(res.succeed); */
      })
      .fail((error) => {
        if(error.responseJSON){
					if(
						typeof error.responseJSON.errors.manuel === 'object' &&
						error.responseJSON.errors.manuel !== null
					) {
						this.inputReadonly = false;
						this.manuel = 1;
						this.fieldValues.content = error.responseJSON.errors.content;

						delete error.responseJSON.errors.manuel;
						delete error.responseJSON.errors.content;

					}else {
						this.inputReadonly = false;
						/* let files = event.target;
						files.value = null; */
					}

					this.setErrors(error.responseJSON.errors);
					this.$parent.$parent.modalErrorMsgShow(true);
					
        }
      })
      .then((res) => {
        // this.$parent.dataTable.ajax.reload();				
      })
      .always(() => {
				this.$parent.$parent.checkForm();
        // this.formElement.scrollTo(0, 0);
        // btn.classList.remove("running");
      });
    },
		fileOnDrop: function(evt) {

			let fileInput = this.getFileInputElement();
			let filesCount = evt.dataTransfer.files.length;
			let co = 0;
			
			let errors = {};
			for (const [key, file] of Object.entries(evt.dataTransfer.files)) {
				let reader = new FileReader();
				let addBool = true;

				reader.readAsText(file);

				reader.onloadstart = () => {
					/* Resim yüklenene kadar kaydet butonu pasif olsun start */
					let element = document.getElementById('document-submit');
					element.disabled = true;
					/* Resim yüklenene kadar kaydet butonu pasif olsun end */

					let index = this.htmlFileList.findIndex(object => {
						return object.fileName === file.name;
					});

					if(index < 0) {
						this.htmlFileList.push({fileName: file.name, fileKey: (this.htmlFileList.length + 1)});
					}else {
						addBool = false;
					}
				};
				
				reader.onload = () => {

					if(addBool) {
						if(reader.result.length > 0) {

							this.dT.items.add(file);
						}else {
							let index = this.htmlFileList.findIndex(object => {
								return object.fileName === file.name;
							});
							this.htmlFileList.splice(index, 1);

							errors['fileDropException'+key] = [
								`"${file.name}" isimli dosya boş yada bozuk olduğu için eklenemedi.`
							];
						}
					}
				};

				reader.onerror = () => {
					if(reader.result.length < 1) {
						errors['fileDropException'+key] = [
							`"${file.name}" isimli dosya boş yada bozuk olduğu için eklenemedi.`
						];
					}
				};

				reader.onprogress = (event) => {
					let fileListItem = this.htmlFileList.find(object => {
						return object.fileName === file.name;
					});

					let progress = (event.loaded * 100) / event.total;
					
					let el = document.getElementsByClassName(`progress-${this.elUniqueID}-${fileListItem.fileKey}`)[0];

					el.style.width = progress+"%";
					el.style['aria-valuenow'] = progress+"%";

					if(progress === 100) {
						setTimeout(() => {
							el.parentElement.parentElement.innerHTML=`
								<b><u>Ön yükleme tamamlandı.</u></b> 
								<a class="text-success">
									<i class="bi bi-check-circle-fill"></i>
								</a>
							`;
							el.parentElement.remove();
						}, 500);
						// el.classList.add("bg-success");
					}
				};

				reader.onloadend = () => {
					co++;

					if(co == filesCount) {
						/* Bütün resimler yüklendikten sonra kaydet butonu aktif olsun start*/
						let element = document.getElementById('document-submit');
						element.disabled = false;
						/* Bütün resimler yüklendikten sonra kaydet butonu aktif olsun end*/

						if(Object.keys(errors).length >0) {
							this.setErrors(errors);
							this.$parent.$parent.modalErrorMsgShow(true);			
						}
						fileInput.files = this.dT.files;			
					}
				};
			}

			evt.preventDefault();
		},
		fileOnDragenter: function(evt) {
			evt.preventDefault();
		},
		delFileList: function(key) {
			let fileInput = this.getFileInputElement();

			let fileListItemProm = new Promise((resolve, reject) => {

				this.htmlFileList.find(obj => {
					if(obj.fileKey === key) {
						resolve(obj.fileName);
					}
				});
			});

			fileListItemProm.then((fileName) => {

				this.htmlFileList = [];
				for (const [key, file] of Object.entries(this.dT.files)) {

					if(file.name === fileName) {
						this.dT.items.remove(key);
					}else {
						this.htmlFileList.push({fileName: file.name, fileKey: key, uploaded: false});
					}					
				}

				fileInput.files = this.dT.files;
				
			}).catch((err) => {
				console.log(err);
			});
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
	},
	mounted() {
    /* var inputBaseNumber = document.getElementById("inputBaseNumber");
		if(inputBaseNumber) {
			var im = new Inputmask();
    	im.mask(inputBaseNumber);
		} */
	},
	components: {
    Treeselect,
  }
}
</script>

<style>
.upload-container {
	position: relative;
}
.upload-container input {
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
.upload-btn {
	margin-left: 300px;
	padding: 7px 20px;
}
#dropContainer {
	border: 1px solid #92b0b3;
	/* background: #f1f1f1;
	outline: 2px dashed #92b0b3;
	outline-offset: -10px; */
	padding: 10px 0px 0px 4px;
	margin-bottom: 5px;
	width: 100%;
	height: 111px;
	overflow-y: scroll;
} 
#fileNameList > div{
	width: 99%;
	display: table;
	border: 2px solid #123dd9;
	padding: 3px 3px 0px 7px;
}
</style>