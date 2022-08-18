@extends('admin.base.index')
@section('contents')
  <doc-mng-create-document-component
    :pproutes="{
      getCategory: '{{ route('admin.document_mng.category.getCategory') }}', 
      dcStore: '{{ route('admin.document_mng.document.store') }}', 
      getFileInfos: '{{ route('admin.document_mng.document.getFileInfos') }}', 
    }"
    :pperrors="{{ count($errors) > 0?$errors:'{}' }}"
  >
  </doc-mng-create-document-component>
@endsection