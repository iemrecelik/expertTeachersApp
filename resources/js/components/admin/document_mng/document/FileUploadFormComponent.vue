<template>
<div>
	<input type="hidden" name="dc_content">
	<div class="row">
		<div class="col-6">
			<div class="form-group">

				<label for="file_upload">Evrak Dosyasını Ekle</label>
				<div class="upload-container">
					<input type="file" 
						id="file_upload" 
						:name="fieldNames.senderFile" 
						@change="uploadForm"
					/>
				</div>
				
				<small id="emailHelp" class="form-text text-muted">
					udf formatındaki evrağı yükleyin.
				</small>

			</div>
		</div>
		
		<div class="col-6">
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
	</div>
	
	<div class="row">
		<div class="col-6">
			
			<div class="form-group">
				<label for="exampleInputEmail1">Gönderen</label>
				<input type="text" class="form-control" id="exampleInputEmail1" 
					disabled  aria-describedby="emailHelp" 
					:name="fieldNames.sender"
					:value="fieldValues.sender"
				>
				<small id="emailHelp" class="form-text text-muted">Evrağı gönderen yer.</small>
			</div>
			
		</div>
		
		<div class="col-6">
			<div class="form-group">

				<label for="exampleInputEmail1">Gönderilen</label>
				<input type="text" class="form-control" id="exampleInputEmail1" 
					disabled 
					aria-describedby="emailHelp" 
					:name="fieldNames.receiver"
					:value="fieldValues.receiver"
				>
				<small id="emailHelp" class="form-text text-muted">
					Evrağın gönderildiği yer.
				</small>

			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-2">
			
			<div class="form-group">
				<label for="exampleInputEmail1">Evrak Numarası</label>
				<input type="text" class="form-control" id="exampleInputEmail1" 
					disabled
					aria-describedby="emailHelp" 
					:name="fieldNames.number"
					:value="fieldValues.number"
				>
				<small id="emailHelp" class="form-text text-muted">
					Evrağın benzersiz numarası.
				</small>
			</div>
			
		</div>
		
		<div class="col-8">
			
			<div class="form-group">
				<label for="exampleInputEmail1">Evrak Konusu</label>
				<input type="text" class="form-control" id="exampleInputEmail1" 
					disabled
					aria-describedby="emailHelp" 
					:name="fieldNames.subject"
					:value="fieldValues.subject"
				>
				<small id="emailHelp" class="form-text text-muted">
					Evrağın konusunu içerir.
				</small>
			</div>
			
		</div>
		
		<div class="col-2">
			
			<div class="form-group">
				<label for="exampleInputEmail1">Evrak Tarihi</label>
				<input type="text" class="form-control" id="exampleInputEmail1" 
					disabled
					aria-describedby="emailHelp" 
					:name="fieldNames.date"
					:value="fieldValues.date"
				>
				<small id="emailHelp" class="form-text text-muted">
					Evrağın gönderildiği tarih.
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
			},
		}
  },
	props: {
		ppfieldNames: {
			type: Object,
			required: true,
		}
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
		uploadForm: function(event){      
      // let form = $('#' + this.formIDName)[0];
      /* let form = $('#' + this.$parent.$parent.formIDName)[0];

      let data = new FormData(form); */

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
        }
      })
      .then((res) => {
        // this.$parent.dataTable.ajax.reload();
      })
      .always(() => {
        // this.formElement.scrollTo(0, 0);
        // btn.classList.remove("running");
      });
    },
	}
}
</script>

<style>

</style>