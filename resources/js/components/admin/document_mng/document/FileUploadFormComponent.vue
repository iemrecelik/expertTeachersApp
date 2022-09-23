<template>
<div>
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
		<div class="col-12">
			<button type="button" 
				class="btn btn-md btn-danger"
				@click="resetFieldValues"
			>
				{{$t('messages.resetForm')}}
			</button>
		</div>
	</div>

	<div class="row" v-if="showForm">
		<input type="hidden" :name="fieldNames.content" :value="fieldValues.content">
		<input type="hidden" :name="fieldNames.rawContent" :value="fieldValues.rawContent">
		<input type="hidden" :name="fieldNames.showContent" :value="fieldValues.showContent">
		<input type="hidden" name="dc_manuel" :value="manuel">
		
		<div class="col-3">
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
					<!-- <div class="upload-container">
						<input type="file" id="file_upload" multiple
							:name="fieldNames.senderAttachFiles"
						/>
					</div> -->

					<!-- <small id="emailHelp" 
						class="form-text text-muted">
							Buraya evrakın eklerini ekleyiniz.
					</small> -->

					<div id="dropContainer"
						@dragover="fileOnDragenter"
						@drop="fileOnDrop"
					>
						Dosya(ları) buraya sürükleyip bırakınız.
						<div id="fileNameList">
							<div class="mt-2" v-for="(item, key) in htmlFileList">
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

								<div class="w-75 float-left">{{item.fileName}}</div>
								<div class="float-right" style="cursor:pointer" @click="delFileList(key)">
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

		<div class="col-5">

			<div class="form-group mb-1">
				<label for="exampleInputEmail1">Evrak Numarası</label>
				<input type="text" class="form-control" id="exampleInputEmail1" 
					:readonly="inputReadonly"
					aria-describedby="emailHelp" 
					:name="fieldNames.number"
					:value="fieldValues.number"
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
					:value="fieldValues.date"
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
					:value="fieldValues.subject.trim()" 
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
					:value="fieldValues.sender.trim()"
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
					:value="fieldValues.receiver.trim()" 
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
			fileInputId: this.getFileInputIdGenerate(),
			elUniqueID: this.uniqueID(),
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
  computed: {
    ...mapState([
      'routes',
    ])
  },
	methods: {
		...mapMutations([
      'setErrors',
    ]),
		getFileInputIdGenerate() {
			return 'fileInput'+ this.uniqueId;
		},
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
						let files = event.target;
						files.value = null;
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

			// let addFileListProm = new Promise((resolve, reject) => {

				// let filesCount = evt.dataTransfer.files.length;
				// let errors = {};

			// 	for (const [key, file] of Object.entries(evt.dataTransfer.files)) {

			// 		this.reader.readAsText(file);

			// 		/* file onload start */
			// 		this.reader.onload = () => {

			// 			if(this.reader.result.length > 0) {

			// 				this.htmlFileList.push({fileName: file.name, fileKey: key});
			// 				this.dT.items.add(file);
			// 			}else {
			// 				errors['fileDropException'+key] = [
			// 					`"${file.name}" isimli dosya boş yada bozuk olduğu için eklenemedi.`
			// 				];
			// 			}
			// 		};
			// 		/* file onload end */

			// 		/* file onerror start */
			// 		this.reader.onerror = () => {
			// 			if(this.reader.result.length < 1) {
			// 				errors['fileDropException'+key] = [
			// 					`"${file.name}" isimli dosya boş yada bozuk olduğu için eklenemedi.`
			// 				];
			// 			}
			// 		};
			// 		/* file onerror end */
					
			// 		this.reader.onloadend = () => {
			// 			if((parseInt(key)+1) == filesCount) {
			// 				resolve(errors);
			// 			}
			// 		};
			// 	}
			// });

			// addFileListProm
			// 	.then((data) => {
			// 		console.log(data);

			// 		if(Object.keys(obj).length === 0) {
			// 			this.setErrors(data);
			// 			this.$parent.$parent.modalErrorMsgShow(true);
			// 		}

			// 		fileInput.files = this.dT.files;

			// 		evt.preventDefault();
			// 	})
			// 	.catch((data) => {
			// 		console.log('data rej')
			// 		console.log(data)
			// 	});

					/* this.reader.onloadend = () => {
						if((parseInt(key)+1) == filesCount) {
							resolve(errors);
						}
					}; */

			let fileInput = this.getFileInputElement();
			let filesCount = evt.dataTransfer.files.length;
			let co = 0;
			
			let errors = {};
			for (const [key, file] of Object.entries(evt.dataTransfer.files)) {
				let reader = new FileReader();
				let fileKey = this.htmlFileList.length + 1;

				reader.readAsText(file);

				reader.onloadstart = () => {
					this.htmlFileList.push({fileName: file.name, fileKey});
				};
				
				reader.onload = () => {
					console.log('reader.result.length', reader.result.length)
					if(reader.result.length > 0) {

						// this.htmlFileList.push({fileName: file.name, fileKey: key});
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
				};

				reader.onerror = () => {
					if(reader.result.length < 1) {
						errors['fileDropException'+key] = [
							`"${file.name}" isimli dosya boş yada bozuk olduğu için eklenemedi.`
						];
					}
				};

				reader.onprogress = (event) => {
					let progress = (event.loaded * 100) / event.total;
					
					let el = document.getElementsByClassName(`progress-${this.elUniqueID}-${fileKey}`)[0];

					el.style.width = progress+"%";
					el.style['aria-valuenow'] = progress+"%";

					console.log(el);
					console.log('yüklenen: ', event.loaded);
					console.log('toplam:', event.total);
					console.log('yüzde: ', progress);
				};

				reader.onloadend = () => {
					co++;
					console.log(filesCount)
					console.log(co);

					if(co == filesCount) {
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

			this.dT.items.remove(key);

			this.htmlFileList = [];
			for (const [key, file] of Object.entries(this.dT.files)) {
				this.htmlFileList.push({fileName: file.name, fileKey: key});
			}

			fileInput.files = this.dT.files;
    },
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
	display: table;
	border: 2px solid #123dd9;
	padding: 3px 3px 0px 7px;
}
</style>