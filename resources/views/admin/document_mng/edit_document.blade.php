@extends('admin.base.index')
@section('contents')
  <doc-mng-edit-document-component
    :pproutes="{
      getCategory: '{{ route('admin.document_mng.category.getCategory') }}', 
      dcStore: '{{ route('admin.document_mng.document.store') }}', 
      getFileInfos: '{{ route('admin.document_mng.document.getFileInfos') }}', 
      udfControl: '{{ route('admin.document_mng.document.udfControl') }}', 
      getList: '{{ route('admin.document_mng.list.getList') }}',
      getReqList: '{{ route('admin.document_mng.list.getReqList') }}',
      getTeachersSearchList: '{{ route('admin.teachers.searchList') }}',
      update: '{{ route('admin.document_mng.document.update') }}',
      getDocumentSearchList: '{{ route('admin.document_mng.document.searchList') }}',
    }"
    :ppdata="{{ json_encode($data) }}"
    :pperrors="{{ count($errors) > 0?$errors:'{}' }}"
    :ppsuccess="'{{ session('succeed') ?? '' }}'"
    :ppoldinput="{
      dc_item_status: '{{ old('dc_item_status') }}', 
    }"
  >
  </doc-mng-edit-document-component>
@endsection