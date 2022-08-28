<template>
<div>
	<input type="hidden" :name="fieldNames.content" :value="fieldValues.content">
	<input type="hidden" :name="fieldNames.rawContent" :value="fieldValues.rawContent">
	
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

	<div class="row" v-if="showForm">
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
					<div class="upload-container">
						<input type="file" id="file_upload" multiple
							:name="fieldNames.senderAttachFiles"
						/>
					</div>
					<small id="emailHelp" 
						class="form-text text-muted">
							Buraya evrakın eklerini ekleyiniz.
					</small>

				</div>
		</div>

		<div class="col-5">

			<div class="form-group mb-1">
				<label for="exampleInputEmail1">Evrak Numarası</label>
				<input type="text" class="form-control" id="exampleInputEmail1" 
					readonly
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
					readonly
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
					readonly 
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
					readonly  
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
					readonly
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
import { EQUALITY_BINARY_OPERATORS } from '@babel/types';
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
			},
			itemStatus: this.ppitemStatus == 0 ? "selected" : "",
			showForm: false,
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
    ]),
  },
	methods: {
		...mapMutations([
      'setErrors',
    ]),
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
        url: this.routes.getFileInfos,
        enctype: 'multipart/form-data',
        type: 'POST',
        data: data,
        processData: false,
        contentType: false,
        cache: false,
      })
      .done((res) => {
				// console.log(res);
				// this.text = res;
				this.fieldValues = res;
        /* this.setErrors('');
        this.setSucceed(res.succeed); */
      })
      .fail((error) => {
        if(error.responseJSON){
          // this.setSucceed('');
          this.setErrors(error.responseJSON.errors);
					this.$parent.$parent.modalErrorMsgShow(true)

					let files = event.target;
					files.value = null;
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
	}
}
</script>

<style>

</style>