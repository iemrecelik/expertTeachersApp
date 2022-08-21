@extends('admin.base.index')
@section('contents')
<span>
  {{-- {{ json_encode(session()->getOldInput()) }} --}}
  <h1>
    {{ old('dc_item_status') }}
  </h1>
  
</span>

  <doc-mng-create-document-component
    :pproutes="{
      getCategory: '{{ route('admin.document_mng.category.getCategory') }}', 
      dcStore: '{{ route('admin.document_mng.document.store') }}', 
      getFileInfos: '{{ route('admin.document_mng.document.getFileInfos') }}', 
    }"
    :pperrors="{{ count($errors) > 0?$errors:'{}' }}"
    :ppsuccess="'{{ session('succeed') ?? '' }}'"
    :ppoldinput="{
      dc_item_status: '{{ old('dc_item_status') }}', 
      dc_cat_id: '{{ old('dc_cat_id') ?? 0 }}', 
    }"
  >
  </doc-mng-create-document-component>
@endsection