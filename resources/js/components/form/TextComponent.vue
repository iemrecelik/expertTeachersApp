<template>
	<div class="form-group">
    <label :for="idName"
      v-html="getLabelHtml()"
    >
    </label>
    <error-msg-component
      :ppsettings="{
        fieldName,
        renderType
      }"
    >
    </error-msg-component>
    <input type="text" class="form-control" 
	    :id="idName" 
	    :aria-describedby="ariaDescribedby" 
	    :name="fieldName" 
	    :placeholder="fieldLabelName"
      :value="value"
    />
  </div>
</template>

<script>
export default {
  name: 'TextComponent',
  data () {
    return {
      fieldName: this.ppfieldname,
      funcs: this.ppfuncs,
    };
  },
  props: {
    ppfieldname: {
      type: String,
      required: true,
    },
    ppfieldlabelname: {
      type: String,
      required: false,
    },
    ppvalue: {
      type: Object,
      required: false,
      default: ''
    },
    ppfuncs: {
      type: Object,
      required: true,
    },
    ppnecessity: {
      type: Boolean,
      required: true,
    },
  },
  computed: {
    filtFieldName: function(){
      return this.funcs.filtFieldName(this.fieldName);
    },
    idName: function () {
      return this.funcs.idName(this.filtFieldName);
    },
    ariaDescribedby: function () {
      return this.funcs.ariaDescribedby(this.filtFieldName);
    },
    fieldLabelName: function(){
      return this.ppfieldlabelname ?? this.funcs.fieldLabelName(this.filtFieldName);
    },
    value: function(){
      return this.ppvalue.val;
    },
    renderType: function(){
      return this.ppvalue.settings.renderType || 0;
    },
  },
  methods: {
    getLabelHtml: function() {
      return this.fieldLabelName + (this.ppnecessity ? " <span class='text-danger'>*</span>" : "");
    }
  }
}
</script>