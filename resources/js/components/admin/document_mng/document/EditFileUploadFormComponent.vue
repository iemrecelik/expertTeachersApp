<template>
<div>
	<div class="row">
		<input type="hidden" :name="fieldNames.id" :value="this.data.id">
		<div class="col-3">
			<div class="form-group">
				<label for="validationCustom04">Evrağın Durumu</label>
				<select id="validationCustom04" 
					class="custom-select"
					required 
					:name="fieldNames.itemStatus"
					@change="showingForm"
				>
					<option disabled value="2">Evrak Durumunu Seçiniz.</option>
					<option :selected="this.data.dc_item_status == 1" value="1">Giden Evrak</option>
					<option :selected="this.data.dc_item_status == 0" value="0">Gelen Evrak</option>
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
		<div class="col-3 pl-4">
			<div class="form-check form-check-inline">
				<!-- <input class="form-check-input" type="checkbox" id="inlineCheckbox1" v-model="manuelEnter" @change="manuelEnterCheck"> -->
				<!-- <input class="form-check-input" type="checkbox" :id="'inlineCheckbox1'+elUniqueID" @change="manuelEnterCheck"> -->
				<input class="form-check-input" type="checkbox" :id="'inlineCheckbox1'+elUniqueID" v-model="manuelEnter">
				<label class="form-check-label" :for="'inlineCheckbox1'+elUniqueID">
					Manuel giriş yapınız.
				</label>
			</div>
		</div>
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
							<div v-else-if="item.uploaded == true">
								<b><u>Yüklü Dosya :</u></b>
								<input type="hidden" :name="fieldNames.uploadedSenderAttachFiles" :value="item.id">
							</div>
							<div v-else>
								<div  class="progress">
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
				<!-- <textarea id="exampleInputEmail1" class="form-control" 
					:readonly="inputReadonly" 
					rows="4"
					aria-describedby="emailHelp"
					:name="fieldNames.subject"
					:value="fieldValues.subject.trim()" 
				>
				</textarea> -->
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
</div>
</template>

<script>
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
			data: Object.keys(this.ppdata).length > 0 ? 
				this.ppdata :
				{
					dc_item_status: '',
					dc_attach_files: []
				},
			attachFileCount: 0,
			copyHtmlFileList: []
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
		ppdata: {
			type: Object,
			required: false,
			default: {}
		}
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
				};
			}
		},
		setShowForm: async function(event) {
			this.showForm = event.target.value < 2 ? true : false;
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
							// el.classList.add("bg-success");
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
		reloadHtmlFileList: function(key) {
			this.copyHtmlFileList.splice(key, 1);
			/* console.log('***************************');
			console.log('key: ', key);
			console.log(this.copyHtmlFileList); */

			this.copyHtmlFileList.forEach((item, itemKey) => {
				this.htmlFileList.push({
					fileName: item.fileName, 
					fileKey: itemKey,
					uploaded: item.uploaded == true ? true : false,
					id: item.id ?? null
				});
			});

			/* console.log('------------------');
			console.log(this.dT.files); */
		},
		delFileList: function(key) {
			let fileInput = this.getFileInputElement();

			let fileListItemProm = new Promise((resolve, reject) => {
				//this.copyHtmlFileList = JSON.parse(JSON.stringify(nestedNumthis.htmlFileListbers));;
				this.copyHtmlFileList = [...this.htmlFileList];
				this.htmlFileList = [];

				this.copyHtmlFileList.forEach((obj, objKey) => {
					if(obj.fileKey === key) {
						if(obj.uploaded) {
							this.attachFileCount--;
							this.reloadHtmlFileList(key);

							resolve(obj.uploaded);
						}else {
							resolve(obj.fileName);
						}
					}
				});
			});

			fileListItemProm.then((fileName) => {

				if(fileName !== true) {
					for (const [key, file] of Object.entries(this.dT.files)) {

						if(file.name === fileName) {
							this.dT.items.remove(key);
							this.reloadHtmlFileList((parseInt(key)+parseInt(this.attachFileCount)));
						}/* else {
							this.htmlFileList.push({fileName: file.name, fileKey: key});
						} */					
					}
				}

				fileInput.files = this.dT.files;
				
			}).catch((err) => {
				console.log(err);
			});
    },
	},
	created() {
		if(this.data) {
			this.showForm = true;
			this.fieldValues = {
				'sender': this.data.dc_who_send,
				'subjectNumber': this.data.dc_number,
				'number': this.data.dc_number,
				'date': this.data.dc_date,
				'subject': this.data.dc_subject,
				'receiver': this.data.dc_who_receiver,
				'content': this.data.dc_content,
				'rawContent': this.data.rawContent,
				'showContent': this.data.showContent,
			}

			if(this.data.dc_attach_files !== undefined) {
				this.attachFileCount = this.data.dc_attach_files.length;

				this.data.dc_attach_files.forEach((file, key) => {
					this.htmlFileList.push({
						fileName: this.getFileNameInPathFunc(file.dc_att_file_path),
						fileKey: key,
						uploaded: true,
						id: file.id
					});
				});
			}
		}
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
	height: 211px;
	overflow-y: scroll;
} 
#fileNameList > div{
	width: 99%;
	display: table;
	border: 2px solid #123dd9;
	padding: 3px 3px 0px 7px;
}
</style>