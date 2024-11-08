@extends('admin.base.index')
@section('contents')
  <doc-mng-search-component
    :pproutes="{
      getSearchDocuments: '{{ route('admin.document_mng.search.getSearchDocuments') }}', 
      show: '/admin/document-management/search', 
      getCategoryAndList: '{{ route('admin.document_mng.search.getCategoryAndList') }}', 
      getTeachersSearchList: '{{ route('admin.teachers.searchList') }}',
      getCategory: '{{ route('admin.document_mng.category.getCategory') }}', 
      getList: '{{ route('admin.document_mng.list.getList') }}', 
      getListAndSelected: '{{ route('admin.document_mng.list.getListAndSelected') }}', 
      addList: '{{ route('admin.document_mng.list.addList') }}',
      deleteList: '{{ route('admin.document_mng.list.deleteList') }}',
      getComments: '{{ route('admin.document_mng.comment.getComments') }}',
      addComment: '{{ route('admin.document_mng.comment.addComment') }}',
      deleteComment: '{{ route('admin.document_mng.comment.deleteComment') }}',
      getRecordNeedDocuments: '{{ route('admin.document_mng.report.getRecordNeedDocuments') }}',
    }"
    :ppdatas="{{ json_encode($datas) }}"
  >
  </doc-mng-search-component>
@endsection